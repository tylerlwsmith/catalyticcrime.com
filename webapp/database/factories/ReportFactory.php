<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use App\Models\Vehicle;
use App\Repositories\BakersfieldZipRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'time' => $this->faker->time(),
            'user_id' => User::factory()->create()->id,
            'vehicle_code' => Vehicle::query()->inRandomOrder()->first()->code,
            'street_address_1' => $this->faker->address,
            'street_address_2' => '',
            'zip' => (new BakersfieldZipRepository())->random(),
            'police_report_number' => "",
            'description' => $this->faker->text(),
            'admin_approved' => false,
        ];
    }
}
