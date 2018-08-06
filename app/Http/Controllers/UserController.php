<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show_users()
    {
        return view('department/users');
    }

    public function get()
    {
        $users = User::with('departments')->select(['id','name','email','created_at'])->get();
        return response()->json(['data'=> $users]);
    }
}
