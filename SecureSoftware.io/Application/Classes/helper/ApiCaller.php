<?php

namespace Helper;

use Helper\SessionManager;
use Helper\RefreshToken;

/**
* 
*/
class apiCaller
{
	
	static function apiRequest($url, $post=FALSE, $headers=array()) {
	
	  SessionManager::sessionStart('user');
	  if(!$post['grant_type'] == 'refresh_token' && !$post['grant_type'] == 'authorization_code')
      		RefreshToken::checkValidity();


	  if($_SESSION['access_token'])
	    $url = $url.'?access_token='.$_SESSION['access_token'].'&state='.$_SESSION['state'];
	 

	  $ch = curl_init($url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	 
	  if($post){
	  	curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
	 }

	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	  $response = curl_exec($ch);
	  return $response;
	}
}