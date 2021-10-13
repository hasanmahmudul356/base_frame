<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'display_name' => 'RBAC',
                'name' => 'rbac',
                'link' => '/admin/rbac',
                'permission' => ['view', 'add', 'update', 'delete']
            ],

            [
                'display_name' => 'Roles',
                'name' => 'role',
                'link' => '/admin/role',
                'permission' => ['view', 'add', 'update', 'delete']
            ],
            [
                'display_name' => 'Modules',
                'name' => 'module',
                'link' => '/admin/module',
                'permission' => ['view', 'add', 'update', 'delete']
            ],
            [
                'display_name' => 'Permissions',
                'name' => 'role_module',
                'link' => '/admin/role_module',
                'permission' => ['view', 'add', 'update', 'delete']
            ],
            [
                'display_name' => 'Configurations',
                'name' => 'configuration',
                'link' => '/admin/configuration',
                'permission' => ['view', 'add', 'update', 'delete']
            ],
        ];
        foreach ($permissions as $key => $permission) {
            $module_exist = Module::where('name', $permission['name'])->first();
            if (!$module_exist) {
                $module = new Module();
                $module->display_name = $permission['display_name'];
                $module->name = $permission['name'];
                $module->link = $permission['link'];
                $module->save();

                foreach ($permission['permission'] as $each) {
                    $permission = new Permission();
                    $permission->module_id = $module->id;
                    $permission->permission = $module->name.'_'.$each;
                    $permission->save();
                }
            }else{
                foreach ($permission['permission'] as $each) {
                    $permission_name = $module_exist->name.'_'.$each;
                    $exist_permission = Permission::where('permission', $permission_name)->first();
                    if (!$exist_permission){
                        $permission = new Permission();
                        $permission->module_id = $module_exist->id;
                        $permission->permission = $permission_name;
                        $permission->save();
                    }
                }
            }
        }
    }
}
