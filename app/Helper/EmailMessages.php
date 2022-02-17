<?php

namespace App\Helper;

class EmailMessages
{

    public $applicationname = "Sourcehub";

    public $baseurl = "https://test.com";


    public function sendEmailConfirmationEmail($name,$confirmationcode){

        return "Hi, $name <br>
        Account ceated successfully on $this->applicationname. Click link to verify account<br>
        $this->baseurl/confirmaccount/$confirmationcode

";




    }


    public function sendPasswordresetlink($name,$resetlinkcode){

        return "Hi, $name <br>
        Password rest request $this->applicationname. Click link to reset<br>
        $this->baseurl/resetpassword/$resetlinkcode

";




    }

}
