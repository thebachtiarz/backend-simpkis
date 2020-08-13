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
    private function userstat() // move to constructor at services
    {
        return User_getStatus(User_checkStatus());
    }

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
            $getPresensi = $getPresensi->where('id_semester', isset($request->smtid) ? $request->smtid : Cur_getActiveIDSemesterNow());
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
        if ($this->userstat() == 'ketuakelas') {
            // buat waktu pelaksanaan diizinkan, buat mengunakan config
            $approve = '5';
        } else $approve = '7';
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanPresensi()->find($request->kegiatanid);
        if ((bool) $getKegiatan) {
            $getKegiatan = $getKegiatan->kegiatanCollectMap();
            $kodeKegiatan = pluckArray($getKegiatan['nilai'], 'code');
            $getDataPresensi = (new \App\Models\School\Activity\Presensi)->getNilai($request->presensidata);
            $getNewIDPresensi = (int) Atv_getLastIdPresensi() + 1;
            $newPresensi = [];
            foreach ($getDataPresensi as $key => $value) $newPresensi[] = in_array($value['nilai'], $kodeKegiatan) ? ['id_presensi' => strval($getNewIDPresensi), 'id_semester' => strval(Cur_getActiveIDSemesterNow()), 'id_kegiatan' => $request->kegiatanid, 'id_siswa' => $value['id_siswa'], 'nilai' => $value['nilai']] : [];
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($request, $approve, $newPresensi) {
                    \Illuminate\Support\Facades\DB::table('presensi_groups')->insert(['catatan' => $request->catatan, 'approve' => $approve]);
                    \Illuminate\Support\Facades\DB::table('presensis')->insert($newPresensi);
                }, 5);
                return response()->json(successResponse('Berhasil melakukan presensi'), 201);
            } catch (\Exception $e) {
                return response()->json(errorResponse('Presensi gagal dilakukan, silahkan coba kembali'), 202);
            }
        }
        return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
    }

    private function showPresensi($id)
    {
        $getPresensi = \App\Models\School\Activity\Presensi::find($id);
        if ((bool) $getPresensi) {
            return response()->json(dataResponse($getPresensi->presensiSimpleInfoMap()), 200);
        }
        return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
    }

    private function updatePresensi($id, $request)
    {
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getPresensi = \App\Models\School\Activity\Presensi::find($id);
        $getKegiatan = \App\Models\School\Activity\Kegiatan::find((bool) $getPresensi ? $getPresensi->id_kegiatan : '');
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
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        $getPresensi = \App\Models\School\Activity\Presensi::find($id);
        if ((bool) $getPresensi) {
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
        return Validator($request, [
            'catatan' => 'nullable|string|regex:/^[a-zA-Z0-9_,.\s]+$/',
            'kegiatanid' => 'required|string|numeric',
            'presensidata' => 'required|string'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'nilai' => 'required|string|alpha_num|size:6'
        ]);
    }

    private function testing()
    {
        $presensi = \App\Models\School\Activity\Presensi::where('id', '>', 1000)->limit(30)->get();
        $arr = [];
        foreach ($presensi as $key => $value) {
            $arr[] = [
                'id_siswa' => $value['id_siswa'],
                'nilai' => $value['nilai']
            ];
        }
        $serialize = 'a:30:{i:0;a:2:{s:8:"id_siswa";s:3:"707";s:5:"nilai";s:6:"WCivkZ";}i:1;a:2:{s:8:"id_siswa";s:3:"712";s:5:"nilai";s:6:"29X1Iw";}i:2;a:2:{s:8:"id_siswa";s:3:"714";s:5:"nilai";s:6:"WCivkZ";}i:3;a:2:{s:8:"id_siswa";s:3:"755";s:5:"nilai";s:6:"bURO1J";}i:4;a:2:{s:8:"id_siswa";s:3:"758";s:5:"nilai";s:6:"wPeGxd";}i:5;a:2:{s:8:"id_siswa";s:3:"786";s:5:"nilai";s:6:"WCivkZ";}i:6;a:2:{s:8:"id_siswa";s:3:"798";s:5:"nilai";s:6:"WCivkZ";}i:7;a:2:{s:8:"id_siswa";s:3:"802";s:5:"nilai";s:6:"bURO1J";}i:8;a:2:{s:8:"id_siswa";s:3:"816";s:5:"nilai";s:6:"wPeGxd";}i:9;a:2:{s:8:"id_siswa";s:3:"819";s:5:"nilai";s:6:"WCivkZ";}i:10;a:2:{s:8:"id_siswa";s:3:"820";s:5:"nilai";s:6:"WCivkZ";}i:11;a:2:{s:8:"id_siswa";s:3:"827";s:5:"nilai";s:6:"bURO1J";}i:12;a:2:{s:8:"id_siswa";s:3:"838";s:5:"nilai";s:6:"wPeGxd";}i:13;a:2:{s:8:"id_siswa";s:3:"842";s:5:"nilai";s:6:"29X1Iw";}i:14;a:2:{s:8:"id_siswa";s:3:"854";s:5:"nilai";s:6:"bURO1J";}i:15;a:2:{s:8:"id_siswa";s:3:"855";s:5:"nilai";s:6:"WCivkZ";}i:16;a:2:{s:8:"id_siswa";s:3:"862";s:5:"nilai";s:6:"29X1Iw";}i:17;a:2:{s:8:"id_siswa";s:3:"866";s:5:"nilai";s:6:"WCivkZ";}i:18;a:2:{s:8:"id_siswa";s:3:"874";s:5:"nilai";s:6:"29X1Iw";}i:19;a:2:{s:8:"id_siswa";s:3:"885";s:5:"nilai";s:6:"29X1Iw";}i:20;a:2:{s:8:"id_siswa";s:3:"892";s:5:"nilai";s:6:"WCivkZ";}i:21;a:2:{s:8:"id_siswa";s:3:"922";s:5:"nilai";s:6:"29X1Iw";}i:22;a:2:{s:8:"id_siswa";s:3:"924";s:5:"nilai";s:6:"wPeGxd";}i:23;a:2:{s:8:"id_siswa";s:3:"935";s:5:"nilai";s:6:"bURO1J";}i:24;a:2:{s:8:"id_siswa";s:3:"939";s:5:"nilai";s:6:"bURO1J";}i:25;a:2:{s:8:"id_siswa";s:3:"942";s:5:"nilai";s:6:"WCivkZ";}i:26;a:2:{s:8:"id_siswa";s:3:"953";s:5:"nilai";s:6:"WCivkZ";}i:27;a:2:{s:8:"id_siswa";s:3:"956";s:5:"nilai";s:6:"wPeGxd";}i:28;a:2:{s:8:"id_siswa";s:3:"957";s:5:"nilai";s:6:"wPeGxd";}i:29;a:2:{s:8:"id_siswa";s:3:"967";s:5:"nilai";s:6:"wPeGxd";}}';
        // return serialize($arr);
        return unserialize($serialize);
    }
}
