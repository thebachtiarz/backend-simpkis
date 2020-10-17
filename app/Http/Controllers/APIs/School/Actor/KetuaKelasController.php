<?php

namespace App\Http\Controllers\APIs\School\Actor;

use App\Http\Controllers\Controller;

class KetuaKelasController extends Controller
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
        return $this->listKetuaKelas(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // todo: menggunakan (Services:: UserManagement)
        // kemungkinan route ini tidak digunakan
        return $this->storeKetuaKelas('', '');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showKetuaKelas($id, request());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateKetuaKelas($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyKetuaKelas($id);
    }

    # private -> move to services
    private function listKetuaKelas($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKetua = \App\Models\School\Actor\KetuaKelas::all();
        return response()->json(dataResponse($getKetua->map->ketuaSimpleListMap()), 200);
    }

    private function storeKetuaKelas($new_uid, $id_siswa)
    {
        // lanjutan dari (Services:: UserManagement)
        // setelah input data ke user selesai
        // apabila status == ketuakelas
        $getSiswa = \App\Models\School\Actor\Siswa::find($id_siswa);
        if ((bool) $getSiswa) {
            \Illuminate\Support\Facades\DB::table('ketua_kelas')->insert([
                'id_siswa' => $id_siswa,
                'id_kelas' => $getSiswa->id_kelas,
                'id_user' => $new_uid
            ]);
        }
        // lanjut ke transaction
    }

    private function showKetuaKelas($id, $request)
    {
        $validator = $this->showValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKetua = \App\Models\School\Actor\KetuaKelas::query();
        if ($request->_getby == 'siswa') $getKetua = $getKetua->where('id_siswa', $id);
        else $getKetua = $getKetua->where('id_kelas', $id);
        if ($getKetua->count()) return response()->json(dataResponse($getKetua->get()->map->ketuaSimpleInfoMap()[0]), 200);
        return response()->json(errorResponse('Ketua kelas tidak ditemukan'), 202);
    }

    private function updateKetuaKelas($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKetua = \App\Models\School\Actor\KetuaKelas::find($id);
        if ((bool) $getKetua) {
            // cari siswa untuk menjadi ketua kelas baru
            $getSiswa = \App\Models\School\Actor\Siswa::find($request->idSiswa);
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

    private function destroyKetuaKelas($id)
    {
        $getKetua = \App\Models\School\Actor\KetuaKelas::find($id);
        if ((bool) $getKetua) {
            try {
                $response = ['deleted' => ['nama' => $getKetua->siswa->nama, 'kelas' => Cur_getKelasNameByID($getKetua->id_kelas)]];
                $getKetua->user->userbio->delete();
                $getKetua->user->forceDelete();
                $getKetua->delete();
                return response()->json(dataResponse($response, '', 'Berhasil menghapus ketua kelas'), 200);
            } catch (\Exception $e) {
                return response()->json(errorResponse('Gagal menghapus ketua kelas'), 202);
            }
        }
        return response()->json(errorResponse('Ketua kelas tidak ditemukan'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, []);
    }

    private function showValidator($request)
    {
        return Validator($request, [
            '_getby' => 'nullable|string|alpha'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'idSiswa' => 'required|string|numeric'
        ]);
    }
}
