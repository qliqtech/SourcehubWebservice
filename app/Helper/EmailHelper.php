<?php


namespace App\Helper;

// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{

    public static function sendEmail($emailtosend,$fullnametosend,$message,$title){

        //   dd($emailtosend);//check//again

        $mail = new PHPMailer(true);
        $email = $emailtosend;
        $fullname = $fullnametosend;
        try {
            $mail->SMPTOptions =  array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true

                )
            );
            //Server settings
            //$mail->SMTPDebug = 2;                                       // Enable verbose debug output
            $mail->isSMTP();
            $mail->Host       = env("F_EMAIL_HOST");  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = env("F_EMAIL_ADDRESS");                   // SMTP username
            $mail->Password   = env("F_EMAIL_PASSWORD");                                 // SMTP password
            $mail->SMTPSecure = 'tls';   'ssl';                                   // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = '587';


            //Recipients
            $mail->setFrom('noreplyflair69@gmail.com', 'FLAIR');
            $mail->addAddress($email, $fullname);     // Add a recipient
            $mail->addReplyTo('noreplyflair69@gmail.com', 'FLAIR');
            //$mail->addCC('cc@example.com');

            // Attachments
            //$mail->addAttachment($file_location);         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $title;

            //   ob_start();




            $html = $message;// ob_get_clean();
            $mail->Body    = $html;
            $mail->AltBody = strip_tags($html);
            $mail->send();


            return 1;

            //   echo "Message sent successfully";

        } catch (Exception $e) {
            //   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";  // OR take you to start all over

            //   die();

            return 0;
        }


    }





    public static function sendEmail2($emailtosend,$fullnametosend,$message,$title){

        //   dd($emailtosend);




        //    dd($emailtosend);


        $mail = new PHPMailer(true);
        $email = $emailtosend;
        $fullname = $fullnametosend;
        try {
            $mail->SMPTOptions =  array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true

                )
            );
            //Server settings
            //$mail->SMTPDebug = 2;                                       // Enable verbose debug output
            $mail->isSMTP();
            $mail->Host       = "email-smtp.eu-west-1.amazonaws.com";  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = "AKIAXEX2TIXBFYQKXHVG";                     // SMTP username
            $mail->Password   = "BOD1P9AoxA4hZonne9zlAJBXH6MOf3ZAKP2OJpgryT7V";                                 // SMTP password
            $mail->SMTPSecure = 'tls';   'ssl';                                   // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = '587';


            //Recipients
            $mail->setFrom('notify@nss.gov.gh', 'NSS ');
            $mail->addAddress($email, $fullname);     // Add a recipient
            $mail->addReplyTo('notify@nss.gov.gh', 'NSS ');
            //$mail->addCC('cc@example.com');

            // Attachments
            //$mail->addAttachment($file_location);         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $title;

            //   ob_start();




            $html = $message;// ob_get_clean();
            $mail->Body    = $html;
            $mail->AltBody = strip_tags($html);
            $mail->send();


            return 1;

            //   echo "Message sent successfully";

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";  // OR take you to start all over

            return 0;
        }


    }





}
