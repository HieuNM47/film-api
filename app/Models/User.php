<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Base;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_permission',
        'identity_card',
        'birthday',
        'avatar',
        'url',
        'location',
        'bio',
        'currently_learning',
        'skills',
        'work',
        'education'
    ];
    // public function createByTable($table, $data){
    //     $model1 =  new Base();
    //     return $model1->createByTable($table, $data);
    // }
    // public function getByCondition($table, $data){
    //     $this->model->createByTable($table, $data);
    // }
    // public function updateCondition($table, $data){
    //     $this->model->createByTable($table, $data);
    // }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comment()
    {
        return $this->hasMany(Comment::class, 'id_user', 'id');
    }

    public function userIconRank()
    {
        return $this->hasMany(UserIconRank::class, 'id_user', 'id');
    }
    public function userOrganization()
    {
        return $this->hasMany(UserOrganization::class, 'id_user', 'id');
    }
    public function donate()
    {
        return $this->hasMany(Donate::class, 'id_user', 'id');
    }
    public function creditCart()
    {
        return $this->hasMany(CreditCart::class, 'id_user', 'id');
    }
    public function post()
    {
        return $this->hasMany(Post::class, 'id_user', 'id');
    }
    public function userFeel()
    {
        return $this->hasMany(UserFeel::class, 'id_user', 'id');
    }

    public function getCheckUserByMail($email, $password)
    {
        $user =  $this->where('email',$email)->first();
        if($user && Hash::check($password , $user->password)){
            return $user;
        }
        return false;
    }
    public function updatePasswordByEmail($email, $password)
    {
        return $this->where('email',$email)->update(['password' =>  $password]);

    }
    public function searchLikeAll($key) {
        $sql = $this->where('name', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('email', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('identity_card', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('birthday', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('location', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('bio', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('currently_learning', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('skills', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('work', 'LIKE', '%'.$key.'%');
        $sql = $sql->orWhere('education', 'LIKE', '%'.$key.'%');
        return  $sql->get();
    }
}
