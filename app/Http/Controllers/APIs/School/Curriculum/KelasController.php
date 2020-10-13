<?php

namespace App\Http\Controllers\APIs\School\Curriculum;

use App\Http\Controllers\Controller;

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
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if ($request->method == 'searchgroup') {
            $getKelasGroup = \App\Models\School\Curriculum\KelasGroup::searchKelasGroupByName($request->searchnama, $request->tingkat);
            return response()->json(dataResponse($getKelasGroup->get()->map->kelasgroupSimpleListMap()), 200);
        }
        if ($request->method == 'graduated') {
            $getGraduated = \App\Models\School\Curriculum\Kelas::getGraduatedKelas();
            return response()->json(dataResponse($getGraduated->get()->map->kelasSimpleListMap()), 200);
        }
        $getKelas = \App\Models\School\Curriculum\Kelas::getActiveKelas();
        if ($request->method == 'havenoketuakelas') {
            $getKelas = $getKelas->whereNotIn('id', function ($q) {
                $q->select('id_kelas')->from('ketua_kelas');
            });
        }
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
        $checkAvailable = \App\Models\School\Curriculum\Kelas::getAvailableKelas($request->tingkat, $request->nama)->count();
        if (!$checkAvailable) {
            $checkGroupKelas = \App\Models\School\Curriculum\KelasGroup::getAvailableGroupKelas($request->tingkat, $request->nama);
            if ($checkGroupKelas->count()) {
                $getKelasGroupId = $checkGroupKelas->get()[0]->id;
                \App\Models\School\Curriculum\Kelas::create(['nama' => $request->nama, 'id_group' => $getKelasGroupId, 'status' => Cur_setKelasStatus('active')]);
            } else {
                $newKelasGroup = \App\Models\School\Curriculum\KelasGroup::create(['tingkat' => $request->tingkat, 'nama_group' => Str_pregStringOnly($request->nama)]);
                \App\Models\School\Curriculum\Kelas::create(['nama' => $request->nama, 'id_group' => $newKelasGroup->id, 'status' => Cur_setKelasStatus('active')]);
            }
            return response()->json(successResponse('Kelas berhasil dibuat'), 201);
        }
        return response()->json(errorResponse('Kelas sudah ada'), 202);
    }

    private function showKelas($id)
    {
        $getKelas = \App\Models\School\Curriculum\Kelas::withTrashed()->find($id);
        return (bool) $getKelas
            ? response()->json(dataResponse($getKelas->kelasFullInfoMap()), 200)
            : response()->json(errorResponse('Kelas tidak ditemukan'), 202);
    }

    private function updateKelas($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (isset($request->updateTingkat)) {
            $getKelasGroup = \App\Models\School\Curriculum\KelasGroup::find($id);
            if ((bool) $getKelasGroup) {
                $tingkatNow = $getKelasGroup->tingkat;
                $tingkatNew = '';
                $message = 'Berhasil :status kelas';
                if (($request->updateTingkat == 'naik') && ($tingkatNow < '12')) {
                    $tingkatNew = strval(intval($tingkatNow + 1));
                    $message = preg_replace_array('/:[a-z]+/', ['menaikkan'], $message);
                } elseif (($request->updateTingkat == 'naik') && ($tingkatNow >= '12')) {
                    $tingkatNew = Cur_formatKelasLulus();
                    $getKelasGroup->update(['status' => Cur_setKelasStatus('graduated')]);
                    $message = preg_replace_array('/:[a-z]+/', ['meluluskan'], $message);
                }
                if (isset($tingkatNew)) {
                    $getKelasGroup->update(['tingkat' => $tingkatNew]);
                    return response()->json(successResponse($message), 201);
                }
            }
            return response()->json(errorResponse('Group kelas tidak ditemukan'), 202);
        }
        $getKelas = \App\Models\School\Curriculum\Kelas::find($id);
        if ((bool) $getKelas) {
            $getKelas->update(['nama' => $request->nama]);
            return response()->json(successResponse('Berhasil memperbarui kelas'), 201);
        }
        return response()->json(errorResponse('Kelas tidak ditemukan'), 202);
    }

    private function destroyKelas($id, $request)
    {
        $validator = $this->softDeleteValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKelas = \App\Models\School\Curriculum\Kelas::where('id', $id);
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

    private function listValidator($request)
    {
        return Validator($request, [
            'method' => 'nullable|string|alpha',
            'tingkat' => 'nullable|string|numeric|between:10,12',
            'searchnama' => 'nullable|string|min:3|regex:/^[a-zA-Z_\s]+$/'
        ]);
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'tingkat' => 'required|string|numeric|between:10,12',
            'nama' => 'required|string|regex:/^[a-z0-9A-Z_\s]+$/'
        ], [
            'tingkat.between' => 'Tingkat kelas yang benar antara 10 - 12'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, [
            'updateTingkat' => 'nullable|string|alpha',
            'nama' => 'nullable|string|regex:/^[a-z0-9A-Z_\s]+$/|required_without:updateTingkat'
        ]);
    }

    private function softDeleteValidator($request)
    {
        return Validator($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
