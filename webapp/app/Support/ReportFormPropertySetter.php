<?php

namespace App\Support;

use App\Http\Livewire\ReportForm;
use App\Models\Report;
use App\Repositories\VehicleRepository;

class ReportFormPropertySetter
{
    protected $vehicleRepository;

    public function __construct(VehicleRepository $repository)
    {
        $this->vehicleRepository = $repository;
    }

    public function set(ReportForm $form, Report $report)
    {
        $form->report_id = $report->id;

        $form->date = $report->date->format('Y-m-d');
        $form->time = $report->time->format('H:i');
        $form->street_address_1 = $report->street_address_1;
        $form->street_address_2 = $report->street_address_2;
        $form->zip = $report->zip;
        $form->vehicle_make = $report->vehicle->make ?? '';
        $form->vehicle_model = $report->vehicle->model ?? '';
        $form->vehicle_year = $report->vehicle->year ?? '';
        $form->saved_uploads = $report->uploads;
        $form->police_report_number = $report->police_report_number;
        $form->description = $report->description;

        $form->vehicle_make_list = $this->vehicleRepository->getVehicleMakeList();

        if (!$report->vehicle) return;
        $form->vehicle_model_list = $this->vehicleRepository->getVehicleModelList(
            $report->vehicle->make
        );
        $form->vehicle_year_list = $this->vehicleRepository->getVehicleYearList(
            $report->vehicle->make,
            $report->vehicle->model
        );
    }
}
