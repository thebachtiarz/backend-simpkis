<?php

namespace App\Managements\School\Actor;

use App\Models\School\Actor\KetuaKelas;
use App\Models\School\Actor\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class KetuaKelasManagement
{
    // protected ;

    public function __construct()
    {
        //
    }

    # Public
    public function ketuakelasList()
    {
        if (Auth::user()->tokenCan('ketkel:get')) {
            $getKetua = KetuaKelas::all();
            return response()->json(dataResponse($getKetua->map->ketuaSimpleListMap()), 200);
        }
        return _throwErrorResponse();
    }

    public function ketuakelasStore($new_uid, $id_siswa)
    {
        if (Auth::user()->tokenCan('ketkel:create')) {
            $getSiswa = Siswa::find($id_siswa);
            if ((bool) $getSiswa) {
                DB::table('ketua_kelas')->insert([
                    'id_siswa' => $id_siswa,
                    'id_kelas' => $getSiswa->id_kelas,
                    'id_user' => $new_uid
                ]);
            }
        }
        return _throwErrorResponse();
    }

    public function ketuakelasShow($id)
    {
        if (Auth::user()->tokenCan('ketkel:show')) {
            $getKetua = KetuaKelas::find($id);
            return (bool) $getKetua ?
                response()->json(dataResponse($getKetua->ketuaSimpleInfoMap()), 200) :
                response()->json(errorResponse('Ketua kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function ketuakelasUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('ketkel:update')) {
            $validator = $this->ketuakelasUpdateValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getKetua = KetuaKelas::find($id);
            if ((bool) $getKetua) {
                // cari siswa untuk menjadi ketua kelas baru
                $getSiswa = Siswa::find($request->idSiswa);
                // jika siswa yang dicari ada dan siswa juga dari kelas tersebut maka benar
                if (((bool) $getSiswa) && ($getKetua->id_kelas == $getSiswa->id_kelas)) {
                    try {
                        $result = ['old_ketua' => $getKetua->siswa->nama, 'new_ketua' => $getSiswa->nama];
                        $getKetua->update(['id_siswa' => $getSiswa->id]);
                        $getKetua->user->userbio->update(['name' => $getSiswa->nama]);
                        $getKetua->user->update(['username' => Act_formatNewSiswaUsername($getSiswa->nisn), 'password' => Act_formatNewSiswaPassword($getSiswa->nisn)]);
                        return response()->json(dataResponse($result, '', 'Berhasil mengubah ketua kelas'), 200);
                    } catch (\Exception $e) {
                        return response()->json(errorResponse('Gagal melakukan perubahan pada ketua kelas'), 202);
                    }
                }
                return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
            }
            return response()->json(errorResponse('Ketua kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function ketuakelasDestory($id)
    {
        if (Auth::user()->tokenCan('ketkel:delete')) {
            $getKetua = KetuaKelas::find($id);
            if ((bool) $getKetua) {
                try {
                    $response = ['deleted' => ['nama' => $getKetua->siswa->nama, 'kelas' => Cur_getKelasNameByID($getKetua->id_kelas)]];
                    $getKetua->user->userbio->delete();
                    $getKetua->user->userstat->delete();
                    $getKetua->user->forceDelete();
                    $getKetua->delete();
                    return response()->json(dataResponse($response, '', 'Berhasil menghapus ketua kelas'), 200);
                } catch (\Exception $e) {
                    return response()->json(errorResponse('Gagal menghapus ketua kelas'), 202);
                }
            }
            return response()->json(errorResponse('Ketua kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Validator
    private function ketuakelasUpdateValidator($request)
    {
        return Validator::make($request, [
            'idSiswa' => 'required|string|numeric'
        ]);
    }
}
