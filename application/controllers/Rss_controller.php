<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Rss_controller extends CI_Controller
{
    private $platforms;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Post_model");
        $this->load->model("Social_media_model");
        $this->load->helper(["url", "form"]);
        $this->load->library("form_validation");
        $this->load->library("session");

        // Fetch platforms from database
        $this->platforms = $this->Social_media_model->get_all_platforms();
    }

    public function index()
    {
        redirect("rss/import");
    }

    public function import()
    {
        $data["active_tab"] = "import";
        $data["platforms"] = $this->platforms;
        $this->load->view("rss/header", $data);
        $this->load->view("rss/import", $data);
        $this->load->view("rss/footer");
    }

public function manage()
{
    $per_page = 10;
    $page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
    if ($page < 1) $page = 1;
    
    $offset = ($page - 1) * $per_page;
    
    // Use manage-specific function to get ALL posts
    $data['posts'] = $this->Post_model->get_posts_for_manage($per_page, $offset);
    $data['total_posts'] = $this->Post_model->count_all_posts();
    $data['current_page'] = $page;
    $data['total_pages'] = ceil($data['total_posts'] / $per_page);
    $data['per_page'] = $per_page;
    $data['platforms'] = $this->platforms;
    $data['active_tab'] = 'manage';
    
    $this->load->view('rss/header', $data);
    $this->load->view('rss/manage', $data);
    $this->load->view('rss/footer');
}


public function dashboard()
{
    $per_page = 10;
    $page = $this->input->get("page") ? intval($this->input->get("page")) : 1;
    if ($page < 1) $page = 1;
    
    $platform_filter = $this->input->get("platform") ?? "all";
    $offset = ($page - 1) * $per_page;

    // Use dashboard-specific function
    $data["posts"] = $this->Post_model->get_posts_for_dashboard($platform_filter, $per_page, $offset);
    $data["total_posts"] = $this->Post_model->count_posts_for_dashboard($platform_filter);
    $data["current_page"] = $page;
    $data["total_pages"] = ceil($data["total_posts"] / $per_page);
    $data["per_page"] = $per_page;
    $data["platforms"] = $this->platforms;
    $data["platform_filter"] = $platform_filter;
    $data["active_tab"] = "dashboard";

    $this->load->view("rss/header", $data);
    $this->load->view("rss/dashboard", $data);
    $this->load->view("rss/footer");
}

    public function fetch_feed()
    {
        header("Content-Type: application/json");

        $feed_url = $this->input->post("feed_url");
        $sort_mode = $this->input->post("sort_mode");

        // Validate URL
        if (!filter_var($feed_url, FILTER_VALIDATE_URL)) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid RSS feed URL",
            ]);
            return;
        }

        // Fetch RSS
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $feed_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => "Mozilla/5.0 (compatible; RSSImporter/1.0)",
        ]);

        $rss_content = curl_exec($ch);
        curl_close($ch);

        if (empty($rss_content)) {
            echo json_encode([
                "success" => false,
                "message" => "Failed to fetch RSS feed",
            ]);
            return;
        }

        // Parse RSS
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($rss_content);

        if ($xml === false) {
            echo json_encode([
                "success" => false,
                "message" => "Failed to parse RSS feed",
            ]);
            return;
        }

        $posts_data = [];
        foreach ($xml->channel->item as $item) {
            $title = (string) $item->title;
            $description = (string) $item->description;
            $content = strip_tags($description);
            $pub_date = date("Y-m-d H:i:s", strtotime((string) $item->pubDate));

            // Extract image
            $image_url = "";

            // Check media namespace
            $media = $item->children("media", true);
            if (isset($media->content)) {
                $image_url = (string) $media->content->attributes()->url;
            }

            // Check enclosure
            if (empty($image_url) && isset($item->enclosure)) {
                $attrs = $item->enclosure->attributes();
                if (isset($attrs->type) && strpos($attrs->type, "image") !== false) {
                    $image_url = (string) $attrs->url;
                }
            }

            // Extract from description
            if (empty($image_url) && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
                $image_url = $matches[1];
            }

            // Count characters for title only
            $char_count = mb_strlen($title, "UTF-8");

            $posts_data[] = [
                "title" => $title,
                "content" => $content,
                "image_url" => $image_url,
                "char_count" => $char_count,
                "pub_date" => $pub_date,
                "priority" => 0,
            ];
        }

        if (empty($posts_data)) {
            echo json_encode([
                "success" => false,
                "message" => "No posts found in RSS feed",
            ]);
            return;
        }

        // Insert posts
        $this->Post_model->insert_posts($posts_data, $sort_mode);

        echo json_encode([
            "success" => true,
            "message" => count($posts_data) . " posts imported successfully",
            "count" => count($posts_data),
        ]);
    }

    public function delete($id)
    {
        $this->Post_model->delete_post($id);
        $this->session->set_flashdata("success", "Post deleted successfully");
        redirect("rss/manage");
    }

    public function update_priority()
    {
        header("Content-Type: application/json");

        $post_id = $this->input->post("post_id");
        $new_priority = intval($this->input->post("new_priority"));

        if (empty($post_id) || $new_priority < 1) {
            echo json_encode(["success" => false, "message" => "Invalid parameters"]);
            return;
        }

        $result = $this->Post_model->update_priority($post_id, $new_priority);

        echo json_encode([
            "success" => $result,
            "message" => $result ? "Priority updated successfully" : "Failed to update priority",
            "new_priority" => $new_priority
        ]);
    }

    public function toggle_platform()
    {
        header("Content-Type: application/json");

        $post_id = $this->input->post("post_id");
        $platform = $this->input->post("platform");

        $result = $this->Post_model->toggle_platform($post_id, $platform);

        echo json_encode(["success" => true, "action" => $result]);
    }

    public function update_platforms()
    {
        header("Content-Type: application/json");

        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        $post_id = $data["post_id"] ?? null;
        $platforms = $data["platforms"] ?? [];

        if (empty($post_id)) {
            echo json_encode([
                "success" => false,
                "message" => "Post ID required",
            ]);
            return;
        }

        // Remove all existing platforms for this post
        $this->db->delete("post_platforms", ["post_id" => $post_id]);

        // Add new selections
        $success = true;
        foreach ($platforms as $platform) {
            $result = $this->db->insert("post_platforms", [
                "post_id" => $post_id,
                "platform" => $platform,
            ]);
            if (!$result) {
                $success = false;
            }
        }

        echo json_encode([
            "success" => $success,
            "message" => $success ? "Platforms updated successfully" : "Failed to update platforms",
            "post_id" => $post_id,
            "platforms" => $platforms,
        ]);
    }
}