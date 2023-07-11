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
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
          $this->load->model('Login');
          $this->load->model("login");
          $this->load->model('general_model');
        //   $this->load->library('email');
    }

    function register()
    {
        $first_name=trim($this->input->post("firstname"));
        $last_name=trim($this->input->post("lastname"));
        $mobile=trim($this->input->post("phone"));
        $password=md5(trim($this->input->post("password")));
        $Email=trim(strtolower($this->input->post("email")));
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
        $username=trim(strtolower($this->input->post("username")));
        $password=md5(trim($this->input->post("password")));
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        if($this->form_validation->run() == true)
        {
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

function realTimeEmailCheck()
{
    $emailValidate=trim(strtolower($this->input->post("emailCheck")));
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
            $email=trim(strtolower($this->input->post("email")));
            $data=$this->Login->UserData($email);
            if($data->num_rows() > 0)
            {
                $result_data=$data->row_array();
                // $name=$result_data['name'];
                $name="pinkesh";
                $this->general_model->generatePassword();
                $ver_code=md5($this->general_model->generatePassword());// THIS field has a doute
                $emailcode=base64_encode($ver_code.":|:".$email);
                $this->load->library('form_validation');
                $this->form_validation->set_rules('email', 'email', 'required|valid_email');
                // $this->form_validation->set_rules("email",'email','trim|required|xss_clean|valid_email|callback_chk_user');
                if($this->form_validation->run()==true)
                    {
                        $data=$this->db->query("update users set password_reset_code='".$ver_code."',password_reset_status='2' where email='$email'  ");
                        require_once('class/mailer/custom_mail.php');
                        $ses_name = $name;
                        $code_url="http://localhost:3000/forgotpassword/?vc=".$emailcode;
                        
                        include_once('class/reset_password.php');

                        $suB='Reset Password';
                       
                        // href='mailto:support@quickvee.com'
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
                        // print_r($EmailFirst);
                        // echo $this->db->last_query();
                    }
                    else
                    {
                        $response =array(
                            "status"=>400,
                            "message"=>"Some thing went wrong"
                        );
                        echo json_encode($response);
                       
                    }

            }else{
            $response =array(
                "status"=>400,
                "message"=>"Email-id is not registered with us."
            );
            echo json_encode($response);

            }

        }
        

        function reset_password_save(){
            $data="MjAxNmYwZDY2ZGI5MTIwMzk5NzRhNzE5YTA3OGViZmI6fDpyaW5rZXNoQG1lcmNoYW50ZWNoLmNvbQ==";//post vs will come
            $codeFilter=explode(":|:",base64_decode($data));
            $Username=$codeFilter[0];
            $vrCode=$codeFilter[1]; 
            $new_password= $this->input->post("new_password");
            $confirm_password=$this->input->post('confirm_password');
            // echo $new_password;
            // echo $confirm_password;
            $this->load->library('form_validation');
            // $this->form_validation->set_rules('new_password','Newpassword','New Password','trim|required|xss_clean');|callback_reset_chk
            // $this->form_validation->set_rules('confirm_password','Confirm Password','trim|required|xss_clean|callback_reset_chk');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('new_password', 'NewPassword', 'trim|required');
            $this->form_validation->set_rules('confirm_password', 'ConfPassword', 'trim|required|callback_reset_chk');
            // echo $this->form_validation->run();
            // exit();
            if($this->form_validation->run() == true){
                echo "valid";
                $data=$this->Login->reset_Password($Username,$new_password);
                $response=array(
                    "status"=>200,
                    "message"=>"Password updated successfully"
                );
                echo json_encode($response);

            }
            // else{
                // echo"invalid";
            //     $response=array(
            //         "status"=>400,
            //         "message"=>"Somethings went wrong"
            //     );
            //     echo json_encode($response);
            // }
        }
        function reset_chk(){
            $new_password=$this->input->post('new_password');
            $confirm_password=$this->input->post('confirm_password');
            if($new_password !=$confirm_password){
                $resonse=array(
                    "status"=>400,
                    "message"=>"password does not match"
                );
                echo json_encode($resonse);
                return false;

            }else{
                return true;
            }
        }
        // ------------------------------------------
        // chk_merchanteck login this function call when merchan fill user fill
        // e11fd1cc90e98ce06543f9963d4cb8f6
        function chk_merchant(){
                $username=$this->input->post("username");
                $this->load->model("login");
                $q=$this->login->chk_merchant($username);  
                if($q){
                    $response=array(
                        "status" =>200,
                        "message"=>true
                );
                echo json_encode($response);

                }else{
                    $response=array(
                        "status" =>400,
                        "message"=>false
                );
                echo json_encode($response);

                }            
        }
        // ------------------------------------------
        function create_session($ref_page="main", $guest=''){
            $username=$this->input->post("username");
            if($this->input->post("email"))
            {
            $username=$this->input->post("email");
             }
            $password=md5($this->input->post("password"));
            $merchant_login=$this->login->Merchant_login($username);
            $result=$merchant_login->row_array();

            if(!empty($result)){
               $this->session->set_userdata("store_status",$result['status']);
            }
            if( $result['oauth_provider']=='google'){
                redirect(base64_decode($this->input->get('google')));
                exit();
            }
            if( $result['oauth_provider']=='facebook'){
                redirect(base64_decode($this->input->get('facebook')));
                exit();
            }
            $ref=$this->input->post('ref');
            


        }
}
?>