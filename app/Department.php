<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
//    public function childDepartment()
//    {
//        return $this->hasMany('App\Department','pid','id');
//    }
//
//    public function allChildrenDepartments()
//    {
//        return $this->childDepartment()->with('allChildrenDepartments');
//    }

//使用递归获取分类
    public function getCategory($sourceItems, $targetItems, $pid=0){
        foreach ($sourceItems as $k => $v) {
            if($v->pid == $pid){
                $targetItems[] = $v;
                $this->getCategory($sourceItems, $targetItems, $v->id);
            }
        }
    }
//使用递归获取分类信息
    public function getCategoryInfo(){
        $sourceItems = $this->get();
        $targetItems = new Collection();
        $this->getCategory($sourceItems, $targetItems,0);
        return $targetItems;
    }


}
