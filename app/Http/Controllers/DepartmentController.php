<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function show()
    {
        $departments = Department::all();
        $deps = $this->getTree($departments,'id','pid');
        $datas = $this->getMenuList($deps,'name','id','pid');
        return view('department',compact('datas'));
    }

    /**
     * getTree无限递归函数
     * @param $data
     * @param $field_id
     * @param $field_pid
     * @param int $pid
     * @return array
     * @fillable 变量，保存子集数据
     */
    public function getTree($data, $field_id, $field_pid, $pid = 0) {
        $arr = array();
        foreach ($data as $k=>$v) {
            if ($v->$field_pid == $pid) {
                $arr[$k] = $v;
                $arr[$k]['fillable'] = self::getTree($data, $field_id, $field_pid, $v->$field_id );
            }
        }
        return $arr;
    }

    /**
     * 分类列表视图
     * @param $menus
     * @param $name
     * @param $id
     * @param $pid
     * @return string
     */
    public function getMenuList($menus, $name, $id, $pid)
    {
        if ($menus) {
            $item = '';
            foreach ($menus as $v) {
                $item .= $this->getNestableItem($v, $name, $id, $pid);
            }
            return $item;
        }
        return '暂无菜单';
    }

    /**
     * 返回HTML代码
     * @param $menu
     * @param $name
     * @param $id
     * @param $pid
     * @return string
     */
    protected function getNestableItem($menu, $name, $id, $pid)
    {
        if ($menu['fillable']) {
            return $this->getHandleList($menu[$id], $menu[$name], $menu['fillable']);
        }
        if ($menu[$pid] == 0) {
            return '
                    <li class="dd-item" data-id="'.$menu[$id].'">
                        <div class="dd-handle"> '.$menu[$name].' </div>
                    </li>';
        }
        return '
                <li class="dd-item" data-id="'.$menu[$id].'">
                    <div class="dd-handle"> '.$menu[$name].' </div>
                </li>';
    }

    /**
     * 判断是否存在子集
     * @param $id		数据库的ID字段
     * @param $name		数据库的name字段
     * @param $pid		数据库的pid字段
     * @param $fillable
     * @return string
     */
    protected function getHandleList($id, $name, $fillable)
    {
        $handle = '
                    <li class="dd-item" data-id="'.$id.'">
                        <div class="dd-handle"> '.$name.' </div>
                        <ol class="dd-list">';
        if ($fillable) {
            foreach ($fillable as $v) {
                $handle .= $this->getNestableItem($v, 'name', 'id', 'pid');
            }
        }
        $handle .= '</ol></li>';
        return $handle;
    }
}
