<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;
use App\Managements\School\Activity\NilaiTambahanManagement;

class NilaiTambahanController extends Controller
{
    protected $NilaiTambahanManage;

    public function __construct()
    {
        $this->NilaiTambahanManage = new NilaiTambahanManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->NilaiTambahanManage->nilaiTambahanList(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->NilaiTambahanManage->nilaiTambahanStore(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->NilaiTambahanManage->nilaiTambahanShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->NilaiTambahanManage->nilaiTambahanUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->NilaiTambahanManage->nilaiTambahanDestory($id);
    }
}
