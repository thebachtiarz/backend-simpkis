<?php

namespace App\Managements\School\Curriculum;

use App\Models\School\Curriculum\KelasGroup;
use App\Models\School\Curriculum\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KelasManagement
{
    # Public
    public function kelasList($request)
    {
        if (Auth::user()->tokenCan('kelas:get')) {
            $validator = $this->kelasListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if ($request->method == 'getgroup') {
                $getKelasGroup = KelasGroup::query();
                if ($request->groupstatus == 'all') $getKelasGroup = $getKelasGroup->get()->map->kelasgroupSimpleListMap();
                elseif ($request->groupstatus == 'graduated') $getKelasGroup = $getKelasGroup->getGraduatedKelas()->get()->map->kelasgroupSimpleListMap();
                else $getKelasGroup = $getKelasGroup->getActiveKelas()->get()->map->kelasgroupSimpleListMap();
                return response()->json(dataResponse($getKelasGroup), 200);
            }
            if ($request->method == 'searchgroup') {
                $getKelasGroup = KelasGroup::searchKelasGroupByName($request->searchnama, $request->tingkat);
                return response()->json(dataResponse($getKelasGroup->get()->map->kelasgroupSimpleListMap()), 200);
            }
            if ($request->method == 'graduated') {
                $getGraduated = Kelas::getGraduatedKelas();
                return response()->json(dataResponse($getGraduated->get()->map->kelasSimpleListMap()), 200);
            }
            $getKelas = Kelas::getActiveKelas();
            if ($request->method == 'havenoketuakelas') $getKelas = $getKelas->getHaveNoKetuaKelas();
            if ($request->method == 'all') $getKelas = $getKelas->withTrashed();
            if ($request->method == 'deleted') $getKelas = $getKelas->onlyTrashed();
            return response()->json(dataResponse($getKelas->get()->map->kelasSimpleListMap()), 200);
        }
        return _throwErrorResponse();
    }

    public function kelasStore($request)
    {
        if (Auth::user()->tokenCan('kelas:create')) {
            $validator = $this->kelasStoreValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $checkAvailable = Kelas::getAvailableKelas($request->tingkat, $request->nama)->count();
            if (!$checkAvailable) {
                $checkGroupKelas = KelasGroup::getAvailableGroupKelas($request->tingkat, $request->nama);
                if ($checkGroupKelas->count()) {
                    $getKelasGroupId = $checkGroupKelas->get()[0]->id;
                    Kelas::createNewKelas($request, $getKelasGroupId);
                } else {
                    $newKelasGroup = KelasGroup::createNewKelasGroup($request);
                    Kelas::createNewKelas($request, $newKelasGroup->id);
                }
                return response()->json(successResponse('Kelas berhasil dibuat'), 201);
            }
            return response()->json(errorResponse('Kelas sudah ada'), 202);
        }
        return _throwErrorResponse();
    }

    public function kelasShow($id)
    {
        if (Auth::user()->tokenCan('kelas:show')) {
            $getKelas = Kelas::find($id);
            return (bool) $getKelas
                ? response()->json(dataResponse($getKelas->kelasFullInfoMap()), 200)
                : response()->json(errorResponse('Kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function kelasUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('kelas:update')) {
            $validator = $this->kelasUpdateValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if (isset($request->updateTingkat)) {
                $getKelasGroup = KelasGroup::find($id);
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
                        return response()->json(dataResponse($getKelasGroup->kelasgroupSimpleListMap(), '', $message), 201);
                    }
                }
                return response()->json(errorResponse('Group kelas tidak ditemukan'), 202);
            }
            $getKelas = Kelas::find($id);
            if ((bool) $getKelas) {
                $getKelas->update(['nama' => $request->nama]);
                return response()->json(successResponse('Berhasil memperbarui kelas'), 201);
            }
            return response()->json(errorResponse('Kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function kelasDestory($id, $request)
    {
        if (Auth::user()->tokenCan('kelas:delete')) {
            $validator = $this->kelasDestoryValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getKelas = Kelas::find($id);
            if ($getKelas->count()) {
                if ($request->method == 'force') {
                    $getKelas->forceDelete();
                    return response()->json(successResponse('Berhasil menghapus kelas secara permanen'), 200);
                } else {
                    $getKelas->delete();
                    return response()->json(successResponse('Berhasil menghapus kelas'), 200);
                }
            }
            return response()->json(errorResponse('Kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Validator
    private function kelasListValidator($request)
    {
        return Validator::make($request, [
            'method' => 'nullable|string|alpha',
            'groupstatus' => 'nullable|string|required_if:method,getgroup',
            'tingkat' => 'nullable|string|numeric|between:10,12',
            'searchnama' => 'nullable|string|min:3|regex:/^[a-zA-Z0-9_\s]+$/'
        ]);
    }

    private function kelasStoreValidator($request)
    {
        return Validator::make($request, [
            'tingkat' => 'required|string|numeric|between:10,12',
            'nama' => 'required|string|regex:/^[a-z0-9A-Z_\s]+$/'
        ], [
            'tingkat.between' => 'Tingkat kelas yang benar antara 10 - 12'
        ]);
    }

    private function kelasUpdateValidator($request)
    {
        return Validator::make($request, [
            'updateTingkat' => 'nullable|string|alpha',
            'nama' => 'nullable|string|regex:/^[a-z0-9A-Z_\s]+$/|required_without:updateTingkat'
        ]);
    }

    private function kelasDestoryValidator($request)
    {
        return Validator::make($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
