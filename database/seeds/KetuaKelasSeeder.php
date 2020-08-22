<?php

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;
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
        $newUser = [];
        $newUserBiodata = [];
        $newUserStatus = [];
        $newKetua = [];
        $getLastIdUser = User::orderByDesc('id')->first('id')->id;
        $getSiswa = Siswa::select(['id', 'id_kelas', 'nisn', 'nama'])->where('id', '>', 500)->groupBy('id_kelas')->get();
        $getKelasOnly = Arr_pluck($getSiswa, 'id_kelas');
        $getSiswaOnly = Arr_pluck($getSiswa, 'id');
        $getNisnOnly = Arr_pluck($getSiswa, 'nisn');
        $getNamaOnly = Arr_pluck($getSiswa, 'nama');

        for ($i = 0; $i < count($getKelasOnly); $i++) {
            $moreUser = [
                'code' => User_createNewCode(),
                'name' => $getNamaOnly[$i],
                'tagname' => strtolower($getNisnOnly[$i]),
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
                'id_siswa' => strval($getSiswaOnly[$i]),
                'id_kelas' => strval($getKelasOnly[$i]),
                'id_user' => strval($getLastIdUser + 1)
            ];
            $getLastIdUser++;
        }

        User::insert($newUser);
        UserBiodata::insert($newUserBiodata);
        UserStatus::insert($newUserStatus);
        KetuaKelas::insert($newKetua);
    }
}
