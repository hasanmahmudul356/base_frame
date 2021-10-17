<?php

namespace App\Http\Controllers\RBAC;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use Helper;

    public function index()
    {
        $keyword = request()->input('keyword');

        $users = Role::when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'Like', "%$keyword%");
        })->paginate($this->perPage);

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
        ]);
        if ($validate->fails()){
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $role = new Role();
        $role->fill($input);
        $role->save();

        return response()->json($this->returnData(2000, $role, 'Successfully Inserted'));
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
            'name' => 'required',
        ]);
        if ($validate->fails()){
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $role = Role::where('id', $request->id)->first();
        if ($role){
            $role->fill($request->all());
            $role->save();

            return response()->json($this->returnData(2000, $role, 'Successfully Inserted'));
        }

        return response()->json($this->returnData(5000 , null, 'Data Not found'));
    }

    public function destroy($id)
    {
        $role = Role::where('id', $id)->first();
        if ($role){
            $role->delete();

            return response()->json($this->returnData(2000, $role, 'Successfully Deleted'));
        }

        return response()->json($this->returnData(5000 , null, 'Data Not found'));
    }
}
