<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rss_controller extends CI_Controller {
    
    private $platforms = array('Facebook', 'X (Twitter)', 'LinkedIn', 'Instagram', 'TikTok', 'Threads');
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->helper(array('url', 'form'));
        $this->load->library('form_validation');
    }
    
    public function index() {
        redirect('rss/import');
    }
    
    public function import() {
        $data['active_tab'] = 'import';
        $this->load->view('rss/header', $data);
        $this->load->view('rss/import');
        $this->load->view('rss/footer');
    }
    
    public function manage() {
        $per_page = 10;
        $page = $this->input->get('page') ?? 1;
        $offset = ($page - 1) * $per_page;
        
        $data['posts'] = $this->Post_model->get_all_posts($per_page, $offset);
        $data['total_posts'] = $this->Post_model->count_posts();
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($data['total_posts'] / $per_page);
        $data['per_page'] = $per_page;
        $data['platforms'] = $this->platforms;
        $data['active_tab'] = 'manage';
        
        $this->load->view('rss/header', $data);
        $this->load->view('rss/manage', $data);
        $this->load->view('rss/footer');
    }
    
    public function dashboard() {
        $per_page = 10;
        $page = $this->input->get('page') ?? 1;
        $platform_filter = $this->input->get('platform') ?? 'all';
        $offset = ($page - 1) * $per_page;
        
        $data['posts'] = $this->Post_model->get_all_posts($per_page, $offset, $platform_filter);
        $data['total_posts'] = $this->Post_model->count_posts($platform_filter);
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($data['total_posts'] / $per_page);
        $data['per_page'] = $per_page;
        $data['platforms'] = $this->platforms;
        $data['platform_filter'] = $platform_filter;
        $data['active_tab'] = 'dashboard';
        
        $this->load->view('rss/header', $data);
        $this->load->view('rss/dashboard', $data);
        $this->load->view('rss/footer');
    }
    
    public function fetch_feed() {
        header('Content-Type: application/json');
        
        $feed_url = $this->input->post('feed_url');
        $sort_mode = $this->input->post('sort_mode');
        
        // Validate URL
        if (!filter_var($feed_url, FILTER_VALIDATE_URL)) {
            echo json_encode(array('success' => false, 'message' => 'Invalid RSS feed URL'));
            return;
        }
        
        // Fetch RSS feed
        $rss_content = @file_get_contents($feed_url);
        
        if ($rss_content === FALSE) {
            echo json_encode(array('success' => false, 'message' => 'Failed to fetch RSS feed'));
            return;
        }
        
        // Parse RSS
        $xml = @simplexml_load_string($rss_content);
        
        if ($xml === FALSE) {
            echo json_encode(array('success' => false, 'message' => 'Failed to parse RSS feed'));
            return;
        }
        
        $posts_data = array();
foreach ($xml->channel->item as $item) {
    $title = (string)$item->title;
    $description = (string)$item->description;
    $content = strip_tags($description);
    $pub_date = date('Y-m-d H:i:s', strtotime((string)$item->pubDate));
    
    // Extract image - Enhanced version
    $image_url = '';
    
    // Method 1: media:content namespace
    $media = $item->children('media', true);
    if (isset($media->content)) {
        $image_url = (string)$media->content->attributes()->url;
    }
    
    // Method 2: media:thumbnail
    if (empty($image_url) && isset($media->thumbnail)) {
        $image_url = (string)$media->thumbnail->attributes()->url;
    }
    
    // Method 3: enclosure tag
    if (empty($image_url) && isset($item->enclosure)) {
        $attrs = $item->enclosure->attributes();
        if (isset($attrs->type) && strpos($attrs->type, 'image') !== false) {
            $image_url = (string)$attrs->url;
        }
    }
    
    // Method 4: Look for <image> tag
    if (empty($image_url) && isset($item->image)) {
        $image_url = (string)$item->image;
    }
    
    // Method 5: Extract from description HTML (img tag)
    if (empty($image_url) && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
        $image_url = $matches[1];
    }
    
    // Method 6: Look for content:encoded
    if (empty($image_url)) {
        $content_ns = $item->children('content', true);
        if (isset($content_ns->encoded)) {
            $encoded = (string)$content_ns->encoded;
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $encoded, $matches)) {
                $image_url = $matches[1];
            }
        }
    }
    
    // Count characters for title only
    $char_count = mb_strlen($title, 'UTF-8');
    
    $posts_data[] = array(
        'title' => $title,
        'content' => $content,
        'image_url' => $image_url,
        'char_count' => $char_count,
        'pub_date' => $pub_date,
        'priority' => 0
    );
}     
        if (empty($posts_data)) {
            echo json_encode(array('success' => false, 'message' => 'No posts found in RSS feed'));
            return;
        }
        
        // Insert posts
        $this->Post_model->insert_posts($posts_data, $sort_mode);
        
        echo json_encode(array(
            'success' => true, 
            'message' => count($posts_data) . ' posts imported successfully',
            'count' => count($posts_data)
        ));
    }
    
    public function delete($id) {
        $this->Post_model->delete_post($id);
        $this->session->set_flashdata('success', 'Post deleted successfully');
        redirect('rss/manage');
    }
    
    public function update_priority() {
        header('Content-Type: application/json');
        
        $post_id = $this->input->post('post_id');
        $new_priority = $this->input->post('new_priority');
        
        $result = $this->Post_model->update_priority($post_id, $new_priority);
        
        echo json_encode(array('success' => $result));
    }
    
    public function toggle_platform() {
        header('Content-Type: application/json');
        
        $post_id = $this->input->post('post_id');
        $platform = $this->input->post('platform');
        
        $result = $this->Post_model->toggle_platform($post_id, $platform);
        
        echo json_encode(array('success' => true, 'action' => $result));
    }
}
