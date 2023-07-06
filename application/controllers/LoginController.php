<?php
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// header('Access-Control-Allow-Origin: *');
// // if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// // header('Access-Control-Allow-Origin: *');
// if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
//     header('Access-Control-Allow-Headers:  Origin, X-Requested-With, Content-Type, Accept');
//     exit;
// }
// ==========================================================================
// defined('BASEPATH') OR exit('No direct script access allowed');

// header('Access-Control-Allow-Origin: *');

// if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
//     header('Access-Control-Allow-Headers: Content-Type');
//     exit;
// }
// ============================================================================
class LoginController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //  parent::__construct($config);

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
          $this->load->model('Login');
          $this->load->model('general_model');
        //   $this->load->library('email');
    }

    function register()
    {
        $first_name=trim($this->input->post("firstname"));
        $last_name=trim($this->input->post("lastname"));
        $mobile=trim($this->input->post("phone"));
        $password=md5(trim($this->input->post("password")));
        $Email=trim($this->input->post("email"));
         // echo $first_name."  ".$last_name."  ".$mobile." ".$password." ".$Email;
         // exit();
        $this->form_validation->set_rules('firstname', 'Firstname', 'required|alpha');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required|alpha');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $data=array(
            'f_name'=>$first_name,
            'l_name'=>$last_name,
            'phone'=>$mobile,
            'password'=>$password,
            'email'=>$Email
        );

        if ($this->form_validation->run() == true)
                {
                    
                    $numRow=$this->Login->getRequest($data);
                    if($numRow->num_rows() > 0){
                      $response=[
                        "status"=>400,
                        "message"=>"Email already exist"
                      ];
                      echo json_encode($response);
                    }else{
                         $dataRow=$this->Login->getNewRequest($data);
                         $response=[
                            "status"=>200,
                            "message"=>"new record is added"
                         ];
                         echo json_encode($response);
                    }
                }
                else
                {
                    $response=[
                        'status'=>400,
                        'message'=>"Please Enter valid record"
                    ];
                    echo json_encode($response);
                }
    }
	function login()
    {
        // print_r($this->input->post());
        // exit();
        $username=trim($this->input->post("username"));
        $password=md5(trim($this->input->post("password")));
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        if($this->form_validation->run() == true)
        {
                    // $this->load->model('Login');
                    $numRow=$this->Login->getUserLogin($username,$password);
                    if($numRow->num_rows() > 0)
                    {
                       $data = array(
                                        'status' => 200,
                                        'message' => 'Data saved successfully.'
                                    );

                                $json = json_encode($data);
                                echo $json;
                    }
                    else
                    {
                                $response=[
                                            "status"=>400,
                                            "message"=>'invalid username and password'
                                          ];
                                          echo json_encode($response);
                    }
        }else
        {
          $response=[
                                "status"=>400,
                                "message"=>'username && password required'

                              ];
                        echo json_encode($response);
        }
    }
        // print_r( $numRow->num_rows());
        // if($numRow > 0){
        //   echo"hello user"
        // }else{
        //     echo"wrong"; 
        // }
        // echo $num_row;
        // $query = $this->db->select('email')
        //         ->where('email', $username)
        //          ->where('password',$password)
        //         ->get('users');
        // // print_r($query->num_rows() > 0);
        // if($query->num_rows() > 0){
        //     echo"hello";
        // }else{
        //     // echo"wrong";
        //     $response=[
        //         "status"=>400,
        //         "message"=>'invalid username and password'

        //     ];
        //     // print_r($response);
        //     echo json_encode($response);
        // }

        // $response=[
        //     "status"=>200,
        //     "message"=>"you are done good job"
        // ];
        // row()
        // echo json_encode($response);
        // $username=$this->input->post("username");
        // $password=$this->input->post("password");
        // $email=$this->input->post("email");
        // $data=array(
        //     "username"=>$username,
        //     "password"=>$password,
        //     "email"=>$email
        // );
        // $query = $this->db->select('username')
        //         ->where('username', $username)
        //         ->get('userrecord');
        // // $sql=$this->db->insert('userrecord', $data);
        // echo $query->num_rows();


        // echo $this->db->last_query();
        // print_r($data);
        
// ==========================================================================================================
        // $sql="INSERT INTO `userrecord`(`username`, `password`, `email`) VALUES ('".$post_data['username']."','".$post_data['password']."',
    // '".$post_data['email']."')";
    // $this->db->query($sql);

        // print_r($username." ".$username." ".$password." ".$email);

        // print_r($this->input->post());

		// echo"hello rinkesh";
    // $post_data = json_decode(file_get_contents('php://input'), true);
    // echo $post_data;
    
    // $sql = "SELECT `username` FROM `userrecord` WHERE  `username`='".$post_data['username']."'";
    // $result=$this->db->query($sql);
    // print_r($sql);
    // $result=$this->db->query($sql);
    // $username=strtolower($post_data['username']);
    // echo $result;
    
    // $sql="INSERT INTO `userrecord`(`username`, `password`, `email`) VALUES ('".$post_data['username']."','".$post_data['password']."',
    // '".$post_data['email']."')";
    // $this->db->query($sql);


    // print_r($sql);

    // print_r($post_data['username']);
    // print_r($post_data['password']);
    // print_r($post_data['email']);
    // $post_data['stdId'];
//     $Data = json_encode((file_get_contents('php://input')));
//     echo $id    = $this->input->input_stream("username", true);
//    echo $Data;
    // $name = $this->input->post('username');
    // print_r($name);
    // $json = json_decode($data, true);
    // echo $json['username'];
    // foreach($this->input->post("data") as $day){
    //     echo $day;
    // }
    
    // print_r($this->input->post('data'));
    // print_r($this->input->post('name', set_value('name', $data['username'])));
    // echo form_input('username');


function realTimeEmailCheck()
{
    $emailValidate=trim($this->input->post("emailCheck"));
    $data=array('email'=>$emailValidate);

    $numRow=$this->Login->getRequest($data);
    // echo $numRow;
                if($numRow->num_rows() > 0){
                      $response=[
                        "status"=>400,
                        "message"=>"Email already exist"
                      ];
                      echo json_encode($response);
                    }else{
                         // $dataRow=$this->Login->getNewRequest($data);
                         $response=[
                            "status"=>200,
                            // "message"=>"new record is added"
                         ];
                         echo json_encode($response);
                    }
}
function ResetPassword(){
    $newPassword=trim($this->input->post("newPassword"));
    echo $newPassword;
}
// function sendMailData(){
    
//     // $this->load->library('email');
//     $to = 'rinkeshyadav72@gmail.com';
//     $subject = 'Test Email';
//     $message = 'This is a test email';

    

// if ($this->sendEmail($to, $subject, $message)) {
//     echo 'Email sent successfully.';
// } else {
//     echo 'Email sending failed.';
// }
// }
// public function sendEmail($to, $subject, $message) {
// require 'vendor/autoload.php';

//     $mail = new PHPMailer(true);

//     try {
//         // Server settings
//         $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output (change to DEBUG_SERVER for detailed debug output)
//         $mail->isSMTP();
//         $mail->Host = 'sandbox.smtp.mailtrap.io'; // SMTP host
//         $mail->SMTPAuth = true;
//         $mail->Username = '78451e28f51767'; // SMTP username
//         $mail->Password = 'dcdf771dacf863'; // SMTP password
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
//         $mail->Port = 465; // TCP port to connect to
//         $mail->SMTPSecure='ssl';

//         // Recipients
//         $mail->setFrom('rinkeshyadav72@gmail.com', 'Rinkesh yadav'); // Sender's email and name
//         $mail->addAddress($to); // Recipient's email
//         // echo($to);
//         // Content
//         $mail->isHTML(true); // Set email format to HTML
//         $mail->Subject = $subject;
//         $mail->Body = $message;

//         $mail->send();
//         echo 'Mailer Error: ' . $mail->ErrorInfo;
//         return true;
//     } catch (Exception $e) {
//         // Handle exception and log errors
//         return false;
//     }
// }
function addMyProfileImage(){
        $file_data=$this->input->post("filename");
        $filename=explode(".",$file_data);
        $base64Image = $this->input->post('imageCrop');
    
        $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $base64Image = str_replace(' ', '+', $base64Image);
        $imageData = base64_decode($base64Image);
        $filename = uniqid() . '.png';
        $filePath = $_SERVER['DOCUMENT_ROOT'].'/quickvee/public/image/' . $filename;
        file_put_contents($filePath, $imageData);
        $response=[
            'status'=>200,
            'filename'=>$filename,
            'message'=>'Image saved successfully!'
        ];
        echo json_encode($response);
}
function addMyProfilePage(){ 
    $fullname=trim($this->input->post("f_name"));
    $lastname=trim($this->input->post("l_name"));
    $email=trim(strtolower($this->input->post("email")));
    $phone=trim($this->input->post("phone"));
    $IDNumber=trim($this->input->post("IDNumber"));
    $cartType=trim($this->input->post("cartType"));
    $Expires=trim($this->input->post("expire"));
    $age=trim($this->input->post("age"));
    $imageName=trim($this->input->post("imageName"));
    echo $fullname." ".$lastname." ".$email." ".$phone." ".$IDNumber." ".$cartType." ".$Expires."   ".$age." ".$imageName;
}
function forgot_password(){

}
function reset_password_send()
        {
            $response=array();
            $email=trim(strtolower($this->input->post("email")));
            $data=$this->Login->UserData($email);
            
            // print_r($name);
            // echo $data->num_rows();
            // exit();
            if($data->num_rows() > 0)
            {
                $result_data=$data->row_array();
                $name=$result_data['name'];
                // $name="pinkesh";
                $this->general_model->generatePassword();
                $ver_code=md5($this->general_model->generatePassword());// THIS field has a doute
                $emailcode=base64_encode($ver_code.":|:".$email);
                $this->load->library('form_validation');
                $this->form_validation->set_rules('email', 'email', 'required');
                // $this->form_validation->set_rules("email",'email','trim|required|xss_clean|valid_email|callback_chk_user');
                if($this->form_validation->run()==true)
                    {
                        $data=$this->db->query("update users set password_reset_code='".$ver_code."',password_reset_status='2' where email='$email'  ");
                        require_once('class/mailer/custom_mail.php');
                        $suB='Reset Password';
                        $emailBody="<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' style='max-width:600px;margin:auto;border-spacing:0;border-collapse:collapse;background:white;border-radius:0px 0px 10px 10px'>
                        <tbody style='background-color:#fafafa;'>
                        <tr style='background-size:cover'>
                        <td colspan='3' style='text-align:center;border-collapse:collapse;border-radius:10px 10px 0px 0px; color:white; height:50px;background-color:#0a64f9;padding:10px'>
                        <img src='https://ci6.googleusercontent.com/proxy/KMcbu8zrXoyWKSbPbnxVubGTx7PgYRs0S09MuME0p2pHSnUzhBCauFlLKn8LlYdveuxEOkeZehwgsghRc06WBSAvXg=s0-d-e1-ft#https://sandbox.quickvee.com/images/maillogo.png' width='80' class='CToWUd'>
                        </td>
                        </tr>
                        <tr style='margin-bottom:10px;display:block;margin-top:30px;'>
                                <td style='padding: 0 20px;'>Hello .'".$name."',</td>
                        </tr>
                        <tr style='margin-bottom:10px;display:block;'>
                        <td style='padding: 0 20px;'>We have received a request to reset the password for your account.To reset your password, please </td>
                        </tr>
                        </tbody>
                        </table>";
                        $mailClass=new mailCustom;
                        $options=array(
                        'from'=>'admin@quickvee.com',
                        'from_name'=>'Quickvee',
                        'to'=>array($email=>$name),
                        'subject'=>$suB,
                        'body'=>$emailBody,
                        'attachment'=>'off',
                        );
                        $EmailFirst=$mailClass->Email($options);
                        print_r($EmailFirst);
                        // echo $this->db->last_query();
                    }
                    else{
                    echo"hello";
                    }

            }else{
            $response =array(
                "status"=>400,
                "message"=>"invalid email"
            );

            }
            echo json_encode($response);
        


        }
}
?>