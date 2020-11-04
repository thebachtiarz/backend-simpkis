<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
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
    public function run(Faker $faker)
    {
        $users = collect([
            ['status' => User_setStatus('admin'), 'name' => $faker->name, 'username' => 'admin', 'password' => User_encPass('admin'), 'created_at' => Carbon_DBtimeNow(), 'updated_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')],
            ['status' => User_setStatus('kurikulum'), 'name' => $faker->name, 'username' => 'kurikulum', 'password' => User_encPass('kurikulum'), 'created_at' => Carbon_DBtimeNow(), 'updated_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')],
            ['status' => User_setStatus('guru'), 'name' => $faker->name, 'username' => 'guru', 'password' => User_encPass('guru'), 'created_at' => Carbon_DBtimeNow(), 'updated_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')]
        ]);

        $user = $users->map(function ($data) {
            return ['username' => $data['username'], 'password' => $data['password'], 'active' => $data['active']];
        })->all();

        $biodata = $users->map(function ($data) {
            return ['name' => $data['name']];
        })->all();

        $status = $users->map(function ($data) {
            return ['status' => $data['status']];
        })->all();

        User::insert($user);
        UserBiodata::insert($biodata);
        UserStatus::insert($status);
    }
}
