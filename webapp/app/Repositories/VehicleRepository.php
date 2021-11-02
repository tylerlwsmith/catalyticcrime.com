<?php

namespace App\Repositories;

use App\Models\Vehicle;

class VehicleRepository
{
    public function getVehicleMakeList()
    {
        return [
            "",
            ...Vehicle::select('make')
                ->distinct()
                ->orderBy('make')
                ->get()
                ->map(fn ($make) => $make['make'])
                ->toArray()
        ];
    }

    public function getVehicleModelList($make)
    {
        return [
            "",
            ...Vehicle::select('model')
                ->where('make', $make)
                ->distinct()
                ->orderBy('model')
                ->get()
                ->map(fn ($model) => $model['model'])
                ->toArray()
        ];
    }

    public function getVehicleYearList($make, $model)
    {
        return [
            "",
            ...Vehicle::select('year')
                ->where('make', $make)
                ->where('model', $model)
                ->orderBy('year')
                ->get()
                ->map(fn ($year) => $year['year'])
                ->toArray()
        ];
    }
}
