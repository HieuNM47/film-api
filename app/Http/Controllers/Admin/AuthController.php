<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\User;
use Facade\FlareClient\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth as Auth_V2;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\UserToken;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    private $table;
    function __construct()
    {
        parent::__construct();
        $this->table = new UserToken();
    }
    public function index(Request $request)
    {
       return view($this->rootView.'.Auth.login');
    }


    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Vui lòng nhập đúng định dạng email',
            'email.exists' => 'Email không tồn tại',
            'password.required' => 'Vui lòng nhập nhập khẩu'
        ]);
        $data = [
            'email' =>$request->email,
            'password' =>$request->password,
            'id_permission'=>2
        ];
        $userModel = new User();
        $admin = $userModel->getCheckUserByMail($data['email'], $data['password'], true);
        if($admin){
            $request->session()->put('just-login', true);
            $request->session()->put('logged_in_admin', $admin);
            return redirect('admin');
        }
        return redirect('admin/login')->with('thongbao', 'Email hoặc mật khẩu không đúng !! ');
    }
    public function forgetPassword(Request $request)
    {
       return view($this->rootView.'.Auth.forgetPassword');
    }
    public function postForgetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Vui lòng nhập đúng định dạng email',
            'email.exists' => 'Email không tồn tại'
        ]);
        $token = randomString(10);
        $emailUser = $request->email;
        // send mail
        try {
            Mail::send('email.sendTokenAdmin', ['token' => $token], function ($message) use ($emailUser) {
                $message->from(env('MAIL_USERNAME'), 'DEV | GỬI MÃ XÁC NHẬN ĐỔI MẬT KHẨU');
                $message->to($emailUser, 'Hi bạn');
                $message->subject('Gửi mail xác nhận');
            });
            if (count(Mail::failures()) > 0) {
                return redirect('admin/forget-password')->with('thongbao', 'Gửi mã xác nhận thất bại, vui lòng thử lại !! ');
            } else {
                $data = [
                    'email'=> $emailUser,
                    'token'=> $token
                ];
                $this->table->createToken($data);
                return redirect('admin/forget-password')->with('thongbao', 'Gửi mã thành công, vui lòng xem mail để lấy mã !! ');
            }
        } catch (\Exception $e) {
            return redirect('admin/forget-password')->with('thongbao', 'Hệ thống đang bảo trì, vui lòng thử lại sau !! ');
        }
    }

    public function confirmForgetPassword(Request $request){
        return view($this->rootView.'.Auth.confirmToken');
    }
    public function postConfirmForgetPassword(Request $request){

        $this->validate($request, [
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required',
            'password_new' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Vui lòng nhập đúng định dạng email',
            'email.exists' => 'Email không tồn tại',
            'email.exists' => 'Email không tồn tại',
            'token.required' => 'Vui lòng nhập token',
            'password_new.required' => 'Vui lòng nhập mật khẩu mới',
        ]);

        $userToken =   $this->table->getTokenByMail($request->email);
        if($userToken){
            ///2022-06-25 08:05:00       ///2022-06-25 08:15:00
            if(strtotime($userToken['created_at']) > time() - 60*5) {
                if($request->token == $userToken['token']){
                    $checkUpdate =  (new User())->updatePasswordByEmail($request->email,Hash::make($request->password_new));
                    return redirect('admin/login')->with('thongbao', 'Đổi mật khẩu thành công!! ');
                }
                return redirect('admin/confirm-forget-password')->with('thongbao', 'Bạn đã nhập sai token, vui lòng nhập lại!! ');
            } else {
                return redirect('admin/confirm-forget-password')->with('thongbao', 'Bạn đã nhập sai token hoặc token đã hết hạn !! ');
            }
        }
        return redirect('admin/confirm-forget-password')->with('thongbao', 'Bạn đã nhập sai token hoặc token đã hết hạn !! ');
    }
    public function logout(Request $request)
    {
        $request->session()->forget(['logged_in_admin']);
        Session::flash('error_message', 'Phiên đăng nhập của bạn đã hết hạn, vui lòng đăng nhập lại');
        return redirect('admin/login');
    }
    public function getChangePassword(){
        return 1;
    }

    public function changePassword(Request $request){
        if($request->isMethod('get')){
            return view($this->rootView.'.Auth.changePassword');
        }else{
            $this->validate($request, [
                'email'=> 'required|email|exists:users,email',
                'password'=> 'required',
                'password_new'=> 'required|different:password',
            ], [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Vui lòng nhập đúng định dạng email',
                'email.exists' => 'Email không tồn tại',
                'password.required' => 'Vui lòng nhập password',
                'password_new.required' => 'Vui lòng nhập mật khẩu mới',
                'password_new.different' => 'Mật khẩu mới phải khác mật khẩu cũ',
            ]);

            $checkIssetUser =  (new User())->getCheckUserByMail($request->email,$request->password);
            if($checkIssetUser){
                $data =  (new User())->updateByEmail($request->email, ['password'=>Hash::make($request->password_new)]);
                return redirect()->back()->with('thongbao', 'Đổi mật khẩu thành công!! ');
            }else{
                return redirect()->back()->with('thongbao', 'Nhập mật khẩu cũ sai!! ');
            }

        }
    }
}

