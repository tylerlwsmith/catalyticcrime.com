<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use League\Csv\Reader;

class VehicleSeeder extends Seeder
{
    protected $chunkSize = 200;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "Deleting existing records.";
        DB::table('vehicles')->delete();

        LazyCollection::make(function () {
            $file = env('APP_ENV') !== "testing" ? 'vehicles.csv' : "vehicles-test.csv";
            $reader = Reader::createFromPath(database_path("raw/{$file}"), 'r');
            $reader->setHeaderOffset(0);
            $records = $reader->getRecords();

            foreach ($records as $record) {
                yield collect($record)->only(['make', 'model', 'year'])->toArray();
            }
        })
            ->chunk($this->chunkSize)
            ->each(function ($chunk, $index) {
                $start_row = ($this->chunkSize * $index) + 1;
                $end_row = $start_row + count($chunk) - 1;
                echo "Processing rows ${start_row}-${end_row}\n";

                Vehicle::insert($chunk->source);
            });

        echo "All rows retrieved. Beginning deduplication.";

        LazyCollection::make(function () {
            $unique_vehicles = DB::table('vehicles')
                ->select(['make', 'model', 'year'])
                ->distinct(['make', 'model', 'year'])
                ->orderBy('make')
                ->orderBy('model')
                ->orderBy('year')
                ->get();

            DB::table('vehicles')->delete();

            foreach ($unique_vehicles as $record) {
                yield (array)$record;
            }
        })
            ->chunk($this->chunkSize)
            ->each(function ($chunk, $index) {
                $start_row = ($this->chunkSize * $index) + 1;
                $end_row = $start_row + count($chunk) - 1;
                echo "Processing rows ${start_row}-${end_row}\n";

                Vehicle::insert($chunk->source);
            });
    }
}
