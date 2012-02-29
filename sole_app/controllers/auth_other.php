<?php

class auth_other extends CI_Controller 
{	
	function __construct()
	{
		parent::__construct();
		$this->load->model('tank_auth/users');
	}

	// handle when users log in using facebook account
	public function fb_signin()
	{
		// get the facebook user id and then get the user data via curl
		$fb_user_id = $this->input->post('user_id');
		$this->curl->ssl(false);
		$fb_user = json_decode($this->curl->simple_get('https://graph.facebook.com/' . $fb_user_id));
		
		// check if the user is in the DB already
		if( !($user = $this->member_model->get_user_by_sm(array('facebook_id' => $fb_user->id), 'facebook_id'))) 
		{
			// create the user
			$password = generate_password(9, 8);
			$this->tank_auth->create_user($fb_user->username . $fb_user->id, $fb_user->username . $fb_user->id, $password, false);
			$user = $this->generic_model->get_where_single_row('users', array('username' => $fb_user->username . $fb_user->id));
			$this->generic_model->update('user_profiles', 
									 	 array('facebook_id' => $fb_user->id, 
									 	 	   'display_name' => $fb_user->username,
									 	       'profile_image' => 'http://graph.facebook.com/' . $fb_user->id . '/picture'), 
									 	 array('user_id' => $user->id));
			// let's also configure a newsletter profile for this user
			$this->generic_model->insert('newsletter', array('user_id' => $user->id, 'frequency' => 'never'));									 	 
		}
		$user = $this->member_model->get_user_by_sm(array('facebook_id' => $fb_user->id), 'facebook_id');
		echo $user->id;
	}
	
	public function fb_login($user_id)
	{
		$user = $this->member_model->get_user($user_id);
		if( $user->facebook_id )
		{
			$this->tank_auth_login($user);
		}
		else
		{
			
		}
	}
	
	// function to allow users to log in via twitter
	public function twitter_signin()
	{
		// Enabling debug will show you any errors in the calls you're making, e.g:
		$this->tweet->enable_debug(false);
		
		// If you already have a token saved for your user
		// (In a db for example) - See line #37
		// 
		// You can set these tokens before calling logged_in to try using the existing tokens.
		// $tokens = array('oauth_token' => 'foo', 'oauth_token_secret' => 'bar');
		// $this->tweet->set_tokens($tokens);
		if ( !$this->tweet->logged_in() )
		{
			// This is where the url will go to after auth. (Callback url)
			$this->tweet->set_callback(site_url('auth_other/twitter_signin'));
			
			// Send the user off for login!
			$this->tweet->login();
		}
		else
		{
			// check if the user is in the DB already
			$twitter_user = $this->tweet->call('get', 'account/verify_credentials');
			if( !($user = $this->member_model->get_user_by_sm(array('twitter_id' => $twitter_user->id), 'twitter_id'))) 
			{
				// create the user
				$password = generate_password(9, 8);
				$this->tank_auth->create_user($twitter_user->screen_name . $twitter_user->id, $twitter_user->screen_name . $twitter_user->id, $password, false);
				$user = $this->generic_model->get_where_single_row('users', array('username' => $twitter_user->screen_name . $twitter_user->id));
			 	
			 	// update the user profile table
				$tokens = $this->tweet->get_tokens();
				$this->generic_model->update('user_profiles', 
										 	 array('twitter_id' => $twitter_user->id, 
										 	 	   'display_name' => $twitter_user->screen_name,
										 	   	   'twitter_access_token' => $tokens['oauth_token'], 
										 	   	   'twitter_access_token_secret' => $tokens['oauth_token_secret'], 
										 	       'profile_image' => $twitter_user->profile_image_url), 
										 	 array('user_id' => $user->id));
				$user = $this->member_model->get_user_by_sm(array('twitter_id' => $twitter_user->id), 'twitter_id');
				$this->tank_auth_login($user);
				redirect('member/member_location', 'redirect');					
			}
			else
			{
				$user = $this->member_model->get_user_by_sm(array('twitter_id' => $twitter_user->id), 'twitter_id');
				
				// if the profile image url is set to gravatar, then update to twitter profile pic
				if( strpos($user->profile_image, 'gravatar') !== false ) {
					$this->generic_model->update('user_profiles', array('profile_image' => $twitter_user->profile_image_url), array('user_id' => $user->id));
					$user->profile_image = $twitter_user->profile_image_url;
				}
				// if twitter access token is empty, then save it
				if( $user->twitter_access_token == '' || $user->twitter_access_token_secret == '') {
					$tokens = $this->tweet->get_tokens();
					$this->generic_model->update('user_profiles', 
												 array('twitter_access_token' => $tokens['oauth_token'], 'twitter_access_token_secret' => $tokens['oauth_token_secret']), 
												 array('user_id' => $user->id));
				}
				// user signs in and go to the login page
				$this->tank_auth_login($user);
				redirect('auth', 'refresh');
			}
		}		
	}
	
	// called when user logs in via facebook/twitter for the first time
	function fill_user_info()
	{
		// load validation library and rules
		$this->load->config('tank_auth', TRUE);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length['.$this->config->item('username_min_length', 'tank_auth').']|callback_username_check');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|callback_email_check');
		
		// Run the validation
		if ($this->form_validation->run() == false ) 
		{
			$this->load->view('auth/fill_user_info'); 
		}
		else
		{
			$username = $this->input->post('username');
			$email = $this->input->post('email');
			
			/*
			 * We now must create a new user in tank auth with a random password in order
			 * to insert this user and also into user profile table with tank auth id
			 */
			$password = generate_password(9, 8);
			$this->tank_auth->create_user($username, $email, $password, false);
			$user = $this->generic_model->get_where_single_row('users', array('email' => $email));
			if( $this->session->userdata('facebook_id')) 
			{ 
				$this->generic_model->update('user_profiles', 
											 array('facebook_id' => $this->session->userdata('facebook_id'), 
											 	   'profile_image' => 'http://graph.facebook.com/' . $this->session->userdata('facebook_id') . '/picture?type=large'), 
											 array('user_id' => $user->id));
			}
			else if( $this->session->userdata('twitter_id'))
			{
				//$user = $this->tweet->call('get', 'account/verify_credentials');
				$this->generic_model->update('user_profiles', 
											 array('twitter_id' => $this->session->userdata('twitter_id'), 
											 	   'twitter_access_token' => $this->session->userdata('twitter_access_token'), 
											 	   'twitter_access_token_secret' => $this->session->userdata('twitter_access_token_secret'), 
											 	   'profile_image' => $this->session->userdata('twitter_profile_image')), 
											 array('user_id' => $user->id));
				// unset the twitter access tokens
				$this->session->unset_userdata(array('twitter_access_token' => '', 'twitter_access_token_secret' => '', 'twitter_profile_image' => ''));
			}
			// let the user login via tank auth
			$this->tank_auth->login($email, $password, true, true, true);
			redirect('auth', 'refresh');
		}
	}
	
	// a logout function for 3rd party
	public function logout()
	{
		$redirect = site_url('auth/logout');
		if( $this->session->userdata('gfc_id') && $this->session->userdata('gfc_id') != '') { $redirect = null; }
		
		// set all user data to empty
		$this->session->set_userdata(array('facebook_id' => '', 
										   'twitter_id' => '', 
										   'gfc_id' => '',
										   'google_open_id' => '',
										   'yahoo_open_id' => ''));
		if( $redirect ) { redirect($redirect, 'refresh'); } 
		else { $this->load->view('gfc_logout'); }
	}
	
	// we simulate tank auth login procedure
	private function tank_auth_login($user)
	{
		// simulate what happens in the tank auth
		$this->session->set_userdata(array(	'user_id' => $user->id, 'username' => $user->username,
											'display_name' => $user->display_name,
											'profile_image' => $user->profile_image,
											'status' => ($user->activated == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED));
		$this->users->update_login_info( $user->id, $this->config->item('login_record_ip', 'tank_auth'),
										 $this->config->item('login_record_time', 'tank_auth'));													
		redirect('auth', 'refresh');			
	}
		
	// check if the email already exists
	function email_check($email)
	{
		if ( $user = $this->generic_model->get_where_single_row('users', array('email' => $email))) 
		{
			$this->form_validation->set_message('email_check', 'This %s is already registered.');
			return false;
		}
		else { return true; }
	}
	
	// check if the username already exists
	function username_check($username)
	{
		if ( $user = $this->generic_model->get_where_single_row('users', array('username' => $username))) 
		{
			$this->form_validation->set_message('username_check', 'This %s is already registered.');
			return false;
		}
		else { return true; }		
	}
}

/* End of file main.php */
/* Location: ./freally_app/controllers/main.php */