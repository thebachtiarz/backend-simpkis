<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(KelasGroupSeeder::class);
        $this->call(KelasSeeder::class);
        $this->call(SiswaSeeder::class);
        $this->call(KetuaKelasSeeder::class);
        $this->call(SemesterSeeder::class);
        $this->call(KegiatanSeeder::class);
        $this->call(NilaiTambahanSeeder::class);
    }
}
