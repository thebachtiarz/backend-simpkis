<?php

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
        $getLastIdUser = User::orderByDesc('id')->first('id')->id;
        $getKelas = Kelas::all();

        $newUser = [];
        $newUserBiodata = [];
        $newUserStatus = [];
        $newKetua = [];

        for ($i = 0; $i < count($getKelas); $i++) {
            $getRandomCandidate = Siswa::where('id_kelas', $getKelas[$i]->id)->inRandomOrder()->first();
            $moreUser = [
                'code' => User_createNewCode(),
                'name' => $getRandomCandidate->nama,
                'tagname' => strtolower($getRandomCandidate->nisn),
                'active' => User_setActiveStatus('active'),
                'status' => User_setStatus('ketuakelas')
            ];
            $newUser[] = [
                'username' => Act_formatNewSiswaUsername($moreUser['tagname']),
                'password' => Act_formatNewSiswaPassword($moreUser['tagname']),
                'code' => $moreUser['code'],
                'active' => $moreUser['active']
            ];
            $newUserBiodata[] = [
                'name' => $moreUser['name']
            ];
            $newUserStatus[] = [
                'status' => $moreUser['status']
            ];
            $newKetua[] = [
                'id_siswa' => strval($getRandomCandidate->id),
                'id_kelas' => strval($getRandomCandidate->id_kelas),
                'id_user' => strval($getLastIdUser + ($i + 1))
            ];
        }

        User::insert($newUser);
        UserBiodata::insert($newUserBiodata);
        UserStatus::insert($newUserStatus);
        KetuaKelas::insert($newKetua);
    }
}
