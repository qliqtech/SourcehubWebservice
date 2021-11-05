<?php

namespace App\ImplementationService;



use App\DBOperations\UserDBOperations;
use App\Enums\InAppResponsTypes;
use App\Helper\LoginHelper;
use App\Models\User;


use Illuminate\Support\Str;

class AuthenticationService extends BaseImplemetationService
{

    public function registeruser($params) : array
    {

        $user = new User();

        $userdboperations = new UserDBOperations($user);

        $responsearray = array();


        //do validation
        //process request
        //audit
        try {

        if ($params == null) {


            return $this::responseHelper($responsearray)[0];
        }
        $params['password'] = LoginHelper::HashPassWord($params['password']);  //Hash::make($params['password']);
        $params['remember_token'] = Str::random(10);
        $user = $userdboperations->create($params);
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                InAppResponsTypes::responsemessagekey => $token
            );



      //  return false;

        }catch (\Exception $ex){


            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Error,
                    InAppResponsTypes::responsemessagekey => $ex->getMessage()
            );

        //    return $this::responseHelper($responsearray)[0];
        }



        return $this::responseHelper($responsearray)[0];
    }







}
