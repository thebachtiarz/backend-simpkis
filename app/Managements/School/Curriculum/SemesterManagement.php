<?php

namespace App\Managements\School\Curriculum;

use App\Models\School\Curriculum\Semester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SemesterManagement
{
    public function __construct()
    {
        //
    }

    # Public
    public function semesterList($request)
    {
        if (Auth::user()->tokenCan('semester:get')) {
            $validator = $this->semesterListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getSemester = Semester::query();
            if ($request->get == 'now') $getSemester = $getSemester->getSemesterActiveNow();
            return response()->json(dataResponse($getSemester->get()->map->semesterSimpleListMap()), 200);
        }
        return _throwErrorResponse();
    }

    public function semesterStore($request)
    {
        if (Auth::user()->tokenCan('semester:create')) {
            $validator = $this->semesterValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $newSemester = Cur_setNewSemesterFormat($request->tahun, $request->semester);
            $getSemester = Semester::getAvailableSemester($newSemester);
            if (!$getSemester->count()) {
                Semester::createNewSemester($newSemester);
                return response()->json(dataResponse(['new_semester' => $newSemester], '', 'Berhasil menambahkan semester baru'), 201);
            }
            return response()->json(errorResponse('Semester sudah ada'), 202);
        }
        return _throwErrorResponse();
    }

    public function semesterShow($id)
    {
        if (Auth::user()->tokenCan('semester:show')) {
            $getSemester = Semester::find($id);
            if ((bool) $getSemester) return response()->json(dataResponse($getSemester->semesterSimpleInfoMap()), 200);
            return response()->json(errorResponse('Semester tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function semesterUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('semester:update')) {
            $validator = $this->semesterValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getSemester = Semester::find($id);
            if ((bool) $getSemester) {
                $oldSemester = $getSemester->semester;
                $newSemester = Cur_setNewSemesterFormat($request->tahun, $request->semester);
                Semester::updateSemester($id, $newSemester);
                return response()->json(dataResponse(['old' => $oldSemester, 'new' => $newSemester], '', 'Berhasil memperbarui semester'), 201);
            }
            return response()->json(errorResponse('Semester tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function semesterDestory($id)
    {
        if (Auth::user()->tokenCan('semester:delete')) {
            $getSemester = Semester::find($id);
            if ((bool) $getSemester) {
                $getSemester->delete();
                return response()->json(successResponse('Berhasil menghapus semester'), 201);
            }
            return response()->json(errorResponse('Semester tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Validator
    private function semesterListValidator($request)
    {
        return Validator::make($request, [
            'get' => 'nullable|string|alpha'
        ]);
    }

    private function semesterValidator($request)
    {
        return Validator::make($request, [
            'tahun' => 'nullable|numeric|digits_between:4,4|between:1970,2070|required_with:semester,tahun',
            'semester' => 'nullable|numeric|between:1,2|required_with:tahun,semester'
        ], [
            'tahun.digits_between' => 'Isikan tahun yang benar (4 digit)',
            'tahun.between' => 'Tahun hanya dari 1970 - 2070',
            'semester.between' => 'Kode semester yang benar (1 => Ganjil, 2 => Genap)'
        ]);
    }
}
