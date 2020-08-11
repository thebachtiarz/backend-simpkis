<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;

class PresensiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:guru,ketuakelas']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listPresensi(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storePresensi(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showPresensi($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updatePresensi($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyPresensi($id);
    }

    # private -> move to services
    private function listPresensi($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (isset($request->getOnly) && ($request->getType == 'list')) {
            if ($request->getOnly == 'unapproved') {
                $getPresensiGroup = \App\Models\School\Activity\PresensiGroup::getUnapprovedPresenceToday();
                return response()->json(dataResponse($getPresensiGroup->get()->map->presensigroupSImpleListMap(), '', 'Total presensi: ' . $getPresensiGroup->count() . ' belum divalidasi hari ini'), 200);
            }
        }
        if (isset($request->presensiid)) {
            $getPresensi = \App\Models\School\Activity\Presensi::where('id_presensi', $request->presensiid);
            return response()->json(dataResponse($getPresensi->get()->map->presensiSimpleListMap(), '', 'Presensi: ' . $getPresensi->get()[0]->kegiatan->nama . ', Kelas: ' . Cur_getKelasNameByID($getPresensi->get()[0]->siswa->id_kelas)), 200);
        }
        if (isset($request->kelasid) || isset($request->siswaid)) {
            $getPresensi = \App\Models\School\Activity\Presensi::query();
            if (isset($request->smtid)) $getPresensi = $getPresensi->where('id_semester', $request->smtid);
            else $getPresensi = $getPresensi->where('id_semester', Cur_getActiveIDSemesterNow());
            $getPresensi = $getPresensi->where('id_kegiatan', $request->kegiatanid);
            if (isset($request->kelasid)) {
                $getPresensi = $getPresensi->whereIn('id_siswa', function ($q) use ($request) {
                    $q->select('id')->from('siswas')->where('id_kelas', $request->kelasid);
                });
            }
            if (isset($request->siswaid)) $getPresensi = $getPresensi->where('id_siswa', $request->siswaid);
            return response()->json(dataResponse($getPresensi->get()->map->presensiSimpleListMap(), '', 'Total: ' . $getPresensi->count() . ' rekap presensi'), 200);
        }
        return response()->json(errorResponse('Tentukan [id siswa] atau [id kelas] yang akan dicari'), 202);
    }

    private function storePresensi($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
    }

    private function showPresensi($id)
    {
        //
    }

    private function updatePresensi($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
    }

    private function destroyPresensi($id)
    {
        //
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'getOnly' => 'nullable|string|alpha|required_with:getType,getOnly',
            'getType' => 'nullable|string|alpha|required_with:getOnly,getType',
            'presensiid' => 'nullable|string|numeric|required_without:kegiatanid',
            'siswaid' => 'nullable|string|numeric',
            'kelasid' => 'nullable|string|numeric',
            'kegiatanid' => 'nullable|string|numeric|required_without:presensiid',
            'smtid' => 'nullable|string|numeric'
        ]);
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
