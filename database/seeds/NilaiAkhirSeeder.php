<?php

use Illuminate\Database\Seeder;
use App\Services\School\Curriculum\NilaiAkhirCreatorService;

class NilaiAkhirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NilaiAkhirCreatorService::runProcessNilaiAkhir();
    }
}
