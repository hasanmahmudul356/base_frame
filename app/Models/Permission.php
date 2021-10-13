<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';

    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['exact', 'checked'];

    public function getExactAttribute(){
        return exact_permission($this->permission);
    }
    public function getCheckedAttribute(){
        return false;
    }
}
