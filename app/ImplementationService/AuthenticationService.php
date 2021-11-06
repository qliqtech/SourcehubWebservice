<?php

namespace App\ImplementationService;



use App\DBOperations\UserDBOperations;
use App\Enums\InAppResponsTypes;
use App\Enums\UserRoles;
use App\Helper\GenerateRandomCharactersHelper;
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

        $params =  array_add($params,'Isconfirmed',false);

        $params =  array_add($params,'userroleid',UserRoles::Student);

        $params =  array_add($params,'IsActive',false);




        try {

        if ($params == null) {


            return $this::responseHelper($responsearray)[0];
        }



        $params['password'] = LoginHelper::HashPassWord($params['password']);  //Hash::make($params['password']);

        $params['remember_token'] = Str::random(10);

        $params['activityname'] = "User Signup";

        $params['confirmationcode'] = GenerateRandomCharactersHelper::generaterandomAlphabets(15);


        //Send ConfirmationEmail

        $user = $userdboperations->create($params);
        $token = $user->createToken('creds')->accessToken;

            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                InAppResponsTypes::responsemessagekey => $token
            );
         //   return $this::responseHelper($responsearray)[0];



        }catch (\Exception $ex){


            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Error,
                                   InAppResponsTypes::responsemessagekey => $ex->getMessage()
                        );

            $params['activityname'] = "User Signup";

            $params['responsemessage'] = $ex->getMessage();

        //    return $this::responseHelper($responsearray)[0];
        }

        $responsearray = array_add($responsearray,'AuditItems',$params);


      //  dd($responsearray);

        return $this::responseHelper($responsearray)[0];
    }





    public function login($params) : array
    {

        $user = new User();

        $userdboperations = new UserDBOperations($user);

        $responsearray = array();

        $successful = true;

        if ($params == null) {


            return $this::responseHelper($responsearray)[0];
        }

        $params =  array_add($params,'Isconfirmed',false);

        $params =  array_add($params,'userroleid',UserRoles::Student);

        $params =  array_add($params,'IsActive',false);


        $userdetails =  $userdboperations->findUserByEmail($params['email']);



        if($userdetails){

            try {
                if (LoginHelper::PasswordCheck($params['password'], $userdetails->password)) {
                    $token = $user->createToken('creds')->accessToken;

                    $successful = true;

                 //   dd($user->IsActive);

                    if($user->IsActive === false){

                        $params['responsemessage'] = "Account is inactive ".$params['email'];


                        $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Failed,
                            InAppResponsTypes::responsemessagekey => "Account Inactive"
                        );

                        $successful = false;

                    }

                    if($successful == true){

                        $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                            InAppResponsTypes::responsemessagekey => $token
                        );


                        $params['confirmationcode'] = GenerateRandomCharactersHelper::generaterandomAlphabets(15);

                        $params['userid'] = $userdetails->id;

                        $params['activityname'] = "Login";

                        $params['responsemessage'] = "Login Success ";

                    }

                } else {
                    $params['responsemessage'] = "Wrong username or password: ".$params['email'];

                    $params['activityname'] = "Login";



                    $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Failed,
                        InAppResponsTypes::responsemessagekey => "Wrong username or password"
                    );
                }





            }catch (\Exception $ex){


                $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Error,
                    InAppResponsTypes::responsemessagekey => $ex->getMessage()
                );

                $params['activityname'] = "Login";

                $params['responsemessage'] = $ex->getMessage();

                //    return $this::responseHelper($responsearray)[0];
            }

        }else{

            $params['responsemessage'] = "Account not found ".$params['email'];

            $params['activityname'] = "Login";



            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Failed,
                InAppResponsTypes::responsemessagekey => "Account not found");
        }


        $responsearray = array_add($responsearray,'AuditItems',$params);


        //  dd($responsearray);

        return $this::responseHelper($responsearray)[0];
    }









}
