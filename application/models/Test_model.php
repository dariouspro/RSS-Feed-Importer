<?php
class Test_model extends CI_Model {

    public function check() {
        return $this->db->query("SELECT NOW() as time")->row();
    }

}
public function testdb()
{
    $this->load->model('Test_model');
    $result = $this->Test_model->check();
    echo "Database Connected Successfully! Server Time: " . $result->time;
}
