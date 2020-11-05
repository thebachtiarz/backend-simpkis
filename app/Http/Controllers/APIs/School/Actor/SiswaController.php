<?php

namespace App\Http\Controllers\APIs\School\Actor;

use App\Http\Controllers\Controller;
use App\Managements\School\Actor\SiswaManagement;

class SiswaController extends Controller
{
    protected $SiswaManage;

    public function __construct()
    {
        $this->SiswaManage = new SiswaManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->SiswaManage->siswaList(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->SiswaManage->siswaStore(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->SiswaManage->siswaShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->SiswaManage->siswaUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->SiswaManage->siswaDestory($id, request());
    }
}
