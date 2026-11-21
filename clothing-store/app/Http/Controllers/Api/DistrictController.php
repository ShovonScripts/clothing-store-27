<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use Illuminate\Http\JsonResponse;

class DistrictController extends Controller
{
    public function index(): JsonResponse
    {
        $districts = District::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_bn']);

        return response()->json($districts);
    }

    public function areas(int $districtId): JsonResponse
    {
        $areas = Area::where('district_id', $districtId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_bn']);

        return response()->json($areas);
    }
}
