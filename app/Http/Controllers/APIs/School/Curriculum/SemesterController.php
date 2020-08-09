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
        return $this->listSemester();
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
        //
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
    private function listSemester()
    {
        //
    }

    private function storeNewSemester($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function updateSemester($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function destroySemester($id)
    {
        //
    }

    private function storeValidator($request)
    {
        return Validator($request, []);
    }

    private function updateValidator($request)
    {
        return Validator($request, []);
    }
}
