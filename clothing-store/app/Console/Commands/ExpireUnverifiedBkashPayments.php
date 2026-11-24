<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CancelOrderAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpireUnverifiedBkashPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:expire-unverified-bkash
                            {--hours=24 : Hours after which unverified bKash payments expire}
                            {--dry-run : Show what would be expired without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire unverified bKash payments that have been awaiting verification for too long';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');

        $cutoff = now()->subHours($hours);

        $orders = Order::query()
            ->where('payment_method', PaymentMethod::BkashSendMoney)
            ->where('payment_status', PaymentStatus::AwaitingVerification)
            ->where('status', OrderStatus::Pending)
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No unverified bKash payments found older than '.$hours.' hours.');

            return self::SUCCESS;
        }

        $this->info("Found {$orders->count()} unverified bKash payment(s) older than {$hours} hours:");

        foreach ($orders as $order) {
            $this->line("  - Order {$order->order_number} (created {$order->created_at->diffForHumans()}, TrxID: {$order->bkash_trxid})");
        }

        if ($dryRun) {
            $this->warn('Dry run mode - no changes made.');

            return self::SUCCESS;
        }

        $cancelled = 0;
        $failed = 0;

        $systemUser = User::where('email', 'system@clothing-store.local')->first();
        if (! $systemUser) {
            $systemUser = User::create([
                'name' => 'System',
                'email' => 'system@clothing-store.local',
                'password' => bcrypt(Str::random(32)),
                'is_active' => true,
            ]);
            $systemUser->assignRole('owner');
        }

        foreach ($orders as $order) {
            try {
                app(CancelOrderAction::class)->execute(
                    $order,
                    $systemUser,
                    'bKash payment expired - not verified within '.$hours.' hours'
                );
                $cancelled++;
                $this->line("  ✓ Cancelled order {$order->order_number}");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("  ✗ Failed to cancel order {$order->order_number}: ".$e->getMessage());
                Log::error('Failed to expire unverified bKash payment', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Summary: {$cancelled} cancelled, {$failed} failed");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
