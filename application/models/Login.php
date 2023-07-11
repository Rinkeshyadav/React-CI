<?php
  class Login  extends CI_Model {

  	 function getUserLogin($username,$password){
  	 	// echo $username." ".$password;
  	 	$query = $this->db->select('email')
                ->where('email', $username)
                 ->where('password',$password)
                ->get('users');
         return $query;
  	 }
     function getRequest($data){
      // return $data;
        $query= $this->db->select('id','email')
                ->where('email',$data['email'])
                ->get('users');
              return $query;

     }
     function getNewRequest($data){
      // return $data;
      $query=$this->db->insert('users', $data);
      return $query;

     }
     function UserData($email){

      $result=$this->db->select('*')->from('users')->where('email',$email)->get();
      return $result;

     }
     function reset_Password($username,$new_password){
      $data = array(
                      'password' => md5($new_password),
                      'password_reset_status'=>1,
                      'password_reset_code'=>''
                   );
                  $this->db->where('email', $username);
                 $result= $this->db->update('users', $data);
                  // echo $str = $this->db->last_query();
     }
     function chk_merchant($username){
      $q=$this->db->query("select merchant_id,name from users where email='$username' and (user_type='merchant' or user_type='superadmin' or user_type='admin') and password IS NOT NULL")->row_array();
      return $q;
     }
     function Merchant_login($username){
      // echo $username;
      // $q=$this->db->query("select id,oauth_provider,merchant_id,status,clover_customer_id from users where email='$username'  AND password!=NULL ");
      // echo "hello rinkesh";
      $q=$this->db->select('id,oauth_provider,merchant_id,status,clover_customer_id')
         ->from("users")
         ->where('email',$username)
         ->where('password is NOT NULL', NULL, FALSE)
         ->get();
        //  ->row_array();
         return $q;
        //  ->row_array();
        //  echo $this->db->last_query();
     }
  }
?>