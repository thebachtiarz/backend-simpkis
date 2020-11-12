<?php

namespace App\Managements\Access;

use App\Managements\School\Actor\KetuaKelasManagement;
use App\Models\Auth\User;
use App\Models\School\Actor\Siswa;
use App\Repositories\User\UserRepository;
use App\Repositories\School\Actor\SiswaRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserManagement
{
    protected $UserRepo;
    protected $SiswaRepo;

    public function __construct()
    {
        $this->UserRepo = new UserRepository;
        $this->SiswaRepo = new SiswaRepository;
    }

    # Public
    public function userList($request)
    {
        if (Auth::user()->tokenCan('user:get')) {
            $validator = $this->userListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if ($this->UserRepo->userAllow($request->status, Auth::user()->userstat->status)) {
                $getUsers = User::getUsersByStatus($request->status);
                $_data = $getUsers->get()->map->userInfoMap();
                $_status = '';
                $_message = 'Total: ' . $getUsers->count() . ' ' . User_getStatusForHuman(User_setStatus($request->status));
                return response()->json(dataResponse($_data, $_status, $_message), 200);
            }
        }
        return _throwErrorResponse();
    }

    public function userStore($request)
    {
        if (Auth::user()->tokenCan('user:create')) {
            $validator = $this->userStoreValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if ($this->UserRepo->userAllow($request->status, Auth::user()->userstat->status)) {
                try {
                    $checkUser = User::getAvailableByUsername($request->username);
                    if ($checkUser->count()) throw new \Exception("Pengguna sudah ada", 1);
                    DB::transaction(function () use ($request) {
                        $username = $request->username;
                        $password = User_encPass($request->password);
                        $fullname = $request->name;
                        if (isset($request->siswaid)) {
                            $getSiswa = Siswa::find($request->siswaid);
                            if (!(bool) $getSiswa) throw new \Exception("Siswa tidak ditemukan", 2);
                            $username = Act_formatNewKetuaKelasUsername($getSiswa->nisn);
                            $password = Act_formatNewKetuaKelasPassword($getSiswa->nisn);
                            $fullname = $getSiswa->nama;
                        }
                        DB::table('users')->insert([
                            'username' => $username, 'password' => $password, 'active' => User_setActiveStatus('active')
                        ]);
                        DB::table('user_biodatas')->insert([
                            'name' => ucwords($fullname)
                        ]);
                        DB::table('user_statuses')->insert([
                            'status' => User_setStatus($request->status)
                        ]);
                        if ($request->status == 'ketuakelas') {
                            $new_uid = $this->UserRepo->getLastUserId();
                            (new KetuaKelasManagement)->ketuakelasStore($new_uid, $request->siswaid);
                        }
                    }, 5);
                    return response()->json(successResponse('Berhasil membuat pengguna baru'), 201);
                } catch (\Exception $e) {
                    return response()->json(errorResponse('Gagal membuat pengguna baru, silahkan coba kembali'), 202);
                }
            }
            return response()->json(errorResponse('Anda tidak diperkenankan untuk membuat pengguna ini'), 202);
        }
        return _throwErrorResponse();
    }

    public function userShow($id)
    {
        if (Auth::user()->tokenCan('user:show')) {
            $getUser = User::withTrashed()->find($id);
            $getUserStatus = (bool) $getUser ? User_getStatus($getUser->userstat->status) : '';
            if ($this->UserRepo->userAllow($getUserStatus, Auth::user()->userstat->status)) {
                return response()->json(dataResponse($getUser->userInfoMap()), 200);
            }
            return response()->json(errorResponse('Anda tidak diperkenankan untuk melihat pengguna ini'), 202);
        }
        return _throwErrorResponse();
    }

    public function userUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('user:update')) {
            $validator = $this->userUpdateValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getUser = User::withTrashed()->find($id);
            $getUserStatus = (bool) $getUser ? User_getStatus($getUser->userstat->status) : '';
            if ($this->UserRepo->userAllow($getUserStatus, Auth::user()->userstat->status)) {
                $getChange = array_filter($request->all());
                if ((bool) $getChange) {
                    try {
                        $getChangeKey = array_keys($getChange); // get key from request
                        $oldData = [];
                        if (isset($request->name)) {
                            array_push($oldData, $getUser->userbio->name);
                            $getUser->userbio()->update(['name' => $request->name]);
                        }
                        if (isset($request->status) && $this->UserRepo->userAllow($request->status, Auth::user()->userstat->status)) {
                            array_push($oldData, User_getStatusForHuman($getUser->userstat->status));
                            $getUser->userstat()->update(['status' => User_setStatus($request->status)]);
                            $getChange['status'] = User_getStatusForHuman(User_setStatus($request->status));
                        }
                        $response = ['oldData' => array_combine($getChangeKey, $oldData), 'newData' => $getChange];
                        return response()->json(dataResponse($response, '', 'Berhasil memperbarui data pengguna'), 201);
                    } catch (\Exception $e) {
                        return response()->json(errorResponse('Gagal memperbarui data pengguna, silahkan coba kembali'), 202);
                    }
                }
                return response()->json(errorResponse('Silahkan sebutkan apa yang ingin diubah'), 202);
            }
            return response()->json(errorResponse('Anda tidak diperkenankan untuk mengubah pengguna ini'), 202);
        }
        return _throwErrorResponse();
    }

    public function userDestory($id, $request)
    {
        if (Auth::user()->tokenCan('user:delete')) {
            $validator = $this->userDestroyValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getUser = ($request->method == 'force') ? User::withTrashed()->find($id) : User::find($id);
            $getUserStatus = (bool) $getUser ? $getUser->userstat->status : '';
            if ($this->UserRepo->userAllow(User_getStatus($getUserStatus), Auth::user()->userstat->status)) {
                if ($request->method == 'force') {
                    if (($getUserStatus == User_setStatus('ketuakelas') && (bool) $getUser->ketuakelas)) $getUser->ketuakelas->delete();
                    $getUser->userbio->delete();
                    $getUser->userstat->delete();
                    $getUser->forceDelete();
                    return response()->json(successResponse('Berhasil menghapus pengguna secara permanen'), 200);
                } else {
                    if (($getUserStatus == User_setStatus('ketuakelas') && (bool) $getUser->ketuakelas)) $getUser->ketuakelas->delete();
                    $getUser->delete();
                }
                return response()->json(successResponse('Berhasil menghapus pengguna'), 200);
            }
            return response()->json(errorResponse('Anda tidak diperkenankan untuk menghapus pengguna ini'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Validator
    private function userListValidator($request)
    {
        return Validator::make($request, [
            'status' => 'required|string|alpha'
        ]);
    }

    private function userStoreValidator($request)
    {
        return Validator::make($request, [

            'username' => 'nullable|string|min:8|alpha_num|unique:users,username|required_without:siswaid',
            'password' => 'nullable|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/|required_without:siswaid',
            'name' => 'nullable|string|min:3|regex:/^[a-zA-Z_,.\s]+$/|required_without:siswaid',
            'status' => 'required|string',
            'siswaid' => 'nullable|string|numeric'
        ]);
    }

    private function userUpdateValidator($request)
    {
        return Validator::make($request, [
            'name' => 'nullable|string|min:3|regex:/^[a-zA-Z_,.\s]+$/',
            'status' => 'nullable|string'
        ]);
    }

    private function userDestroyValidator($request)
    {
        return Validator::make($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
