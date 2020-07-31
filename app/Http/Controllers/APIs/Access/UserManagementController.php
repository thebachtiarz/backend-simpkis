<?php

namespace App\Http\Controllers\APIs\Access;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    private $canAllow = ['admin' => ['kurikulum', 'guru', 'siswa'], 'guru' => ['siswa']];

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
        if (in_array(request('_getUsers'), $this->canAllow[User_getStatus(auth()->user()->userstat->status)])) {
            return response()->json(dataResponse($this->getUsersByStatus(request('_getUsers'))), 200);
        }
        return _throwErrorResponse();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
