<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Post_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /* -------------------------------
       GET MAX PRIORITY
    --------------------------------*/
    private function get_max_priority()
    {
        $query = $this->db->query("SELECT MAX(priority) AS priority FROM posts");
        if (!$query || $query->num_rows() == 0) {
            return 0;
        }
        $value = $query->row()->priority;
        return $value ? intval($value) : 0;
    }

    /* ------------------------------------
       INSERT POSTS 
    -------------------------------------*/
    public function insert_posts($posts_data, $sort_mode)
    {
        $max_priority = $this->get_max_priority();

        // Sort by date ASC or DESC
        usort($posts_data, function ($a, $b) use ($sort_mode) {
            return $sort_mode === "ASC"
                ? strtotime($a["pub_date"]) - strtotime($b["pub_date"])
                : strtotime($b["pub_date"]) - strtotime($a["pub_date"]);
        });

        // Insert posts with increasing priority
        foreach ($posts_data as $index => $post) {
            $post["priority"] = $max_priority + $index + 1;
            $this->db->insert("posts", $post);
        }

        return true;
    }

 /* ---------------------------
   GENERAL: Get posts
---------------------------*/
public function get_all_posts($limit = null, $offset = 0, $platform_filter = null, $include_no_platforms = true)
{
    if ($platform_filter && $platform_filter !== "all") {
        // Filter by specific platform
        $this->db->select("posts.*, GROUP_CONCAT(post_platforms.platform) as platforms");
        $this->db->from("posts");
        $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "inner");
        $this->db->where("post_platforms.platform", $platform_filter);
        $this->db->group_by("posts.id");
    } else {
        // No specific filter
        $this->db->select("posts.*, GROUP_CONCAT(post_platforms.platform) as platforms");
        $this->db->from("posts");
        
        // Use LEFT join for manage page, INNER join for dashboard
        if ($include_no_platforms) {
            $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "left");
        } else {
            $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "inner");
        }
        
        $this->db->group_by("posts.id");
    }
    
    $this->db->order_by("posts.priority", "ASC");

    if ($limit) {
        $this->db->limit($limit, $offset);
    }

    $query = $this->db->get();
    $results = $query->result_array();

    // Convert platforms to array
    foreach ($results as &$result) {
        $result["platforms"] = $result["platforms"]
            ? explode(",", $result["platforms"])
            : [];
    }

    return $results;
}

 /* ---------------------------
   GENERAL: Get posts for dashboard
---------------------------*/
public function get_posts_for_dashboard($platform_filter = 'all', $limit = null, $offset = 0)
{
    // Method 1: Simple IN clause (works for most cases)
    if ($platform_filter === 'all') {
        // Get posts with at least one platform
        $this->db->select("posts.*, 
            GROUP_CONCAT(DISTINCT post_platforms.platform ORDER BY post_platforms.platform) as platforms");
        $this->db->from("posts");
        $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "inner");
        $this->db->where("post_platforms.platform IS NOT NULL");
    } else {
        // Get posts that have the specific platform
        // First, get the post IDs
        $post_ids_query = $this->db->query("
            SELECT DISTINCT post_id 
            FROM post_platforms 
            WHERE platform = ?
        ", [$platform_filter]);
        
        $post_ids = array_column($post_ids_query->result_array(), 'post_id');
        
        if (empty($post_ids)) {
            return [];
        }
        
        // Now get ALL platforms for these posts
        $this->db->select("posts.*, 
            GROUP_CONCAT(DISTINCT post_platforms.platform ORDER BY post_platforms.platform) as platforms");
        $this->db->from("posts");
        $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "left");
        $this->db->where_in("posts.id", $post_ids);
    }
    
    $this->db->group_by("posts.id");
    $this->db->order_by("posts.priority", "ASC");
    
    if ($limit) {
        $this->db->limit($limit, $offset);
    }

    $query = $this->db->get();
    
    if (!$query) {
        log_message('error', 'Query failed: ' . $this->db->last_query());
        return [];
    }
    
    $results = $query->result_array();

    // Convert platforms to array
    foreach ($results as &$result) {
        $result["platforms"] = $result["platforms"]
            ? explode(",", $result["platforms"])
            : [];
    }

    return $results;
}

/* ---------------------------
   FOR MANAGE PAGE: Get ALL posts (including those with 0 platforms)
---------------------------*/
public function get_posts_for_manage($limit = null, $offset = 0)
{
    // Get ALL posts including those with 0 platforms
    $this->db->select("posts.*, GROUP_CONCAT(post_platforms.platform) as platforms");
    $this->db->from("posts");
    $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "left");
    $this->db->group_by("posts.id");
    $this->db->order_by("posts.priority", "ASC");

    if ($limit) {
        $this->db->limit($limit, $offset);
    }

    $query = $this->db->get();
    $results = $query->result_array();

    // Convert platforms to array
    foreach ($results as &$result) {
        $result["platforms"] = $result["platforms"]
            ? explode(",", $result["platforms"])
            : [];
    }

    return $results;
}

/* ---------------------------
   COUNT FUNCTIONS FOR DASHBOARD
---------------------------*/
public function count_posts_for_dashboard($platform_filter = 'all')
{
    if ($platform_filter === 'all') {
        // Count posts with at least one platform
        return $this->db->query("
            SELECT COUNT(DISTINCT posts.id) as count
            FROM posts
            INNER JOIN post_platforms ON post_platforms.post_id = posts.id
        ")->row()->count ?? 0;
    } else {
        // Count posts with specific platform
        return $this->db->query("
            SELECT COUNT(DISTINCT posts.id) as count
            FROM posts
            INNER JOIN post_platforms ON post_platforms.post_id = posts.id
            WHERE post_platforms.platform = ?
        ", [$platform_filter])->row()->count ?? 0;
    }
}
/* ---------------------------
   COUNT ALL POSTS (for manage page)
---------------------------*/
public function count_all_posts()
{
    return $this->db->count_all("posts");
}
	
	/* ---------------------------
   COUNT POSTS WITH AT LEAST ONE PLATFORM
---------------------------*/
public function count_posts_with_platforms()
{
    $this->db->select("COUNT(DISTINCT posts.id) as count");
    $this->db->from("posts");
    $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "inner");
    
    $query = $this->db->get();
    return $query->row()->count ?? 0;
}

/* ---------------------------
   COUNT POSTS BY SPECIFIC PLATFORM
---------------------------*/
public function count_posts_by_platform($platform)
{
    $this->db->select("COUNT(DISTINCT posts.id) as count");
    $this->db->from("posts");
    $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "inner");
    $this->db->where("post_platforms.platform", $platform);
    
    $query = $this->db->get();
    return $query->row()->count ?? 0;
}
    /* --------------------------
       COUNT POSTS
    ---------------------------*/
    public function count_posts($platform_filter = null)
    {
        if ($platform_filter && $platform_filter !== "all") {
            $this->db->select("COUNT(DISTINCT posts.id) as count");
            $this->db->from("posts");
            $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "inner");
            $this->db->where("post_platforms.platform", $platform_filter);
            $query = $this->db->get();
            return $query->row()->count ?? 0;
        }
        
        // Count ALL posts
        return $this->db->count_all("posts");
    }

    /* --------------------------
       GET POST BY ID
    ---------------------------*/
    public function get_post_by_id($id)
    {
        $this->db->select("posts.*, GROUP_CONCAT(post_platforms.platform) as platforms");
        $this->db->from("posts");
        $this->db->join("post_platforms", "post_platforms.post_id = posts.id", "left");
        $this->db->where("posts.id", $id);
        $this->db->group_by("posts.id");

        $query = $this->db->get();
        $result = $query->row_array();

        if ($result) {
            $result["platforms"] = $result["platforms"]
                ? explode(",", $result["platforms"])
                : [];
        }

        return $result;
    }

    /* --------------------------
       DELETE POST
    ---------------------------*/
    public function delete_post($id)
    {
        $post = $this->db->get_where("posts", ["id" => $id])->row_array();
        if (!$post) {
            return false;
        }

        $old_priority = $post["priority"];

        // Delete post
        $this->db->delete("posts", ["id" => $id]);

        // Delete associated platforms
        $this->db->delete("post_platforms", ["post_id" => $id]);

        // Reorder remaining posts
        $this->db->set("priority", "priority - 1", false);
        $this->db->where("priority >", $old_priority);
        $this->db->update("posts");

        return true;
    }

    /* --------------------------
       UPDATE PRIORITY (FIXED: Proper reordering)
    ---------------------------*/
    public function update_priority($post_id, $new_priority)
    {
        $post = $this->db->get_where("posts", ["id" => $post_id])->row_array();
        if (!$post) {
            return false;
        }

        $old_priority = $post["priority"];

        // If moving to a higher number (down the list)
        if ($old_priority < $new_priority) {
            $this->db->set("priority", "priority - 1", false);
            $this->db->where("priority >", $old_priority);
            $this->db->where("priority <=", $new_priority);
            $this->db->update("posts");
        } 
        // If moving to a lower number (up the list)
        elseif ($old_priority > $new_priority) {
            $this->db->set("priority", "priority + 1", false);
            $this->db->where("priority >=", $new_priority);
            $this->db->where("priority <", $old_priority);
            $this->db->update("posts");
        }

        // Update the moved post
        $this->db->where("id", $post_id);
        $this->db->update("posts", ["priority" => $new_priority]);

        // Renumber all priorities sequentially to fix gaps
        $this->renumber_priorities();

        return true;
    }

    /* --------------------------
       RENUMBER PRIORITIES SEQUENTIALLY
    ---------------------------*/
    private function renumber_priorities()
    {
        // Get all posts sorted by current priority
        $this->db->order_by("priority", "ASC");
        $posts = $this->db->get("posts")->result_array();
        
        // Renumber starting from 1
        $priority = 1;
        foreach ($posts as $post) {
            $this->db->where("id", $post["id"]);
            $this->db->update("posts", ["priority" => $priority]);
            $priority++;
        }
    }

    /* --------------------------
       TOGGLE PLATFORM
    ---------------------------*/
    public function toggle_platform($post_id, $platform)
    {
        $existing = $this->db->get_where("post_platforms", [
            "post_id" => $post_id,
            "platform" => $platform,
        ])->row_array();

        if ($existing) {
            $this->db->delete("post_platforms", [
                "post_id" => $post_id,
                "platform" => $platform,
            ]);
            return "removed";
        }

        $this->db->insert("post_platforms", [
            "post_id" => $post_id,
            "platform" => $platform,
        ]);

        return "added";
    }

    /* --------------------------
       GET PLATFORMS FOR POST
    ---------------------------*/
    public function get_platforms_for_post($post_id)
    {
        $this->db->select("platform");
        $this->db->from("post_platforms");
        $this->db->where("post_id", $post_id);

        $query = $this->db->get();
        return array_column($query->result_array(), "platform");
    }
}