<?php

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = collect([
            ['status' => User_setStatus('admin'), 'name' => 'Admin', 'username' => 'admin', 'password' => User_encPass('admin'), 'code' => User_createNewCode(), 'created_at' => Carbon_DBtimeNow(), 'updated_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')],
            ['status' => User_setStatus('kurikulum'), 'name' => 'Kurikulum', 'username' => 'kurikulum', 'password' => User_encPass('kurikulum'), 'code' => User_createNewCode(), 'created_at' => Carbon_DBtimeNow(), 'updated_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')],
            ['status' => User_setStatus('guru'), 'name' => 'Guru', 'username' => 'guru', 'password' => User_encPass('guru'), 'code' => User_createNewCode(), 'created_at' => Carbon_DBtimeNow(), 'updated_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')],
            ['status' => User_setStatus('siswa'), 'name' => 'Ketua Kelas', 'username' => 'siswa', 'password' => User_encPass('siswa'), 'code' => User_createNewCode(), 'created_at' => Carbon_DBtimeNow(), 'updated_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')]
        ]);

        $user = $users->map(function ($data) {
            return ['username' => $data['username'], 'password' => $data['password'], 'code' => $data['code'], 'active' => $data['active']];
        })->all();

        $biodata = $users->map(function ($data) {
            return ['code' => $data['code'], 'name' => $data['name']];
        })->all();

        $status = $users->map(function ($data) {
            return ['code' => $data['code'], 'status' => $data['status']];
        })->all();

        User::insert($user);
        UserBiodata::insert($biodata);
        UserStatus::insert($status);
    }
}
