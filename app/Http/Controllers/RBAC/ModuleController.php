<?php

namespace App\Http\Controllers\RBAC;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use App\Models\RoleModule;
use App\Models\RoleModulePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    use Helper;

    public function index()
    {
        $keyword = request()->input('keyword');
        $users = Module::with('permissions')->where('parent_id', 0)->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'Like', "%$keyword%");
        })
            ->with('submenu.permissions')
            ->paginate($this->perPage);

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
            'name' => 'required',
            'display_name' => 'required',
            'link' => 'required',
            'permissions' => 'array',
        ]);
        if ($validate->fails()) {
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $module = new Module();
        $module->fill($input);
        $module->save();

        foreach ($request->permissions as $eachPermission) {
            if (isset($eachPermission['exact']) && $eachPermission['exact']) {
                $permission = new Permission();
                $permission->module_id = $module->id;
                $permission->permission = $module->name.'_'.$eachPermission['exact'];
                $permission->save();
            }
        }

        return response()->json($this->returnData(2000, null, 'Successfully Inserted'));
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
        $input = $request->all();

        $validate = Validator::make($input, [
            'name' => 'required',
            'display_name' => 'required',
            'link' => 'required',
            'permissions' => 'array',
        ]);
        if ($validate->fails()) {
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $module = Module::where('id', $request->id)->first();
        if ($module){
            $module->fill($input);
            $module->save();

            $permissions = [];

            foreach ($request->permissions as $eachPermission) {
                if (isset($eachPermission['exact']) && $eachPermission['exact']) {
                    if (isset($eachPermission['id']) && $eachPermission['id']){
                        $permission = Permission::where('id', $eachPermission['id'])->first();
                        $permission->module_id = $module->id;
                        $permission->permission = $module->name.'_'.$eachPermission['exact'];
                        $permission->save();

                        $permissions[] = $permission->id;
                    }else{
                        $permission = new Permission();
                        $permission->module_id = $module->id;
                        $permission->permission = $module->name.'_'.$eachPermission['exact'];
                        $permission->save();

                        $permissions[] = $permission->id;
                    }
                }
            }

            Permission::whereNotIn('id', $permissions)->where('module_id', $module->id)->delete();
        }


        return response()->json($this->returnData(2000, null, 'Successfully Inserted'));
    }

    public function destroy($id)
    {
        $user = Module::where('id', $id)->first();
        if ($user) {
            $user->delete();

            return response()->json($this->returnData(2000, $user, 'Successfully Deleted'));
        }

        return response()->json($this->returnData(5000, null, 'Data Not found'));
    }
}
