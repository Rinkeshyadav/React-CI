<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class General_model  extends CI_Model{
    function generatePassword($length=9,$strength=0) {
		$vowels = 'aeuy';
		$consonants = '1234567890bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}
		$password = '';
			$alt = time() % 2; 
			for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
			}
		}
		return $password;
	}
}