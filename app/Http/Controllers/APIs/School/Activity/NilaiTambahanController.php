<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;

class NilaiTambahanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:guru']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listNilaiTambahan(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeNilaiTambahan(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showNilaiTambahan($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateNilaiTambahan($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyNilaiTambahan($id);
    }

    # private -> move to services
    private function listNilaiTambahan($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (isset($request->kegiatanid) || isset($request->siswaid)) {
            $getNilaiTambahan = \App\Models\School\Activity\NilaiTambahan::query();
            if (isset($request->smtid)) $getNilaiTambahan = $getNilaiTambahan->where('id_semester', $request->smtid);
            else $getNilaiTambahan = $getNilaiTambahan->where('id_semester', Cur_getActiveIDSemesterNow());
            if (isset($request->kegiatanid)) $getNilaiTambahan = $getNilaiTambahan->where('id_kegiatan', $request->kegiatanid);
            if (isset($request->siswaid)) $getNilaiTambahan = $getNilaiTambahan->where('id_siswa', $request->siswaid);
            return response()->json(dataResponse($getNilaiTambahan->get()->map->nilaitambahanSimpleListMap(), '', 'Total: ' . $getNilaiTambahan->count() . ' poin kegiatan'), 200);
        }
        return response()->json(errorResponse('Tentukan [id siswa] atau [id kegiatan] yang akan dicari'), 202);
    }

    private function storeNilaiTambahan($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getSiswa = \App\Models\School\Actor\Siswa::find($request->siswaid);
        $getKegiatan = \App\Models\School\Activity\Kegiatan::find($request->kegiatanid);
        if (((bool) $getSiswa) && ((bool) $getKegiatan)) {
            $getNilaiData = unserialize($getKegiatan->nilai);
            $getKegiatanKey = in_array($request->nilai, array_keys($getNilaiData));
            if ((bool) $getKegiatanKey) {
                \App\Models\School\Activity\NilaiTambahan::create(['id_semester' => Cur_getActiveIDSemesterNow(), 'id_siswa' => $request->siswaid, 'id_kegiatan' => $request->kegiatanid, 'nilai' => $request->nilai]);
                $result = ['siswa' => $getSiswa->nama, 'kegiatan' => $getKegiatan->nama, 'poin' => $getNilaiData[$request->nilai]['poin']];
                return response()->json(successResponse('Berhasil menambahkan nilai tambahan', $result), 201);
            }
            return response()->json(errorResponse('Poin kegiatan tidak ditemukan'), 202);
        }
        return response()->json(errorResponse('Siswa atau Kegiatan tidak ditemukan'), 202);
    }

    private function showNilaiTambahan($id)
    {
        $getNilaiTambahan = \App\Models\School\Activity\NilaiTambahan::find($id);
        if ((bool) $getNilaiTambahan) return response()->json(dataResponse($getNilaiTambahan->nilaitambahanInfoSimpleMap()), 200);
        return response()->json(errorResponse('Nilai tambahan tidak ditemukan'), 202);
    }

    private function updateNilaiTambahan($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getNilaiTambahan = \App\Models\School\Activity\NilaiTambahan::find($id);
        $getKegiatan = \App\Models\School\Activity\Kegiatan::find($request->kegiatanid);
        if (((bool) $getNilaiTambahan) && ((bool) $getKegiatan)) {
            $getNilaiData = unserialize($getKegiatan->nilai);
            $getKegiatanKey = in_array($request->nilai, array_keys($getNilaiData));
            if ((bool) $getKegiatanKey) {
                $getNilaiTambahan->update(['id_kegiatan' => $request->kegiatanid, 'nilai' => $request->nilai]);
                $result = ['siswa' => $getNilaiTambahan->siswa->nama, 'kegiatan' => $getKegiatan->nama, 'poin' => $getNilaiData[$request->nilai]['poin']];
                return response()->json(successResponse('Berhasil memperbarui nilai tambahan', $result), 201);
            }
            return response()->json(errorResponse('Poin kegiatan tidak ditemukan'), 202);
        }
        return response()->json(errorResponse('Nilai Tambahan atau Kegiatan tidak ditemukan'), 202);
    }

    private function destroyNilaiTambahan($id)
    {
        $getNilaiTambahan = \App\Models\School\Activity\NilaiTambahan::find($id);
        if ((bool) $getNilaiTambahan) {
            $getNilaiTambahan->delete();
            return response()->json(successResponse('Berhasil menghapus nilai tambahan'), 201);
        }
        return response()->json(errorResponse('Nilai tambahan tidak ditemukan'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'siswaid' => ['nullable', 'string', 'numeric', 'required_without:kegiatanid'],
            'kegiatanid' => ['nullable', 'string', 'numeric', 'required_without:siswaid'],
            'smtid' => 'nullable|string|numeric'
        ]);
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'siswaid' => 'required|string|numeric',
            'kegiatanid' => 'required|string|numeric',
            'nilai' => 'required|string|alpha_num|size:6'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'kegiatanid' => 'required|string|numeric',
            'nilai' => 'required|string|alpha_num|size:6'
        ]);
    }
}
