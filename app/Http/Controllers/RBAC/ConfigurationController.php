<?php

namespace App\Http\Controllers\RBAC;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    use Helper;

    public function getConfigurations()
    {
        $role_modules = RoleModule::where('role_id', auth::user()->role_id)->get()->pluck('module_id')->toArray();
        $all_modules = Module::whereIn('id', $role_modules)
            ->where('parent_id', 0)
            ->with(['role_module'=>function($query){
                $query->where('role_id', auth::user()->role_id);
                $query->with('permissions');
            }])
            ->with(['submenu'])
            ->get();

        $all_permissions = Permission::all()->keyBy('id')->toArray();

        $modules = [];

        foreach ($all_modules as $key => $module) {
            $modules[$key]['name'] = $module->name;
            $modules[$key]['display_name'] = $module->display_name;

            if (isset($module->role_module->permissions) &&
                count($module->role_module->permissions) > 0) {

                $modules_permissions = collect($module->role_module->permissions)->pluck('permission_id')->toArray();
                foreach ($modules_permissions as $permission) {
                    $eachPermission = $all_permissions[$permission];
                    $permission_name = $eachPermission['permission'];
                    $permissions[$permission_name] = $eachPermission['id'];
                }
                $modules[$key]['permissions'] = $permissions;
                $permissions = [];
            } else {
                $modules[$key]['permissions'] = [];
            }

            $submenus = [];
            if (count($module->submenu) > 0) {
                $modules[$key]['link'] = '#';
                foreach ($module->submenu as $subkey => $submenu) {
                    $submenus[$subkey]['name'] = $submenu->name;
                    $submenus[$subkey]['display_name'] = $submenu->display_name;
                    $submenus[$subkey]['link'] = $submenu->link;

                    if (isset($submenu->role_module->permissions) &&
                        count($submenu->role_module->permissions) > 0) {
                        $modules_permissions = collect($submenu->role_module->permissions)->pluck('permission_id')->toArray();
                        foreach ($modules_permissions as $permission) {
                            $eachPermission = $all_permissions[$permission];
                            $permission_name = $eachPermission['permission'];
                            $permissions[$permission_name] = $eachPermission['id'];
                        }
                        $submenus[$subkey]['permissions'] = $permissions;
                        $permissions = [];
                    } else {
                        $submenus[$subkey]['permissions'] = [];
                    }
                }
            } else {
                $modules[$key]['link'] = $module->link;
            }
            $modules[$key]['submenus'] = $submenus;
        }

        $data['menus'] = $modules;
        $data['user'] = Auth::user();
        $data['config'] = configs();

        return response()->json($this->returnData(2000, $data));
    }

    public function getGeneralData(Request $request)
    {
        $data = [];
        $input = $request->all();
        if (in_array('role', $input)) {
            $data['role'] = Role::where('status', 1)->get();
        }

        return response()->json($this->returnData(2000, $data));
    }
}
