<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Social_media_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
	
	/* -------------------------------
       Get all social media platforms
	 --------------------------------*/
     
    public function get_all_platforms() {
        $this->db->select('name');
        $this->db->from('social_media');
        $this->db->order_by('id', 'ASC');
        
        $query = $this->db->get();
        $results = $query->result_array();
        
        // Extract just the platform names into a simple array
        $platforms = array();
        foreach ($results as $row) {
            $platforms[] = $row['name'];
        }
        
        return $platforms;
    }
 
	 /* -------------------------------
       Get all platform details with all columns
	 --------------------------------*/
    public function get_all_platforms_details() {
        $this->db->select('*');
        $this->db->from('social_media');
        $this->db->order_by('id', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
   
	  /* -------------------------------
       Get platform by name
	 --------------------------------*/
    public function get_platform_by_name($platform_name) {
        $this->db->select('*');
        $this->db->from('social_media');
        $this->db->where('name', $platform_name);
        $this->db->limit(1);
        
        $query = $this->db->get();
        return $query->row_array();
    }
    
 
	 /* -------------------------------
       Get platform by ID
	 --------------------------------*/
    public function get_platform_by_id($id) {
        $this->db->select('*');
        $this->db->from('social_media');
        $this->db->where('id', $id);
        $this->db->limit(1);
        
        $query = $this->db->get();
        return $query->row_array();
    }
    
    
	 
	 /* -------------------------------
       Get icon class for platform
	 --------------------------------*/
    public function get_icon_class($platform_name) {
        $platform = $this->get_platform_by_name($platform_name);
        return $platform ? $platform['icon_class'] : 'bi bi-share';
    }
    
   
	 /* -------------------------------
       Get character limit for platform
	 --------------------------------*/
    public function get_char_limit($platform_name) {
        $platform = $this->get_platform_by_name($platform_name);
        return $platform ? $platform['max_chars'] : 0;
    }
    
 
	 /* -------------------------------
       Add new platform
	 --------------------------------*/
    public function add_platform($data) {
        return $this->db->insert('social_media', $data);
    }
    
 
	 /* -------------------------------
       Update platform
	 --------------------------------*/
    public function update_platform($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('social_media', $data);
    }
    
    
	 /* -------------------------------
       Delete platform
	 --------------------------------*/
    public function delete_platform($id) {
        $this->db->where('id', $id);
        return $this->db->delete('social_media');
    }
}