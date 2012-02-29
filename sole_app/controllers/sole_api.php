<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class sole_api extends REST_Controller
{
	// get users given the conditions
    function users_get() {
		$country = $this->input->get('country');
    	$state = $this->input->get('state');
    	$city = $this->input->get('city');
    	$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$display_name = $this->input->get('display_name');
    	$tag = $this->input->get('tag');
    	$order_by = $this->input->get('order_by');
    	$offset = $this->input->get('offset');
    	$items = $this->sole_model->get_users($country, $state, $city, $age_from, $age_to, $display_name, $tag, $order_by, $offset);
    	$count = $offset+1;
    	foreach($items as $user) {
    		$user->rank = $count;
    		$count++;
    	}
        if($items)
            $this->response($items, 200); // 200 being the HTTP response code
        else
            $this->response(array(), 200); // 200 being the HTTP response code
    }

	// get users given the conditions
    function countries_get() {
		$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$display_name = $this->input->get('display_name');
    	$tag = $this->input->get('tag');
    	$order_by = $this->input->get('order_by');
    	$offset = $this->input->get('offset');
    	$items = $this->sole_model->get_rank_countries($age_from, $age_to, $display_name, $tag, $order_by, $offset);
    	$count = $offset+1;
    	foreach($items as $user) {
    		$user->rank = $count;
    		$count++;
    	}
        if($items)
            $this->response($items, 200); // 200 being the HTTP response code
        else
            $this->response(array(), 200); // 200 being the HTTP response code
    }
    
	// get users given the conditions
    function states_get() {
		$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$display_name = $this->input->get('display_name');
    	$tag = $this->input->get('tag');
    	$order_by = $this->input->get('order_by');
    	$offset = $this->input->get('offset');
    	$items = $this->sole_model->get_rank_states($age_from, $age_to, $display_name, $tag, $order_by, $offset);
    	$count = $offset+1;
    	foreach($items as $user) {
    		$user->rank = $count;
    		$count++;
    	}
        if($items)
            $this->response($items, 200); // 200 being the HTTP response code
        else
            $this->response(array(), 200); // 200 being the HTTP response code
    }
    
	// get users given the conditions
    function cities_get() {
		$age_from = $this->input->get('age_from');
    	$age_to = $this->input->get('age_to');
    	$display_name = $this->input->get('display_name');
    	$tag = $this->input->get('tag');
    	$order_by = $this->input->get('order_by');
    	$offset = $this->input->get('offset');
    	$items = $this->sole_model->get_rank_cities($age_from, $age_to, $display_name, $tag, $order_by, $offset);
    	$count = $offset+1;
    	foreach($items as $user) {
    		$user->rank = $count;
    		$count++;
    	}
        if($items)
            $this->response($items, 200); // 200 being the HTTP response code
        else
            $this->response(array(), 200); // 200 being the HTTP response code
    }    
}