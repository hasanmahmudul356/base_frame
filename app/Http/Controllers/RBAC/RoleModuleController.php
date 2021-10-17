<?php

namespace App\Http\Controllers\RBAC;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleModule;
use App\Models\RoleModulePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RoleModuleController extends Controller
{
    use Helper;

    public function index(Request $request)
    {
        $role_modules = RoleModule::where('role_id', $request->role_id)->get()->pluck('module_id')->toArray();
        $all_modules = Module::with('permissions')->whereNotIn('id', $role_modules)->get();

        $permitted_modules = Module::whereIn('id', $role_modules)
            ->with(['role_module'=>function($query) use ($request){
                $query->where('role_id', $request->role_id)
                ->with('permissions');
            }])
            ->get();

        $all_permissions = Permission::get();
        $permission = collect($all_permissions)->keyBy('id')->toArray();

        $modules = [];

        foreach ($permitted_modules as $key => $module) {
            $modules[$key]['id'] = $module->id;
            $modules[$key]['role_id'] = $request->role_id;
            $modules[$key]['name'] = $module->name;
            $modules[$key]['display_name'] = $module->display_name;

            $all_module_permission = collect($all_permissions)->where('module_id', $module->id)->toArray();

            $permissions = [];
            $generated_permissions = [];

            if (isset($module->role_module->permissions) &&
                count($module->role_module->permissions) > 0) {
                $exists_permissions = collect($module->role_module->permissions)->pluck('permission_id')->toArray();
                foreach ($exists_permissions as $singlePermission) {
                    $eachPermission = $permission[$singlePermission];
                    $permission_name = $eachPermission['permission'];
                    $permissions[] = $permission_name;
                }
            }

            foreach ($all_module_permission as $single_permission) {
                if (in_array($single_permission['permission'], $permissions)) {
                    $single_permission['checked'] = true;
                }
                $generated_permissions[] = $single_permission;
            }

            $modules[$key]['permissions'] = $permissions;
            $modules[$key]['all_permissions'] = array_values(collect($generated_permissions)->toArray());
        }

        return response()->json($this->returnData(2000, [
            'date' => $modules,
            'modules' => $all_modules
        ]));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'role_id' => 'required',
            'data' => 'required|array|min:1',
        ]);
        if ($validate->fails()) {
            return response()->json($this->returnData(3000, $validate->errors()));
        }

        if (is_array($request->data) && count($request->data)) {
            foreach ($request->data as $data) {
                if (isset($data['checked']) && $data['checked']) {
                    $role_module = new RoleModule();
                    $role_module->module_id = $data['id'];
                    $role_module->role_id = $request->role_id;
                    $role_module->save();

                    if (isset($data['permissions']) && count($data['permissions']) > 0) {
                        foreach ($data['permissions'] as $permission) {
                            if (isset($permission['checked']) && $permission['checked']) {
                                $role_module_permission = new RoleModulePermission();
                                $role_module_permission->role_module_id = $role_module->id;
                                $role_module_permission->permission_id = $permission['id'];
                                $role_module_permission->save();
                            }
                        }
                    }

                }
            }
        }

        return response()->json($this->returnData(2000, [], 'Successfully Inserted'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $role_module = RoleModule::where('module_id', $request->module_id)
            ->where('role_id', $request->role_id)
            ->first();

        $permission = RoleModulePermission::where('role_module_id', $role_module->id)
            ->where('permission_id', $request->permission_id)
            ->first();

        if ($permission && !$request->checked) {
            $permission->delete();
            return response()->json($this->returnData(3000, [], 'Permission Deleted'));
        }

        if (!$permission && $request->checked) {
            $permission = new RoleModulePermission();
            $permission->role_module_id = $role_module->id;
            $permission->permission_id = $request->permission_id;
            $permission->save();

            return response()->json($this->returnData(2000, [], 'Permission Successfully Inserted'));
        }

        return response()->json($this->returnData(3000, [], 'Data not found'));
    }

    public function destroy(Request $request, $id)
    {
        $role_module = RoleModule::where('module_id', $id)
            ->where('role_id', $request->role_id)
            ->first();

        if ($role_module) {

            RoleModule::where('module_id', $id)->where('role_id', $request->role_id)->delete();

            RoleModulePermission::where('role_module_id', $role_module->id)->delete();

            return response()->json($this->returnData(2000, null, 'Successfully Deleted'));
        }

        return response()->json($this->returnData(5000, null, 'Data not found'));
    }
}
