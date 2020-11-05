<?php

namespace App\Repositories\User;

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

    # Scope
    public function getUsersByStatus($status)
    {
        return $this->User->select('users.*')->join('user_statuses', 'users.id', '=', 'user_statuses.id')->where('status', User_setStatus($status));
    }

    # Soft Delete
    public function findTrashed($id)
    {
        return $this->User->withTrashed()->find($id);
    }

    public function delete($id)
    {
        $getUser = $this->findTrashed($id);
        $_status = $getUser->userstat->status;
        $getUser->delete();
        if ($_status == 'goodleader') $getUser->ketuakelas()->delete();
    }

    public function forceDelete($id)
    {
        $getUser = $this->findTrashed($id);
        $_status = $getUser->userstat->status;
        $getUser->forceDelete();
        if ($_status == 'goodleader') $getUser->ketuakelas()->forceDelete();
    }

    # Repo
    public function userCan($status)
    {
        $userCan = [
            # Admin Access
            'greatadmin' => $this->userAbility([
                ['key' => 'auth', 'can' => ['*']],
                ['key' => 'user', 'can' => ['*']]
            ]),
            # Kurikulum Access
            'themanager' => $this->userAbility([
                ['key' => 'auth', 'can' => ['*']],
                ['key' => 'kelas', 'can' => ['*']]
            ]),
            # Guru Access
            'bestteacher' => $this->userAbility([]),
            # Ketua Kelas Access
            'goodleader' => $this->userAbility([]),
        ];
        return $userCan[$status];
    }

    public function userAllow($request, $status)
    {
        return $this->userAllowChildBool($request, $status);
    }

    # Private
    private static function userAbility($request)
    {
        $userAbility = [
            'auth' => ['getCred', 'postLogout'],
            'user' => ['get', 'create', 'show', 'update', 'delete'],
            'kelas' => ['get', 'create', 'show', 'update', 'delete'],
        ];
        $arrayCan = [];
        foreach ($request as $key => $rq) {
            if (in_array($rq['key'], array_keys($userAbility))) {
                if ($rq['can'] == ['*']) {
                    foreach ($userAbility[$rq['key']] as $key => $uac) {
                        $arrayCan[] = $rq['key'] . ":$uac";
                    }
                } else {
                    foreach ($rq['can'] as $key => $rqc) {
                        if (isset($userAbility[$rq['key']][$rqc])) $arrayCan[] = $rq['key'] . ":" . $userAbility[$rq['key']][$rqc];
                    }
                }
            }
        }
        return $arrayCan;
    }

    private static function userAllowChildBool($request, $status)
    {
        $userAllowChild = ['admin' => ['kurikulum', 'guru', 'ketuakelas'], 'guru' => ['ketuakelas']];
        return in_array($request, $userAllowChild[User_getStatus($status)]);
    }
}
