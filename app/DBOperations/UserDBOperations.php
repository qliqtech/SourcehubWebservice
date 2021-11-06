<?php

namespace App\DBOperations;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserDBOperations extends BaseDBOperations
{

    protected $model;

    public function findUserByEmail($emailaddress): ?Model
    {
        return User::where('email',$emailaddress)->first();
    }

}
