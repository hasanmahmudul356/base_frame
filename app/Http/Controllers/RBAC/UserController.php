<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use Helper;

    public function index()
    {
        $keyword = request()->input('keyword');
        $users = User::when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'Like', "%$keyword%");
        })
            ->with('role')
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
}
