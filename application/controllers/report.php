<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of report
 *
 * @author ASUS
 */
class report extends CI_Controller{
    //put your code here
    
public function __construct() {
        parent::__construct();
        $this->load->model('report_model');
        $this->load->model('db_load');
    }
    
    function getprojectreport(){
        $get=$this->report_model->getreportproject();
        echo json_encode($get);
    }
    function getprojectdetail($project_id){
       $detail=$this->report_model->getdetailreportdata($project_id);
       echo json_encode($detail);
    }
    
    function loginreport(){
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
            $this->load->view('login_report', $content);
        }
    }
    
    function timesheetreport(){
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
    
    function gettimesheetdata(){
        $get=$this->report_model->getreportproject();
        echo json_encode($get);
    }
    
    function getloginreport(){
        $get=$this->report_model->getreportuser();
        echo json_encode($get);
    }
    function linkback(){
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        //$chek = $this->report_model->chekdataforlink($data);

        if ($data['type'] == 'detailproj') {
            $html = "<< <a href=\"" . base_url() . "report/timesheetreport\" style=\"margin:none;\">Report Project</a>";
        }
        else if($data['type'] == 'detailproj2'){
           /* $html = "<<<a href=\"" . base_url() . "report/timesheetreport\" style=\"margin:none;\">Report Project</a><button type=\"button\" onclick=\"showDetailRepProject(".$data['id'].")()\" class=\"btn btn-link\" style=\"margin:none;\"><< Project Detail</button>";*/
           $html = "<<<a href=\"" . base_url() . "report/timesheetreport\" style=\"margin:none;\">Project</a><button type=\"button\" onclick=\"showDetailRepProject('" . $data['id'] . "')\" class=\"btn btn-link\" style=\"margin:none;\"><< Feature</button>";
        }
        echo $html;
    }
    function getdetailprojectplatform($id_platform, $id_project){
         $detail=$this->report_model->getdetailreportprojectplatform($id_platform, $id_project);
        echo json_encode($detail);
    }
    function detailmainhours($item_id) {
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('timesheet_model');
        $detailmainhours = $this->timesheet_model->detailmainhours($item_id);
        $detailitemname = $this->timesheet_model->getitemname($item_id);

        $itemname = $detailitemname['name'];

        $table = ' <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <tr>
                      <th>No.</th>
                      <th>Main Hours</th>
                      <th>Range Date</th>
                      <th>Assign</th>
                      
                    </tr>';
        $x = 1;
        if (sizeof($detailmainhours) > 0) {
            foreach ($detailmainhours as $row) {
                $row['start_date'] = strtotime($row['start_date']);
                $row['start_date'] = date('d M Y H:i:s', $row['start_date']);
                $row['end_date'] = strtotime($row['end_date']);
                $row['end_date'] = date('d M Y H:i:s', $row['end_date']);

                $range = $row['start_date'] . " - " . $row['end_date'];

                $table .=' 
                    <tr>
                      <td>' . $x . '</td>
                      <td>' . $row['main_hour'] . '</td>
                      <td>' . $range . '</td>
                      <td>' . $row['username'] . '</td>
                      
                    </tr>';
                $x++;
            }
        }
        $table .='</table>
                </div>';

        echo json_encode(array('table' => $table, 'name' => $itemname));
    }
    function changeitemstatus() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
//        echo json_encode($data);
        $update = $this->report_model->updatestatusclosed($data);
        echo json_encode($update);
    }
}
