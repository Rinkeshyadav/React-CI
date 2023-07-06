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
  }
?>