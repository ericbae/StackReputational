<?php

class generic_model extends CI_Model 
{	
	function insert($table, $data, $return_insert_id = false)
	{
		@$this->db->insert($table, $data);
		//echo $this->db->last_query(); 
		if( $return_insert_id ) { return $this->db->insert_id(); } 
	}
	
	// we return the id of the given table with matching paramters. used to see if data already exists
	function get_table_id($table, $data)
	{
		$query = $this->db->get_where($table, $data);
		//echo $this->db->last_query() . '<br/>'; 
		//log_message('info', 'checking dupes : ' . $this->db->last_query()); 
		if( $query->num_rows() > 0 ) { return $query->row()->id; }
	}

	// a generic option for retrieving data with an order by
	function get_orderby($table, $order_column, $order_type, $limit = null, $offset = null)
	{
		$this->db->order_by($order_column, $order_type);
		$query = $this->db->get($table, $limit, $offset);
		return $query->result();
	}
	
	// a generic option for retrieving data with an order by
	function get_where($table, $data)
	{
		$query = $this->db->get_where($table, $data);
		return $query->result();
	}
	
	// a generic option for retrieving data with an order by
	function get_distinct_where($table, $data)
	{
		$this->db->distinct();
		$query = $this->db->get_where($table, $data);
		return $query->result();
	}	
		
	// a generic option for retrieving data with an order by
	function get_where_orderby($table, $data, $order_column, $order_type, $limit = null, $offset = null)
	{
		$this->db->order_by($order_column, $order_type);
		$query = $this->db->get_where($table, $data, $limit, $offset);
		return $query->result();
	}
		
	// a generic query for getting a single row
	function get_where_single_row($table, $data)
	{
		//$this->db->_compile_select(); 
		$query = $this->db->get_where($table, $data);
		//echo $this->db->last_query(); 
		return $query->row();		
	}
	
	// a generic query for getting a single row and ordering
	function get_where_single_row_order_by($table, $data, $orderby, $sort_type)
	{
		//$this->db->_compile_select(); 
		$this->db->order_by($orderby, $sort_type);
		$query = $this->db->get_where($table, $data);
		//echo $this->db->last_query(); 
		return $query->row();		
	}	
	
	// a generic query for getting a single row and ordering
	function get_single_row_orderby($table, $orderby, $sort_type)
	{
		//$this->db->_compile_select(); 
		$this->db->order_by($orderby, $sort_type);
		$query = $this->db->get($table);
		//echo $this->db->last_query(); 
		return $query->row();		
	}	
	
	// query to find the number of rows for given table and data array
	function get_num_rows($table, $data)
	{
		//$this->db->_compile_select();
		$this->db->select('id');
		$this->db->from($table);
		$this->db->where($data);
		//echo $this->db->last_query();
		return $this->db->count_all_results();
	}
	
	// a generic query for updating table
	function update($table, $data, $where)
	{
		$this->db->update($table, $data, $where);
		//log_message('info', 'checking dupes : ' . $this->db->last_query()); 
	}

	// a generic function to delete
	function delete($table, $data)
	{
		$this->db->delete($table, $data); 
	}	
}
?>