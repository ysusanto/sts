<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of timesheet
 *
 * @author ASUS
 */
class timesheet extends CI_Controller {

    //put your code here

    public function __construct() {
        parent::__construct();
        $this->load->model('timesheet_model');
        $this->load->model('user_model');
        $this->load->model('db_load');
    }

    function viewproject() {
        $getproject = $this->timesheet_model->gettimesheettableall();
        echo json_encode($getproject);
    }

    function detailproject($id) {
        $content['user_id'] = $this->session->userdata('user_id');
        $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
        $content['user'] = $this->user_model->get_user($content['user_id']);
        $this->load->view('header', $content);

        $getdetailproject = $this->timesheet_model->get_detailproject($id);
        $content['projectid'] = $id;
        $content['project'] = $getdetailproject['name'];
        $content['platform'] = $getdetailproject['platform'];
//        print_r($content);die(0);
        $this->load->view('timesheetdetail_view', $content);
    }

    function timesheettable() {
        $data = array();
        foreach ($_GET as $key => $value) {
            $data[$key] = $value;
        }
        $gettimesheet = $this->timesheet_model->gettimesheettable($data);
        echo json_encode($gettimesheet);
    }

    function savetimesheetbaru() {
         date_default_timezone_set('Asia/Jakarta');
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        
//        $implodeitemid=explode('_',$data['item_id']);
        $data['start_date'] = $data['date'] . " " . $data['start_time'];
        $data['end_date'] = $data['date'] . " " . $data['end_time'];
        $datetime1 = strtotime($data['start_date']);
        $datetime2 = strtotime($data['end_date']);
        $interval = abs($datetime2 - $datetime1);
        $data['hours'] = $interval / 60 / 60;
//        echo json_encode($data);die(0);

        $savedata = $this->timesheet_model->savetimesheet($data);
        
        
        $updatetime = $this->user_model->update_lastactivity($this->session->userdata('user_id'));
        
        Redirect(base_url()+"STS/projects", false);
        exit();
    }

    function savetimesheet() {
        date_default_timezone_set('Asia/Jakarta');
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
//        print_r($data);die(0);
//        $implodeitemid=explode('_',$data['item_id']);
        $data['start_date'] = $data['date'] . " " . $data['start_time'];
        $data['end_date'] = $data['date'] . " " . $data['end_time'];
        $datetime1 = strtotime($data['start_date']);
        $datetime2 = strtotime($data['end_date']);
        $interval = abs($datetime2 - $datetime1);
        $data['hours'] = round($interval / 60 / 60);

        $savedata = $this->timesheet_model->savetimesheet($data);
        echo json_encode($savedata);
    }

    function detailmainhours($item_id) {
        date_default_timezone_set('Asia/Jakarta');
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

    function itemname($id) {
        $detailitemname = $this->timesheet_model->getitemname($id);
        $itemname = $detailitemname['name'];
        echo $itemname;
    }

}
