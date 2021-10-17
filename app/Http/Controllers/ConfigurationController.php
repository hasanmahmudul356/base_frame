<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends Controller
{
    use Helper;

    public function index()
    {
        $keyword = request()->input('keyword');
        $datas = Configuration::when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'Like', "%$keyword%");
        })
            ->paginate($this->perPage);

        return response()->json($this->returnData(2000, $datas));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validate = Validator::make($input, [
            'type' => 'required',
            'key' => 'required',
            'display_name' => 'required',
            'value' => !$request->file ? 'required' : '',
        ]);
        if ($validate->fails()) {
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $data = new Configuration();

        if ($request->file){
            $input['value'] = uploadFile($request->file, $request->key, 'images/');
        }
        $data->fill($input);
        $data->save();

        return response()->json($this->returnData(2000, $data, 'Successfully Inserted'));
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
            'type' => 'required',
            'key' => 'required',
            'display_name' => 'required',
            'value' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json($this->returnData(2000, $validate->errors()));
        }
        $data = Configuration::where('id', $request->id)->first();
        if ($data) {

            if ($request->file){
                $input['value'] = uploadFile($request->file, $request->key, 'images/');
            }

            $data->fill($input);
            $data->save();

            return response()->json($this->returnData(2000, $data, 'Successfully Inserted'));
        }

        return response()->json($this->returnData(5000, null, 'Data Not found'));
    }

    public function destroy($id)
    {
        $data = Configuration::where('id', $id)->first();
        if ($data) {
            $data->delete();

            return response()->json($this->returnData(2000, $data, 'Successfully Deleted'));
        }

        return response()->json($this->returnData(5000, null, 'Data Not found'));
    }
}
