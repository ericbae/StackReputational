<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class crawler extends CI_Controller {
	
	// method to crawl through Stack users
	public function users() {
		set_time_limit(0);
		//while(true) {
		//	sleep(5);
			// get the last page number crawled
			$crawler = $this->generic_model->get_where_single_row('users', array('user_id' => -1));
			if( $crawler ) { 
				$page = $crawler->creation_date + 1;
				$this->generic_model->update('users', array('creation_date' => $page), array('user_id' => -1));
			} else {
				$page = 1;
				$this->generic_model->insert('users', array('user_id' => -1, 'creation_date' => 1));
			}
			@$this->curl->options(array(CURLOPT_ENCODING => 'gzip', CURLOPT_RETURNTRANSFER => 1));
			$users = json_decode($this->curl->simple_get('http://api.stackexchange.com/2.0/users', 
														 array('key' => '3oSU2RNRoxJV9tr1LmhXIw((',
															   'page' => $page, 'pagesize' => 100,
														 	   'order' => 'desc', 'sort' => 'reputation', 
															   'site' => 'stackoverflow')));
			// check the size of user items
			if( sizeof($users->items) > 0 ) {
				foreach($users->items as $user) {
					// first check if user exists!
					if( !$curr_user = $this->generic_model->get_where_single_row('users', array('user_id' => $user->user_id)) ) {
						$this->generic_model->insert('users', 
										 			array('user_id' => $user->user_id, 
													 	  'creation_date' => $user->creation_date, 
													 	  'display_name' => $user->display_name, 
													 	  'profile_image' => isset($user->profile_image) ? $user->profile_image : '', 
													 	  'reputation' => $user->reputation, 
													 	  'reputation_change_day' => $user->reputation_change_day,
													 	  'reputation_change_week' => $user->reputation_change_week, 
													 	  'reputation_change_month' => $user->reputation_change_month, 
													 	  'reputation_change_quarter' => $user->reputation_change_quarter, 
													 	  'reputation_change_year' => $user->reputation_change_year, 
													 	  'age' => isset($user->age) ? $user->age : null,
													 	  'link' => $user->link,
													 	  'location_id' => 0,
													 	  'website_url' => isset($user->website_url) ? $user->website_url : '',
													 	  'location' => isset($user->location) ? $user->location : '', 
													 	  'account_id' => $user->account_id, 
													 	  'accept_rate' => isset($user->accept_rate) ? $user->accept_rate : null));
						$curr_user = $this->generic_model->get_where_single_row('users', array('user_id' => $user->user_id));
					} else {
						$this->generic_model->update('users', 
													 array('reputation' => $user->reputation, 
													 	   'reputation_change_day' => $user->reputation_change_day,
													 	   'reputation_change_week' => $user->reputation_change_week, 
													 	   'reputation_change_month' => $user->reputation_change_month, 
													 	   'reputation_change_quarter' => $user->reputation_change_quarter, 
													 	   'reputation_change_year' => $user->reputation_change_year, 
													 	   'age' => isset($user->age) ? $user->age : null,
													 	   'website_url' => isset($user->website_url) ? $user->website_url : '',
													 	   'accept_rate' => isset($user->accept_rate) ? $user->accept_rate : null), 
													 array('user_id' => $curr_user->user_id));				
					}
				}
			} else // we reached the end of the users!
				$this->generic_model->update('users', array('creation_date' => 0), array('user_id' => -1));
			
			// update all users locations
			$this->sole_model->update_user_locations_raw();
			// have we reached limit?
			//if($users->quota_remaining < 1000 )
			//	break;
			
			//if( $crawler->creation_date > 100000 ) {
			//	$this->generic_model->update('users', array('creation_date' => 0), array('user_id' => -1));
			//	break;
			//}
		//}
	}
	
	public function locations() {
		set_time_limit(0);
		$locations = $this->sole_model->get_raw_locations();
		foreach($locations as $location) {
			$this->generic_model->update('users', array('location_done' => true), array('location' => $location->location));
			if( !$curr_location = $this->generic_model->get_where_single_row('locations', array('raw_location' => $location->location))) {
				sleep(5);
				$address = $this->ja_geocode->query($location->location);
				foreach($address->results as $new_location)
				{
					$formatted_address = '';
					$city = '';
					$city_long = '';
					$state = '';
					$state_long = '';
					$postcode = '';
					$postcode_long = '';
					$country = '';
					$country_long = '';
					$latitude = '';
					$longitude = '';
					foreach($new_location->address_components as $component)
					{
						//echo $component->long_name . '<br/>';
						//echo $component->short_name . '<br/>';
						if( $component->types[0] == 'locality' )
						{
							$city = $component->short_name;
							$city_long = $component->long_name;
						}
						else if( $component->types[0] == 'administrative_area_level_1')
						{
							$state = $component->short_name;
							$state_long = $component->long_name;
						}
						else if( $component->types[0] == 'country')
						{
							$country = $component->short_name;
							$country_long = $component->long_name;
						}
						else if( $component->types[0] == 'postal_code')
						{
							$postcode = $component->short_name;
							$postcode_long = $component->long_name;
						}
						//echo '<br/><br/>';
					}
					$formatted_address = $new_location->formatted_address;
					$latitude = $new_location->geometry->location->lat;
					$longitude = $new_location->geometry->location->lng;
						
					if( $formatted_address != '' && $latitude != '' && $longitude != '') //&& $city != '' && $state != '' && $country != '')
					{
						if( !$location_id = $this->generic_model->get_table_id('locations', array('formatted_address' => $formatted_address)))
						{
							$location_id = $this->generic_model->insert('locations', array('formatted_address' => $formatted_address,
																						   'city' => $city,
																						   'city_long' => $city_long, 
																						   'state' => $state,
																						   'state_long' => $state_long, 
																						   'postcode' => $postcode,
																						   'postcode_long' => $postcode_long, 
																						   'country' => $country,
																						   'country_long' => $country_long,
																						   'latitude' => $latitude,
																						   'longitude' => $longitude, 
																						   'raw_location' => $location->location), true);
							$this->generic_model->update('users', array('location_id' => $location_id), array('location' => $location->location));
						}
						break;
					}
				}
			} else {
				$this->generic_model->update('users', array('location_id' => $curr_location->id), array('location' => $curr_location->raw_location));
			}
			//break;
		}
	}
	
	// crawl user's tags
	public function tags() {
		$offset = 1;
		while(true) {
			$ids = $this->sole_model->get_user_ids($offset);
			$id_string = "";
			$count = 0;
			foreach($ids as $id) {
				$id_string .= $id->user_id;
				$count++;
				if( $count + 1 == sizeof($ids))
					break;
				else
					$id_string .= ";";	
			}
			//echo $id_string;
			@$this->curl->options(array(CURLOPT_ENCODING => 'gzip', CURLOPT_RETURNTRANSFER => 1));
			$tags = json_decode($this->curl->simple_get('http://api.stackexchange.com/2.0/users/' . $id_string . '/tags', 
														 array('key' => '3oSU2RNRoxJV9tr1LmhXIw((', //'min' => '500',
															   'site' => 'stackoverflow')));
			foreach($tags->items as $tag) {
				//print_r($tag);
				// insert into DB if it doesn't exist
				if( !$exists = $this->generic_model->get_where_single_row('tags', array('tag' => $tag->name))) {
					$this->generic_model->insert('tags', array('tag' => $tag->name));
				}	
				$tag_name = $tag->name;
				$user_id = $tag->user_id;
				$user = $this->generic_model->get_where_single_row('users', array('user_id' => $user_id));
				if( substr_count($user->tags, ',') < 5 && strpos($user->tags, $tag_name) === false) {
					$curr_tags = $user->tags . $tag_name . ',';
					$this->generic_model->update('users', array('tags' => $curr_tags), array('user_id' => $user->user_id));	
				}
			}
			$offset = $offset+100;
			
			if($tags->quota_remaining < 1000 )
				break;
		}		
	}
	
	public function locations_yahoo() {
		set_time_limit(0);
		$locations = $this->sole_model->get_raw_locations();
		foreach($locations as $location) {
			if( !$curr_location = $this->generic_model->get_where_single_row('locations', array('raw_location' => $location->location))) {
				//sleep(5);
				$address = $this->placefinder->geocode("reading uk");
				print_r($address);
				//$this->generic_model->update('users', array('location_done' => true), array('location' => $location->location));
				foreach($address->ResultSet->Result as $new_location)
				{
//					$formatted_address = '';
//					$city = '';
//					$city_long = '';
//					$state = '';
//					$state_long = '';
//					$postcode = '';
//					$postcode_long = '';
//					$country = '';
//					$country_long = '';
//					$latitude = '';
//					$longitude = '';
//					foreach($new_location->address_components as $component)
//					{
//						//echo $component->long_name . '<br/>';
//						//echo $component->short_name . '<br/>';
//						if( $component->types[0] == 'locality' )
//						{
//							$city = $component->short_name;
//							$city_long = $component->long_name;
//						}
//						else if( $component->types[0] == 'administrative_area_level_1')
//						{
//							$state = $component->short_name;
//							$state_long = $component->long_name;
//						}
//						else if( $component->types[0] == 'country')
//						{
//							$country = $component->short_name;
//							$country_long = $component->long_name;
//						}
//						else if( $component->types[0] == 'postal_code')
//						{
//							$postcode = $component->short_name;
//							$postcode_long = $component->long_name;
//						}
//						//echo '<br/><br/>';
//					}
//					$formatted_address = $new_location->formatted_address;
//					$latitude = $new_location->geometry->location->lat;
//					$longitude = $new_location->geometry->location->lng;
//						
//					if( $formatted_address != '' && $latitude != '' && $longitude != '' && $city != '' && $state != '' && $country != '')
//					{
//						if( !$location_id = $this->generic_model->get_table_id('locations', array('formatted_address' => $formatted_address)))
//						{
//							$location_id = $this->generic_model->insert('locations', array('formatted_address' => $formatted_address,
//																						   'city' => $city,
//																						   'city_long' => $city_long, 
//																						   'state' => $state,
//																						   'state_long' => $state_long, 
//																						   'postcode' => $postcode,
//																						   'postcode_long' => $postcode_long, 
//																						   'country' => $country,
//																						   'country_long' => $country_long,
//																						   'latitude' => $latitude,
//																						   'longitude' => $longitude, 
//																						   'raw_location' => $location->location), true);
//						}
//						break;
//					}
				}
			}
			break;
		}
	}	
	
	// set user's proper location
	public function set_user_location() {
		$locations = $this->generic_model->get_orderby('locations', 'formatted_address', 'asc');
		foreach($locations as $location) {
			$users = $this->generic_model->get_where('users', array('location' => $location->raw_location));
			$user_ids = "";
			foreach($users as $user) {
				$user_ids .= $user->user_id . ",";
			}
			$user_id_arr = explode(",", $user_ids);
			$user_id_string = implode(",", array_filter($user_id_arr));
			$this->sole_model->update_user_location($location->id, $user_id_string);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */