<?php

namespace App\Http\Controllers\APIs\Access;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;

class UserManagementController extends Controller
{
    protected $canAllow = ['admin' => ['kurikulum', 'guru', 'ketuakelas'], 'guru' => ['ketuakelas']];

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
        if (in_array(request('_getUsers'), $this->canAllow[User_getStatus(User_checkStatus())])) {
            return response()->json(dataResponse(User::getUsersByStatus(request('_getUsers'))->get()->map->userSimpleListMap()), 200);
        }
        return _throwErrorResponse();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = $this->storeValidator(request()->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (in_array(request('status'), $this->canAllow[User_getStatus(User_checkStatus())])) {
            return $this->storeNewUser(request());
        }
        return response()->json(errorResponse('You are not authorized to create this user'), 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userStatus = (bool) User::find($id) ? User::find($id)->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            return response()->json(dataResponse(User::where('id', $id)->get()->map->userInfoMap()), 200);
        }
        return response()->json(errorResponse('You are not authorized to view this user'), 202);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $validator = $this->updateValidator(request()->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        return response()->json(successResponse(User::findOrFail($id)->userbio->name), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userStatus = (bool) User::find($id) ? User::find($id)->userstat->status : '';
        if (in_array(User_getStatus($userStatus), $this->canAllow[User_getStatus(User_checkStatus())])) {
            return response()->json(dataResponse(User::where('id', $id)->get()->map->userInfoMap()), 200);
        }
        return response()->json(errorResponse('You are not authorized to delete this user'), 202);
    }

    # private -> move to services
    private function storeNewUser($user)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                $newCode = User_createNewCode();
                $name = $user->idSiswa ? '\App\Models\Actor\Siswa::findOrFail(idSiswa)->name' : $user->name;
                \Illuminate\Support\Facades\DB::table('users')->insert([
                    'username' => $user->username, 'password' => User_encPass($user->password), 'code' => $newCode, 'active' => User_setActiveStatus('active')
                ]);
                \Illuminate\Support\Facades\DB::table('user_biodatas')->insert([
                    'code' => $newCode, 'name' => ucwords($name)
                ]);
                \Illuminate\Support\Facades\DB::table('user_statuses')->insert([
                    'code' => $newCode, 'status' => User_setStatus($user->status)
                ]);
            }, 5);
            return response()->json(successResponse('Successfully create new user'), 201);
        } catch (\Exception $e) {
            return response()->json(errorResponse('Failed create new user, please try again'), 202);
        }
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/',
            'name' => ['nullable', 'string', 'min:3', 'regex:/^[a-zA-Z_,.\s]+$/', \Illuminate\Validation\Rule::requiredIf(!request('idSiswa'))],
            'status' => 'required|string|',
            'idSiswa' => 'nullable|string|numeric'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'idSiswa' => 'required|string|numeric',
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/'
        ]);
    }
}
