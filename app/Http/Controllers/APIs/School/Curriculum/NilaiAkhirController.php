<?php

namespace App\Http\Controllers\APIs\School\Curriculum;

use App\Http\Controllers\Controller;
use App\Managements\School\Curriculum\NilaiAkhirManagement;

class NilaiAkhirController extends Controller
{
    protected $NilaiAkhirManage;

    public function __construct()
    {
        $this->NilaiAkhirManage = new NilaiAkhirManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->NilaiAkhirManage->nilaiAkhirList(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->NilaiAkhirManage->nilaiAkhirStore(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->NilaiAkhirManage->nilaiAkhirShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->NilaiAkhirManage->nilaiAkhirUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->NilaiAkhirManage->nilaiAkhirDestory($id);
    }
}
