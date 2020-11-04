<?php

namespace App\Repositories\Auth;

use App\Models\Auth\User;

class UserRepository
{
    protected $User;

    public function __construct()
    {
        $this->User = new User;
    }

    # Public
    public function getList()
    {
        return $this->User->get();
    }

    public function findInfo($id)
    {
        # code...
    }

    # Repo
    public function userCan($status)
    {
        $userCan = [
            # Admin Access
            'greatadmin' => [
                'login:getCred', 'login:postLogout'
            ],
            # Kurikulum Access
            'themanager' => [
                'login:getCred', 'login:postLogout'
            ],
            # Guru Access
            'bestteacher' => [
                'login:getCred', 'login:postLogout'
            ],
            # Ketua Kelas Access
            'goodleader' => [
                'login:getCred', 'login:postLogout'
            ]
        ];
        return $userCan[$status];
    }

    # Private

}
