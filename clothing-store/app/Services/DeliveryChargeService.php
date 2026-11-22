<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Area;
use App\Models\DeliveryZone;
use App\Models\District;

class DeliveryChargeService
{
    public function __construct(protected SettingsService $settings) {}

    public function calculate(int $districtId, int $areaId, int $subtotal): int
    {
        $district = District::find($districtId);
        $area = Area::find($areaId);

        if (! $district || ! $area) {
            return $this->settings->deliveryOutsideDhaka();
        }

        $zone = $area->is_inside_city ? 'inside_dhaka' : 'outside_dhaka';
        $deliveryZone = DeliveryZone::where('key', $zone)->where('is_active', true)->first();

        $charge = $deliveryZone?->charge ?? ($zone === 'inside_dhaka' ? $this->settings->deliveryInsideDhaka() : $this->settings->deliveryOutsideDhaka());

        if ($subtotal >= $this->settings->freeDeliveryOver()) {
            return 0;
        }

        return $charge;
    }

    public function getZone(int $districtId, int $areaId): string
    {
        $area = Area::find($areaId);

        if (! $area) {
            return 'outside_dhaka';
        }

        return $area->is_inside_city ? 'inside_dhaka' : 'outside_dhaka';
    }

    public function getEstimatedDays(int $districtId, int $areaId): array
    {
        $zone = $this->getZone($districtId, $areaId);
        $deliveryZone = DeliveryZone::where('key', $zone)->where('is_active', true)->first();

        return [
            'min' => $deliveryZone?->est_days_min ?? ($zone === 'inside_dhaka' ? 1 : 2),
            'max' => $deliveryZone?->est_days_max ?? ($zone === 'inside_dhaka' ? 2 : 5),
        ];
    }
}
