<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOrganization extends Base
{
    use HasFactory, SoftDeletes;
    protected $table = 'user_organizations';
    protected $fillable = [
        'id_user',
        'id_organization',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'id_comment');
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'id_organization');
    }
}
