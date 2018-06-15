<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function show()
    {
//        $departments = Department::with('allChildrenDepartments')->get();
//
//
//
//
//        return view('department',compact('departments'));

        $category = new Department;
        $items = $category->getCategoryInfo();

        return view('department',compact('items'));
//        foreach ($items as $key => $item) {
//            dump($item->name);
//        }
    }
}
