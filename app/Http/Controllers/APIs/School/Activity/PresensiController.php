<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;
use App\Managements\School\Activity\PresensiManagement;

class PresensiController extends Controller
{
    protected $PresensiManage;

    public function __construct()
    {
        $this->PresensiManage = new PresensiManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->PresensiManage->presensiList(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->PresensiManage->presensiStore(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->PresensiManage->presensiShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->PresensiManage->presensiUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->PresensiManage->presensiDestory($id);
    }
}
