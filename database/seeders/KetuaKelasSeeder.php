<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;
use App\Models\School\Curriculum\Kelas;
use App\Models\School\Actor\Siswa;
use App\Models\School\Actor\KetuaKelas;

class KetuaKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $getLastIdUser = (new \App\Repositories\User\UserRepository)->getLastUserId();
        $getKelas = Kelas::all();

        $newUser = [];
        $newUserBiodata = [];
        $newUserStatus = [];
        $newKetua = [];

        for ($i = 0; $i < count($getKelas); $i++) {
            $getRandomCandidate = Siswa::where('id_kelas', $getKelas[$i]->id)->inRandomOrder()->first();
            $moreUser = [
                'name' => $getRandomCandidate->nama,
                'tagname' => strtolower($getRandomCandidate->nisn),
                'active' => User_setActiveStatus('active'),
                'status' => User_setStatus('ketuakelas')
            ];
            $newUser[] = [
                'username' => Act_formatNewKetuaKelasUsername($moreUser['tagname']),
                'password' => Act_formatNewKetuaKelasPassword($moreUser['tagname']),
                'active' => $moreUser['active'],
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ];
            $newUserBiodata[] = [
                'name' => $moreUser['name'],
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ];
            $newUserStatus[] = [
                'status' => $moreUser['status'],
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ];
            $newKetua[] = [
                'id_siswa' => strval($getRandomCandidate->id),
                'id_kelas' => strval($getRandomCandidate->id_kelas),
                'id_user' => strval($getLastIdUser + ($i + 1)),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ];
        }

        User::insert($newUser);
        UserBiodata::insert($newUserBiodata);
        UserStatus::insert($newUserStatus);
        KetuaKelas::insert($newKetua);
    }
}
