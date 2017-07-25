    <?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -  
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('db_load');
    }

    public function index() {
        $content['user_id'] = $this->session->userdata('user_id');
        $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
        $content['user'] = $this->user_model->get_user($content['user_id']);
//        print_r($content);die(0);
        $this->load->view('header', $content);

        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user) {
            redirect('projects');
        } else {
            $this->load->view('signin');
        }
    }

    public function register() {
        if ($this->session->userdata('user_id')) {
            redirect();
        } else {
            $this->load->view('header');
            $this->load->view('register');
        }
    }

    public function signup() {
        if ($this->session->userdata('user_id')) {
            redirect();
        } else {
            $name = trim($this->input->post('name'));
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $confirm_password = $this->input->post('confirm_password');
            $admin_password = md5($this->input->post('admin_password'));

            if (!$this->user_model->is_email_available($email)) {
                $error_type = 'email';
            } else if ($password != $confirm_password) {
                $error_type = 'different_password';
            } else {
                $is_admin = isset($admin_password) && $admin_password == '9e304d4e8df1b74cfa009913198428ab' ? true : false;

                $result = $this->user_model->add($email, $password, $name, $is_admin);
                if ($result != 'error') {
                    // Log in here
                    $this->session->set_userdata(array('user_id' => $result));
                    redirect();
                    return;
                } else {
                    $error_type = $result;
                }
            }

            if ($error_type == 'email') {
                $this->error_register($name, '', 'E-mail has been used, please specify another e-mail.');
            } else if ($error_type == 'different_password') {
                $this->error_register($name, $email, 'Your password does not match, please try again.');
            } else {
                $this->error_register($name, $email);
            }
        }
    }

    public function signin() {
        if ($this->session->userdata('user_id')) {
            redirect();
        } else {
            $email = $this->input->post('email');
            $password = md5($this->input->post('password'));

            $user = $this->user_model->validate($email, $password);
            if ($user && $user->userid) {
                $this->session->set_userdata(array('user_id' => $user->userid, 'roleid' => $user->role_id, 'username' => $user->username));
                redirect();
            } else {
                $this->load->view('header');

                $data = array(
                    'error' => 'Your email or password is incorrect, please try again.',
                    'email' => $email);
                $this->load->view('signin', $data);
            }
        }
    }

    public function signout() {
        $this->session->unset_userdata(array('user_id' => ''));
        redirect();
    }

    protected function error_register($name, $email, $error_msg = 'General Error!') {
        $this->load->view('header');

        $data = array('error' => $error_msg,
            'name' => $name,
            'email' => $email);
        $this->load->view('register', $data);
    }

    public function projectMaster() {

        /*$sql = "select tpm.*, tp.firstname from t_project_member tpm join tperson tp on tpm.member_id = tp.user_id where project_id = '1'";
        $query = $this->db->query($sql);
        var_dump($query->result_array());exit;*/

        $content['user_id'] = $this->session->userdata('user_id');
        $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
        $content['user'] = $this->user_model->get_user($content['user_id']);
        $this->load->view('header', $content);


        $user = $this->user_model->get_user($content['user_id']);
        $content['platform'] = $this->db_load->getplatform();
        $content['manager'] = $this->db_load->getmember();
        $content['peran'] = $this->db_load->getrolewithouthob();
//        echo json_encode($content['role']);die(0);

        if ($user == false) {
            $this->load->view('signin');
        } else {
            $this->load->view('setupprojects_view', $content);
        }
    }

    public function timesheet() {
        $content['user_id'] = $this->session->userdata('user_id');
        $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
        $content['user'] = $this->user_model->get_user($content['user_id']);
        $this->load->view('header', $content);
//        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($content['user_id']);
        $content['platform'] = $this->db_load->getplatform();
        $content['manager'] = $this->db_load->getmember();

        if ($user == false) {
            $this->load->view('signin');
        } else {
            $this->load->view('timesheet_view', $content);
        }
    }

    public function timesheetdetail() {
        $content['user_id'] = $this->session->userdata('user_id');
        $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
        $content['user'] = $this->user_model->get_user($content['user_id']);
        $this->load->view('header', $content);


//        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($content['user_id']);
        $content['platform'] = $this->db_load->getplatform();
        $content['manager'] = $this->db_load->getmember();

        if ($content['user'] == false) {
            $this->load->view('signin');
        } else {
            $this->load->view('timesheetdetail_view', $content);
        }
    }

    public function report() {
        $content['user_id'] = $this->session->userdata('user_id');
        $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
        $content['user'] = $this->user_model->get_user($content['user_id']);
        $this->load->view('header', $content);
//        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($content['user_id']);
        $content['platform'] = $this->db_load->getplatform();
        $content['manager'] = $this->db_load->getmember();

        if ($user == false) {
            $this->load->view('signin');
        } else {
            $this->load->view('report_view', $content);
        }
    }
    
    public function setupuser(){
        $content['user_id'] = $this->session->userdata('user_id');
         $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
        $this->load->view('header', $content);
        $user = $this->user_model->get_user($content['user_id']);
        $content['position'] = $this->db_load->getrole();
        if ($user == false) {
            $this->load->view('signin');
        } else {
            $this->load->view('setupuser', $content);
        }
    }
    
    public function sendReminder(){
        $this->user_model->send_reminder();
    }
    
    public function registersample(){
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        $balikan = $this->user_model->register_sample($data);
        echo $balikan;
                
    }
    
    public function loginsample(){
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        $balikan = $this->user_model->login_sample($data);
        
        echo $balikan;
    }

}
