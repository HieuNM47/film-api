<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class UserController2 extends BaseController
{
    private $table;
    function __construct()
    {
        $this->table = new User();
        $this->controllerName = 'UserController2';
        $this->controllerView = 'pages.user2';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!empty($request->all()) && $request->ajax()) {
            $condition['name'] = $request->name ??'';
            $condition['email'] =  $request->email ??'';
            $condition['identity_card'] = $request->identity_card ??'';
            $condition['skills'] = $request->skills ??'';

            return  $this->table->getDataUserByCondition($condition);
        }
        $this->dataResponse['controllerName'] = $this->controllerName;

        return view($this->controllerView.'.listUser',   $this->dataResponse);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $today = date('Y-m-d');
        return view($this->controllerView.'.addUser' , ['today'=>$today]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'birthday'=> 'required|date_format:Y-m-d|before:today',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'avatar' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'avatar' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'identity_card' => 'digits_between:9,11|required|numeric'
        ],[
            'birthday.before'           => 'Vui l??ng ch???n ng??y sinh nh??? h??n ng??y hi???n t???i',
            'birthday.required'           => 'Vui l??ng ch???n ng??y sinh',
            'name.required'     => 'Vui l??ng nh???p t??n',
            'email.required'     => 'Vui l??ng nh???p email',
            'email.email'     => 'Sai ?????nh d???ng email',
            'emai.unique'     => 'Email ???? t???n t???i',
            'avatar.required'     => 'Vui l??ng ch???n ???nh',
            'password.required'     => 'Vui l??ng nh???p m???t kh???u',
            'password.min'     => '????? d??i m???t kh???u l???n h??n 6 6 k?? t???',
            'avatar.mimes'     => 'Vui l??ng ch???n ????ng ?????nh d???ng ???nh jpeg,jpg,png,gif ',
            'identity_card.required'     => 'Vui l??ng nh???p CMND ',
            'identity_card.numeric'     => 'Vui l??ng nh???p CMND l?? s???',
            'identity_card.digits_between'     => 'Vui l??ng nh???p CMND ????ng k??ch th?????c ',
            'identity_card.max'     => 'Vui l??ng nh???p CMND ????ng k??ch th?????c ',
        ]);
        $data = $request->all();
        $data['avatar'] = uploadImage($request, 'avatar', 'User');
        $data['password'] = Hash::make($request->password);
        $data = $this->table->create($data);
        if($data){
            return back()->with('success', 'S???a th??nh c??ng');
        }
        return back()->with('error', 'S???a th???t b???i');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $dataReponse['adminLogin'] = User::findOrFail($id);
        return view($this->rootView.'.user2.edit', $dataReponse);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
        $this->validate($request,[
                'birthday'=> 'required|date_format:Y-m-d|before:today',
            ],[
                'birthday.required'     => 'Vui l??ng ch???n ng??y sinh',
                'birthday.before'           => 'Vui l??ng ch???n ng??y sinh nh??? h??n ng??y hi???n t???i'
            ]);
        $data =  $request->all();
        if(isset($data['_token'])){
            unset($data['_token']);
        }
        if(isset($data['_method'])){
            unset($data['_method']);
        }
        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage($request, 'avatar', 'User');
        }
        $data = User::where("id", $request->id)->update($data);
        if($data){
            return back()->with('success', 'S???a th??nh c??ng');
        }
        return back()->with('error', 'S???a th???t b???i');

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
            return $this->dataResponse($data ? '200' : '404', $data ? config('statusCode.SUCCESS_VI') : config('statusCode.NOT_FOUND_VI'),  $data);
        }
        return view('pages.post.detail', ['typeSite' => $this->table->orderBy('id', 'desc')->get()]);
    }




}
