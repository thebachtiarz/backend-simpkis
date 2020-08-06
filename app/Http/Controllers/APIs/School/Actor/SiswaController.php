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
            if (auth()->user()->userstat->status == User_setStatus('ketuakelas')) $kelas = auth()->user()->ketuakelas->id_kelas;
            $getSiswa->where('id_kelas', $kelas);
        }
        return response()->json(dataResponse($getSiswa->get()->map->siswaSimpleListMap()), 200);
    }

    private function storeSiswa($request)
    {
        /**
         * insert data siswa berdasarkan file(csv) yang sudah di upload pada tabel csv_inserts(Model:: CsvInsert)
         * request berisikan kode file yang menuju lokasi file yang telah tersedia
         * file(csv) akan diubah menjadi array untuk dikelola
         * lalu insert ke tabel siswas(Model::Siswa)
         */
    }

    private function showSiswa($id)
    {
        $getSiswa = \App\Models\School\Actor\Siswa::withTrashed()->find($id);
        if ((bool) $getSiswa) {
            $getMe = auth()->user();
            $getMyStatus = User_getStatus($getMe->userstat->status);
            // jika (saya == ketuakelas dan siswa ada pada kelas saya) atau (saya bukan ketuakelas) maka benar
            if ((($getMyStatus == 'ketuakelas') && ($getSiswa->id_kelas == $getMe->ketuakelas->id_kelas)) || ($getMyStatus != 'ketuakelas'))
                return response()->json(dataResponse($getSiswa->siswaSimpleInfoMap()), 200);
        }
        return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
    }

    private function updateSiswa($id, $request)
    {
        //
    }

    private function destroySiswa($id, $request)
    {
        //
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'kelasID' => ['nullable', 'string', 'numeric', \Illuminate\Validation\Rule::requiredIf(auth()->user()->userstat->status != User_setStatus('ketuakelas'))],
            'method' => 'nullable|string|alpha'
        ], [
            'kelasID.required' => 'Kelas ID field is required.'
        ]);
    }

    private function storeValidator($request)
    {
        //
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'nisn' => 'nullable|string|numeric',
            'nama' => 'nullable|string|regex:/^[a-zA-Z_,.\s]+$/',
            'id_kelas' => 'nullable|string|numeric'
        ]);
    }
}
