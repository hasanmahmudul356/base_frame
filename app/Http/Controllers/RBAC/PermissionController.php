<?php

namespace App\Http\Controllers\RBAC;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleModule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    use Helper;

    public function index()
    {
        $keyword = request()->input('keyword');
        $users = Module::when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'Like', "%$keyword%");
        })
            ->with('permissions')
            ->paginate(20);
        return response()->json($this->returnData(2000, $users));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validate = Validator::make($input, [
            'email' => 'required',
            'password' => 'required',
            'name' => 'required',
            'username' => 'required',
            'role_id' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $user = new User();
        $user->fill($input);
        $user->password = Hash::make($input['password']);
        $user->save();

        return response()->json($this->returnData(2000, $user, 'Successfully Inserted'));
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
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'name' => 'required',
            'username' => 'required',
            'role_id' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $user = User::where('id', $request->id)->first();
        if ($user) {
            $user->fill($request->all());
            $user->save();

            return response()->json($this->returnData(2000, $user, 'Successfully Inserted'));
        }

        return response()->json($this->returnData(5000, null, 'Data Not found'));
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            $user->delete();

            return response()->json($this->returnData(2000, $user, 'Successfully Deleted'));
        }

        return response()->json($this->returnData(5000, null, 'Data Not found'));
    }

    public function old_permission()
    {
        $role_modules = RoleModule::where('role_id', auth::user()->role_id)->get()->pluck('module_id')->toArray();
        $all_modules = Module::whereIn('id', $role_modules)
            ->where('parent_id', 0)
            ->with(['submenu', 'role_module'])
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
                    $permissions[exact_permission($permission_name)] = $permission_name;
                }
                $modules[$key]['permissions'] = $permissions;
            } else {
                $modules[$key]['permissions'] = [];
            }

            $submenus = [];
            if (count($module->submenu) > 0) {
                foreach ($module->submenu as $subkey => $submenu) {
                    $submenus[$subkey]['name'] = $submenu->name;
                    $submenus[$subkey]['display_name'] = $submenu->display_name;

                    if (isset($submenu->role_module->permissions) &&
                        count($submenu->role_module->permissions) > 0) {
                        $modules_permissions = collect($submenu->role_module->permissions)->pluck('permission_id')->toArray();
                        foreach ($modules_permissions as $permission) {
                            $eachPermission = $all_permissions[$permission];
                            $permission_name = $eachPermission['permission'];
                            $permissions[exact_permission($permission_name)] = $permission_name;
                        }
                        $submenus[$subkey]['permissions'] = $permissions;
                    } else {
                        $submenus[$subkey]['permissions'] = [];
                    }
                }
            }
            $modules[$key]['submenus'] = $submenus;
        }

        return response()->json($this->returnData(2000, $modules));
    }
}
