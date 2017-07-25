<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class stssetup extends CI_Controller {

    public function __construct() {
        parent::__construct();
//        $this->load->model('project_model');
        $this->load->model('user_model');
        $this->load->model('db_load');
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user_id > 0) {
            $content['user_id'] = $this->session->userdata('user_id');
            $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
            $content['user'] = $this->user_model->get_user($content['user_id']);
            $this->load->view('header', $content);
            $projects = '';
            $data = array(
                'projects' => $projects,
                'is_admin' => true);

            $this->load->view('projects', $data);
        } else {
            show_404();
        }
    }

    public function getuserlist() {
        $userlist = $this->user_model->getalluser();
        $centertemplate = "<div style='text-align:center;'>";
        $endtemplate = "</div>";
        $htmlarray = array();
//        echo json_encode($userlist);die(0);
        foreach ($userlist as $value) {
            $useridhtml = $centertemplate . $value['userid'] . $endtemplate;
            $salutationhtml = $centertemplate . $value['salutation'] . $endtemplate;
            $firstnamehtml = $centertemplate . $value['firstname'] . $endtemplate;
            $lastnamehtml = $centertemplate . $value['lastname'] . $endtemplate;
            $usernamehtml = $centertemplate . $value['username'] . $endtemplate;
            $emailhtml = $centertemplate . $value['email'] . $endtemplate;
            $rolehtml = $centertemplate . $value['name'] . $endtemplate;
            $htmlarray['aaData'][] = array($useridhtml, $salutationhtml,
                $firstnamehtml, $lastnamehtml, $usernamehtml, $emailhtml, $rolehtml);
        }
        echo json_encode($htmlarray);



//        echo json_encode($querygetall);
    }

    function saveuser() {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = time();
        $data = array();
        $readabletanggal = date('Y-m-d', $tanggal);
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }

        $isexist = $this->checkuser($data['email']);

        if ($isexist > 0) {
            echo json_encode(array("status"=>"0","message"=>"User Sudah Terdaftar Silahkan daftar User Baru"));
        } else {
            $datainsertdetail = array(
                'username' => strtolower($data['firstname'] . $data['lastname']),
                'email' => $data['email'],
                'password' => md5("seatechmobile"),
                'role_id' => $data['position'],
                'created_date' => $readabletanggal,
                'isdelete' => "0"
            );
            $insertuser = $this->user_model->addnewuser($datainsertdetail, "tuser");
            $tanggal2 = time();
            $datainsertperson = array(
                'user_id' => $insertuser,
                'salutation' => $data['salutation'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'created_by' => "Tjuk Indarsin",
                'created_date' => $readabletanggal
            );
            $insertperson = $this->user_model->addnewuser($datainsertperson, "tperson");
            echo json_encode(array("status"=>"1","message"=>"Registrasi Sukses"));
        }
    }

    function checkuser($email) {
        $returnedbool = 0;      
        $checkuser = $this->user_model->checkuser($email);
        if (sizeof($checkuser)>0)
            $returnedbool++;
        return $returnedbool;
    }

}
