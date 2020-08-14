<?php

namespace App\Http\Controllers\APIs\School\Actor;

use App\Http\Controllers\Controller;

class SiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:kurikulum'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listSiswa(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeSiswa(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showSiswa($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateSiswa($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroySiswa($id, request());
    }

    # private -> move to services
    private function userstat() // move to constructor at services
    {
        return User_getStatus(User_checkStatus());
    }

    private function listSiswa($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getSiswa = \App\Models\School\Actor\Siswa::query();
        if ($request->method == 'all') {
            $getSiswa->where('id_kelas', $request->kelasID)->withTrashed();
        } elseif ($request->method == 'deleted') {
            $getSiswa->where('id_kelas', $request->kelasID)->onlyTrashed();
        } else {
            $kelas = $request->kelasID;
            if ($this->userstat() == 'ketuakelas') $kelas = auth()->user()->ketuakelas->id_kelas;
            $getSiswa->where('id_kelas', $kelas);
        }
        return response()->json(dataResponse($getSiswa->get()->map->siswaSimpleListMap()), 200);
    }

    private function storeSiswa($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        /**
         * insert data siswa berdasarkan file(csv) yang sudah di upload pada tabel csv_inserts(Model:: CsvInsert)
         * request berisikan kode file yang menuju lokasi file yang telah tersedia
         * file(csv) akan diubah menjadi array untuk dikelola
         * lalu insert ke tabel siswas(Model::Siswa)
         * todo: gunakan try catch!!
         */
        return response()->json(successResponse('Berhasil menambahkan data siswa dengan kode file: ' . $request->fileCode), 200);
    }

    private function showSiswa($id)
    {
        $getSiswa = \App\Models\School\Actor\Siswa::withTrashed()->find($id);
        if ((bool) $getSiswa) {
            // jika (saya == ketuakelas dan siswa ada pada kelas saya) atau (saya bukan ketuakelas) maka benar
            if ((($this->userstat() == 'ketuakelas') && ($getSiswa->id_kelas == auth()->user()->ketuakelas->id_kelas)) || ($this->userstat() != 'ketuakelas'))
                return response()->json(dataResponse($getSiswa->siswaSimpleInfoMap()), 200);
        }
        return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
    }

    private function updateSiswa($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getChange = array_filter($request->all());
        if ((bool) $getChange) {
            $getSiswa = \App\Models\School\Actor\Siswa::find($id);
            if ((bool) $getSiswa) {
                try {
                    $getChangeKey = array_keys($getChange); // get key from request
                    $oldData = [];
                    for ($i = 0; $i < count($getChangeKey); $i++) array_push($oldData, $getSiswa[$getChangeKey[$i]]);
                    $getSiswa->update($getChange);
                    if ((bool) $getSiswa->ketuakelas && isset($request->nama)) $getSiswa->ketuakelas->user->userbio->update(['name' => $request->nama]);
                    $response = ['oldData' => array_combine($getChangeKey, $oldData), 'newData' => $getChange];
                    return response()->json(dataResponse($response, '', 'Berhasil memperbarui data siswa'), 200);
                } catch (\Exception $e) {
                    return response()->json(errorResponse('Terdapat kesalahan dalam proses, silahkan coba kembali'), 202);
                }
            }
            return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
        }
        return response()->json(errorResponse('Silahkan sebutkan apa yang ingin diubah'), 202);
    }

    private function destroySiswa($id, $request)
    {
        $validator = $this->softDeleteValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getSiswa = \App\Models\School\Actor\Siswa::where('id', $id);
        if ($request->method == 'force') $getSiswa->withTrashed();
        if ($getSiswa->count()) {
            if ($request->method == 'force') {
                return response()->json(successResponse('Berhasil menghapus siswa secara permanen'), 200);
            } else {
                return response()->json(successResponse('Berhasil menghapus siswa'), 200);
            }
        }
        return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'kelasID' => ['nullable', 'string', 'numeric', \Illuminate\Validation\Rule::requiredIf($this->userstat() != 'ketuakelas')],
            'method' => 'nullable|string|alpha'
        ], [
            'kelasID.required' => 'Kelas ID field is required.'
        ]);
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'fileCode' => 'required|string|alpha_num'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'nisn' => 'nullable|string|numeric|digits_between:10,15',
            'nama' => 'nullable|string|regex:/^[a-zA-Z_,.\s]+$/',
            'id_kelas' => 'nullable|string|numeric'
        ]);
    }

    private function softDeleteValidator($request)
    {
        return Validator($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
