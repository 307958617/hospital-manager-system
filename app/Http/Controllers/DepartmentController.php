<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function show()
    {
        return view('department');
    }

    public function get()
    {
        $departments = Department::all()->toTree();
        return response()->json(['data'=>$departments]);
    }
}
