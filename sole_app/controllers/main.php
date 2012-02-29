<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class main extends CI_Controller {
	public function index() {
		$data['tags'] = $this->generic_model->get_orderby('tags', 'tag', 'asc');
		$this->load->view('home', $data);
	}
	
	public function country() {
		$data['tags'] = $this->generic_model->get_orderby('tags', 'tag', 'asc');
		$this->load->view('country', $data);
	}
	
	public function state() {
		$data['tags'] = $this->generic_model->get_orderby('tags', 'tag', 'asc');
		$this->load->view('state', $data);
	}	
	
	public function city() {
		$data['tags'] = $this->generic_model->get_orderby('tags', 'tag', 'asc');
		$this->load->view('city', $data);
	}
	
	public function about() {
		$this->load->view('about');
	}
	
	public function get_countries() {
		$countries = $this->sole_model->get_countries();
		echo json_encode($countries);
	}

	public function get_states() {
		$countries = $this->sole_model->get_states($this->input->get('country'));
		echo json_encode($countries);
	}
	
	public function get_cities() {
		$countries = $this->sole_model->get_cities($this->input->get('country'), $this->input->get('state'));
		echo json_encode($countries);
	}
	
	public function user_fames() {
		$user_id = $this->input->get('user_id');
		$user = $this->sole_model->get_user($user_id);
		$data['overall'] = $this->sole_model->get_user_ranking_overall($user_id);
		if( $user->location_id != 0) {
			$data['country'] = $this->sole_model->get_user_ranking_country($user_id, $user->country_long);
			$data['state'] = $this->sole_model->get_user_ranking_state($user_id, $user->country_long, $user->state_long);
			$data['city'] = $this->sole_model->get_user_ranking_city($user_id, $user->country_long, $user->state_long, $user->city_long);
		}
		if( $user->age ) {
			$data['age'] = $this->sole_model->get_user_ranking_age($user_id, $user->age);
			$data['age_group'] = $this->sole_model->get_user_ranking_age_group($user_id, ($user->age/10)*10, ($user->age/10 + 1)*10);
		}
		echo json_encode($data); 
	}
	
	public function get_num_users() {
		$country = $this->input->get('country');
    	$state = $this->input->get('state');
    	$city = $this->input->get('city');
    	$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$display_name = $this->input->get('display_name');
    	$tag = $this->input->get('tag');
    	$this->load->library('pagination');
		$config['base_url'] = '';
		$config['total_rows'] = $this->sole_model->get_num_users($country, $state, $city, $age_from, $age_to, $display_name, $tag);
		$config['per_page'] = 50; 
		$config['num_links'] = 8;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		$data['total'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		echo json_encode($data);
	}
	
	public function get_num_countries() {
		$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$tag = $this->input->get('tag');
    	$this->load->library('pagination');
		$config['base_url'] = '';
		$config['total_rows'] = $this->sole_model->get_num_countries($age_from, $age_to, $tag);
		$config['per_page'] = 50; 
		$config['num_links'] = 8;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		$data['total'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		echo json_encode($data);
	}
	
	public function get_num_states() {
		$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$tag = $this->input->get('tag');
    	$this->load->library('pagination');
		$config['base_url'] = '';
		$config['total_rows'] = $this->sole_model->get_num_states($age_from, $age_to, $tag);
		$config['per_page'] = 50; 
		$config['num_links'] = 8;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		$data['total'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		echo json_encode($data);
	}
	
	public function get_num_cities() {
		$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$tag = $this->input->get('tag');
    	$this->load->library('pagination');
		$config['base_url'] = '';
		$config['total_rows'] = $this->sole_model->get_num_cities($age_from, $age_to, $tag);
		$config['per_page'] = 50; 
		$config['num_links'] = 8;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		$data['total'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		echo json_encode($data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */