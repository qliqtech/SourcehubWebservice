<?php

namespace App\ImplementationService;



use App\CacheOperations\CacheUserRegistrationOps;
use App\DBOperations\UserDBOperations;
use App\Enums\InAppResponsTypes;
use App\Enums\UserRoles;
use App\Helper\EmailHelper;
use App\Helper\EmailMessages;
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

        $params['remember_token'] = GenerateRandomCharactersHelper::generaterandomAlphabets(10);

        $params['confirmationcode'] = GenerateRandomCharactersHelper::generaterandomnumbeer(6);

        $params['activityname'] = "User Signup";

        $params['confirmationcode'] = GenerateRandomCharactersHelper::generaterandomAlphabets(15);


        //Send ConfirmationEmail

        $user = $userdboperations->create($params);
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                InAppResponsTypes::responsemessagekey => $token
            );


        }catch (\Exception $ex){


            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Error,
                                   InAppResponsTypes::responsemessagekey => $ex->getMessage()
                        );

            $params['activityname'] = "User Signup";

            $params['responsemessage'] = $ex->getMessage();

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

      //  $params =  array_add($params,'Isconfirmed',false);

      //  $params =  array_add($params,'userroleid',UserRoles::Student);

    //    $params =  array_add($params,'IsActive',false);


        $userdetails =  $userdboperations->findUserByEmail($params['email']);



        if($userdetails){

            try {
                if (LoginHelper::PasswordCheck($params['password'], $userdetails->password)) {

                 //   dd();

                    $token = $userdetails->createToken('Laravel Password Grant Client')->accessToken;

                    $successful = true;

                    if($successful == true){

                        $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                            InAppResponsTypes::responsemessagekey => $token
                        );



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




    public function resetpassword($params) : array
    {


        $user = new User();

        $userdboperations = new UserDBOperations($user);

        $cacheUserRegistrationOps = new CacheUserRegistrationOps();

        $responsearray = array();



        try {

            if ($params == null) {

                return $this::responseHelper($responsearray)[0];
            }

            //    $valuesfromcache = json_decode($cacheUserRegistrationOps->getuserregistrationdetailsfromcache($params["confirmationcode"]),true);

            $newpassword = LoginHelper::HashPassWord($params['password']);

            $userdetails = $userdboperations->updateuseraccountpasswordfromconfirmationcode($params["confirmationcode"],$newpassword);




            //   dd($valuesfromcache);




            if($userdetails == null){

                $this::StopProcessAndDisplayMessage("404","Confirmation code not found");

            }

            $params['userid'] = $userdetails->id;


            $token = $user->createToken('Laravel Password Grant Client')->accessToken;



            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                InAppResponsTypes::responsemessagekey => $token
            );

            $params['activityname'] = "Password reset";

            $params['responsemessage'] = "password reset successful";

        }catch (\Exception $ex){


            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Error,
                InAppResponsTypes::responsemessagekey => $ex->getMessage()
            );

            $params['activityname'] = "Password reset";

            $params['responsemessage'] = $ex->getMessage();

            //    return $this::responseHelper($responsearray)[0];
        }

        $responsearray = array_add($responsearray,'AuditItems',$params);


        //    $cacheUserRegistrationOps->deleteconfirmationcode($params["confirmationcode"]);

        return $this::responseHelper($responsearray)[0];
    }




    public function sendpasswordresetlink($params) : array
    {


        $user = new User();

        $userdboperations = new UserDBOperations($user);


        $responsearray = array();

        if ($params == null) {

            return $this::responseHelper($responsearray)[0];
        }

        $userdetails = $userdboperations->findUserByEmail($params["email"]);

        if($userdetails->IsDeleted == true){

            $this::StopProcessAndDisplayMessage("404","account is deleted");


        }

        if($userdetails->IsActive == false){


            $this::StopProcessAndDisplayMessage("401","account is deactivated");

        }

        try {



            //  $valuesfromcache = json_


            $token = $userdetails->createToken('Laravel Password Grant Client')->accessToken;


            $emailmessages = new EmailMessages();

            $confirmationcode = GenerateRandomCharactersHelper::generaterandomAlphabets(16);



            $emailmessagebody = $emailmessages->sendPasswordresetlink("", $confirmationcode);



            $userdboperations->updateById($userdetails->id,array('confirmationcode'=>$confirmationcode,'requirespasswordreset'=>true));

            //confirmationcode


            EmailHelper::sendEmail($params["email"],"",$emailmessagebody,"Password reset");

            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                InAppResponsTypes::responsemessagekey => $token
            );

        }catch (\Exception $ex){


            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Error,
                InAppResponsTypes::responsemessagekey => $ex->getMessage()
            );

            $params['activityname'] = "Account Confirmation";

            $params['responsemessage'] = $ex->getMessage();

            //    return $this::responseHelper($responsearray)[0];
        }

        $responsearray = array_add($responsearray,'AuditItems',$params);



        return $this::responseHelper($responsearray)[0];
    }




    public function confirmaccount($params) : array
    {


        $user = new User();

        $userdboperations = new UserDBOperations($user);

        $cacheUserRegistrationOps = new CacheUserRegistrationOps();

        $responsearray = array();



        try {

            if ($params == null) {

                return $this::responseHelper($responsearray)[0];
            }

            $valuesfromcache = json_decode($cacheUserRegistrationOps->getuserregistrationdetailsfromcache($params["confirmationcode"]),true);

            if($valuesfromcache == null){

                $this::StopProcessAndDisplayMessage("404","Invalid Confirmation code");
            }

            $valuesfromcache["Isconfirmed"] = true;

            $valuesfromcache["ConfirmedOn"] = now();

            $valuesfromcache["IsActive"] = true;


            //   dd($valuesfromcache);

            if($userdboperations->findUserByEmail($valuesfromcache["email"])!=null){


                $this::StopProcessAndDisplayMessage("201","Account already confirmed");

            }


            $userdetails = $userdboperations->create($valuesfromcache);



            if($userdetails == null){

                $this::StopProcessAndDisplayMessage("404","Confirmation code not found");

            }

            $params['userid'] = $userdetails->id;


            $token = $user->createToken('Laravel Password Grant Client')->accessToken;



            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Success,
                InAppResponsTypes::responsemessagekey => $token
            );

        }catch (\Exception $ex){


            $responsearray = array(InAppResponsTypes::responsetypekey => InAppResponsTypes::Error,
                InAppResponsTypes::responsemessagekey => $ex->getMessage()
            );

            $params['activityname'] = "Account Confirmation";

            $params['responsemessage'] = $ex->getMessage();

            //    return $this::responseHelper($responsearray)[0];
        }

        $responsearray = array_add($responsearray,'AuditItems',$params);


        $cacheUserRegistrationOps->deleteconfirmationcode($params["confirmationcode"]);

        return $this::responseHelper($responsearray)[0];
    }










}
