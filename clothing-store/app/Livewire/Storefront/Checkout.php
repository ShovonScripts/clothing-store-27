<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Actions\PlaceOrderAction;
use App\Models\Area;
use App\Models\District;
use App\Services\CartService;
use App\Services\PricingService;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class Checkout extends Component
{
    public array $form = [
        'name' => '',
        'phone' => '',
        'email' => '',
        'district_id' => '',
        'area_id' => '',
        'address' => '',
        'note' => '',
        'payment_method' => 'cod',
        'bkash_trxid' => '',
        'bkash_sender' => '',
        'bkash_amount' => '',
        'coupon_code' => '',
        'idempotency_token' => '',
    ];

    public array $districts = [];

    public array $areas = [];

    public bool $showBkashFields = false;

    public bool $processing = false;

    public ?array $pricing = null;

    public ?string $error = null;

    protected $listeners = ['refresh-pricing' => 'calculatePricing'];

    public function mount(): void
    {
        $this->districts = District::where('is_active', true)->orderBy('name')->get(['id', 'name', 'name_bn'])->toArray();
        $this->form['idempotency_token'] = Str::random(8);
        $this->calculatePricing();
    }

    public function updatedFormPhone(string $value): void
    {
        $this->form['phone'] = PhoneNormalizer::normalize($value);
    }

    public function updatedFormDistrictId(): void
    {
        $this->form['area_id'] = '';
        $this->areas = Area::where('district_id', $this->form['district_id'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_bn'])
            ->toArray();
        $this->calculatePricing();
    }

    public function updatedFormAreaId(): void
    {
        $this->calculatePricing();
    }

    public function updatedFormPaymentMethod(): void
    {
        $this->showBkashFields = $this->form['payment_method'] === 'bkash';
        $this->calculatePricing();
    }

    public function updatedFormCouponCode(): void
    {
        $this->calculatePricing();
    }

    public function applyCoupon(): void
    {
        $this->calculatePricing();
    }

    public function removeCoupon(): void
    {
        $this->form['coupon_code'] = '';
        $this->calculatePricing();
    }

    public function calculatePricing(): void
    {
        try {
            $this->pricing = app(PricingService::class)->calculate(
                (int) $this->form['district_id'],
                (int) $this->form['area_id'],
                $this->form['coupon_code'] ?: null,
                $this->form['payment_method'] === 'bkash' ? 2 : 1
            );
            $this->error = null;
        } catch (\Exception $e) {
            $this->pricing = null;
            $this->error = $e->getMessage();
        }
    }

    public function submit(): void
    {
        $this->validate([
            'form.name' => 'required|string|max:100',
            'form.phone' => 'required|string|min:11|max:14',
            'form.email' => 'nullable|email|max:100',
            'form.district_id' => 'required|exists:districts,id',
            'form.area_id' => 'required|exists:areas,id',
            'form.address' => 'required|string|max:500',
            'form.payment_method' => 'required|in:cod,bkash',
            'form.bkash_trxid' => $this->form['payment_method'] === 'bkash' ? 'required|string|min:10|max:20' : '',
            'form.bkash_sender' => $this->form['payment_method'] === 'bkash' ? 'required|string|min:11|max:14' : '',
            'form.bkash_amount' => $this->form['payment_method'] === 'bkash' ? 'required|integer|min:1' : '',
            'form.note' => 'nullable|string|max:500',
        ], [], [
            'form.name' => 'Name',
            'form.phone' => 'Phone',
            'form.email' => 'Email',
            'form.district_id' => 'District',
            'form.area_id' => 'Area',
            'form.address' => 'Address',
            'form.payment_method' => 'Payment method',
            'form.bkash_trxid' => 'bKash TrxID',
            'form.bkash_sender' => 'bKash Sender Number',
            'form.bkash_amount' => 'bKash Amount',
        ]);

        $this->form['phone'] = PhoneNormalizer::normalize($this->form['phone']);
        if ($this->form['bkash_sender']) {
            $this->form['bkash_sender'] = PhoneNormalizer::normalize($this->form['bkash_sender']);
        }

        // Verify bKash amount matches total
        if ($this->form['payment_method'] === 'bkash') {
            $expected = $this->pricing['total'] ?? 0;
            if ((int) $this->form['bkash_amount'] !== $expected) {
                $this->addError('form.bkash_amount', 'bKash amount must match order total');

                return;
            }
        }

        $this->processing = true;
        $this->error = null;

        try {
            $order = app(PlaceOrderAction::class)->execute([
                'name' => $this->form['name'],
                'phone' => $this->form['phone'],
                'email' => $this->form['email'],
                'district_id' => (int) $this->form['district_id'],
                'area_id' => (int) $this->form['area_id'],
                'address' => $this->form['address'],
                'note' => $this->form['note'],
                'payment_method' => $this->form['payment_method'],
                'bkash_trxid' => $this->form['bkash_trxid'] ?: null,
                'bkash_sender' => $this->form['bkash_sender'] ?: null,
                'bkash_amount' => (int) ($this->form['bkash_amount'] ?? 0),
                'coupon_code' => $this->form['coupon_code'] ?: null,
                'idempotency_token' => $this->form['idempotency_token'],
            ]);

            // Clear pricing cache
            $this->pricing = null;

            $this->redirect(route('order.success', $order), navigate: true);
        } catch (\InvalidArgumentException $e) {
            $this->error = $e->getMessage();
        } catch (\Exception $e) {
            $this->error = 'An error occurred. Please try again.';
            \Log::error('Checkout failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        } finally {
            $this->processing = false;
        }
    }

    public function getCartItemsProperty()
    {
        return app(CartService::class)->get();
    }

    public function getCartCountProperty(): int
    {
        return app(CartService::class)->count();
    }

    public function render()
    {
        return view('livewire.storefront.checkout', [
            'items' => $this->cartItems,
            'pricing' => $this->pricing,
            'districts' => $this->districts,
            'areas' => $this->areas,
        ]);
    }
}
