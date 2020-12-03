<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;
use App\Managements\School\Activity\KegiatanManagement;

class KegiatanController extends Controller
{
    protected $KegiatanManage;

    public function __construct()
    {
        $this->KegiatanManage = new KegiatanManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->KegiatanManage->kegiatanList(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->KegiatanManage->kegiatanStore(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->KegiatanManage->kegiatanShow($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->KegiatanManage->kegiatanUpdate($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->KegiatanManage->kegiatanDestory($id);
    }
}
