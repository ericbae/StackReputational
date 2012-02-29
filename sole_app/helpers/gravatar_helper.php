<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	// helper method for getting the gravatar image
	function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) 
	{
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) 
		{
	    	$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
			{
				$url .= ' ' . $key . '="' . $val . '"';
			}	
			$url .= ' />';
		}
		return $url;
	}
	
	// helper method for getting the gravatar profile, if no profile, go to default
	function get_gravatar_profile($email)
	{
		$hash_email = md5( strtolower( trim( $email ) ) );
		$request_url = 'http://www.gravatar.com/' . $hash_email . '.php';
		$str = @file_get_contents( $request_url );
		if($str == TRUE ) 
		{ 
			$profile = unserialize( $str );
			if( is_array( $profile ) && isset( $profile['entry'] ) )
			{
				//print_r($profile['entry'][0]);
				return $profile['entry'][0]['photos'][0]['value'];	
			}
		} 
		else 
		{ 
			$CI =& get_instance();
   			return $CI->config->item('default_profile_image');
		}
	}

/* End of file user_helper.php */
/* Location: ./application/helpers/user_helper.php */
