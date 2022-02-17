<?php

namespace App\ImplementationService;

use App\Enums\InAppResponsTypes;

class BaseImplemetationService
{

    public function responseHelper($response): array {


        if($response == null){

            return array([InAppResponsTypes::responsetypekey=>InAppResponsTypes::Error]);
        }
        if(!array_key_exists(InAppResponsTypes::responsetypekey, $response)){

            return array(InAppResponsTypes::responsetypekey =>InAppResponsTypes::ValidationError);

        }

        $auditparams = $response["AuditItems"];


        $this::LogAudit($auditparams);

        return array($response);



    }

    public function LogAudit($parameters){


     //   dd($parameters);
        $auditservice = new AuditService();

        $auditservice->SaveAuditInDB($parameters);


    }



    public function StopProcessAndDisplayMessage($httpcode,$displaymessage){


        //   dd($httpcode);

        header("HTTP/1.1 ".$httpcode);

        die($displaymessage);
        //   dd($parameters);





    }

}
