<?php

namespace App\ImplementationService;

use App\Enums\InAppResponsTypes;

class BaseImplemetationService
{

    public function responseHelper($response): array {


    //    dd($response);

        if($response == null){

            return array([InAppResponsTypes::responsetypekey=>InAppResponsTypes::Error]);
        }
        if(!array_key_exists(InAppResponsTypes::responsetypekey, $response)){

            return array(InAppResponsTypes::responsetypekey =>InAppResponsTypes::ValidationError);

        }


        return array($response);



    }

}
