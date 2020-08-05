<?php

namespace App\Http\Controllers\APIs\School\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\School\Curriculum\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(dataResponse(Kelas::all()->map->kelasSimpleListMap()), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = $this->storeValidator(request()->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        return $this->storeNewKelas(request());
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $getKelas = Kelas::where('id', $id);
        return $getKelas->count()
            ? response()->json(dataResponse($getKelas->get()->map->kelasFullInfoMap(), 200))
            : response()->json(errorResponse('Kelas tidak ditemukan', 202));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\School\Curriculum\Kelas  $kelas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kelas $kelas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\School\Curriculum\Kelas  $kelas
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kelas $kelas)
    {
        //
    }

    # private -> move to services
    private function storeNewKelas($kelas)
    {
        $checkAvailabel = Kelas::getAvailableKelas($kelas->tingkat, $kelas->nama)->count();
        if (!$checkAvailabel) {
            return response()->json(successResponse('Kelas berhasil dibuat'), 201);
        }
        return response()->json(errorResponse('Kelas sudah ada'), 202);
    }

    private function storeValidator($request)
    {
        return validator($request, [
            'tingkat' => 'required|string|numeric|min:10|max:12',
            'nama' => 'required|string|regex:/^[a-z0-9A-Z_\s]+$/'
        ], [
            'tingkat.min' => 'Tingkat kelas yang benar antara 10 - 12',
            'tingkat.max' => 'Tingkat kelas yang benar antara 10 - 12'
        ]);
        // return preg_replace("/[^A-Za-z?![:space:]]/", '', 'Teknik Komputer Jaringan 2');
    }
}
