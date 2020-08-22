<?php

namespace App\Http\Controllers\APIs\Access;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:admin,guru']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listUser(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeNewUser(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showUser($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateUser($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyUser($id, request());
    }

    # private -> move to services
    protected $canAllow = ['admin' => ['kurikulum', 'guru', 'ketuakelas'], 'guru' => ['ketuakelas']];

    private function listUser($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (in_array($request->status, $this->canAllow[User_getStatus(User_checkStatus())])) {
            $getUsers = User::getUsersByStatus($request->status);
            return response()->json(dataResponse($getUsers->get()->map->userSimpleListMap(), '', 'Total: ' . $getUsers->count() . ' ' . User_getStatusForHuman(User_setStatus($request->status))), 200);
        }
        return _throwErrorResponse();
    }

    private function storeNewUser($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (in_array($request->status, $this->canAllow[User_getStatus(User_checkStatus())])) {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                    $newCode = User_createNewCode();
                    $name = $request->siswaid ? \App\Models\School\Actor\Siswa::find($request->siswaid)->nama : $request->name;
                    \Illuminate\Support\Facades\DB::table('users')->insert([
                        'username' => $request->username, 'password' => User_encPass($request->password), 'code' => $newCode, 'active' => User_setActiveStatus('active')
                    ]);
                    \Illuminate\Support\Facades\DB::table('user_biodatas')->insert([
                        'code' => $newCode, 'name' => ucwords($name)
                    ]);
                    \Illuminate\Support\Facades\DB::table('user_statuses')->insert([
                        'code' => $newCode, 'status' => User_setStatus($request->status)
                    ]);
                    if ($request->status == 'ketuakelas') {
                        /**
                         * gunakan(Services:: KetuaKelasManagement)
                         * input : id_user, id_siswa
                         */
                    }
                }, 5);
                return response()->json(successResponse('Berhasil membuat pengguna baru'), 201);
            } catch (\Exception $e) {
                return response()->json(errorResponse('Gagal membuat pengguna baru, silahkan coba kembali'), 202);
            }
        }
        return response()->json(errorResponse('Anda tidak diperkenankan untuk membuat pengguna ini'), 202);
    }

    private function showUser($id)
    {
        $getUser = User::withTrashed()->find($id);
        $userStatus = (bool) $getUser ? $getUser->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            return response()->json(dataResponse($getUser->userInfoMap()), 200);
        }
        return response()->json(errorResponse('Anda tidak diperkenankan untuk melihat pengguna ini'), 202);
    }

    private function updateUser($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getUser = User::withTrashed()->find($id);
        $userStatus = (bool) $getUser ? $getUser->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            $getChange = array_filter($request->all());
            if ((bool) $getChange) {
                try {
                    $getChangeKey = array_keys($getChange); // get key from request
                    $oldData = [];
                    if (isset($request->name)) {
                        array_push($oldData, $getUser->userbio->name);
                        $getUser->userbio->update(['name' => $request->name]);
                    }
                    if (isset($request->status) && in_array($request->status, $this->canAllow[User_getStatus(User_checkStatus())])) {
                        array_push($oldData, User_getStatusForHuman($getUser->userstat->status));
                        $getUser->userstat->update(['status' => User_setStatus($request->status)]);
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

    private function destroyUser($id, $request)
    {
        $validator = $this->softDeleteValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getUser = User::withTrashed()->find($id);
        $userStatus = (bool) $getUser ? $getUser->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            if ($request->method == 'force') {
                return response()->json(successResponse('Berhasil menghapus pengguna secara permanen'), 200);
            }
            return response()->json(successResponse('Berhasil menghapus pengguna'), 200);
        }
        return response()->json(errorResponse('Anda tidak diperkenankan untuk menghapus pengguna ini'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'status' => 'required|string|alpha'
        ]);
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/',
            'name' => ['nullable', 'string', 'min:3', 'regex:/^[a-zA-Z_,.\s]+$/', \Illuminate\Validation\Rule::requiredIf(!isset($request->siswaid))],
            'status' => 'required|string',
            'siswaid' => 'nullable|string|numeric'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'name' => 'nullable|string|min:3|regex:/^[a-zA-Z_,.\s]+$/',
            'status' => 'nullable|string'
        ]);
    }

    private function softDeleteValidator($request)
    {
        return Validator($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
