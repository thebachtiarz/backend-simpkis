<?php

namespace App\Http\Controllers\APIs\School\Actor;

use App\Http\Controllers\Controller;
use App\Managements\Access\UserManagement;
use App\Managements\School\Actor\KetuaKelasManagement;

class KetuaKelasController extends Controller
{
    protected $KetuaKelasManage;
    protected $UserManage;

    public function __construct()
    {
        $this->KetuaKelasManage = new KetuaKelasManagement;
        $this->UserManage = new UserManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->KetuaKelasManage->ketuakelasList();
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
        return $this->KetuaKelasManage->ketuakelasShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->KetuaKelasManage->ketuakelasUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->KetuaKelasManage->ketuakelasDestory($id);
    }
}
