<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function show()
    {
        $data = json_decode('[{"id":13,"children":[{"id":14,"children":[{"id":15}]}]},{"id":16},{"id":17}]');
//        return gettype($serialize);
        $users = array_map('get_object_vars', $data);

//       return ($users);

        return view('department',['us'=>$users]);
    }
}
