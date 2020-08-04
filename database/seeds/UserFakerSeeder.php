<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;

class UserFakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $newUser = [];
        $newUserBiodata = [];
        $newUserStatus = [];
        for ($i = 0; $i < env('SEED_MORE_USER', 50); $i++) {
            $moreUser = [
                'code' => User_createNewCode(),
                'name' => $faker->name,
                'tagname' => strtolower(randString(7)),
                'active' => User_setActiveStatus('active'),
                'status' => User_setStatus('ketuakelas')
            ];
            $newUser[] = [
                'username' => 'u' . $moreUser['tagname'],
                'password' => User_encPass('p' . $moreUser['tagname']),
                'code' => $moreUser['code'],
                'active' => $moreUser['active']
            ];
            $newUserBiodata[] = [
                'code' => $moreUser['code'],
                'name' => $moreUser['name']
            ];
            $newUserStatus[] = [
                'code' => $moreUser['code'],
                'status' => $moreUser['status']
            ];
        }
        foreach (array_chunk($newUser, 10000) as $setUser) {
            User::insert($setUser);
        }
        foreach (array_chunk($newUserBiodata, 10000) as $setUserBiodata) {
            UserBiodata::insert($setUserBiodata);
        }
        foreach (array_chunk($newUserStatus, 10000) as $setUserStatus) {
            UserStatus::insert($setUserStatus);
        }
    }
}
