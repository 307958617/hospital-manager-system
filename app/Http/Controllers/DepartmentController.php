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
        $departments = Department::orderBy('order','ASC')->get()->toTree();
//        Department::rebuildTree($tree,$delete);  //这为什么不起作用呢，没办法啊。
        return response()->json(['data'=>$departments]);
    }

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
