<?php

class member_model extends CI_Model 
{
	// get user by their social media id
	function get_user_by_sm($data, $sm_id)
	{
		$this->db->select("u.*, up." . $sm_id . ", up.profile_image, up.display_name, up.twitter_access_token, up.twitter_access_token_secret");
		$this->db->from("users AS u");
		$this->db->join("user_profiles AS up", "u.id=up.user_id");
		$this->db->where($data);
		$query = $this->db->get();
		return $query->row();
	}

	// Returns user by its email
	function get_user_by_email($email)
	{
		$query = $this->db->query("SELECT * FROM users u, user_profiles up WHERE u.email='$email' and u.id = up.user_id");
		return $query->row();
	}
	
	// Returns user by its email
	function get_user_by_username($username)
	{
		$query = $this->db->query("SELECT * FROM users u, user_profiles up WHERE u.username='$username' and u.id = up.user_id");
		return $query->row();
	}

	// return the user given the id
	function get_user($user_id)
	{
		$query = $this->db->query("SELECT users.*, user_profiles.* FROM users, user_profiles WHERE " .
								  "users.id='$user_id' AND user_profiles.user_id='$user_id'");
		return $query->row();
	}

//	// get all users
//	function get_users()
//	{
//		$query = $this->db->query("SELECT u.*, up.location_id, up.display_name, up.profile_image, up.twitter_id, up.facebook_id, " .
//								  "l.formatted_address, l.city, l.state, l.country_long, l.latitude, l.longitude " .
//								  "FROM users u " .
//								  "JOIN user_profiles up ON u.id=up.user_id " .
//								  "LEFT JOIN locations l ON up.location_id=l.id");
//		return $query->result();
//	}	
//	
//	// return the data required for user account
//	public function get_user_account($user_id)
//	{
//		$query = $this->db->query("SELECT u.id, u.username, u.email, " .
//								  "up.display_name, up.twitter_id, up.facebook_id, up.locations, up.categories, n.email " .
//								  "FROM users u, user_profiles up, newsletter n WHERE " .
//								  "u.id=$user_id AND up.user_id=$user_id AND n.user_id=$user_id");
//		return $query->row();
//	}
//	
//	// get the list of locations
//	public function get_locations($query)
//	{
//		$query = $this->db->query("SELECT formatted_address FROM locations WHERE LOWER(formatted_address) LIKE '$query%' ORDER BY formatted_address ASC");
//		//log_message('info', 'checking dupes : ' . $this->db->last_query());
//		return $query->result();
//	}
}
?>