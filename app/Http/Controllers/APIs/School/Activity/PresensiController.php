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
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanPresensi()->find($request->kegiatanid);
        if ((bool) $getKegiatan) {
            if ($this->userstat() == 'ketuakelas') {
                // cek apakah presensi dilakukan pada waktunya
                if (!Atv_boolPresensiTimeAllowed($getKegiatan->hari, $getKegiatan->waktu_mulai, $getKegiatan->waktu_selesai)) return response()->json(errorResponse('Presensi tidak dilakukan pada waktunya'), 202);
                $approve = '5';
            } else $approve = '7';
            $getKegiatan = $getKegiatan->kegiatanCollectMap();
            $kodeKegiatan = Arr_pluck($getKegiatan['nilai'], 'code');
            $getDataPresensi = (new \App\Models\School\Activity\Presensi)->getNilai($request->presensidata);
            $newPresensi = [];
            $newPresensiGroup = \App\Models\School\Activity\PresensiGroup::create(['catatan' => $request->catatan, 'approve' => $approve]);
            foreach ($getDataPresensi as $key => $value) if (in_array($value['nilai'], $kodeKegiatan)) $newPresensi[] = ['id_presensi' => strval($newPresensiGroup->id), 'id_semester' => strval(Cur_getActiveIDSemesterNow()), 'id_kegiatan' => $request->kegiatanid, 'id_siswa' => $value['id_siswa'], 'nilai' => $value['nilai']];
            if (count($newPresensi)) \App\Models\School\Activity\Presensi::insert($newPresensi);
            return response()->json(successResponse('Berhasil melakukan presensi'), 201);
        }
        return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
    }

    private function showPresensi($id)
    {
        $getPresensi = \App\Models\School\Activity\Presensi::find($id);
        if ((bool) $getPresensi) return response()->json(dataResponse($getPresensi->presensiSimpleInfoMap()), 200);
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
        $presensi = \App\Models\School\Activity\Presensi::where('id_kegiatan', 2)->limit(30)->get();
        $arr = [];
        foreach ($presensi as $key => $value) {
            $arr[] = [
                'id_siswa' => $value['id_siswa'],
                'nilai' => $value['nilai']
            ];
        }
        $serialize = 'a:30:{i:0;a:2:{s:8:"id_siswa";s:1:"2";s:5:"nilai";s:6:"U0WSfQ";}i:1;a:2:{s:8:"id_siswa";s:2:"16";s:5:"nilai";s:6:"U0WSfQ";}i:2;a:2:{s:8:"id_siswa";s:2:"17";s:5:"nilai";s:6:"yYU3iO";}i:3;a:2:{s:8:"id_siswa";s:2:"31";s:5:"nilai";s:6:"MDcaHu";}i:4;a:2:{s:8:"id_siswa";s:2:"53";s:5:"nilai";s:6:"yYU3iO";}i:5;a:2:{s:8:"id_siswa";s:2:"79";s:5:"nilai";s:6:"yYU3iO";}i:6;a:2:{s:8:"id_siswa";s:3:"100";s:5:"nilai";s:6:"yYU3iO";}i:7;a:2:{s:8:"id_siswa";s:3:"105";s:5:"nilai";s:6:"MDcaHu";}i:8;a:2:{s:8:"id_siswa";s:3:"107";s:5:"nilai";s:6:"U0WSfQ";}i:9;a:2:{s:8:"id_siswa";s:3:"108";s:5:"nilai";s:6:"GU6NpL";}i:10;a:2:{s:8:"id_siswa";s:3:"112";s:5:"nilai";s:6:"yYU3iO";}i:11;a:2:{s:8:"id_siswa";s:3:"113";s:5:"nilai";s:6:"U0WSfQ";}i:12;a:2:{s:8:"id_siswa";s:3:"117";s:5:"nilai";s:6:"GU6NpL";}i:13;a:2:{s:8:"id_siswa";s:3:"136";s:5:"nilai";s:6:"GU6NpL";}i:14;a:2:{s:8:"id_siswa";s:3:"139";s:5:"nilai";s:6:"yYU3iO";}i:15;a:2:{s:8:"id_siswa";s:3:"146";s:5:"nilai";s:6:"U0WSfQ";}i:16;a:2:{s:8:"id_siswa";s:3:"151";s:5:"nilai";s:6:"U0WSfQ";}i:17;a:2:{s:8:"id_siswa";s:3:"161";s:5:"nilai";s:6:"U0WSfQ";}i:18;a:2:{s:8:"id_siswa";s:3:"165";s:5:"nilai";s:6:"U0WSfQ";}i:19;a:2:{s:8:"id_siswa";s:3:"213";s:5:"nilai";s:6:"U0WSfQ";}i:20;a:2:{s:8:"id_siswa";s:3:"217";s:5:"nilai";s:6:"MDcaHu";}i:21;a:2:{s:8:"id_siswa";s:3:"218";s:5:"nilai";s:6:"U0WSfQ";}i:22;a:2:{s:8:"id_siswa";s:3:"222";s:5:"nilai";s:6:"GU6NpL";}i:23;a:2:{s:8:"id_siswa";s:3:"234";s:5:"nilai";s:6:"GU6NpL";}i:24;a:2:{s:8:"id_siswa";s:3:"239";s:5:"nilai";s:6:"yYU3iO";}i:25;a:2:{s:8:"id_siswa";s:3:"243";s:5:"nilai";s:6:"GU6NpL";}i:26;a:2:{s:8:"id_siswa";s:3:"244";s:5:"nilai";s:6:"MDcaHu";}i:27;a:2:{s:8:"id_siswa";s:3:"247";s:5:"nilai";s:6:"MDcaHu";}i:28;a:2:{s:8:"id_siswa";s:3:"249";s:5:"nilai";s:6:"MDcaHu";}i:29;a:2:{s:8:"id_siswa";s:3:"259";s:5:"nilai";s:6:"MDcaHu";}}';
        // return serialize($arr);
        return unserialize($serialize);
    }
}
