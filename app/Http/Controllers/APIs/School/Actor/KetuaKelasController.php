<?php

namespace App\Http\Controllers\APIs\School\Actor;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator;

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
        return $this->destroyKetuaKelas($id, request());
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
        if ($request->_getby == 'siswa') {
            $getKetua = $getKetua->where('id_siswa', $id);
        } else {
            $getKetua = $getKetua->where('id_kelas', $id);
        }
        if ($getKetua->count()) {
            return response()->json(dataResponse($getKetua->get()->map->ketuaSimpleInfoMap()), 200);
        }
        return response()->json(errorResponse('Ketua kelas tidak ditemukan'), 202);
    }

    private function updateKetuaKelas($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKetua = \App\Models\School\Actor\KetuaKelas::where('id_kelas', $id);
        if ($getKetua->count()) {
            $getSiswa = \App\Models\School\Actor\Siswa::find($request->idSiswa);
            // jika siswa yang dicari ada dan siswa juga dari kelas tersebut maka benar
            if (((bool) $getSiswa) && ($getKetua->get()[0]->id_kelas == $getSiswa->id_kelas)) {
                $result = [
                    'old_ketua' => $getKetua->get()[0]->siswa->nama,
                    'new_ketua' => $getSiswa->nama
                ];
                // ubah data(id_siswa) pada ketua_kelas(Model:: KetuaKelas) dengan data input($request->id_siswa) berdasarkan(id_kelas)
                // ubah data(name) pada user_biodatas(Model:: UserBiodata)dengan data yang diambil dari (Model:: Siswa[nama])
                // ubah data(username, password) pada users(Model:: User) dengan data(Model:: Siswa[nisn]) dengan rincian:
                // :::: [username => ('u'.nisn), password => ('p'.nisn)]
                return response()->json(dataResponse($result, '', 'Berhasil mengubah ketua kelas'), 200);
            }
            return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
        }
        return response()->json(errorResponse('Ketua kelas tidak ditemukan'), 202);
    }

    private function destroyKetuaKelas($id, $request)
    {
        $validator = $this->destroyValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKetua = \App\Models\School\Actor\KetuaKelas::where('id_kelas', $id);
        if ($getKetua->count()) {
            $dataKetua = $getKetua->get()[0];
            $response = ['deleted' => ['nama' => $dataKetua->siswa->nama, 'kelas' => Cur_getKelasNameByID($dataKetua->id_kelas)]];
            /**
             * hapus data pada users(Model:: User) berdasarkan data pada(Model:: KetuaKelas[id_user])
             * hapus data pada ketua_kelas(Model:: KetuaKelas) berdasarkan input($id)
             * todo: gunakan try catch!!
             */
            return response()->json(dataResponse($response, '', 'Berhasil menghapus ketua kelas'), 200);
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

    private function destroyValidator($request)
    {
        return Validator($request, []);
    }
}
