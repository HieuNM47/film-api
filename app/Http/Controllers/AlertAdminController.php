<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;
use App\Models\AlertAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AlertAdminController extends BaseController
{
    private $table;
    function __construct()
    {
        $this->table = new AlertAdmin();
    }
    public function createAlert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'id_user' => 'required|numeric|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->dataResponse('401', $validator->errors(), []);
        }
        $data = $this->table->createByTable($this->table, $request->all());
        return  $this->dataResponse('200',  $data ? config('statusCode.SUCCESS_VI') : config('statusCode.FAIL'), []);
    }
    public function showAlert(Request $request)
    {
        if ($request->ajax()) {
            return  $this->table->getData();
        }
        return view('pages.AlertAdmin.listAlert');
    }
    public function sendMail(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('email.sendAllAd');
        } else {

            // {
            //     "_token": "cyV856nEeYL7UoK1HTjYT66RpHoQji3lPuXhuj85",
            //     "_method": "POST",
            //     "TieuDe": "title",
            //     "TomTat": "<p>content</p>",
            //     "link": "link",
            //     "nhapTaiKhoan": "on",
            //     "taiKhoan": "nguyenminhhieuk3@gmail.com, HieuMinh@gmail.com, Hieu@gmail.com, Teo123@gmail.com"
            // }

            // kiem tra tham so truyen vao
            $this->validate($request, [
                'TieuDe' => 'required|min:6',
                'TomTat' => 'required|min:12',
                'link'   => 'required'
            ], [
                'TieuDe.required' => 'B???n ch??a nh???p ti??u ?????',
                'TieuDe.min'      => 'Ti??u ????? ??t nh???t 6 k?? t???',
                'TomTat.required' => 'B???n ch??a nh???p t??m t???t',
                'TomTat.min'      => 'T??m t???t ??t nh???t 12 k?? t???',
                'link.required'   => 'B???n ch??a nh???p link'
            ]);

            // chuyen data sang mang
            $TieuDe =  $request->TieuDe;
            $TomTat = $request->TomTat;
            $link   = $request->link;
            $data = [
                'TieuDe' => $TieuDe,
                'TomTat' => $TomTat,
                'link'   => $link
            ];

            $email = [];

            // gui email theo ?? ng d??ng
            if (isset($request->nhapTaiKhoan)) {
                // c???t chu???i t??i kho???n
                $str = $request->taiKhoan;
                $subStr = str_replace(' ', '', $str);  // lo???i b??? kho???ng tr???ng
                $subStr = explode(',', $subStr);     // c???t theo d???u ph???y

                foreach ($subStr as $st) {
                    if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+\.[A-Za-z]{2,6}$/", $st)){
                        return back()->with('error', 'Sai ?????nh d???ng email g???i');
                    }
                }
                $email  =  $subStr;
            } else {
                // g???i t???t c???

                // chuyen data gmail sang mang
                $lstuserGmail = User::whereIn('id_permission', $this->listIdPermissionUser)->get();
                // chuyen sang mang
                foreach ($lstuserGmail as $ug) {
                    $email[] =  $ug->email;
                }
            }
             // send mail
            try {
                Mail::send('email.adMail', $data , function ($message) use ($email){
                    $message->from(env('MAIL_USERNAME'), 'DEV');
                    $message->to($email, 'B???n');
                    $message->subject('DEV th??ng b??o');
                });
                if (count(Mail::failures()) > 0) {
                    return  back()->with('error', 'G???i qu???ng c??o th???t b???i, vui l??ng th??? l???i !! ');
                } else {
                    return  back()->with('success', 'G???i qu???ng c??o th??nh c??ng !! ');
                }
            } catch (\Exception $e) {
                return back()->with('error', 'H??? th???ng ??ang b???o tr??, vui l??ng th??? l???i sau !! ');
            }
        }
    }
    public function sendMessage(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('message.sendMessage');
        } else {
        }
    }
    public function getNoti(){
        $data = DB::table('alert_admin')->leftJoin('users', 'users.id','alert_admin.id_user')
        ->select('title', 'content','alert_admin.created_at','name')
        ->orderBy('created_at','DESC')->get();

        return $this->dataResponse('200',  config('statusCode.SUCCESS_VI') ,  $data);
    }
}
