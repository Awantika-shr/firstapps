<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get()
    {
        return Student::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add(Request $request)
    {
        $student=Student::create($request->all());
        return response()->json([
            'message'=>'Student added',
            'data'=>$student
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $student->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'course'=>$request->course,
        ]);

        return response()->json([
        "message"=>"Student updated",
        "data"=>$student
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            "message"=>"Student deleted"
        ]);
    }
}
