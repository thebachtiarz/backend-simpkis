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
        if ($request->getOnly == 'unapproved') {
            if ($request->getType == 'list') {
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
        //
    }

    private function showPresensi($id)
    {
        $getPresensi = \App\Models\School\Activity\Presensi::find($id);
        if ((bool)$getPresensi) {
            return response()->json(dataResponse($getPresensi->presensiSImpleInfoMap()), 200);
        }
        return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
    }

    private function updatePresensi($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getPresensi = \App\Models\School\Activity\Presensi::find($id);
        $getKegiatan = \App\Models\School\Activity\Kegiatan::find((bool)$getPresensi ? $getPresensi->id_kegiatan : '');
        if (((bool) $getPresensi) && ((bool) $getKegiatan)) {
            $getNilaiData = unserialize($getKegiatan->nilai);
            $getKegiatanKey = in_array($request->nilai, array_keys($getNilaiData));
            if ((bool) $getKegiatanKey) {
                $result = ['siswa' => $getPresensi->siswa->nama, 'kegiatan' => $getKegiatan->nama, 'poin_lama' => $getNilaiData[$getPresensi->nilai]['name'], 'poin_baru' => $getNilaiData[$request->nilai]['name']];
                $getPresensi->update(['nilai' => $request->nilai]);
                return response()->json(successResponse('Berhasil memperbarui data presensi', $result), 201);
            }
            return response()->json(errorResponse('Poin kegiatan tidak ditemukan'), 202);
        }
        return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
    }

    private function destroyPresensi($id)
    {
        $getPresensi = \App\Models\School\Activity\Presensi::find($id);
        if ((bool)$getPresensi) {
            $getPresensi->delete();
            return response()->json(successResponse('Berhasil menghapus presensi'), 200);
        }
        return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'getOnly' => 'nullable|string|alpha',
            'getType' => 'nullable|string|alpha',
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
        return Validator($request, [
            'nilai' => 'required|string|alpha_num|size:6'
        ]);
    }
}
