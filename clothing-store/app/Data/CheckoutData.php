<?php

declare(strict_types=1);

namespace App\Data;

use App\Support\PhoneNormalizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

readonly class CheckoutData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public int $districtId,
        public int $areaId,
        public string $address,
        public string $paymentMethod, // 'cod' | 'bkash'
        public ?string $bkashTrxid,
        public ?string $bkashSender,
        public int $bkashAmount,
        public string $couponCode,
        public string $note,
        public int $total,
        public string $idempotencyToken,
        public string $ipAddress,
        public Collection $lines, // CartLineData objects
    ) {}

    public static function fromRequest(array|self $data, Collection $lines, string $ip): self
    {
        if ($data instanceof self) {
            return $data->withIp($ip);
        }

        return new self(
            name: $data['name'] ?? '',
            phone: PhoneNormalizer::normalize($data['phone'] ?? ''),
            email: $data['email'] ?? null,
            districtId: (int) ($data['district_id'] ?? 0),
            areaId: (int) ($data['area_id'] ?? 0),
            address: $data['address'] ?? '',
            paymentMethod: $data['payment_method'] ?? 'cod',
            bkashTrxid: $data['bkash_trxid'] ?? null,
            bkashSender: ($data['bkash_sender'] ?? null) ? PhoneNormalizer::normalize($data['bkash_sender']) : null,
            bkashAmount: (int) ($data['bkash_amount'] ?? 0),
            couponCode: $data['coupon_code'] ?? '',
            note: $data['note'] ?? '',
            total: (int) ($data['total'] ?? 0),
            idempotencyToken: $data['idempotency_token'] ?? Str::random(8),
            ipAddress: $data['ip'] ?? '127.0.0.1',
            lines: $lines,
        );
    }

    public function withIp(string $ip): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $ip,
            lines: $this->lines,
        );
    }

    public function withPaymentMethod(string $method): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $method,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withCartLines(Collection $lines): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $lines,
        );
    }

    public function withTotal(int $total): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withIdempotencyToken(string $token): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $token,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withCouponCode(string $code): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $code,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withPhone(string $phone): self
    {
        return new self(
            name: $this->name,
            phone: PhoneNormalizer::normalize($phone),
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withBkashTrxid(string $trxid): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $trxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withBkashSender(string $sender): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: PhoneNormalizer::normalize($sender),
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withBkashAmount(int $amount): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $this->address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $amount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }

    public function withAddress(string $address): self
    {
        return new self(
            name: $this->name,
            phone: $this->phone,
            email: $this->email,
            districtId: $this->districtId,
            areaId: $this->areaId,
            address: $address,
            paymentMethod: $this->paymentMethod,
            bkashTrxid: $this->bkashTrxid,
            bkashSender: $this->bkashSender,
            bkashAmount: $this->bkashAmount,
            couponCode: $this->couponCode,
            note: $this->note,
            total: $this->total,
            idempotencyToken: $this->idempotencyToken,
            ipAddress: $this->ipAddress,
            lines: $this->lines,
        );
    }
}
