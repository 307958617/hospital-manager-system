<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show_users()
    {
        return view('department/users');
    }

    public function get()
    {
        $users = User::with('departments')->select(['id','name','email','created_at','password'])->get();
        return response()->json(['data'=> $users]);
    }

    public function add(Request $request)
    {
        $departmentId = $request->get('departmentId');
        $userName = $request->get('userName');
        $userEmail = $request->get('userEmail');
        $userPassword = $request->get('userPassword');

        $user = User::firstOrCreate(['name'=>$userName,'email'=>$userEmail,'password'=>Hash::make($userPassword)]);

        if($departmentId) {
            $user->departments()->attach($departmentId);
        }

        $data = User::with('departments')->find($user->id);


        return $data;
    }

    public function edit(Request $request)
    {
        $id = $request->get('id');
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');

        $user = User::find($id);
        $user->name = $name;
        $user->email = $email;

        if($password) {
            $user->password = Hash::make($password);
        }

        $user->save();
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        $node = User::find($id);
        $node->departments()->detach();
        $node->delete();
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->get('ids');
        foreach ($ids as $id) {
            $dep = User::find($id);
            $dep->departments()->detach();
        }
        User::destroy($ids);
    }

    public function checkPassword(Request $request)
    {
        $id = $request->get('id');
        $password = $request->get('password');
        $user = User::find($id);

        if(Hash::check($password,$user->password)) {
            return 'ok';
        }
        return 'notOk';
    }
}
