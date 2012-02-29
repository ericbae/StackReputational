<?php

class sole_model extends CI_Model 
{
	// get the list of locations
	public function get_raw_locations()
	{
		$query = $this->db->query("SELECT DISTINCT location FROM users " .
								  "WHERE location IS NOT NULL AND location_done=false AND location_id=0 " .
								  "ORDER BY RAND() LIMIT 10");
		return $query->result();
	}
	
	// get user ids
	public function get_user_ids($offset) {
		$query = $this->db->query("SELECT user_id FROM users ORDER BY reputation DESC LIMIT 100 OFFSET $offset");
		return $query->result();
	}
	
	// get the users given the condition
	public function get_num_users($country, $state, $city, $age_from, $age_to, $display_name, $tag) {
		$country_condition = $country ? " l.country_long='$country' " : " 1=1 ";
		$state_condition = $state ? " l.state_long='$state' " : " 1=1 ";
		$city_condition = $city ? " l.city_long='$city' " : " 1=1 ";
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}		
		$display_name_condition = $display_name ?  " u.display_name = '$display_name' " : " 1=1 ";
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT u.user_id FROM users u " .
								  "LEFT JOIN locations l ON l.id=u.location_id " .
								  "WHERE $country_condition AND $state_condition AND $city_condition " .
								  "AND (($age_from_condition AND $age_to_condition) $age_null_condition) " .
								  "AND $display_name_condition AND $tag_condition");
		//log_message('info', $this->db->last_query());
		return $query->num_rows();
	}
		
	// get the users given the condition
	public function get_users($country, $state, $city, $age_from, $age_to, $display_name, $tag, $order_by, $offset) {
		$country_condition = $country ? " l.country_long='$country' " : " 1=1 ";
		$state_condition = $state ? " l.state_long='$state' " : " 1=1 ";
		$city_condition = $city ? " l.city_long='$city' " : " 1=1 ";
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}
		$display_name_condition = $display_name ?  " u.display_name = '$display_name' " : " 1=1 ";
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT u.*, u.$order_by AS main_rep, " .
								  "l.formatted_address, l.country_long, l.state_long, l.city_long " .
								  "FROM users u " .
								  "LEFT JOIN locations l ON l.id=u.location_id " .
								  "WHERE $country_condition AND $state_condition AND $city_condition " .
								  "AND (($age_from_condition AND $age_to_condition) $age_null_condition) " .
								  "AND $display_name_condition AND $tag_condition " .
								  "ORDER BY $order_by DESC LIMIT 50 OFFSET $offset");
		//log_message('info', $this->db->last_query());
		return $query->result();
	}
	
	// get the users given the condition
	public function get_num_countries($age_from, $age_to, $tag) {
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}		
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT ROUND(AVG(u.reputation), 1) AS main_rep, COUNT(*) AS num_users, l.country_long " .
								  "FROM users u " .
								  "JOIN locations l ON u.location_id=l.id " .
								  "WHERE (($age_from_condition AND $age_to_condition) $age_null_condition) " .
								  "AND $tag_condition " .
								  "GROUP BY l.country_long");
		//log_message('info', $this->db->last_query());
		return $query->num_rows();
	}	
	
	// get the users given the condition
	public function get_rank_countries($age_from, $age_to, $display_name, $tag, $order_by, $offset) {
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}
		$display_name_condition = $display_name ?  " u.display_name = '$display_name' " : " 1=1 ";
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT ROUND(AVG(u.$order_by), 1) AS main_rep, COUNT(*) AS num_users, l.country_long " .
								  "FROM users u " .
								  "JOIN locations l ON u.location_id=l.id " .
								  "WHERE (($age_from_condition AND $age_to_condition) $age_null_condition) " .
								  "AND $tag_condition " .
								  "GROUP BY l.country_long ORDER BY AVG(u.$order_by) DESC LIMIT 50 OFFSET $offset");
		//log_message('info', $this->db->last_query());
		return $query->result();
	}
	
	// get the users given the condition
	public function get_num_states($age_from, $age_to, $tag) {
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}		
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT ROUND(AVG(u.reputation), 1) AS main_rep, COUNT(*) AS num_users, CONCAT(l.state_long, ', ', l.country_long) AS state " .
								  "FROM users u " .
								  "JOIN locations l ON u.location_id=l.id " .
								  "WHERE (($age_from_condition AND $age_to_condition) $age_null_condition) AND $tag_condition " .
								  "GROUP BY CONCAT(l.state_long, ', ', l.country_long)");
		//log_message('info', $this->db->last_query());
		return $query->num_rows();
	}	
	
	// get the users given the condition
	public function get_rank_states($age_from, $age_to, $display_name, $tag, $order_by, $offset) {
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}
		$display_name_condition = $display_name ?  " u.display_name = '$display_name' " : " 1=1 ";
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT ROUND(AVG(u.reputation), 1) AS main_rep, COUNT(*) AS num_users, CONCAT(l.state_long, ', ', l.country_long) AS state " .
								  "FROM users u " .
								  "JOIN locations l ON u.location_id=l.id " .
								  "WHERE (($age_from_condition AND $age_to_condition) $age_null_condition) AND $tag_condition " .
								  "GROUP BY CONCAT(l.state_long, ', ', l.country_long) ORDER BY AVG(u.$order_by) DESC LIMIT 50 OFFSET $offset");
		//log_message('info', $this->db->last_query());
		return $query->result();
	}
	
	// get the users given the condition
	public function get_num_cities($age_from, $age_to, $tag) {
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}		
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT ROUND(AVG(u.reputation), 1) AS main_rep, COUNT(*) AS num_users, CONCAT(l.city_long, ', ', l.state_long, ', ', l.country_long) AS state " .
								  "FROM users u " .
								  "JOIN locations l ON u.location_id=l.id " .
								  "WHERE (($age_from_condition AND $age_to_condition) $age_null_condition) AND $tag_condition " .
								  "GROUP BY CONCAT(l.city_long, ', ', l.state_long, ', ', l.country_long)");
		//log_message('info', $this->db->last_query());
		return $query->num_rows();
	}
	
	// get the users given the condition
	public function get_rank_cities($age_from, $age_to, $display_name, $tag, $order_by, $offset) {
		$age_from_condition = $age_from ?  " u.age >= $age_from " : " 1=1 ";
		$age_to_condition = $age_to ?  " u.age <= $age_to " : " 1=1 ";
		$age_null_condition = " AND 1=1 ";
		if( $age_from_condition == " 1=1 " && $age_to_condition == " 1=1 ") {
			$age_null_condition = " OR u.age IS NULL ";
		}
		if( $age_from == "-1" && $age_to == "-1") {
			$age_from_condition = " u.age IS NULL ";
			$age_to_condition = " 1=1 ";
		}
		$display_name_condition = $display_name ?  " u.display_name = '$display_name' " : " 1=1 ";
		$tag_condition = $tag ? " u.tags LIKE '%$tag,' " : " 1=1 ";
		$query = $this->db->query("SELECT ROUND(AVG(u.reputation), 1) AS main_rep, COUNT(*) AS num_users, CONCAT(l.city_long, ', ', l.state_long, ', ', l.country_long) AS city " .
								  "FROM users u " .
								  "JOIN locations l ON u.location_id=l.id " .
								  "WHERE (($age_from_condition AND $age_to_condition) $age_null_condition) AND $tag_condition " .
								  "GROUP BY CONCAT(l.city_long, ', ', l.state_long, ', ', l.country_long) ORDER BY AVG(u.$order_by) DESC LIMIT 50 OFFSET $offset");
		//log_message('info', $this->db->last_query());
		return $query->result();
	}
	
	public function update_user_location($location_id, $user_id_string) {
		$query = $this->db->query("UPDATE users SET location_id=$location_id WHERE user_id IN ($user_id_string)");
	}
	
	public function update_user_locations_raw() {
		$query = $this->db->query("UPDATE users u, locations l SET u.location_id=l.id WHERE u.location=l.raw_location");
	}
	
	public function get_countries() {
		$query = $this->db->query("SELECT DISTINCT country_long FROM locations " .
								  "WHERE country_long != '' ORDER BY country_long ASC");
		return $query->result();
	}
	
	public function get_states($country) {
		$query = $this->db->query("SELECT DISTINCT state_long FROM locations " .
								  "WHERE country_long='$country' " .
								  "ORDER BY state_long ASC");
		return $query->result();
	}
	
	public function get_cities($country, $state) {
		$query = $this->db->query("SELECT DISTINCT city_long FROM locations " .
								  "WHERE country_long='$country' AND state_long='$state' " .
								  "ORDER BY city_long ASC");
		return $query->result();
	}
	
	public function get_user($user_id) {
		$query = $this->db->query("SELECT u.*, l.* FROM users u LEFT JOIN locations l ON l.id=u.location_id WHERE u.user_id=$user_id");
		return $query->row();
	}
	
	// user ranking queries
	public function get_user_ranking_overall($user_id) {
		$query = $this->db->query("SELECT a.rank FROM " .
								  "(" .
								  "		SELECT @rownum:=@rownum+1 AS 'rank', u.user_id " .
								  "		FROM (SELECT @rownum:=0) r, users u " .
								  "		LEFT JOIN locations l ON l.id=u.location_id " .
								  "		WHERE (u.age >= 0 AND u.age <= 1000) OR u.age IS NULL " .
								  "		ORDER BY reputation DESC " .
								  ") a WHERE a.user_id=$user_id");
		//log_message('info', $this->db->last_query());
		return $query->row();
	}
	
	// user ranking queries
	public function get_user_ranking_country($user_id, $country) {
		$query = $this->db->query("SELECT a.rank FROM " .
								  "(" .
								  "		SELECT @rownum:=@rownum+1 AS 'rank', a.user_id " .
								  "		FROM (SELECT @rownum:=0) r, " .
								  "		( " .
								  "			SELECT u.user_id FROM users u " .
								  "			JOIN locations l ON l.id=u.location_id " .
								  "			WHERE l.country_long='$country' AND ((u.age >= 0 AND u.age <= 1000) OR u.age IS NULL) " .
								  "			ORDER BY reputation DESC " .
								  "		) a " .
								  ") a WHERE a.user_id=$user_id");
		//log_message('info', $this->db->last_query());
		return $query->row();
	}
	
	// user ranking queries
	public function get_user_ranking_state($user_id, $country, $state) {
		$query = $this->db->query("SELECT a.rank FROM " .
								  "(" .
								  "		SELECT @rownum:=@rownum+1 AS 'rank', a.user_id " .
								  "		FROM (SELECT @rownum:=0) r, " .
								  "		( " .
								  "			SELECT u.user_id FROM users u " .
								  "			JOIN locations l ON l.id=u.location_id " .
								  "			WHERE l.country_long='$country' AND l.state_long='$state' AND " .
								  "			((u.age >= 0 AND u.age <= 1000) OR u.age IS NULL) " .
								  "			ORDER BY reputation DESC " .
								  "		) a " .
								  ") a WHERE a.user_id=$user_id");
		return $query->row();
	}
	
	// user ranking queries
	public function get_user_ranking_city($user_id, $country, $state, $city) {
		$query = $this->db->query("SELECT a.rank FROM " .
								  "(" .
								  "		SELECT @rownum:=@rownum+1 AS 'rank', a.user_id " .
								  "		FROM (SELECT @rownum:=0) r, " .
								  "		( " .
								  "			SELECT u.user_id FROM users u " .
								  "			JOIN locations l ON l.id=u.location_id " .
								  "			WHERE l.country_long='$country' AND l.state_long='$state' AND l.city_long='$city' AND " .
								  "			((u.age >= 0 AND u.age <= 1000) OR u.age IS NULL) " .
								  "			ORDER BY reputation DESC " .
								  "		) a " .
								  ") a WHERE a.user_id=$user_id");
		return $query->row();
	}
	
	// user ranking queries
	public function get_user_ranking_age($user_id, $age) {
		$query = $this->db->query("SELECT a.rank FROM " .
								  "(" .
								  "		SELECT @rownum:=@rownum+1 AS 'rank', a.user_id " .
								  "		FROM (SELECT @rownum:=0) r, " .
								  "		(" .
								  "			SELECT u.user_id FROM users u " .
								  "			WHERE u.age=$age AND u.age >= 0 AND u.age <= 1000 " .
								  "			ORDER BY reputation DESC " .
								  "		) a " .
								  ") a WHERE a.user_id=$user_id");
		//log_message('info', $this->db->last_query());
		return $query->row();
	}
	
	// user ranking queries
	public function get_user_ranking_age_group($user_id, $age_from, $age_to) {
		$query = $this->db->query("SELECT a.rank FROM " .
								  "(" .
								  "		SELECT @rownum:=@rownum+1 AS 'rank', a.user_id " .
								  "		FROM (SELECT @rownum:=0) r, " .
								  "		( " .
								  "			SELECT u.user_id FROM users u " .
								  "			WHERE u.age >= $age_from AND u.age <= $age_to AND u.age >= 0 AND u.age <= 1000 " .
								  "			ORDER BY reputation DESC " .
								  "		) a " .
								  ") a WHERE a.user_id=$user_id");
		//log_message('info', $this->db->last_query());
		return $query->row();
	}
}
?>