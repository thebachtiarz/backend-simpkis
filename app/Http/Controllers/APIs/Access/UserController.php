<?php

namespace App\Http\Controllers\APIs\Access;

use App\Http\Controllers\Controller;
use App\Managements\Access\UserManagement;

class UserController extends Controller
{
    protected $UserManage;

    public function __construct()
    {
        $this->middleware(['checkrole:admin,guru']);
        $this->UserManage = new UserManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->UserManage->userList(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->UserManage->userStore(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->UserManage->userShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->UserManage->userUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->UserManage->userDestory($id, request());
    }
}
