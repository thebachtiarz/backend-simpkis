<?php

use Illuminate\Database\Seeder;
use App\Models\School\Curriculum\Semester;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newSemester = [];
        for ($i = 2015; $i <= date("Y"); $i++) {
            $newSemester[] = ['semester' => Cur_setNewSemesterFormat(($i - 1), '2')];
            $newSemester[] = ['semester' => Cur_setNewSemesterFormat($i, '1')];
        }
        Semester::insert($newSemester);
    }
}
