<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Actions\PlaceOrderAction;
use App\Data\CartLineData;
use App\Data\CheckoutData;
use App\Enums\PaymentMethod;
use App\Models\Area;
use App\Models\District;
use App\Services\CartService;
use App\Services\PricingService;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

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

    public string $honeypot = '';

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
        $this->showBkashFields = $this->form['payment_method'] === PaymentMethod::BkashSendMoney->value;
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
                $this->isBkash() ? 2 : 1,
                null,
                $this->form['phone'] ? PhoneNormalizer::normalize($this->form['phone']) : null,
            );
            $this->error = null;
        } catch (\Exception $e) {
            $this->pricing = null;
            $this->error = $e->getMessage();
        }
    }

    public function submit(): void
    {
        $throttleKey = 'checkout:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->addError('throttle', __('Too many checkout attempts. Please try again later.'));

            return;
        }

        RateLimiter::hit($throttleKey, 600);

        $this->validate([
            'honeypot' => 'prohibited',
            'form.name' => 'required|string|max:100',
            'form.phone' => 'required|string|min:11|max:14',
            'form.email' => 'nullable|email|max:100',
            'form.district_id' => 'required|exists:districts,id',
            'form.area_id' => 'required|exists:areas,id',
            'form.address' => 'required|string|max:500',
            'form.payment_method' => 'required|in:cod,bkash_send_money',
            'form.bkash_trxid' => $this->isBkash() ? 'required|string|min:8|max:12' : 'nullable',
            'form.bkash_sender' => $this->isBkash() ? 'required|string|min:11|max:14' : 'nullable',
            'form.bkash_amount' => $this->isBkash() ? 'required|integer|min:1' : 'nullable',
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

        $lines = app(CartService::class)->get()->map(fn ($item) => new CartLineData(
            variantId: (int) $item['variant_id'],
            qty: (int) $item['qty'],
        ));

        if ($lines->isEmpty()) {
            $this->error = __('Your cart is empty');

            return;
        }

        try {
            $pricing = app(PricingService::class)->calculate(
                (int) $this->form['district_id'],
                (int) $this->form['area_id'],
                $this->form['coupon_code'] ?: null,
                $this->isBkash() ? 2 : 1,
                $lines,
                $this->form['phone'],
            );
        } catch (\Exception $e) {
            $this->error = $e->getMessage();

            return;
        }

        if ($this->isBkash()) {
            $expected = $pricing['total'];
            if ((int) $this->form['bkash_amount'] < $expected) {
                $this->addError('form.bkash_amount', __('bKash amount must be at least the order total'));

                return;
            }
        }

        $this->processing = true;
        $this->error = null;

        try {
            $data = CheckoutData::fromRequest([
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
                'total' => $pricing['total'],
                'ip' => request()->ip(),
            ], $lines, request()->ip());

            $order = app(PlaceOrderAction::class)->execute($data);

            $this->form['idempotency_token'] = Str::random(8);
            $this->pricing = null;

            $this->redirect(route('order.success', $order), navigate: true);
        } catch (\InvalidArgumentException $e) {
            $this->error = $e->getMessage();
        } catch (\Exception $e) {
            $this->error = __('An error occurred. Please try again.');
            logger()->error('Checkout failed', ['error' => $e->getMessage()]);
        } finally {
            $this->processing = false;
        }
    }

    protected function isBkash(): bool
    {
        return $this->form['payment_method'] === PaymentMethod::BkashSendMoney->value;
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
            'cartCount' => $this->cartCount,
        ]);
    }
}
