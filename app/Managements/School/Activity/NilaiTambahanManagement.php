<?php

namespace App\Managements\School\Activity;

use App\Models\School\Activity\NilaiTambahan;
use App\Models\School\Activity\Kegiatan;
use App\Models\School\Actor\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NilaiTambahanManagement
{
    public function __construct()
    {
        //
    }

    # Public
    public function nilaiTambahanList($request)
    {
        if (Auth::user()->tokenCan('niltam:get')) {
            $validator = $this->nilaiTambahanListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if (isset($request->kegiatanid) || isset($request->siswaid)) {
                $getNilaiTambahan = NilaiTambahan::getBySemesterId($request->smtid);
                if (isset($request->kegiatanid)) $getNilaiTambahan = $getNilaiTambahan->getByKegiatanId($request->kegiatanid);
                if (isset($request->siswaid)) $getNilaiTambahan = $getNilaiTambahan->getBySiswaId($request->siswaid);
                return response()->json(dataResponse($getNilaiTambahan->get()->map->nilaitambahanInfoSimpleMap(), '', 'Total: ' . $getNilaiTambahan->count() . ' poin kegiatan'), 200);
            }
            return response()->json(errorResponse('Tentukan [id siswa] atau [id kegiatan] yang akan dicari'), 202);
        }
        return _throwErrorResponse();
    }

    public function nilaiTambahanStore($request)
    {
        if (Auth::user()->tokenCan('niltam:create')) {
            $validator = $this->nilaiTambahanStoreValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getSiswa = Siswa::find($request->siswaid);
            $getKegiatan = Kegiatan::find($request->kegiatanid);
            if (((bool) $getSiswa) && ((bool) $getKegiatan)) {
                $getNilaiData = Arr_unserialize($getKegiatan->nilai);
                $getKegiatanKey = in_array($request->nilai, array_keys($getNilaiData));
                if ((bool) $getKegiatanKey) {
                    NilaiTambahan::createNewNilaiTambahan($request);
                    $result = ['siswa' => $getSiswa->nama, 'kegiatan' => $getKegiatan->nama, 'poin' => $getNilaiData[$request->nilai]['poin']];
                    return response()->json(successResponse('Berhasil menambahkan nilai tambahan', $result), 201);
                }
                return response()->json(errorResponse('Poin kegiatan tidak ditemukan'), 202);
            }
            return response()->json(errorResponse('Siswa atau Kegiatan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function nilaiTambahanShow($id)
    {
        if (Auth::user()->tokenCan('niltam:show')) {
            $getNilaiTambahan = NilaiTambahan::find($id);
            if ((bool) $getNilaiTambahan) return response()->json(dataResponse($getNilaiTambahan->nilaitambahanInfoSimpleMap()), 200);
            return response()->json(errorResponse('Nilai tambahan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function nilaiTambahanUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('niltam:update')) {
            $validator = $this->nilaiTambahanUpdateValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getNilaiTambahan = NilaiTambahan::find($id);
            $getKegiatan = Kegiatan::find($request->kegiatanid);
            if (((bool) $getNilaiTambahan) && ((bool) $getKegiatan)) {
                $getNilaiData = Arr_unserialize($getKegiatan->nilai);
                $getKegiatanKey = in_array($request->nilai, array_keys($getNilaiData));
                if ((bool) $getKegiatanKey) {
                    NilaiTambahan::updateNilaiTambahan($id, $request);
                    $result = ['siswa' => $getNilaiTambahan->siswa->nama, 'kegiatan' => $getKegiatan->nama, 'poin' => $getNilaiData[$request->nilai]['name']];
                    return response()->json(successResponse('Berhasil memperbarui nilai tambahan', $result), 201);
                }
                return response()->json(errorResponse('Poin kegiatan tidak ditemukan'), 202);
            }
            return response()->json(errorResponse('Nilai Tambahan atau Kegiatan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function nilaiTambahanDestory($id)
    {
        if (Auth::user()->tokenCan('niltam:delete')) {
            $getNilaiTambahan = NilaiTambahan::find($id);
            if ((bool) $getNilaiTambahan) {
                $getNilaiTambahan->delete();
                return response()->json(successResponse('Berhasil menghapus nilai tambahan'), 201);
            }
            return response()->json(errorResponse('Nilai tambahan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Validator
    private function nilaiTambahanListValidator($request)
    {
        return Validator::make($request, [
            'siswaid' => ['nullable', 'string', 'numeric', 'required_without:kegiatanid'],
            'kegiatanid' => ['nullable', 'string', 'numeric', 'required_without:siswaid'],
            'smtid' => 'nullable|string|numeric'
        ]);
    }

    private function nilaiTambahanStoreValidator($request)
    {
        return Validator::make($request, [
            'siswaid' => 'required|string|numeric',
            'kegiatanid' => 'required|string|numeric',
            'nilai' => 'required|string|alpha_num|size:6'
        ]);
    }

    private function nilaiTambahanUpdateValidator($request)
    {
        return Validator::make($request, [
            'kegiatanid' => 'required|string|numeric',
            'nilai' => 'required|string|alpha_num|size:6'
        ]);
    }
}
