<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;

class SettingsController extends BaseController
{
    private $table;
    function __construct()
    {
        $this->table = new Setting();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->dataResponse('401', $validator->errors(), []);
        }

        $condition['id_user'] = $request->id_user;
        if ($request->expectsJson()) {
            $data = $this->table->getByCondition($this->table, $condition);
            return $this->dataResponse('200',  config('statusCode.SUCCESS_VI'),  $data);
        }
        return view('pages.post.list', ['typeSite' => $this->table->orderBy('id', 'desc')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.post.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'value' => 'required',
            'id_user' => 'required|numeric|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->dataResponse('401', $validator->errors(), []);
        }
        $data = $this->table->createByTable($this->table, $request->all());
        return  $this->dataResponse('200',  $data ? config('statusCode.SUCCESS_VI') : config('statusCode.FAIL'), []);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $condition['id'] = $id;
        if ($request->expectsJson()) {
            $data = $this->table->getByCondition( $this->table, $condition);
            // return $data;
            return $this->dataResponse(!$data->IsEmpty() ? '200' :'404',!$data->IsEmpty() ? config('statusCode.SUCCESS_VI') : config('statusCode.NOT_FOUND_VI'),  $data);
        }
        return view('pages.post.detail', ['typeSite' => $this->table->orderBy('id', 'desc')->get()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('pages.post.edit', ['typeSites' => $this->table->find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $condition = [];
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'value' => 'required',
            'id_user' => 'required|numeric|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->dataResponse('401', $validator->errors(), []);
        }
        $data =  $request->all();
        unset($data['_method']);

        $condition['id'] = $id;
        $data = $this->table->updateCondition($this->table, $data ,$condition);
        return  $this->dataResponse($data ?'200' :'404',  $data ? config('statusCode.SUCCESS_VI') : config('statusCode.NOT_FOUND_VI'), []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if ($request->expectsJson()) {
            $data = $this->table->where('id', $id)->delete();
            return $this->dataResponse($data ?'200' :'404', $data ? config('statusCode.SUCCESS_VI') : config('statusCode.NOT_FOUND_VI'),  $data);
        }
        return view('pages.post.detail', ['typeSite' => $this->table->orderBy('id', 'desc')->get()]);
    }
}
