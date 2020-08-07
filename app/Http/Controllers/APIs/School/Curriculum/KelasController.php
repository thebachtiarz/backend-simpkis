<?php

namespace App\Http\Controllers\APIs\School\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\School\Curriculum\Kelas;

class KelasController extends Controller
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
        return $this->listKelas(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeNewKelas(request());
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showKelas($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateKelas($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyKelas($id, request());
    }

    # private -> move to services
    public function listKelas($request)
    {
        $validator = $this->softDeleteValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKelas = Kelas::query();
        if ($request->method == 'all') {
            return response()->json(dataResponse($getKelas->withTrashed()->get()->map->kelasSimpleListMap()), 200);
        } elseif ($request->method == 'deleted') {
            return response()->json(dataResponse($getKelas->onlyTrashed()->get()->map->kelasSimpleListMap()), 200);
        }
        return response()->json(dataResponse($getKelas->get()->map->kelasSimpleListMap()), 200);
    }

    private function storeNewKelas($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $checkAvailabel = Kelas::getAvailableKelas($request->tingkat, $request->nama)->count();
        if (!$checkAvailabel) {
            return response()->json(successResponse('Kelas berhasil dibuat'), 201);
        }
        return response()->json(errorResponse('Kelas sudah ada'), 202);
        // return preg_replace("/[^A-Za-z?![:space:]]/", '', 'Teknik Komputer Jaringan 2'); // for checking group kelas
    }

    private function showKelas($id)
    {
        $getKelas = Kelas::withTrashed()->find($id);
        return (bool) $getKelas
            ? response()->json(dataResponse($getKelas->kelasFullInfoMap()), 200)
            : response()->json(errorResponse('Kelas tidak ditemukan'), 202);
    }

    private function updateKelas($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKelas = Kelas::find($id);
        if ((bool) $getKelas) {
            $tingkatNow = $getKelas->kelasgroup->tingkat;
            if (($request->updateTingkat == 'naik') && ($tingkatNow < '12')) {
                $response = [
                    'sebelumnya' => "{$tingkatNow} - {$getKelas->nama}",
                    'sekarang' => strval(intval($tingkatNow + 1)) . " - {$getKelas->nama}"
                ];
                return response()->json(dataResponse($response, '', 'Berhasil menaikkan kelas'), 200);
            } elseif (($request->updateTingkat == 'lulus') && ($tingkatNow >= '12')) {
                $statLulus = '(L-' . Carbon_AnyDateParse(Carbon_DBtimeToday()) . ')';
                $response = [
                    'sebelumnya' => "{$tingkatNow} - {$getKelas->nama}",
                    'sekarang' => "{$statLulus} - {$getKelas->nama}"
                ];
                return response()->json(dataResponse($response, '', 'Berhasil meluluskan kelas'), 200);
            }
        }
        return response()->json(errorResponse('Kelas tidak ditemukan'), 202);
    }

    private function destroyKelas($id, $request)
    {
        $validator = $this->softDeleteValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKelas = Kelas::where('id', $id);
        if ($request->method == 'force') $getKelas->onlyTrashed();
        if ($getKelas->count()) {
            if ($request->method == 'force') {
                return response()->json(successResponse('Berhasil menghapus kelas secara permanen'), 200);
            } else {
                return response()->json(successResponse('Berhasil menghapus kelas'), 200);
            }
        }
        return response()->json(errorResponse('Kelas tidak ditemukan'), 202);
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'tingkat' => 'required|string|numeric|min:10|max:12',
            'nama' => 'required|string|regex:/^[a-z0-9A-Z_\s]+$/'
        ], [
            'tingkat.min' => 'Tingkat kelas yang benar antara 10 - 12',
            'tingkat.max' => 'Tingkat kelas yang benar antara 10 - 12'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'updateTingkat' => 'required|string|alpha'
        ]);
    }

    private function softDeleteValidator($request)
    {
        return Validator($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
