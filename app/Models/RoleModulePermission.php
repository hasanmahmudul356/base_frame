<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModulePermission extends Model
{
    use HasFactory;

    protected $table = 'role_module_permissions';

    protected $hidden = ['created_at', 'updated_at'];
}
