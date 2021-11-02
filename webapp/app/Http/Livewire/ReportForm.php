<?php

namespace App\Http\Livewire;

use App\Models\Report;
use App\Models\Upload;
use App\Models\Vehicle;
use App\Repositories\BakersfieldZipRepository;
use App\Repositories\VehicleRepository;
use App\Rules\ImageOrVideo;
use App\Support\ReportFormPropertySetter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class ReportForm extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public $report_id;

    public $date;
    public $time;
    public $street_address_1 = "";
    public $street_address_2 = "";
    public $zip = "";
    public $vehicle_make = "";
    public $vehicle_model = "";
    public $vehicle_year = "";
    public $police_report_number = "";
    public $description = "";

    /** @var \Illuminate\Http\UploadedFile[] */
    public $unsubmitted_uploads = [];

    /** @var \Illuminate\Http\UploadedFile[] */
    public $saved_uploads = [];

    public $vehicle_make_list = [];
    public $vehicle_model_list = [];
    public $vehicle_year_list = [];

    public $is_submitted = false;

    public function mount(Report $report, ReportFormPropertySetter $propertySetter)
    {
        if (Auth::guest()) return redirect(route('login'));
        if ($report->exists) $this->authorize('edit', $report);

        $this->vehicle_make_list = $this->vehicleRepository()->getVehicleMakeList();
        $this->date = date("Y-m-d");
        $this->time = date('H:i');

        if ($report->exists) $propertySetter->set($this, $report);
    }

    protected function rules()
    {
        return [
            'date' => 'required',
            'time' => 'present',
            'street_address_1' => 'required',
            'street_address_2' => 'present',
            'zip' => [
                'required',
                Rule::in((new BakersfieldZipRepository())->all()),
            ],
            'vehicle_make' => 'required',
            'vehicle_model' => 'required',
            'vehicle_year' => 'required',
            'police_report_number' => 'present',
            'description' => 'present',
            // 'unsubmitted_uploads.*' => ['nullable', 'file', (new ImageOrVideo)->makeRule(5, 10)],
            'unsubmitted_uploads.*' => ['nullable', 'file', 'image', 'max:5120'],
        ];
    }

    protected function messages()
    {
        return [
            'zip.required' => 'ZIP is required.',
            'zip.in' => 'Must use Bakersfield ZIP.'
        ];
    }

    public function updated($field, $_newValue)
    {
        if (!$this->errorBag->has($field)) return;
        $this->validateOnly($field);
    }

    public function updatedVehicleMake($_new_value)
    {
        $this->vehicle_model = "";
        $this->vehicle_year = "";

        $this->vehicle_model_list = $this->vehicleRepository()
            ->getVehicleModelList($this->vehicle_make);
        $this->vehicle_year_list = [];

        $this->dispatchBrowserEvent('vehicle-make-updated');
    }

    public function updatedVehicleModel($_new_value)
    {
        $this->vehicle_year = "";

        $this->vehicle_year_list = $this->vehicleRepository()
            ->getVehicleYearList($this->vehicle_make, $this->vehicle_model);

        $this->dispatchBrowserEvent('vehicle-model-updated');
    }

    protected function vehicleRepository()
    {
        return app(VehicleRepository::class);
    }

    public function deleteUnsubmittedUpload($index)
    {
        $this->unsubmitted_uploads = collect($this->unsubmitted_uploads)
            ->filter(fn ($_value, $key) => $index != $key)
            ->values() // rekeys array.
            ->toArray();
    }

    public function deleteSavedUpload($uploadId)
    {
        /** @var Upload */
        $upload = Upload::find($uploadId);

        $this->authorize('delete', $upload);

        try {
            $upload->delete();
            Storage::disk(env('UPLOADS_FILESYSTEM'))->delete($upload->path);
        } catch (\Exception $e) {
            // do nothing
        }
        $this->saved_uploads = Report::find($this->report_id)->uploads;
    }

    public function submit()
    {
        $valid = $this->validate();

        /** @var Report */
        $report = $this->report_id
            ? Report::query()
            ->where('id', $this->report_id)
            ->where('user_id', Auth::id())
            ->firstOr(fn () => abort(403))
            : new Report;

        if ($report->exists) $this->authorize('edit', $report);

        $vehicle =  Vehicle::where([
            ['make', $this->vehicle_make],
            ['model', $this->vehicle_model],
            ['year', $this->vehicle_year],
        ])->firstOr(fn () => abort(500));

        $report->user_id = Auth::id();
        $report->date = $valid['date'];
        $report->time = $valid['time'];
        $report->vehicle_code = $vehicle->code;
        $report->street_address_1 = $valid['street_address_1'];
        $report->street_address_2 = $valid['street_address_2'];
        $report->zip = $valid['zip'];
        $report->police_report_number = $valid['police_report_number'];
        $report->description = $valid['description'];
        $report->save();

        // This will happen _after_ we save the report so that users who tamper
        // with the form can't upload files.
        $newly_submitted_uploads = [];
        foreach ($this->unsubmitted_uploads as $upload) {
            // We validated that this is a image or video in the component's
            // validation rules, so if it isn't an image here it's a video.
            $file_type = preg_match('/image/', $upload->getMimeType())
                ? 'image'
                : 'video';

            $path = $upload->storePublicly('public/uploads', env('UPLOADS_FILESYSTEM'));

            $newly_submitted_uploads[] = new Upload([
                'path' => $path,
                'type' => $file_type,
                'mime' => $upload->getMimeType()
            ]);
        }
        $report->uploads()->saveMany($newly_submitted_uploads);

        if ($report->admin_approved)
            return redirect(route('reports.show', ['report' => $this->report_id]));

        $this->is_submitted = true;
    }

    public function render()
    {
        if ($this->is_submitted) return view('livewire.report-form-success');
        return view('livewire.report-form');
    }
}
