<?php

namespace Helper;

/**
* 
*/
class tokenManager
{
	
	static function generateUniqueToken()
	{
		    //Token generation
		    //Generate token
		    $token = uniqid(rand(), true);
		    //store it in session
		    $_SESSION['token'] = $token;
		    //Also store the creation time
		    $_SESSION['token_time'] = time();

		    return $token;

	}

	static function checkToken($token)
	{
		//check valid token
        if($_SESSION['token'] == $token)
        {
          //Get timestamp from 15 min ago
            $timestamp_old = time() - (15*60);

            //Check token expiration time (if generation was more than 15 min ago)
            if($_SESSION['token_time'] >= $timestamp_old)
			    return true;
            else
            	return false;
        }
        else
        	return false;

	}
}