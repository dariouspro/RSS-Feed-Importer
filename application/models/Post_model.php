<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* -------------------------------
       SAFE MAX PRIORITY FETCHER
    --------------------------------*/
    private function get_max_priority() {
        $query = $this->db->query("SELECT MAX(priority) AS priority FROM posts");

        if (!$query || $query->num_rows() == 0) {
            return 0;
        }

        $value = $query->row()->priority;
        return $value ? intval($value) : 0;
    }

    /* ------------------------------------
       INSERT POSTS SAFELY (NULL FIX ADDED)
    -------------------------------------*/
    public function insert_posts($posts_data, $sort_mode) {

        // Safe max priority
        $max_priority = $this->get_max_priority();

        // Sort by date ASC or DESC
        usort($posts_data, function($a, $b) use ($sort_mode) {
            return ($sort_mode === 'ASC')
                ? strtotime($a['pub_date']) - strtotime($b['pub_date'])
                : strtotime($b['pub_date']) - strtotime($a['pub_date']);
        });

        // Insert posts with increasing priority
        foreach ($posts_data as $index => $post) {
            $post['priority'] = $max_priority + $index + 1;
            $this->db->insert('posts', $post);
        }

        return true;
    }

    /* ---------------------------
         GET ALL POSTS
    ----------------------------*/
  public function get_all_posts($limit = null, $offset = 0, $platform_filter = null) {
    if ($platform_filter && $platform_filter !== 'all') {
        // Filter by platform - only get posts with this platform assigned
        $this->db->select('posts.*, GROUP_CONCAT(post_platforms.platform) as platforms');
        $this->db->from('posts');
        $this->db->join('post_platforms', 'post_platforms.post_id = posts.id', 'inner');
        $this->db->where('post_platforms.platform', $platform_filter);
        $this->db->group_by('posts.id');
        $this->db->order_by('posts.priority', 'ASC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        $results = $query->result_array();
    } else {
        // Get all posts
        $this->db->select('posts.*, GROUP_CONCAT(post_platforms.platform) as platforms');
        $this->db->from('posts');
        $this->db->join('post_platforms', 'post_platforms.post_id = posts.id', 'left');
        $this->db->group_by('posts.id');
        $this->db->order_by('posts.priority', 'ASC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        $results = $query->result_array();
    }
    
    // Convert platforms to array
    foreach ($results as &$result) {
        $result['platforms'] = $result['platforms'] ? explode(',', $result['platforms']) : array();
    }
    
    return $results;
}
    /* --------------------------
         COUNT POSTS
    ---------------------------*/
    public function count_posts($platform_filter = null) {
        if ($platform_filter && $platform_filter !== 'all') {

            $this->db->select('COUNT(DISTINCT posts.id) as count');
            $this->db->from('posts');
            $this->db->join('post_platforms', 'post_platforms.post_id = posts.id', 'left');
            $this->db->where('post_platforms.platform', $platform_filter);

            $query = $this->db->get();
            return $query->row()->count ?? 0;
        }

        return $this->db->count_all('posts');
    }

    /* --------------------------
         GET POST BY ID
    ---------------------------*/
    public function get_post_by_id($id) {

        $this->db->select('posts.*, GROUP_CONCAT(post_platforms.platform) as platforms');
        $this->db->from('posts');
        $this->db->join('post_platforms', 'post_platforms.post_id = posts.id', 'left');
        $this->db->where('posts.id', $id);
        $this->db->group_by('posts.id');

        $query = $this->db->get();
        $result = $query->row_array();

        if ($result) {
            $result['platforms'] = $result['platforms']
                ? explode(',', $result['platforms'])
                : [];
        }

        return $result;
    }

    /* --------------------------
         DELETE POST
    ---------------------------*/
    public function delete_post($id) {

        $post = $this->db->get_where('posts', ['id' => $id])->row_array();
        if (!$post) return false;

        $old_priority = $post['priority'];

        // Delete post
        $this->db->delete('posts', ['id' => $id]);

        // Reorder
        $this->db->set('priority', 'priority - 1', FALSE);
        $this->db->where('priority >', $old_priority);
        $this->db->update('posts');

        return true;
    }

    /* --------------------------
       UPDATE PRIORITY LOGIC
    ---------------------------*/
    public function update_priority($post_id, $new_priority) {

        $post = $this->db->get_where('posts', ['id' => $post_id])->row_array();
        if (!$post) return false;

        $old_priority = $post['priority'];

        if ($old_priority < $new_priority) {
            // Move down
            $this->db->set('priority', 'priority - 1', FALSE);
            $this->db->where('priority >', $old_priority);
            $this->db->where('priority <=', $new_priority);
            $this->db->update('posts');

        } else if ($old_priority > $new_priority) {
            // Move up
            $this->db->set('priority', 'priority + 1', FALSE);
            $this->db->where('priority >=', $new_priority);
            $this->db->where('priority <', $old_priority);
            $this->db->update('posts');
        }

        // Update target post
        $this->db->where('id', $post_id);
        $this->db->update('posts', ['priority' => $new_priority]);

        return true;
    }

    /* --------------------------
       TOGGLE PLATFORM
    ---------------------------*/
    public function toggle_platform($post_id, $platform) {

        $existing = $this->db->get_where('post_platforms', [
            'post_id' => $post_id,
            'platform' => $platform
        ])->row_array();

        if ($existing) {
            $this->db->delete('post_platforms', [
                'post_id' => $post_id,
                'platform' => $platform
            ]);
            return 'removed';
        }

        $this->db->insert('post_platforms', [
            'post_id' => $post_id,
            'platform' => $platform
        ]);

        return 'added';
    }

    /* --------------------------
         GET PLATFORMS FOR POST
    ---------------------------*/
    public function get_platforms_for_post($post_id) {

        $this->db->select('platform');
        $this->db->from('post_platforms');
        $this->db->where('post_id', $post_id);

        $query = $this->db->get();
        return array_column($query->result_array(), 'platform');
    }
}
