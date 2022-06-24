<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController;

class MailController extends BaseController
{
    private $table;
    function __construct()
    {
    }

    public function sendMailForgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return $this->dataResponse('401', $validator->errors(), []);
        }
        $token = randomString(10);
        $emailUser = $request->email;
        // send mail
        try {
            Mail::send('email.sendToken', ['token' => $token], function ($message) use ($emailUser) {
                $message->from(env('MAIL_USERNAME'), 'DEV | GỬI MÃ XÁC NHẬN ĐỔI MẬT KHẨU');
                $message->to($emailUser, 'Hi bạn');
                $message->subject('Gửi mail xác nhận');
            });
            if (count(Mail::failures()) > 0) {
                return  $this->dataResponse('200',  config('statusCode.SUCCESS_VI'), []);
            } else {
                return  $this->dataResponse('500', config('statusCode.FAIL'), []);
            }
        } catch (\Exception $e) {
            return $this->dataResponse('500',  $e->getMessage() , []);
        }
    }
}
