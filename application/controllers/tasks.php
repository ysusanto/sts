<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tasks extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('project_model');
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user) {
            $this->load->view('header');

            $this->load->view('tasks');
        } else {
            show_404();
        }
    }

    public function add() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user) {
            $this->load->view('header');

            $projects = $this->project_model->get_projects();

            $data = array(
                'projects' => $projects);

            $this->load->view('task_add', $data);
        } else {
            show_404();
        }
    }

    public function get_project_features() {
        $project_id = $this->input->post('project_id');
        $parent_id = $this->input->post('parent_id');

        if ($parent_id == '') {
            $parent_id = NULL;
        }

        $features = $this->project_model->get_features($project_id, $parent_id);

        $result = '<option value"">Please Select</option>';
        foreach ($features as $feature) {
            $result .= '<option value="' . $feature->feature_id . '">' . $feature->name . '</option>';
        }
        
        if (sizeof($features) > 0) {
            echo $result;
        }
    }
    
    public function add_task() {
        $project_id = $this->input->post('project_id');
        $feature_id = $this->input->post('feature_id');
        $time_required = $this->input->post('time_required');
        $start_date = $this->input->post('start_date');
        
    }

}
