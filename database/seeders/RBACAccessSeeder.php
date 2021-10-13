<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleModule;
use App\Models\RoleModulePermission;
use Illuminate\Database\Seeder;

class RBACAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'Admin')->first();

        if (!$role) {
            $role = new Role();
            $role->name = 'Admin';
            $role->save();

            $modules = Module::all();

            foreach ($modules as $module) {
                $role_module = new RoleModule();
                $role_module->role_id = $role->id;
                $role_module->module_id = $module->id;
                $role_module->save();

                $permissions = Permission::where('module_id', $module->id)->get();

                foreach ($permissions as $permission) {
                    $role_module_permission = new RoleModulePermission();
                    $role_module_permission->role_module_id = $role_module->id;
                    $role_module_permission->permission_id = $permission->id;
                    $role_module_permission->save();
                }
            }
        }

    }
}
