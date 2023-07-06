<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
class mailCustom extends PHPMailer
{
public  function Email($option){
//    return $option;
	$mode='on';
	if(isset($option['from'])){
		$from = $option['from'];
	}else{
		$from = "support@quickvee.com";
	}
	if(isset($option['from_name'])){
		$from_name = $option['from_name'];
	}else{
		$from_name = "Quickvee";
	}
	if($mode=='on'){
			$this->isSMTP();
			$this->Debugoutput = 'html';
			$this->Host = 'smtp.gmail.com';
			$this->Port ='465'; //587
			$this->SMTPSecure = 'ssl';
			$this->SMTPAuth = true;
			$this->Username ='support@quickvee.com'; //admin@swiftpizza.com //malik.saleh@apprication.com
			$this->Password ='D@shwood'; //Mumbai@91 //Swift@786
			$this->From = $from;
			$this->FromName = $from_name;
			// $this->setFrom($option['from'],$option['from_name']);
			
			foreach ($option['to'] as $email=>$reciver_name)
			{
				//echo $email.">".$reciver_name."<br>";
				$this->addAddress($email,$reciver_name); //send to 	
			}
			if(isset($option['cc'])){
				$this->addCC($option['cc']);
			}
			if(isset($option['bcc'])){
				$this->addBCC($option['bcc']);
			}
			$this->Subject =$option['subject'];
			$this->Body = $option['body'];
            // this code is workig as your requirement
			// if($option['attachment']=='on' && isset($option['attachment']) ){
			// 	$this->addAttachment($option['attachment_path'],$option['attachment_fileName']); // send mail of sved copy in drive
			// 	//$this->AddStringAttachment($option['attachment_path'],$option['attachment_fileName']); //output from dompdf and send mail
			// }
			// if($option['attachment']=='multi'){
			// 	foreach ($option['attachment_path'] as $key => $value) {
			// 		$this->addAttachment($value,$option['attachment_fileName'][$key]);
			// 	}
			// }
			// this code is workig as your requirement
			$this->IsHTML(true);
			if($this->send()){
              $resonse=[
                  "status"=>200,
                  "message"=>"Please check your email for additional instructions to complete your password change request"
              ];
              echo json_encode($resonse);
            }else{
                $resonse=[
                    "status"=>400,
                    "message"=>"mail not send please check your id"
                ];
                echo json_encode($resonse);
                

            }
            
		} 
	
	}

} 
?>
