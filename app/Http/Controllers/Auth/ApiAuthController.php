<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ApiResponseCodesKeysAndMessages;
use App\Enums\InAppResponsTypes;
use App\Http\Controllers\Controller;
use App\ImplementationService\AuthenticationService;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Validator;


class ApiAuthController extends Controller
{

    public function register (Request $request) {

        $request->request->add($this->GetUserAgent($request));



        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {

            $responsevalues = array(ApiResponseCodesKeysAndMessages::ResponseCodeKey=>ApiResponseCodesKeysAndMessages::ValidationError,
                ApiResponseCodesKeysAndMessages::ResponseMessageCodeKey=>'Validation Errors',
                'errors'=>$validator->errors()->all()


            );


            return response($responsevalues);


        }
        $allkeys = $request->all();


        $service = new AuthenticationService();

       $response = $service->registeruser($allkeys);

     //  dd($response);

       if($response[InAppResponsTypes::responsetypekey] == InAppResponsTypes::Success){


            $responsevalues = array(ApiResponseCodesKeysAndMessages::ResponseCodeKey=>ApiResponseCodesKeysAndMessages::SuccessCode,
                                    ApiResponseCodesKeysAndMessages::ResponseMessageCodeKey=>'Registration successfull',
                                    'usertoken'=>$response[InAppResponsTypes::responsemessagekey]


            );


            return response($responsevalues);
       }


        $responsevalues = array(ApiResponseCodesKeysAndMessages::ResponseCodeKey=>ApiResponseCodesKeysAndMessages::FailedCode,
            ApiResponseCodesKeysAndMessages::ResponseMessageCodeKey=>'Error',



        );


        return response($responsevalues);


    }


    public function login (Request $request) {

        $request->request->add($this->GetUserAgent($request));


        $validator = Validator::make($request->all(), [

            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {

            $responsevalues = array(ApiResponseCodesKeysAndMessages::ResponseCodeKey=>ApiResponseCodesKeysAndMessages::SuccessCode,
                ApiResponseCodesKeysAndMessages::ResponseMessageCodeKey=>'Validation Errors',
                'errors'=>$validator->errors()->all()


            );


            return response($responsevalues);


        }
        $allkeys = $request->all();


        $service = new AuthenticationService();

        $response = $service->login($allkeys);

        //  dd($response);

        if($response[InAppResponsTypes::responsetypekey] == InAppResponsTypes::Success){


            $responsevalues = array(ApiResponseCodesKeysAndMessages::ResponseCodeKey=>ApiResponseCodesKeysAndMessages::SuccessCode,
                ApiResponseCodesKeysAndMessages::ResponseMessageCodeKey=>'Login successfull',
                'usertoken'=>$response[InAppResponsTypes::responsemessagekey]


            );


            return response($responsevalues);
        }


        $responsevalues = array(ApiResponseCodesKeysAndMessages::ResponseCodeKey=>ApiResponseCodesKeysAndMessages::FailedCode,
                                ApiResponseCodesKeysAndMessages::ResponseMessageCodeKey=>$response[InAppResponsTypes::responsemessagekey],



        );


        return response($responsevalues);


    }





    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }


    public function authenticationerror(){

        $response = ['message' => 'Authentication Error'];
        return response($response, 200);

    }
}
