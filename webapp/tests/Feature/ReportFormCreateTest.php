<?php

namespace Tests\Feature;

use App\Http\Livewire\ReportForm;
use App\Models\Report;
use App\Models\User;
use App\Models\Vehicle;
use App\Repositories\BakersfieldZipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Livewire\TemporaryUploadedFile;
use Tests\TestCase;

class ReportFormCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /**
     * Pending updates from the GitHub discussion:
     * https://github.com/livewire/livewire/discussions/3805
     * @test
     */

    // public function it_can_delete_an_image()
    // {
    //     $image_1 = UploadedFile::fake()->image('avatar.jpg');
    //     $image_2 = UploadedFile::fake()->image('avatar2.jpg');

    //     // /** @var ReportForm */
    //     // $form = app(ReportForm::class);
    //     // $form->uploads = [$image_1, $image_2];

    //     // https://github.com/livewire/livewire/issues/2353#issuecomment-774536985
    //     Livewire::test(ReportForm::class, ['uploads' => [$image_1, $image_2]])
    //         ->call('deleteUpload', 1)
    //         ->assertCount('uploads', 1);
    // }

    /** @test */
    public function it_rejects_non_bakersfield_zip()
    {
        Livewire::test(ReportForm::class, ['zip' => 95816])
            ->call('submit', 1)
            ->assertHasErrors('zip');
    }

    /** @test */
    public function it_accepts_bakersfield_zip()
    {
        Livewire::test(ReportForm::class, ['zip' => 93311])
            ->call('submit', 1)
            ->assertHasNoErrors('zip');
    }

    /** @test */
    public function it_rejects_invalid_file_type()
    {
        $file = UploadedFile::fake()->create('file.txt', 0, "text/plain");

        Livewire::test(ReportForm::class, ['unsubmitted_uploads' => [$file]])
            ->call('submit', 1)
            ->assertHasErrors('unsubmitted_uploads.*');
    }

    /** @test */
    public function it_accepts_images()
    {
        $file = UploadedFile::fake()->image('image.jpg');

        Livewire::test(ReportForm::class, ['unsubmitted_uploads' => [$file]])
            ->call('submit', 1)
            ->assertHasNoErrors('unsubmitted_uploads.*');
    }

    /** @test */
    public function it_rejects_videos()
    {
        $file = UploadedFile::fake()->create('video.mpg', 10, 'video/mpeg');

        Livewire::test(ReportForm::class, ['unsubmitted_uploads' => [$file]])
            ->call('submit', 1)
            ->assertHasErrors('unsubmitted_uploads.*');
    }

    /** @test */
    public function it_loads_vehicle_makes_list_when_form_loads()
    {
        $models = Livewire::test(ReportForm::class)
            ->get('vehicle_make_list');

        // https://phpunit.readthedocs.io/en/9.5/assertions.html#assertthat
        $this->assertGreaterThan(0, count($models), 'Vehicle "makes" were not loaded when form mounted');
    }

    /** @test */
    public function it_loads_vehicle_models_list_when_make_is_selected()
    {
        // Note: these don't need to be loaded in the production database to work:
        // the app will still load a single blank entry.
        $make = 'Chevrolet';

        $models = Livewire::test(ReportForm::class)
            ->set('vehicle_make', $make)
            ->get('vehicle_model_list');

        $this->assertGreaterThan(0, count($models), 'Vehicle models were not loaded when make was selected');
    }

    /** @test */
    public function it_loads_vehicle_years_list_when_model_is_selected()
    {
        // Note: these don't need to be loaded in the production database to work:
        // the app will still load a single blank entry.
        $make = 'Chevrolet';
        $model = 'Blazer 4WD';

        $years = Livewire::test(ReportForm::class)
            ->set('vehicle_make', $make)
            ->set('vehicle_model', $model)
            ->get('vehicle_year_list');

        $this->assertGreaterThan(0, count($years), "Vehicle years were not loaded when model was selected");
    }

    /** @test */
    public function it_clears_model_and_year_when_new_make_is_selected()
    {
        $make = 'Chevrolet';
        $model = 'Blazer 4WD';
        $year = '1995';

        $new_make = "Toyota";

        $form = Livewire::test(ReportForm::class)
            ->set('vehicle_make', $make)
            ->set('vehicle_model', $model)
            ->set('vehicle_year', $year)
            ->set('vehicle_make', $new_make);

        $form->assertSet('vehicle_model', '')->assertSet('vehicle_year', '');
    }

    /** @test */
    public function it_clears_year_when_new_model_is_selected()
    {
        $make = 'Chevrolet';
        $model = 'Blazer 4WD';
        $year = '1995';

        $new_model = "Suburban 1500 4WD";

        $form = Livewire::test(ReportForm::class)
            ->set('vehicle_make', $make)
            ->set('vehicle_model', $model)
            ->set('vehicle_year', $year)
            ->set('vehicle_model', $new_model);

        $form->assertSet('vehicle_year', '');
    }

    /** @test */
    public function it_saves_new_report_when_it_has_required_fields()
    {
        $this->assertCount(0, Report::all());

        $vehicle = Vehicle::query()->inRandomOrder()->first();
        $form = Livewire::test(ReportForm::class)
            ->set('street_address_1', '123 main street')
            ->set('street_address_2', '')
            ->set('zip', (new BakersfieldZipRepository())->random())
            ->set('vehicle_make', $vehicle->make)
            ->set('vehicle_model', $vehicle->model)
            ->set('vehicle_year', $vehicle->year)
            ->set('description', '');

        $form->call('submit');

        $this->assertCount(1, Report::all());
    }

    /** @test */
    public function new_reports_save_with_admin_approved_as_false()
    {
        $this->assertCount(0, Report::all());

        $vehicle = Vehicle::query()->inRandomOrder()->first();
        $form = Livewire::test(ReportForm::class)
            ->set('street_address_1', '123 main street')
            ->set('street_address_2', '')
            ->set('zip', (new BakersfieldZipRepository())->random())
            ->set('vehicle_make', $vehicle->make)
            ->set('vehicle_model', $vehicle->model)
            ->set('vehicle_year', $vehicle->year)
            ->set('description', '');

        $form->call('submit');

        $this->assertFalse(Report::first()->admin_approved);
    }

    /** @test */
    public function it_redirects_unauthenticated_user_to_login()
    {
        Auth::logout();

        Livewire::test(ReportForm::class)->assertRedirect(route('login'));
    }
}
