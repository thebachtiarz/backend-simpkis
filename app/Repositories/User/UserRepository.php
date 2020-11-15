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

    public function getLastUserId()
    {
        return $this->User->orderByDesc('id')->first('id')->id;
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
        if ($_status == 'goodleader') $getUser->ketuakelas()->delete();
        $getUser->delete();
    }

    public function forceDelete($id)
    {
        $getUser = $this->findTrashed($id);
        $_status = $getUser->userstat->status;
        if ($_status == 'goodleader') $getUser->ketuakelas()->forceDelete();
        $getUser->forceDelete();
    }

    # Repo
    public function userCan($status)
    {
        $userCan = [
            # Admin Access
            'admin' => $this->userAbility([
                ['key' => 'auth', 'can' => ['*']],
                ['key' => 'user', 'can' => ['*']],
                ['key' => 'kelas', 'can' => ['get']],
                ['key' => 'siswa', 'can' => ['get']],
                ['key' => 'ketkel', 'can' => ['create']]
            ]),
            # Kurikulum Access
            'kurikulum' => $this->userAbility([
                ['key' => 'auth', 'can' => ['*']],
                ['key' => 'kelas', 'can' => ['*']],
                ['key' => 'siswa', 'can' => ['*']],
                ['key' => 'semester', 'can' => ['*']],
                ['key' => 'nilakh', 'can' => ['get']]
            ]),
            # Guru Access
            'guru' => $this->userAbility([
                ['key' => 'auth', 'can' => ['*']],
                ['key' => 'user', 'can' => ['create']],
                ['key' => 'kelas', 'can' => ['get']],
                ['key' => 'siswa', 'can' => ['get']],
                ['key' => 'ketkel', 'can' => ['*']],
                ['key' => 'kegiatan', 'can' => ['*']],
                ['key' => 'niltam', 'can' => ['*']],
                ['key' => 'presensi', 'can' => ['*']],
                ['key' => 'nilakh', 'can' => ['*']]
            ]),
            # Ketua Kelas Access
            'ketuakelas' => $this->userAbility([
                ['key' => 'auth', 'can' => ['*']],
                ['key' => 'kegiatan', 'can' => ['get']],
                ['key' => 'siswa', 'can' => ['get']],
                ['key' => 'presensi', 'can' => ['get', 'create']]
            ]),
        ];
        return $userCan[User_getStatus($status)];
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
            'siswa' => ['get', 'create', 'show', 'update', 'delete'],
            'ketkel' => ['get', 'create', 'show', 'update', 'delete'],
            'semester' => ['get', 'create', 'show', 'update', 'delete'],
            'kegiatan' => ['get', 'create', 'show', 'update', 'delete'],
            'niltam' => ['get', 'create', 'show', 'update', 'delete'],
            'presensi' => ['get', 'create', 'show', 'update', 'delete'],
            'nilakh' => ['get', 'create', 'show', 'update', 'delete'],
        ];
        $arrayCan = [];
        foreach ($request as $key => $rq) {
            if (in_array($rq['key'], array_keys($userAbility))) {
                if ($rq['can'][0] == '*') {
                    foreach ($userAbility[$rq['key']] as $key => $uac) {
                        $arrayCan[] = $rq['key'] . ":$uac";
                    }
                } else {
                    foreach ($rq['can'] as $key => $rqc) {
                        if (in_array($rqc, $userAbility[$rq['key']])) $arrayCan[] = $rq['key'] . ":$rqc";
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
