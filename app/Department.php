<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Department extends Model
{
    use NodeTrait;
    protected $fillable = ['name','order'];

    public function users()
    {
        return $this->belongsToMany(User::class,'department_user')->withTimestamps();
    }
}
