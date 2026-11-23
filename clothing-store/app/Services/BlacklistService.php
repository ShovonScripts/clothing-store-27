<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Blacklist;
use App\Models\User;
use App\Support\PhoneNormalizer;

class BlacklistService
{
    /**
     * Check any blacklist type. Returns the first matching type or null.
     */
    public function match(?string $phone, ?string $ip, ?string $address): ?string
    {
        if ($phone) {
            $normalized = PhoneNormalizer::normalize($phone) ?? $phone;
            if (Blacklist::where('type', Blacklist::TYPE_PHONE)->where('value', $normalized)->exists()) {
                return Blacklist::TYPE_PHONE;
            }
        }

        if ($ip && Blacklist::where('type', Blacklist::TYPE_IP)->where('value', $ip)->exists()) {
            return Blacklist::TYPE_IP;
        }

        if ($address) {
            $keywords = Blacklist::where('type', Blacklist::TYPE_ADDRESS_KEYWORD)->pluck('value');
            foreach ($keywords as $keyword) {
                if ($keyword !== '' && mb_stripos($address, $keyword) !== false) {
                    return Blacklist::TYPE_ADDRESS_KEYWORD;
                }
            }
        }

        return null;
    }

    public function isBlocked(?string $phone, ?string $ip = null, ?string $address = null): bool
    {
        return $this->match($phone, $ip, $address) !== null;
    }

    public function block(string $type, string $value, ?string $reason = null, ?User $by = null): Blacklist
    {
        if ($type === Blacklist::TYPE_PHONE) {
            $value = PhoneNormalizer::normalize($value) ?? $value;
        }

        return Blacklist::updateOrCreate(
            ['type' => $type, 'value' => $value],
            ['reason' => $reason, 'created_by' => $by?->id],
        );
    }

    public function unblock(string $type, string $value): void
    {
        if ($type === Blacklist::TYPE_PHONE) {
            $value = PhoneNormalizer::normalize($value) ?? $value;
        }

        Blacklist::where('type', $type)->where('value', $value)->delete();
    }
}
