<?php

namespace App\Http\Controllers\APIs\School\Curriculum;

use App\Http\Controllers\Controller;

class SemesterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:kurikulum']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listSemester(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeNewSemester(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showSemester($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateSemester($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroySemester($id);
    }

    # private -> move to services
    private function listSemester($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getSemester = \App\Models\School\Curriculum\Semester::query();
        if ($request->get == 'now') {
            $getSemester = $getSemester->limit(1)->orderByDesc('id')->get();
        } else {
            $getSemester = $getSemester->get();
        }
        return response()->json(dataResponse($getSemester->map->semesterSimpleListMap()), 200);
    }

    private function storeNewSemester($request)
    {
        $validator = $this->semesterValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $tahun = (isset($request->tahun)) ? $request->tahun : date('Y');
        $semester = (isset($request->semester)) ? Cur_convSemesterByCode($request->semester) : Cur_getSemesterNow();
        $getSemester = \App\Models\School\Curriculum\Semester::getAvailableSemester($tahun, $semester);
        if (!$getSemester->count()) {
            \App\Models\School\Curriculum\Semester::create(['semester' => "$tahun/$semester"]);
            return response()->json(dataResponse(['new_semester' => "$tahun/$semester"], '', 'Berhasil menambahkan semester baru'), 201);
        }
        return response()->json(errorResponse('Semester sudah ada'), 202);
    }

    private function showSemester($id)
    {
        $getSemester = \App\Models\School\Curriculum\Semester::find($id);
        if ((bool) $getSemester) {
            return response()->json(dataResponse($getSemester->semesterSimpleInfoMap()), 200);
        }
        return response()->json(errorResponse('Semester tidak ditemukan'), 202);
    }

    private function updateSemester($id, $request)
    {
        $validator = $this->semesterValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getSemester = \App\Models\School\Curriculum\Semester::find($id);
        if ((bool) $getSemester) {
            $tahun = (isset($request->tahun)) ? $request->tahun : date('Y');
            $semester = (isset($request->semester)) ? Cur_convSemesterByCode($request->semester) : Cur_getSemesterNow();
            $oldSemester = $getSemester->semester;
            $newSemester = "{$tahun}/{$semester}";
            $getSemester->update(['semester' => $newSemester]);
            return response()->json(dataResponse(['old' => $oldSemester, 'new' => $newSemester], '', 'Berhasil memperbarui semester'), 201);
        }
        return response()->json(errorResponse('Semester tidak ditemukan'), 202);
    }

    private function destroySemester($id)
    {
        $getSemester = \App\Models\School\Curriculum\Semester::find($id);
        if ((bool) $getSemester) {
            $getSemester->delete();
            return response()->json(successResponse('Berhasil menghapus semester'), 201);
        }
        return response()->json(errorResponse('Semester tidak ditemukan'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'get' => 'nullable|string|alpha'
        ]);
    }

    private function semesterValidator($request)
    {
        return Validator($request, [
            'tahun' => ['nullable', 'string', 'numeric', 'digits_between:4,4', 'required_with:semester,tahun'],
            'semester' => ['nullable', 'string', 'numeric', 'between:1,2', 'required_with:tahun,semester']
        ], [
            'tahun.digits_between' => 'Isikan tahun yang benar (4 digit)',
            'semester.between' => 'Kode semester yang benar (1 => Ganjil, 2 => Genap)'
        ]);
    }
}
