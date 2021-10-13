<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';

    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['checked'];

    protected $fillable = ['name', 'display_name', 'parent_id','link'];

    public function permissions(){
        return $this->hasMany(Permission::class, 'module_id', 'id');
    }

    public function submenu(){
        return $this->hasMany(Module::class, 'parent_id', 'id')->with('role_module');
    }

    public function role_module(){
        return $this->belongsTo(RoleModule::class, 'id', 'module_id');
    }

    public function getCheckedAttribute(){
        return false;
    }
}
