<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;

class KegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:guru'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listKegiatan();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeKegiatan(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showKegiatan($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateKegiatan($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyKegiatan($id);
    }

    # private -> move to services
    protected $canAllow = ['guru' => ['7', '5'], 'ketuakelas' => ['5']];

    private function listKegiatan()
    {
        $getKegiatan = \App\Models\School\Activity\Kegiatan::query();
        if (in_array(User_getStatus(User_checkStatus()), array_keys($this->canAllow))) {
            if (User_checkStatus() == User_setStatus('guru')) {
                $getKegiatan = $getKegiatan;
            } else {
                $getKegiatan = $getKegiatan->whereIn('akses', $this->canAllow['ketuakelas']);
            }
            return response()->json(dataResponse($getKegiatan->get()->map->kegiatanSimpleListMap()), 200);
        }
        return response()->json(errorResponse('Anda tidak memiliki izin untuk melihat kegiatan'), 202);
    }

    private function storeKegiatan($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function showKegiatan($id)
    {
        //
    }

    private function updateKegiatan($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function destroyKegiatan($id)
    {
        //
    }

    private function storeValidator($request)
    {
        return Validator($request, []);
    }

    private function updateValidator($request)
    {
        return Validator($request, []);
    }
}
