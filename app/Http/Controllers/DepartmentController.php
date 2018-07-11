<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    //显示界面
    public function show_org()
    {
        return view('department/organization');
    }

    public function get_org()
    {
        $departments = Department::select(['id','name','created_at'])->get();
        return response()->json(['data'=> $departments]);
    }

    public function show_departments()
    {
        return view('department/departments');
    }

    public function show_users()
    {
        return view('department/users');
    }
    //新增科室保存到数据库
    public function add(Request $request)
    {
        $pid = $request->get('pid');
        $name = $request->get('name');

        $node = new Department(['name'=>$name,'order'=>0]);
        if($pid) {
            $node->parent_id = $pid;
            $node->save();
        }
        $node->save();
    }
    //编辑科室然后保存到数据库
    public function edit(Request $request)
    {
        $pid = $request->get('pid');
        $name = $request->get('name');
        $id = $request->get('id');

        $node = Department::find($id);

        $node->parent_id = $pid;
        $node->name = $name;
        $node->save();
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        $node = Department::find($id);
        $node->delete();
    }

    //将分类以树结构显示出来
    public function get()
    {
        $departments = Department::orderBy('order','ASC')->get()->toTree();
//        Department::rebuildTree($tree,$delete);  //这为什么不起作用呢，没办法啊。
        return response()->json(['data'=>$departments]);
    }

    //将修改后的分类信息保存到数据库
    public function change(Request $request)
    {
        $newTree = $request->get('tree');
//        $arr = $this->getJsonData($newTree);
        $arrs = $this->getJsonData($newTree);
        foreach ($arrs as $arr) {
            $d = Department::find($arr['id']);
            $d->parent_id = $arr['parent_id'];
            $d->order = $arr['order'];
            $d->save();
        }
        Department::fixTree();
        return response()->json(['data'=>$newTree]);
    }

    //将树结构打散，将每个节点的id与parent_id与order组成组成一个数组并放到一个list数组里面。
    private function getJsonData($tree) {

//        $output = json_decode($jsonData,true);
        $list = array();
        $i = 0;
        foreach ($tree as $row) {
            $i++;
            $list[] = array(
                'id' => $row['id'],
                'parent_id' => null,
                'order' => $i
            );
            if (isset($row['children'])) {
                $this->getChildren($list, $row['id'], $row['children']);
            }
        }
        return $list;
    }

    private function getChildren(&$list, $id, $child) {
        $j = 0;
        foreach ($child as $c) {
            $j++;
            $list[] = array(
                'id' => $c['id'],
                'parent_id' => $id,
                'order' => $j
            );
            if (isset($c['children']) ) {
                $this->getChildren($list, $c['id'], $c['children']);
            }
        }
        return ;
    }
}
