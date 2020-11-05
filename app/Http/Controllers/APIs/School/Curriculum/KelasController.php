<?php

namespace App\Http\Controllers\APIs\School\Curriculum;

use App\Http\Controllers\Controller;
use App\Managements\School\Curriculum\KelasManagement;

class KelasController extends Controller
{
    protected $KelasManage;

    public function __construct()
    {
        $this->KelasManage = new KelasManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->KelasManage->kelasList(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->KelasManage->kelasStore(request());
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->KelasManage->kelasShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->KelasManage->kelasUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->KelasManage->kelasDestory($id, request());
    }
}
