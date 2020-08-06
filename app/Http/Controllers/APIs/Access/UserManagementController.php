<?php

namespace App\Http\Controllers\APIs\Access;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;

class UserManagementController extends Controller
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
        if (in_array($request->_getUsers, $this->canAllow[User_getStatus(User_checkStatus())])) {
            return response()->json(dataResponse(User::getUsersByStatus($request->_getUsers)->get()->map->userSimpleListMap()), 200);
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
                    $name = $request->idSiswa ? '\App\Models\Actor\Siswa::findOrFail(idSiswa)->name' : $request->name;
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
                        // set data to ketuakelas table
                    }
                }, 5);
                return response()->json(successResponse('Successfully create new user'), 201);
            } catch (\Exception $e) {
                return response()->json(errorResponse('Failed create new user, please try again'), 202);
            }
        }
        return response()->json(errorResponse('You are not authorized to create this user'), 202);
    }

    private function showUser($id)
    {
        $getUser = User::withTrashed()->find($id);
        $userStatus = (bool) $getUser ? $getUser->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            return response()->json(dataResponse($getUser->userInfoMap()), 200);
        }
        return response()->json(errorResponse('You are not authorized to view this user'), 202);
    }

    private function updateUser($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getUser = User::withTrashed()->find($id);
        $userStatus = (bool) $getUser ? $getUser->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            return response()->json(dataResponse($getUser->userInfoMap()), 200);
        }
        return response()->json(errorResponse('You are not authorized to update this user'), 202);
    }

    private function destroyUser($id, $request)
    {
        $validator = $this->softDeleteValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getUser = User::withTrashed()->find($id);
        $userStatus = (bool) $getUser ? $getUser->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            if ($request->method == 'force') {
                return response()->json(successResponse('Successfully delete user permanently'), 200);
            }
            return response()->json(successResponse('Successfully delete user'), 200);
        }
        return response()->json(errorResponse('You are not authorized to delete this user'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, [
            '_getUsers' => 'required|string|alpha'
        ]);
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/',
            'name' => ['nullable', 'string', 'min:3', 'regex:/^[a-zA-Z_,.\s]+$/', \Illuminate\Validation\Rule::requiredIf(!isset($request->idSiswa))],
            'status' => 'required|string|',
            'idSiswa' => 'nullable|string|numeric'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/'
        ]);
    }

    private function softDeleteValidator($request)
    {
        return Validator($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
