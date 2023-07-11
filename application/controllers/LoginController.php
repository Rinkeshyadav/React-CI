<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller{
    public $data=array();
	function __construct(){
		header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct();
		  
        $this->load->model('general_model');
        $this->data=$this->general_model->load_session_data();
        $this->load->model('login_model');
        $this->load->model('users_model');
        $this->load->model('mail_model');
        $this->load->model('CloverApi_model');
        $this->load->library('facebook');
        		
		// Google Project API Credentials
		$this->data['clientId'] = '517539106165-7nkgr0uikkp7n3v176irnun7tiqk97mc.apps.googleusercontent.com';		
		//860330222516-hn3e8krp51nl4vn61ra3scm4iu69re8c.apps.googleusercontent.com
        $this->data['clientSecret'] = 'tdEoXl-k02dwvyiGjNgsDS7O';
        //RLIesbdeLwDnWr6x3XHimvJz
        $this->data['redirectUrl'] = base_url().'login/google_callback';
	}
	
	function index(){
		//echo "<pre>";print_r($_SESSION);die();
		if($this->session->userdata('logged_in')==TRUE && isset($_SESSION['logged_in']['tmp_id'])){
			if(isset($_GET['ref'])){                
                redirect(base64_decode($_GET['ref']));    
            }else{
			    redirect('dashboard');
            }
		}
		else
		{

            if(isset($_GET['ref']))
            {
				$this->data['ref']=$_GET['ref'];   
            }
            else{
                $this->data['ref']="";
            }

			$this->load->view("login",$this->data);
		}
	}


	function customer_login()
	{
		//$this->session->unset_userdata('pg_url');
		//$refer =  $this->agent->referrer();
		//echo "<pre>";print_r($this->session->userdata());die();
		// if(!isset($this->data['ses_id'])){
		// 	$this->data['page']="middle";
		// }else{
			$this->data['page']="front";
		// }
		$this->data['ind_brn'] = $this->session->userdata('ind_brn');	
		// echo '<pre>';print_r($ind_brn);exit();
		// Include the google api php libraries
		include APPPATH."libraries/google-api-php-client/Google_Client.php";
		include APPPATH."libraries/google-api-php-client/contrib/Google_Oauth2Service.php";
		// Google Client Configuration
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to quickvee.com');
        $gClient->setClientId($this->data['clientId']);
        $gClient->setClientSecret($this->data['clientSecret']);
        $gClient->setRedirectUri($this->data['redirectUrl']);
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        $this->data['authUrl'] = $gClient->createAuthUrl();

        // $fbuser = '';
			
		// Get login URL
        $this->data['authFbUrl'] =  $this->facebook->login_url();
        if($this->session->userdata('callback_error')){
			$this->data['callback_error'] = $this ->session->userdata('callback_error');
			$this->session->unset_userdata('callback_error');
		}
        $this->load->view('mainlayout/login_signup',$this->data);
	}

	function employee_login()
	{
		$this->data['page']="front";
		// }
		$this->data['ind_brn'] = $this->session->userdata('ind_brn');	
		// echo '<pre>';print_r($ind_brn);exit();
		// Include the google api php libraries
		include APPPATH."libraries/google-api-php-client/Google_Client.php";
		include APPPATH."libraries/google-api-php-client/contrib/Google_Oauth2Service.php";
		// Google Client Configuration
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to quickvee.com');
        $gClient->setClientId($this->data['clientId']);
        $gClient->setClientSecret($this->data['clientSecret']);
        $gClient->setRedirectUri($this->data['redirectUrl']);
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        $this->data['authUrl'] = $gClient->createAuthUrl();

        // $fbuser = '';
			
		// Get login URL
        $this->data['authFbUrl'] =  $this->facebook->login_url();
        if($this->session->userdata('callback_error')){
			$this->data['callback_error'] = $this ->session->userdata('callback_error');
			$this->session->unset_userdata('callback_error');
		}
        $this->load->view('mainlayout/employee_login',$this->data);
	}

	function google_callback(){
		// Include the google api php libraries
		include APPPATH."libraries/google-api-php-client/Google_Client.php";
		include APPPATH."libraries/google-api-php-client/contrib/Google_Oauth2Service.php";
		// Google Client Configuration
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to quickvee.com');
        $gClient->setClientId($this->data['clientId']);
        $gClient->setClientSecret($this->data['clientSecret']);
        $gClient->setRedirectUri($this->data['redirectUrl']);
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        //echo "<pre>";print_r($this->cookie->userdata());die();
		if ($this->input->get("code")) {

            $gClient->authenticate();
            $this->session->set_userdata('token', $gClient->getAccessToken());
            //redirect($redirectUrl);
        }

        $token = $this->session->userdata('token');
        // echo '<pre>';print_r($token);exit();
        if (!empty($token)) {
            $gClient->setAccessToken($token);
        }
    
        if ($gClient->getAccessToken()) {
        	
            $userProfile = $google_oauthV2->userinfo->get();

            // Preparing data for database insertion
			$userData['oauth_provider'] = 'google';
			$userData['oauth_uid'] = $userProfile['id'];
            $userData['f_name'] = $userProfile['given_name'];
            $userData['l_name'] = $userProfile['family_name'];
            $userData['email'] = $userProfile['email'];
            $userData['name'] = $userData['f_name']." ".$userData['l_name'];

			/*$userData['gender'] = $userProfile['gender'];            
			$userData['locale'] = $userProfile['locale'];
            $userData['profile_url'] = $userProfile['link'];
            $userData['picture_url'] = $userProfile['picture'];*/
			// Insert or update user data
            $userID = $this->users_model->checkUser($userData);

            if(!empty($userID)){
                $this->data['userData'] = $userData;
                $this->session->set_userdata('userData',$userData);
                
				if($this->session->userdata('order_id')){
					$this->data['mid']=$this->session->userdata('mid');
					$this->data['row']=$this->users_model->get_DataM($this->data['mid']);
			        $sess_array=array(
						'tmp_id'=>$userID,
						'tmp_username'=>$userData['email'],
						'tmp_name'=>$userData['f_name']." ".$userData['l_name'],
						'tmp_fname'=>$userData['f_name'],
						'tmp_user_type'=>'customer',
						'tmp_flag'=>$this->data['row']['flag'],
					);
					$this->session->set_userdata('logged_in',$sess_array);
			        
            		$this->data['order_id']=$this->session->userdata('order_id');
            		$this->data['orderMethod']=$this->session->userdata('orderMethod');
            		$this->load->view('mainlayout/move_to_pay',$this->data);
		                        		
				}else{	
					$sess_array=array(
						'tmp_id'=>$userID,
						'tmp_username'=>$userData['email'],
						'tmp_name'=>$userData['f_name']." ".$userData['l_name'],
						'tmp_fname'=>$userData['f_name'],
						'tmp_user_type'=>'customer',
					);
					$this->session->set_userdata('logged_in',$sess_array);		
					/*if(isset($_SESSION['url'])){
						echo '<pre>';print_r('else');exit();
		            	redirect($_SESSION['url']);
		            }else{
		            	redirect('home');
		            }*/	
		            //echo "<pre>";print_r($this->session->userdata());die;
		            if($this->session->userdata('pg_url'))
		            {			
		            	$checkout_url = base_url()."Findstore/checkout";		
						if($this->session->userdata('pg_url')== $checkout_url)
						{
							$this->session->set_userdata("checked_out", 1);
						}
						redirect($this->session->userdata('pg_url'));
					}
		            else
		            {
		            	redirect(base_url().'findstore/merchant'.'/'.$this->session->userdata('mid').'?orderMethod='.$this->session->userdata('orderMethod').'?distance='.$this->session->userdata('distance')); 
		            }
					
				}
            }else {
                $this->data['userData'] = array();
                $callback_error = "Email address already in use";
				$this->session->set_userdata('callback_error',$callback_error);
               	redirect('login/customer_login');
            }
        }else{
        	redirect('login/customer_login');
        }
	}

	function facebook_callback(){
		$userData = array();
		
		// Check if user is logged in
	
		if($this->facebook->is_authenticated()){
						
			// Get user facebook profile details
			$userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture');
			
            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['oauth_uid'] = $userProfile['id'];
            $userData['f_name'] = $userProfile['first_name'];
            $userData['l_name'] = $userProfile['last_name'];
            $userData['email'] = $userProfile['email'];
            $userData['name'] = $userData['f_name']." ".$userData['l_name'];
            /*$userData['gender'] = $userProfile['gender'];
            $userData['locale'] = $userProfile['locale'];
            $userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['id'];
            $userData['picture_url'] = $userProfile['picture']['data']['url'];*/
			
            // Insert or update user data
            $userID = $this->users_model->checkUser($userData);
			
			// Check user data insert or update status
            if(!empty($userID)){
                $data['userData'] = $userData;
                $this->session->set_userdata('userData',$userData);
                $sess_array=array(
					'tmp_id'=>$userID,
					'tmp_username'=>$userData['email'],
					'tmp_name'=>$userData['f_name']." ".$userData['l_name'],
					'tmp_fname'=>$userData['f_name'],
					'tmp_user_type'=>'customer'
				);
				$this->session->set_userdata('logged_in',$sess_array);				
				if($this->session->userdata('order_id')){
					$this->data['mid']=$this->session->userdata('mid');
            		$this->data['order_id']=$this->session->userdata('order_id');
            		$this->data['orderMethod']=$this->session->userdata('orderMethod');
            		$this->load->view('mainlayout/move_to_pay',$this->data);
				}else{
					if($this->session->userdata('pg_url')){					
						redirect($this->session->userdata('pg_url'));
					}
		            else
		            {				
						redirect('home');
					}
				}
            } else {
               $this->data['userData'] = array();
               $callback_error = "Email address already in use";
				$this->session->set_userdata('callback_error',$callback_error);
               			redirect('login/customer_login');
            }
        }
        else{        	
        	redirect('login/customer_login');
        }
	}

	function register($guest='')
	{
		if($this->input->get('mid')){
			$this->data['order_id']=$this->input->get('order_id');
        	$this->data['mid']=$this->input->get('mid');
        	$this->data['page']=$this->input->get('page');
        	$this->data['orderMethod']=$this->input->get('orderMethod');
		}else{
			$this->data['page']="front";
		}
		$this->data['guest']=$guest;
		//echo "<pre>";print_r($this->data);die();
        $this->load->view('mainlayout/new-signup',$this->data);
	}
	

	function merchant_register()
	{
		$res = $this->db->query("SELECT DISTINCT State FROM mytable ORDER BY State");
		$this->data['states'] = $res->result_array();
		// print_r($data);die();
        $this->load->view('mainlayout/newmerchant_signup',$this->data);
	}


	function create_session_emp()
	{	
		
		$username=$this->input->post('username');
		$pin = $this->input->post('pin');

		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('username','Username','trim|required|xss_clean');
		$this->form_validation->set_rules('pin','Pin','trim|required|xss_clean|callback_check_edb');	
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		if($this->form_validation->run()==false)
		{
			$this->load->view('mainlayout/employee_login',$this->data);
		}
		else
		{
			$username = trim($username);
			$pin = trim($pin);
		    $q=$this->db->query("select users.id, employee.email, users.f_name, employee.id as eid, employee.role,employee.merchant_id,employee.menu_list,users.flag, employee.admin_id, COUNT(employee.merchant_id) as count, users.name  from employee INNER JOIN users ON employee.merchant_id=users.merchant_id where employee.email = '$username' AND employee.pin ='$pin'" );
	        $row=$q->row_array();

	        if($row['count'] == 1)
	        {
				$this->db->query("update employee set is_login='1',login_time='".date("Y-m-d H:i:s")."' where email='$username' ");
				$admin_id = $row['admin_id'];

				$sess_array=array(
					'tmp_id'=>$row['id'],
					'tmp_eid'=>$row['eid'],
					'tmp_emenulist'=>$row['menu_list'],
					'tmp_username'=>$row['email'],
					'tmp_name'=>$row['name'],
					'tmp_fname'=>$row['f_name'],
					'tmp_user_type'=>$row['role'],
                    'tmp_m_id'=>$row['merchant_id'],
                    'tmp_admin_id'=>$admin_id,
                    'tmp_m_token'=>$row['merchant_token'],
                    'tmp_flag'=>$row['flag'],
				);

				$this->session->set_userdata('logged_in',$sess_array);
			}
			else
			{
				$this->session->set_flashdata('message','Invalid Username OR Pin');
				$this->load->view('mainlayout/employee_login',$this->data);
			}
			//$login_info = $this->session->userdata("logged_in");
			//echo "<pre>";print_r($_SESSION);die();
			redirect('rout');
		}		
	}



	function check_edb()
	{
		$username=$this->input->post('username');
	
		$password=$this->input->post('pin');
	
		$result=$this->login_model->elogin($username,$password);
		
		if($result && ($username!="" || $password!="" ) )
		{
			$sess_array=array();
			foreach($result as $row)
			{
				$sess_array=array(
					'tmp_id'=>$row->id,
					'tmp_username'=>$row->email,
					'tmp_name'=>$row->name,
					'tmp_fname'=>$row->f_name,
					'tmp_user_type'=>$row->role,
                    'tmp_m_id'=>$row->merchant_id,
                    'tmp_flag'=>$row->flag,
				);
			}
		
			$this->session->set_userdata('logged_in',$sess_array);
			return true;
		}
		else
		{
			$this->form_validation->set_message('check_edb', 'Invalid username or password');
    		return false;
		}
	}





	function create_session($ref_page="main",$gueste='')
	{	
		
		 //echo '<pre>';print_r($ref_page);echo '<br>';die("debug 260");
		$xe = $this->session->userdata('logged_in')['tmp_user_type'];
		$this->session->set_userdata('login_type',$this->input->post('login_via_superadmin'));
	    $guest = $this->input->post('guest');
		
		if($this->input->post('guest') == ''){
			$guest = $gueste;
		}
	
		if($gueste!='checkregi'){
			$this->session->set_userdata('is_guest', $this->input->post('guest'));
		}
		

		$this->session->unset_userdata('store_status');
		if ($this->input->post('merchant_login') || $this->session->userdata('login_type')=='login_via_superadmin') 
		{
			$this->session->unset_userdata('token');
			$this->session->unset_userdata('userData');
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('mid');
			$this->session->unset_userdata('orderMethod');
			$this->session->unset_userdata('pg_url');
			$this->session->unset_userdata('mid');
			$this->session->unset_userdata('merchant_logo');
			$this->session->unset_userdata('checked_out');
		}
		
		$username=$this->input->post('username');
		if($this->input->post('email')){
			$username = $this->input->post('email');
		}
		$password = md5($this->input->post('password'));
		//echo $username;die;
		$q=$this->db->query("select id,oauth_provider,merchant_id,status,clover_customer_id from users where email='$username'  AND password!=NULL");
        $row=$q->row_array();
        
        if (!empty($row)) {
        	$this->session->set_userdata('store_status', $row['status']);
        }
        
      	if($row['oauth_provider']=='google')
      	{	
       		redirect(base64_decode($this->input->get('google')));
       		exit();
       	}

   		if($row['oauth_provider']=='facebook')
   		{
   			redirect(base64_decode($this->input->get('facebook')));
   			exit();
   		}

   		 $ref=$this->input->post('ref');
   		
		$this->data['page']=$ref_page;  			
			
		$this->load->library('form_validation');
		if($guest=='yes'){
			$this->form_validation->set_rules('email','Username','trim|required|xss_clean');
			$this->form_validation->set_rules('password','Password','trim|xss_clean|callback_check_db');
		}
		else if ($gueste=='checkregi') {
			$this->form_validation->set_rules('email','Username','trim|xss_clean');
			$this->form_validation->set_rules('password','Password','trim|xss_clean|callback_check_db'); 
		}
		else
		{
			$this->form_validation->set_rules('username','Username','trim|required|xss_clean');
			$this->form_validation->set_rules('password','Password','trim|required|xss_clean|callback_check_db');	
		}
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
   			if($this->form_validation->run()==false)
			{
				
				//echo " flag = ".$this->auth->get_sesData('tmp_flag');echo " inside ";die(" ");
				if($ref_page=="middle" ||  $ref_page=="front")
				{
					$this->data['order_id']=$this->input->post('order_id');
	        		$this->data['mid']=$this->input->post('mid');
	        		$this->data['orderMethod']=$this->input->post('orderMethod');
					$this->load->view('mainlayout/login_signup',$this->data);
				}
				else
				{	
					$this->data['ref']=$ref;
					// Include the google api php libraries
					include APPPATH."libraries/google-api-php-client/Google_Client.php";
					include APPPATH."libraries/google-api-php-client/contrib/Google_Oauth2Service.php";
					// Google Client Configuration
			        $gClient = new Google_Client();
			        $gClient->setApplicationName('Login to quickvee.com');
			        $gClient->setClientId($this->data['clientId']);
			        $gClient->setClientSecret($this->data['clientSecret']);
			        $gClient->setRedirectUri($this->data['redirectUrl']);
			        $google_oauthV2 = new Google_Oauth2Service($gClient);
			       
			        $this->data['authUrl'] = $gClient->createAuthUrl();

			        $this->data['authFbUrl'] =  $this->facebook->login_url();
			        if($this->session->userdata('callback_error'))
			        {
						$this->data['callback_error'] = $this->session->userdata('callback_error');
						$this->session->unset_userdata('callback_error');
					}
					if($this->input->post('merchant_login'))
					{
						$this->load->view('login',$this->data);
					}
					elseif ($this->input->post('consumer_login')) 
					{
						$this->data['order_id']=$this->input->post('order_id');
	        			$this->data['mid']=$this->input->post('mid');
	        			$this->data['orderMethod']=$this->input->post('orderMethod');
						$this->load->view('mainlayout/login_signup',$this->data);
					}
					else
					{
						$this->load->view('login',$this->data);
					}
										
				}
			}
			else
			{
	            if($ref!='')
	            {
					// die("Debug 373");
	            	if($row['merchant_id'] != "" && $row['merchant_id'] != 'no_id')
	            	{
						// die("debug 376");
			   			if($this->input->post('orderPage') == 'order_page'){
			   				$this->data['order_id']=$this->input->post('order_id');
					        $this->data['mid']=$this->input->post('mid');
					        $this->data['orderMethod']=$this->input->post('orderMethod');
					        $this->data['orderPage']=$this->input->post('orderPage');
					        $this->data['error_msg']='Please login as consumer';
							$this->load->view('mainlayout/login_signup',$this->data);
			   			}   			
			   		}
			   		else
			   		{
						// die("debug 388");
			   			$this->data['mid']=$this ->session->userdata('mid');
						$this->data['row']=$this->users_model->get_DataM($this->data['mid']);

						$a = $this->session->userdata('logged_in');
							//echo '<pre>';print_r($a);die;
						$a['tmp_flag'] = $this->data['row']['flag'];
						$this->session->set_userdata('logged_in',$a);		
						$login_info = $this->session->userdata("logged_in");
						//if($login_info["tmp_id"])
							$this->session->set_userdata("checked_out", 1);
			   			redirect(base64_decode($ref),'refresh');
			   		}	                
	            }
	            else if($this->session->userdata('pg_url'))
	            {
					// die("Debug 400");	
					$checkout_url = base_url()."Findstore/checkout";
					if($this->session->userdata('pg_url')== $checkout_url)
					{
						$this->session->set_userdata("checked_out", 1);
					}
	            	redirect($this->session->userdata('pg_url'));
				}
	            else
	            {
					// die("Debug 405");
					$check_user = $this->db->query('SELECT user_type,merchant_id FROM users WHERE email="'.$username.'" AND password="'.$password.'"')->row_array();
					//echo $this->db->last_query();die();
					if(is_array($check_user) && $check_user['user_type']=='manager')
					{
						$this->session->set_userdata('login_type','manager');
						//echo $check_user['merchant_id'];die();
						if(strpos($check_user['merchant_id'] , ',') !== false) 
						{
							//echo "hiii";die();
						    $this->manager_store($check_user['merchant_id']);
						}
						else
						{
							$row=$this->users_model->get_DataM($check_user['merchant_id']);
							$admin_id = $row['merchant_id'];
        					if($row['clover_customer_id']!='no_id' || $row['clover_customer_id']!='' || $row['clover_customer_id']!=null)
        					{
        						$admin_id = $row['clover_customer_id'];
        					}
	        					$sess_array=array(
									'tmp_id'=>$row['id'],
									'tmp_username'=>$row['email'],
									'tmp_name'=>$row['name'],
									'tmp_fname'=>$row['f_name'],
									'tmp_user_type'=>$row['user_type'],
				                    'tmp_m_id'=>$row['merchant_id'],
				                    'tmp_admin_id'=>$admin_id,
				                    'tmp_m_token'=>$row['merchant_token'],
				                    'tmp_flag'=>$row['flag']
								);

							$this->session->set_userdata('logged_in',$sess_array);
							$login_info = $this->session->userdata("logged_in");

							if($login_info["tmp_id"])
								$this->session->set_userdata("checked_out", 1);
	        				redirect('rout');
						}
					}
					else
					{
						//die("Debug 405");
						$username = trim($username);
		            	$q=$this->db->query("select COUNT(merchant_id) as count from users where email='$username' AND user_type='merchant' ");
	        			$row=$q->row_array();
	        			//echo $row['count'];die();
	        			if($row['count'] > 1){
	        				$this->merchant_store($username);
	        			}else{
	        				if($row['count'] == 1){
	        					$q_id = $this->db->query("select merchant_id from users where email='$username' AND user_type='merchant'");
	        					//echo $this->db->last_query();die;
	        					$q_row=$q_id->row_array();
	        					
							if(trim($xe) != 'superadmin'){
								$this->db->query("update users set is_login='1',login_time='".date("Y-m-d H:i:s")."' where email='$username'  ");
							}
							
	        					$row=$this->users_model->get_DataM($q_row['merchant_id']);

	        					$admin_id = $row['merchant_id'];
	        					if($row['clover_customer_id']!='no_id' || $row['clover_customer_id']!='' || $row['clover_customer_id']!=null)
	        					{
	        						$admin_id = $row['clover_customer_id'];
	        					}

	        					$sess_array=array(
									'tmp_id'=>$row['id'],
									'tmp_username'=>$row['email'],
									'tmp_name'=>$row['name'],
									'tmp_fname'=>$row['f_name'],
									'tmp_user_type'=>$row['user_type'],
				                    'tmp_m_id'=>$row['merchant_id'],
				                    'tmp_admin_id'=>$admin_id,
				                    'tmp_m_token'=>$row['merchant_token'],
				                    'tmp_flag'=>$row['flag'],
								);

								$this->session->set_userdata('logged_in',$sess_array);
							
	        				}
							$login_info = $this->session->userdata("logged_in");
							if($login_info["tmp_id"])
								$this->session->set_userdata("checked_out", 1);
	        				redirect('rout');
	        			}        	
					}        			            	

	            }
				
			}
   				
	}

	function merchant_store($email){

		$this->session->unset_userdata('logged_in');
		
		$query=$this->db->query("select 
                merchant_id,merchant_token,flag,img,name,email,a_zip,
                a_address_line_1,a_address_line_2,a_address_line_3,a_city,a_state,a_country,clover_customer_id 
                from users where email='$email' AND user_type='merchant' ");
		$this->data['rows']=$query->result_array();

		$this->load->view('merchant_store.php',$this->data);
	}

	function manager_store($merchant_id)
	{
		$this->session->unset_userdata('logged_in');
		$final_array = array();
		$m_id = explode(',', $merchant_id);
		
		for ($i=0; $i < sizeof($m_id) ; $i++) 
		{ 
			$query=$this->db->query("select 
            merchant_id,merchant_token,flag,img,name,email,a_zip,
            a_address_line_1,a_address_line_2,a_address_line_3,a_city,a_state,a_country 
            from users where merchant_id = '".$m_id[$i]."'");
            $merchant = $query->row_array();
            array_push($final_array, $merchant);
		}
		
		

		$this->data['rows']=$final_array;

		$this->load->view('merchant_store.php',$this->data);
	}
		
	
	function merchants(){

		if($this->session->userdata('logged_in')==TRUE){
			// echo '<pre>';print_r($this->session->userdata('logged_in'));exit();
			if(isset($_GET['ref'])){
                redirect(base64_decode($_GET['ref']));    
            }
            else
            {
			    redirect('settings/profile');
            }
		}
		else
		{	

			$merchant_id=$this->input->get('merchant_id');			
			$client_id=$this->input->get('client_id');
			$code=$this->input->get('code');
			$client_secret='7a36bd36-2565-2e46-f48a-0ee353ac03eb';
			$response = file_get_contents('https://clover.com/oauth/token?client_id='.$client_id.'&client_secret='.$client_secret.'&code='.$code);
			$response=json_decode(trim($response),true);
			// echo '<pre>';print_r($response);exit();
			if($response['access_token']!=""){
				$row=$this->users_model->get_DataM($merchant_id);
				
				$username=$this->db->query("select email from users where merchant_id='$merchant_id' ")->row()->email;
				//
				$q=$this->db->query("select COUNT(merchant_id) as count from users where email='$username' ");
        			$row1=$q->row_array();
        			
        			if($row1['count'] > 1){
        				$this->merchant_store($username);
        			}
        			else {
						if($row){
							$pro_data = getAllData('pizza_size', 'id,name,price,type,alternateName,is_online', ['type'=>'PIZZA-PROPORTIONATE','merchant_id'=>$merchant_id], '', 'alternateName ASC')->result_array();
							$code = "PIZZA-PROPORTIONATE";
							if(empty($pro_data)){
					            $data = array(
					                'WHOLE-PIZZA'=>'100.00',
					                'HALF-PIZZA'=>'50.00',
					                'QUARTER-PIZZA'=>'25.00',
					                'HALF-N-HALF'=>'50.00'
					            );
					            foreach ($data as $key => $value) {
					                $userData = array(                    
					                    "name"=>$key,
					                    "price"=>$value,
					                    "type"=>$code,
					                    "alternateName"=>$this->GetalternateNameLastnew($code,"","",'pizza_size',$merchant_id),
					                    "merchant_id"=>$merchant_id
					                );
					                insertData('pizza_size', $userData );
					            }
					        }
							// echo '<pre>';print_r('if');exit();
							$sess_array=array(
								'tmp_id'=>$row['id'],
								'tmp_username'=>$row['email'],
								'tmp_name'=>$row['name'],
								'tmp_fname'=>$row['f_name'],
								'tmp_user_type'=>$row['user_type'],
			                    'tmp_m_id'=>$row['merchant_id'],
			                    'tmp_m_token'=>$response['access_token'],
			                    'tmp_flag'=>$row['flag'],
							);
							$this->session->set_userdata('logged_in',$sess_array);
							$_SESSION["list_popup_show"]='no';
							redirect('settings/profile','refresh');
						}else{
							$this->session->set_flashdata('error_message', "<p>Please complete the registration process by launching Quickvee app.</p>
		            		<p>Once the app is launched registration will automatically complete.</p>
		          			<p>After the app finishes loading, return to your Clover dashboard and launch the Quickvee. </p>
		            		<p> ");
							redirect('login');//$this->load->view('launch_info');
							// echo '<pre>';print_r('else');exit();
						}
					}
			}
		}					
	}

	function GetalternateNameLastnew($type="PIZZA-SIZE",$id="",$name="",$table,$m_id)
    {
        
        $data=$this->CloverApi_model->InvCatnew($m_id,$type,$id,$name,$table);

        $a=array_column($data,'alternateName');
        sort($a);

        //print_r($a); die;
        $b=end($a);      
        $b=++$b;
        if($b==1 || $b=="")
        {
            return 'aa';
        }
        else
        {
            return $b;
        }      
    }

	function check_db(){
		$username=$this->input->post('username');
		if(!$username){
		$username=$this->input->post('email');
		}
		$password=$this->input->post('password');
	
		$result=$this->login_model->login($username,$password);
		
		if($result && ($username!="" || $password!="" ) )
		{
			$sess_array=array();
			foreach($result as $row)
			{
				$sess_array=array(
					'tmp_id'=>$row->id,
					'tmp_username'=>$row->email,
					'tmp_name'=>$row->name,
					'tmp_fname'=>$row->f_name,
					'tmp_user_type'=>$row->user_type,
                    'tmp_m_id'=>$row->merchant_id,
                    'tmp_m_token'=>$row->merchant_token,
                    'tmp_flag'=>$row->flag,
				);
			}
			if ($this->session->userdata('mid')) 
			{
				$result=$this->login_model->merchant_flag($this->session->userdata('mid'));
				$sess_array['tmp_flag']=$result['flag'];
			}
			$this->session->set_userdata('logged_in',$sess_array);
			return true;
		
		}
		else
		{
			$this->form_validation->set_message('check_db', 'Invalid username or password');
    		return false;
		}
	}	       
    
    function forgot_password(){
		if($this->session->userdata('logged_in')==TRUE){
			$flag = $this->session->userdata('logged_in');
			if(isset($flag['tmp_flag'])){
				$this->load->view("forgot_password",$this->data);
			}else{
				redirect('dashboard');
			}
		}
		else
		{
			$this->load->view("forgot_password",$this->data);
		}
	}
	
	function reset_password_send($typ="web"){

		$email=strtolower(trim($this->input->post('email')));
		
		$da=$this->login_model->UserData($email);
		/*echo "<pre>";
		print_r($da['oauth_provider']);die();*/
		$name=$da['name'];

		/*if ($da['oauth_provider']!='') 
		{
			$array=array('message'=>"Please Login with your google account","message_code"=>2);
            echo json_encode($array);
            exit();
		}*/
		
		
		$ver_code=md5($this->general_model->generatePassword());
		$emailcode=base64_encode($ver_code.":|:".$email);
		
		$this->load->library('form_validation');
        $this->form_validation->set_rules('email','email','trim|required|xss_clean|valid_email|callback_chk_user');
		if($this->form_validation->run()==true)
		{
			$this->db->query("update users set password_reset_code='".$ver_code."',password_reset_status='2' where email='$email'  ");
			require_once('class/mailer/custom_mail.php');
			$ses_name = $name;
			$code_url = base_url()."login/reset_password/?vc=".$emailcode;
			include_once('class/reset_password.php');

			/*$emailBody="Hello,<br>".$name.",<br><br>
				We received a request to reset the password for your account.<br><br>
				To reset password , <a href=\"".base_url()."login/reset_password/?vc=".$emailcode."\" target=\"_blank\">click here</a><br><br>
				Or Copy and Paste below Url in your browsers address bar
				".base_url()."login/reset_password/?vc=".$emailcode."<br><br><br><br>
				Thank you<br>
				Swift Pizza<br>
				<a href='https://www.swiftpizza.com'>https://www.swiftpizza.com</a>";*/ 
				
				$suB='Reset Password';
				$mailClass=new mailCustom;
				$options=array(
				'from'=>'admin@quickvee.com',
				'from_name'=>'Quickvee',
				'to'=>array($email=>$name),
				'subject'=>$suB,
				'body'=>$emailBody,
				'attachment'=>'off',
				);
				$mailClass->Email($options);
				
				if($typ=="web")
				{		
					$this->session->set_flashdata('message', "Please check your email for additional instructions to complete your password change request.");
					redirect('login/forgot_password/');
				}
				else
				{
					$array=array('message'=>"Email Send...","message_code"=>1);
                    echo json_encode($array);
				}	
		}
		else
		{
			if($typ=="web")
			{
				$this->load->view("forgot_password",$this->data);
			}
			else
			{
				$array=array('message'=>"User does not exist","message_code"=>2);
                echo json_encode($array);
			}
		}					
		
	}
	
	function reset_password(){
		$this->data['vc']=$this->input->get('vc');
		$this->load->view("reset_password",$this->data);
	}
	
	function reset_password_save(){
		$vc=$this->input->get('vc');
		$new_password=$this->input->post('new_password');
		$confirm_password=$this->input->post('confirm_password');
		
		$codeFilter=explode(":|:",base64_decode($vc));
		 //echo '<pre>';print_r(	$vc);exit();
		$vrCode=$codeFilter[0];
		$Username=$codeFilter[1];
		
		$this->load->library('form_validation');
        $this->form_validation->set_rules('new_password','New Password','trim|required|xss_clean');
		$this->form_validation->set_rules('confirm_password','Confirm Password','trim|required|xss_clean|callback_reset_chk');
		if($this->form_validation->run()==true)
		{
			$this->db->query("update users set password='".md5($new_password)."',password_reset_status='1',password_reset_code='' where email='$Username' ");
			$this->session->set_flashdata('message', "Your password reset successfully");
			redirect('login/customer_login');	
			
		}
		else{
			$this->data['vc']=$this->input->get('vc');
			$this->load->view("reset_password",$this->data);
		}	
	}
	
	function reset_chk(){
		$vc=$this->input->get('vc');
		$new_password=$this->input->post('new_password');
		$confirm_password=$this->input->post('confirm_password');
		
		$codeFilter=explode(":|:",base64_decode($vc));
		$vrCode=$codeFilter[0];
		$Username=$codeFilter[1];
		
		$q=$this->db->query("select id from users where email='$Username' AND
							password_reset_code='".$vrCode."' ");
		/*$q=$this->db->query("select id from users where email='$Username' AND  password_reset_status='2' AND status='2' ");*/
		$count=$q->num_rows();
		
		if($new_password!=$confirm_password)
		{
			$this->form_validation->set_message('reset_chk', 'Password does not match the confirm password');
			return false;
		}
		else if($count==0)
		{
			$this->form_validation->set_message('reset_chk','Unknown error occurred');
			return false;
		}
		else
		{
			return true;	
		}	
	}
	
	function chk_user()
	{
		$result=$this->login_model->user_chk(strtolower(trim($this->input->post('email'))));
		// if (trim($result)=="google") 
		// {
		// 	$this->form_validation->set_message('chk_user', 'Please Login With Your Google Account');
		// 	return false;
		// }
		if($result==true)
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('chk_user', 'Email-id is not registered with us.');
			return false;
		}
	}
     function active_cust()
	{

     $email = $this->input->get('email');

    	$this->db->query("update users set active_customer='1' where email='$email' ");
			$this->session->set_flashdata('message', "Account activated successfully");
			redirect('login/customer_login');	
	}


	function insert_merchant()
	{
		if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) 
		{
			$secret = '6LdVQD0eAAAAABd4RcXev-WO_YwrL3uTTTnrGW8E';
			$captcharesponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
			$responsedata = json_decode($captcharesponse);
			// print_r($responsedata);die();
			if ($responsedata->success) 
			{

				$this->session->unset_userdata('merchant_logo');
				$this->load->library('form_validation');
				$this->form_validation->set_rules('f_name','f_name','trim|required|xss_clean');
				$this->form_validation->set_rules('l_name','l_name','trim|required|xss_clean');
				$this->form_validation->set_rules('name','name','trim|required|xss_clean');
				$this->form_validation->set_rules('email','email','trim|xss_clean|required');
				$this->form_validation->set_rules('phone','Phone','trim|xss_clean');
				if($this->form_validation->run()==true)
				{
					$password = substr(str_shuffle("1234567890abcdefghijklmnopqrstuvwxyz"), 0, 8);
				$post_data=array(            
					'name'=>$this->input->post('name'),
					'email'=>$this->input->post('email'),
					'user_type'=>'merchant',
					'f_name'=>$this->input->post('f_name'),
					'l_name'=>$this->input->post('l_name'),
					'password'=>md5($password),
					'owner_name'=>$this->input->post('f_name')." ".$this->input->post('l_name'),
					'a_address_line_1'=>$this->input->post('a_address_line_1'),
					'a_state'=>$this->input->post('state'),
					'a_zip'=>$this->input->post('a_zip'),
					'a_city'=>$this->input->post('a_city'),
					'phone'=>$this->input->post('phone'),
					'created_date_time'=>DATETIME,
					'ver_status'=>'2',
					'a_country'=>'USA',
					'flag'=>'0',
					'status'=>'1',
					'a_phone'=>$this->input->post('phone'),
					'created_ip'=>$this->input->ip_address(),
					'created_by_user'=>$this->auth->get_sesData('tmp_username')
				); 
				$this->db->insert('users',$post_data);
				$insert_id = $this->db->insert_id();
				if(strlen((string)$insert_id) == 3){
					$un_no = "0".$insert_id;
				}elseif(strlen((string)$insert_id) == 2){
					$un_no = "00".$insert_id;
				}elseif(strlen((string)$insert_id) == 1){
					$un_no = "000".$insert_id;
				}else{
					$un_no = $insert_id;
				}
				$merchant_id = substr($this->input->post('f_name'), 0, 3).$un_no.$this->input->post('state');
				$this->db->where('id',$insert_id);
				$updata = $this->db->update('users',array('merchant_id'=>strtoupper($merchant_id)));
				if($updata){

					$this->db->insert('varients',array("title"=>'Size',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Color',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Materiel',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Style',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Flavor',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Weight',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Title',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Type',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					$this->db->insert('varients',array("title"=>'Nicotine Strength',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id)));
					//add default tax
					$this->db->insert('taxes',array("title"=>'DefaultTax',"alternateName"=>'aa',"merchant_id"=>strtoupper($merchant_id),"created_on"=> date('Y-m-d')));
					
					$this->db->insert('user_options',array("user_id"=>$insert_id,"merchant_id"=>strtoupper($merchant_id)));
					require_once('class/mailer/custom_mail.php');

					$suB='New merchant sign up';
					$emailBody=	"<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' style='max-width:600px;margin:auto;border-spacing:0;border-collapse:collapse;background:white;border-radius:0px 0px 10px 10px'>
					<tbody style='background-color:#fafafa;'>
					<tr style='background-size:cover'>
					<td colspan='3' style='text-align:center;border-collapse:collapse;border-radius:10px 10px 0px 0px;color:white;height:50px;background-color:#0a64f9;padding:10px'>
					  <img src='https://ci6.googleusercontent.com/proxy/KMcbu8zrXoyWKSbPbnxVubGTx7PgYRs0S09MuME0p2pHSnUzhBCauFlLKn8LlYdveuxEOkeZehwgsghRc06WBSAvXg=s0-d-e1-ft#https://sandbox.quickvee.com/images/maillogo.png' width='80' class='CToWUd'>
					</td>
				  </tr>
					<tr style='margin-bottom:10px;display:block;margin-top:30px;'>
						<td style='padding: 0 20px;'>Hello Malik,</td>
					</tr>
						<tr style='margin-bottom:10px;display:block;'>
						<td style='padding: 0 20px;'>You have received new merchant sign up on your website Quickvee.com . Please contact them at your earliest convenience.
						</td>
					   </tr>
					   <tr style='margin-bottom:10px;display:block;'>
					   <td style='padding: 0 20px;'>Owner Name - ".$post_data['f_name']." ".$post_data['l_name']."
					   </td>
					  </tr>	
					  <tr style='margin-bottom:10px;display:block;'>
					  <td style='padding: 0 20px;'>Bussiness Name - ".$post_data['name']."
					  </td>
					 </tr>	
					 <tr style='margin-bottom:10px;display:block;'>
					 <td style='padding: 0 20px;'>Email id - ".$post_data['email']."
					 </td>
					</tr>	
					<tr style='margin-bottom:10px;display:block;'>
					<td style='padding: 0 20px;'>Phone - ".$post_data['phone']."
					</td>
				   </tr>	
				   <tr style='margin-bottom:10px;display:block;'>
				   <td style='padding: 0 20px;'>Address - ".$post_data['a_address_line_1']."
				   </td>
				  </tr>	
				  <tr style='margin-bottom:10px;display:block;'>
				  <td style='padding: 0 20px;'>City - ".$post_data['a_city']."
				  </td>
				 </tr>
				  <tr style='margin-bottom:10px;display:block;'>
				  <td style='padding: 0 20px;'>State - ".$post_data['a_state']."
				  </td>
				 </tr>	
				 <tr style='margin-bottom:10px;display:block;'>
				 <td style='padding: 0 20px;'>Zip - ".$post_data['a_zip']."
				 </td>
				</tr>	
			
				   <tr><td colspan='3'>&nbsp;</td></tr>
					</tbody>
					</table>";
						$mailClass=new mailCustom;
						$options=array(
							'from'=>'admin@quickvee.com',
							'from_name'=>'Quickvee',
						   // 'to'=>array('sachin@imerchantech.com'=>'sachinm'),
						   'to'=>array('support@swiftpizza.com'=>'Quickvee'),  
							'subject'=>$suB,
							'body'=>$emailBody,
							'attachment'=>'off',
						);
						$mailClass->Email($options);


						$suB1='New Registration';
						$emailBody1=	"<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' style='max-width:600px;margin:auto;border-spacing:0;border-collapse:collapse;background:white;border-radius:0px 0px 10px 10px'>
						<tbody style='background-color:#fafafa;'>
						<tr style='background-size:cover'>
						<td colspan='3' style='text-align:center;border-collapse:collapse;border-radius:10px 10px 0px 0px;color:white;height:50px;background-color:#0a64f9;padding:10px'>
						  <img src='https://ci6.googleusercontent.com/proxy/KMcbu8zrXoyWKSbPbnxVubGTx7PgYRs0S09MuME0p2pHSnUzhBCauFlLKn8LlYdveuxEOkeZehwgsghRc06WBSAvXg=s0-d-e1-ft#https://sandbox.quickvee.com/images/maillogo.png' width='80' class='CToWUd'>
						</td>
					  </tr>
						<tr style='margin-bottom:10px;display:block;margin-top:30px;'>
							<td style='padding: 0 20px;'>Hello ".$post_data['f_name'].",</td>
						</tr>
						<tr style='margin-bottom:10px;display:block;'>
						<td style='padding: 0 20px;'>Thank You for registering with us.
						</td>
					   </tr>
					   <tr style='margin-bottom:10px;display:block;'>
					   <td style='padding: 0 20px;'>Your  Username is  - ".$post_data['email']."
					   </td>
					  </tr>	
					  <tr style='margin-bottom:10px;display:block;'>
					  <td style='padding: 0 20px;'>Your  Password is  - ".$password."
					  </td>
					 </tr>	
				
				 <tr style='margin-bottom:10px;display:block;'>
				 <td style='padding: 0 20px;'>Please Update your password after login to secure your account.
				 </td>
				</tr>	
				<tr>
				<td colspan='3' valign='middle' align='center' style='padding:10px 30px 0px 30px;text-align:center;border-collapse:collapse;padding-right:15px;padding-left:15px'>
				<div style='font-size:15px;color:#6d6d6d;font-weight:normal;font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif'>
				 Please reach out on 925-203-1129 or
				</div>
				</td>
				</tr>
				<tr>
				<td colspan='3' valign='middle' align='center' style='padding:10px 30px 20px 30px;text-align:center;border-collapse:collapse;padding-right:15px;padding-left:15px'>
				<div style='font-size:15px;color:#6d6d6d;font-weight:normal;font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif'>
					<a href='mailto:support@quickvee.com' style'color:#000;text-decoration:none' target='_blank'>support@quickvee.com</a>
				</div>
				</td>
				</tr>
				
					   <tr><td colspan='3'>&nbsp;</td></tr>
						</tbody>
						</table>";
							$mailClass1=new mailCustom;
							$options1=array(
								'from'=>'admin@quickvee.com',
								'from_name'=>'Quickvee',
							   // 'to'=>array('sachin@imerchantech.com'=>'sachin'),
							    'to'=>array($post_data['email']=>$post_data['f_name']),  
								'subject'=>$suB1,
								'body'=>$emailBody1,
								'attachment'=>'off',
							);
							$mailClass1->Email($options1);

						$this->session->unset_userdata('logged_in');
					// $this->session->set_flashdata('message', "Thank you, One of our team members will be contacting you shortly.");
						$this->session->set_flashdata('message', "Thank you for registering with Quickvee. We look forward to helping you increase your online sales. One of our Account Managers will be contacting you soon to setup your inventory and get your online ordering started.");
				
				   redirect('login');
				}
			
				}


			}else
			{
				$this->data['error_msg']="Captcha Verification Failed, Please try again.";
				$this->load->view('mainlayout/newmerchant_signup',$this->data);
			}
		}else
		{
			$this->data['error_msg']="Please Click on the recaptcha box.";
			$this->load->view('mainlayout/newmerchant_signup',$this->data);
		}

		


	}



// function insert_merchant(){
// 	$phone = $this->input->post('phone');
// 		$owner_name  = $this->input->post('owner_name');	
// 		$name = $this->input->post('name');	
// 		$a_address_line_1 = $this->input->post('a_address_line_1');
// 		$phone = $this->input->post('phone');
// 		$a_city = $this->input->post('a_city');
// 		$email = $this->input->post('email');

// 		$post_data=array(
// 			'name'=>$this->input->post('name'),
// 			'owner_name'=>$this->input->post('owner_name'),
// 			'a_address_line_1'=>$this->input->post('a_address_line_1'),
// 			'phone'=>$this->input->post('phone'),
// 			'a_city'=>$this->input->post('a_city'),
// 			'email'=>$this->input->post('email'),
// 			'user_type'=>'merchant',
// 			'created_ip'=>$this->input->ip_address()
// 	);	

// 		$this->db->insert('users',$post_data);
// 		$insert_id = $this->db->insert_id();
// 		if($insert_id){
// 			$this->session->set_flashdata('message', "Thank you, One of our team members will be contacting you shortly");
// 			redirect('login');
// 		}

// }




	function insert($ref_page="main")
	{
		// echo "<pre>";print_r($this->input->post());
		// echo "<pre>";print_r($this->session->userdata());
		// die();
		$phone = $this->input->post('phone');
		$name  = $this->input->post('fname')." ".$this->input->post('lname');	
		$f_name = $this->input->post('fname');	
		$l_name = $this->input->post('lname');
		$emails = $this->input->post('email');
		$query=$this->db->query("select id,password from users where email='$emails'");
		$this->data['page']=$ref_page;		
		$this->data['order_id']=$this->input->post('order_id');
		$this->data['mid']=$this->input->post('mid');
		$this->data['orderMethod']=$this->input->post('orderMethod');
		if($this->input->post('guest')=='yes')
		{
			$pass=null;
		    $pass1 = $query->first_row()->password;
		    $gues=1;
		    if($pass1 != ''){
		       	$gues=0;
		    }
		    if($query->num_rows()>0)
			{
				$this->db->query("update users set f_name='".$f_name."',l_name='".$l_name."',phone='".$phone."',name='".$name."',password='".$pass1."',is_guest='".$gues."' where email='".$emails."'");
			
				$result=$this->login_model->login($emails,$this->input->post('password'));
				$this->create_session('middle');
			}
			if($this->input->post('mid') !== ""){
		        $ids =getAllData('users','id',['merchant_id'=>$this->input->post('mid')])->result_array();
		        $id = $ids[0]['id'];
		        $created_by_user = $id;
		    }else{
		       	$created_by_user = null;
		    }
		    $post_data=array(
		            'name'=>$name,
		            'f_name'=>$this->input->post('fname'),
		            'l_name'=>$this->input->post('lname'),
		            'name'=>$this->input->post('fname')." ".$this->input->post('lname'),
					'email'=>$this->input->post('email'),
		            'user_type'=>'customer',
		            'is_guest'=>$gues,
					'password'=> $pass,
					'phone'=>$phone,
		            'created_date_time'=>DATETIME,
		            'created_by_user'=>$created_by_user,
		            'ver_status'=>'2',
		            'status'=>'2',
		            'active_customer'=>'0',
		          	'created_ip'=>$this->input->ip_address()
			);		
			$this->load->library('form_validation');
			$this->form_validation->set_rules('fname','fname','trim|required|xss_clean');
			$this->form_validation->set_rules('lname','lname','trim|required|xss_clean');
			$this->form_validation->set_rules('email','email','trim|xss_clean|required|callback_chk_users');
			$this->form_validation->set_rules('phone','Phone','trim|xss_clean');
			if($this->form_validation->run()==true)
			{
				$this->db->insert('users',$post_data);
		        $insert_id = $this->db->insert_id();    
		        if($insert_id > 0) {
	            	// code prathamesh
	            	$user_merchant_id = $this->session->userdata('mid').''.$insert_id;
	            	$mer_cust_data = array('merchant_id' => $this->session->userdata('mid'), 'user_id' => $insert_id, 'user_merchant_id' => $user_merchant_id);
	            	$this->db->insert('merchant_customer',$mer_cust_data);
			    }
			    $result=$this->login_model->login($this->input->post('email'),$this->input->post('password'));
				if($result)
				{
					$sess_array=array();
					foreach($result as $row)
					{
						$sess_array=array(
							'tmp_id'=>$row->id,
							'tmp_username'=>$row->email,
							'tmp_name'=>$row->name,
							'tmp_fname'=>$row->f_name,
							'tmp_user_type'=>$row->user_type,
			                'tmp_m_id'=>$row->merchant_id,
			                'tmp_m_token'=>$row->merchant_token,
			                'tmp_flag'=>$row->flag,
						);
						if ($this->session->userdata('mid')) 
								{
									$result=$this->login_model->merchant_flag($this->session->userdata('mid'));
									$sess_array['tmp_flag']=$result['flag'];
								}	
						$this->session->set_userdata('logged_in',$sess_array);
					}
				}
				$login_info = $this->session->userdata("logged_in");
				if($login_info["tmp_id"])
					$this->session->set_userdata("checked_out", 1);
				$this->create_session('middle');
			}else{
				$this->data['page']=$ref_page;		
				$this->data['order_id']=$this->input->post('order_id');
				$this->data['mid']=$this->input->post('mid');
				$this->data['orderMethod']=$this->input->post('orderMethod');
				$this->data['error_msg']="!!! Something went wrong";
				$this->load->view('mainlayout/new-signup',$this->data);
			}
		}


		else

		{
			if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
				$secret = '6LdVQD0eAAAAABd4RcXev-WO_YwrL3uTTTnrGW8E';
				$captcharesponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
				$responsedata = json_decode($captcharesponse);
				// print_r($responsedata);die();
				if ($responsedata->success) 
				{
					$pass = md5($this->input->post('password'));
			        $gues=0;
			        $emails = $this->input->post('email');
			        $pass1 = $pass;
			        if($query->num_rows()>0)
					{				
						$this->db->query("update users set f_name='".$f_name."',l_name='".$l_name."',phone='".$phone."',name='".$name."',password='".$pass1."',is_guest='".$gues."' where email='".$emails."'");			
						$result=$this->login_model->login($emails,$this->input->post('password'));
						$this->data['message']=$this->session->set_flashdata('message', "Your account has been successfully registered with Quickvee.");
						if($ref_page=='middle')
						{		
							$this->create_session('middle');
						}else{							
							$result=$this->login_model->login($this->input->post('email'),$this->input->post('password'));
							if($result)
							{
								$sess_array=array();
								foreach($result as $row)
								{
									$sess_array=array(
										'tmp_id'=>$row->id,
										'tmp_username'=>$row->email,
										'tmp_name'=>$row->name,
										'tmp_fname'=>$row->f_name,
										'tmp_user_type'=>$row->user_type,
					                    'tmp_m_id'=>$row->merchant_id,
					                    'tmp_m_token'=>$row->merchant_token,
					                    'tmp_flag'=>$row->flag,

									);
									$this->session->set_userdata('logged_in',$sess_array);
								}
							}
							$login_info = $this->session->userdata("logged_in");
							if($login_info["tmp_id"])
								$this->session->set_userdata("checked_out", 1);
							if($this->session->userdata('pg_url'))
			            	{	
			            		redirect($this->session->userdata('pg_url'));
							}else{
								redirect('home');
							}
						}
					}
					if($this->input->post('mid') !== ""){
		        	$ids =getAllData('users','id',['merchant_id'=>$this->input->post('mid')])->result_array();
		        	$id = $ids[0]['id'];
			        	$created_by_user = $id;
			        }else{
			        	$created_by_user = null;
			        }
			        $post_data=array(
			            'name'=>$name,
			            'f_name'=>$this->input->post('fname'),
			            'l_name'=>$this->input->post('lname'),
			            'name'=>$this->input->post('fname')." ".$this->input->post('lname'),
						'email'=>$this->input->post('email'),
			            'user_type'=>'customer',
			            'is_guest'=>$gues,
						'password'=> $pass,
						'phone'=>$phone,
			            'created_date_time'=>DATETIME,
			            'created_by_user'=>$created_by_user,
			            'ver_status'=>'2',
			            'status'=>'2',
			            'active_customer'=>'0',
			          	'created_ip'=>$this->input->ip_address()
					);		
					$this->load->library('form_validation');
					$this->form_validation->set_rules('fname','fname','trim|required|xss_clean');
					$this->form_validation->set_rules('lname','lname','trim|required|xss_clean');
					$this->form_validation->set_rules('password','Password','trim|required|xss_clean');
					$this->form_validation->set_rules('email','email','trim|xss_clean|required|callback_chk_users');
					$this->form_validation->set_rules('phone','Phone','trim|xss_clean');
					if($this->form_validation->run()==true)
					{
						$this->db->insert('users',$post_data);
			            $insert_id = $this->db->insert_id();       
			            
			            if($insert_id > 0) 
			            {
			            	$p_data = array('user_id' => $insert_id,'fname' => $this->input->post('fname'), 'lname' => $this->input->post('lname'), 'phone' => $this->input->post('phone'));
			            	$this->db->insert('pickup_contact_info',$p_data);
			            	// code prathamesh
			            	$user_merchant_id = $this->session->userdata('mid').''.$insert_id;
			            	$mer_cust_data = array('merchant_id' => $this->session->userdata('mid'), 'user_id' => $insert_id, 'user_merchant_id' => $user_merchant_id);
			            	$this->db->insert('merchant_customer',$mer_cust_data);
			            }
			            
				        $result=$this->login_model->login($this->input->post('email'),$this->input->post('password'));
						if($result)
						{
							$sess_array=array();
							foreach($result as $row)
							{
								$sess_array=array(
									'tmp_id'=>$row->id,
									'tmp_username'=>$row->email,
									'tmp_name'=>$row->name,
									'tmp_fname'=>$row->f_name,
									'tmp_user_type'=>$row->user_type,
				                    'tmp_m_id'=>$row->merchant_id,
				                    'tmp_m_token'=>$row->merchant_token,
								);
								if ($this->session->userdata('mid')) 
								{
									$result=$this->login_model->merchant_flag($this->session->userdata('mid'));
									$sess_array['tmp_flag']=$result['flag'];
								}								
								$this->session->set_userdata('logged_in',$sess_array);
							}
						}
						// echo '<pre>';print_r($this->session->userdata("logged_in"));exit();
						$login_info = $this->session->userdata("logged_in");
						if($login_info["tmp_id"])
            				$this->session->set_userdata("checked_out", 1);
						$this->mail_model->signup_mail($name,$this->input->post('email'));
						$this->data['message']=$this->session->set_flashdata('message', "Your account has been successfully registered with Quickvee.");
						if($ref_page=='middle')
						{

							$this->create_session('middle','checkregi');
							//$this->load->view('mainlayout/move_to_pay',$this->data);
						}
						else if($ref_page=='main')
						{
							redirect('dashboard');	
						}
						else{
							if($this->session->userdata('pg_url'))
			            	{	
			            		redirect($this->session->userdata('pg_url'));
							}else{
								redirect('home');
							}
						}
					}else{
						$this->data['page']=$ref_page;		
						$this->data['order_id']=$this->input->post('order_id');
						$this->data['mid']=$this->input->post('mid');
						$this->data['orderMethod']=$this->input->post('orderMethod');
						$this->data['error_msg']="!!! Something went wrong";
						$this->load->view('mainlayout/new-signup',$this->data);
					}
				}else{
					$this->data['page']=$ref_page;		
					$this->data['order_id']=$this->input->post('order_id');
					$this->data['mid']=$this->input->post('mid');
					$this->data['orderMethod']=$this->input->post('orderMethod');
					$this->data['error_msg']="Captcha Verification Failed, Please try again.";
					$this->load->view('mainlayout/new-signup',$this->data);
				}
			}else{
				$this->data['page']=$ref_page;		
				$this->data['order_id']=$this->input->post('order_id');
				$this->data['mid']=$this->input->post('mid');
				$this->data['orderMethod']=$this->input->post('orderMethod');
				$this->data['error_msg']="Please Click on the recaptcha box.";
				// redirect('login/customer_login');
				$this->load->view('mainlayout/new-signup',$this->data);
			}
		}
	}	




	function chk_users()
	{
		$result=$this->users_model->user_chk($this->input->post('email'));
		if($result==true)
		{
			return true;
		}
		else{
			$this->form_validation->set_message('chk_users', 'User Already exist with this email');
			return false;
			
		}
		
	}		

	function chk_users_aj($guest='')
	{
		$result=$this->users_model->user_chk($this->input->get('email'));
		if($guest=='yes'){
			$result1=$this->users_model->user_chk_merchant($this->input->get('email'));
		 	if($result1==true)
			{
				echo 'true';
			}
			else{
				echo 'false';
			}
		}else{
			if($result==true)
			{
				echo 'true';
			}
			else{
				echo 'false';
			}
		}
	}

	function chk_consumer(){
		$username=$this->input->get('username');
		$q=$this->db->query("select merchant_id from users where email='$username' ");
        $row=$q->row_array();
        // echo '<pre>';print_r($row['merchant_id']);exit();
        if($row['merchant_id'] != "" && $row['merchant_id']!="no_id"){
        	echo 'false';
        }else{
        	echo 'true';
        }
	}

	function chk_employee()
	{
		$username=$this->input->get('username');
		$q=$this->db->query("select merchant_id from employee where email='$username' ");
        $row=$q->row_array();
        // echo '<pre>';print_r($row['merchant_id']);exit();
        if($row['merchant_id'] != "" && $row['merchant_id']!="no_id")
        {
        	echo 'true';
        }
        else
        {
        	echo 'false';
        }
	}

	function chk_merchant(){
		$username=$this->input->get('username');
		//$password= md5($this->input->get('password'));
		$q=$this->db->query("select merchant_id,name from users where email='$username' and (user_type='merchant' or user_type='superadmin' or user_type='admin') and password IS NOT NULL");
		//$q=$this->db->query("select merchant_id,name from users where email='$username' and (user_type='merchant' or user_type='superadmin') and password='$password'");
        $row=$q->row_array();

        if($row){
        	echo 'true';
        
		}else
		{
        	echo 'false';
        	}        
        }
		
						    
}

?>