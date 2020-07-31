<?php

namespace App\Http\Controllers\APIs\Access;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    protected $canAllow = ['admin' => ['kurikulum', 'guru', 'siswa'], 'guru' => ['siswa']];

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
            return response()->json(dataResponse($this->getUsersByStatus(request('_getUsers'))), 200);
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
        $validator = Validator(request()->all(), [
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/',
            'name' => ['nullable', 'string', 'min:3', 'regex:/^[a-zA-Z_.\s]+$/', \Illuminate\Validation\Rule::requiredIf(!request('idSiswa'))],
            'status' => 'required|string|',
            'idSiswa' => 'nullable|string|numeric'
        ]);
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (in_array(request('status'), $this->canAllow[User_getStatus(User_checkStatus())])) {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () {
                    $newCode = User_createNewCode();
                    \Illuminate\Support\Facades\DB::table('users')->insert([
                        'username' => request('username'), 'password' => User_encPass(request('password')), 'code' => $newCode, 'active' => User_setActiveStatus('active')
                    ]);
                    \Illuminate\Support\Facades\DB::table('user_biodatas')->insert([
                        'code' => $newCode, 'name' => ucwords(request('name'))
                    ]);
                    \Illuminate\Support\Facades\DB::table('user_statuses')->insert([
                        'code' => $newCode, 'status' => User_setStatus(request('status'))
                    ]);
                }, 5);
                return response()->json(successResponse('Successfully create new user'), 201);
            } catch (\Exception $e) {
                return response()->json(errorResponse('Failed create new user, please try again'), 202);
            }
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $validator = Validator(request()->all(), [
            'idSiswa' => 'required|string|numeric',
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/'
        ]);
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
        //
    }

    # private -> move to services
    private function getUsersByStatus($status)
    {
        if (in_array($status, $this->canAllow['admin'])) {
            return User::getUsersByStatus($status)->get();
        }
        return 'cannot get user list';
    }
}
