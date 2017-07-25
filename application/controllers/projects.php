<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Projects extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('project_model');
        $this->load->model('timesheet_model');
        $this->load->model('db_load');
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user_id > 0) {
            $content['user_id'] = $this->session->userdata('user_id');
            $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
            $content['user'] = $this->user_model->get_user($content['user_id']);
//        print_r($content);die(0);
            $this->load->view('header', $content);
//            $this->load->view('header');
//            $projects = $this->project_model->get_projects();
//
//            foreach ($projects as $project) {
//                $feature_count = $this->project_model->feature_count($project->project_id);
//                $project->feature_count = $feature_count;
//            }
            $projects = '';
            $data = array(
                'projects' => $projects,
                'is_admin' => true);

            $this->load->view('projects', $data);
        } else {
            show_404();
        }
    }

    public function index2() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user_id > 0) {
            $this->load->view('header');

            $projects = $this->project_model->get_projects();

            foreach ($projects as $project) {
                $feature_count = $this->project_model->feature_count($project->project_id);
                $project->feature_count = $feature_count;
            }

            $data = array(
                'projects' => $projects,
                'is_admin' => true);

            $this->load->view('projects', $data);
        } else {
            show_404();
        }
    }

    public function project($project_id) {
        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user_id > 0 && $user->is_admin == 1) {
            $this->load->view('header');

            $project = $this->project_model->get($project_id);

            $data = array(
                'project' => $project,
                'project_name' => $project->name,
                'project_alias' => $project->alias,
                'features' => $this->get_feature_grid($project_id),
                'tasks' => $this->project_model->get_tasks($project_id, true));

            $this->load->view('project_details', $data);
        } else {
            show_404();
        }
    }

    private function get_feature_grid($project_id) {
        $features_parent = $this->project_model->get_features($project_id);

        $result = '<tr>'
                . '<th>No.</th>'
                . '<th colspan="3">Feature</th>'
                . '<th>Estimated</th>'
                . '<th>Actual</th>'
                . '</tr>';

        $index = 0;
        foreach ($features_parent as $feature_parent) {

            $features_group = $this->project_model->get_features($project_id, $feature_parent->feature_id);

            if (sizeof($features_group) > 0) {

                foreach ($features_group as $feature_group) {

                    $features_function = $this->project_model->get_features($project_id, $feature_group->feature_id);

                    if (sizeof($features_function) > 0) {

                        foreach ($features_function as $feature_function) {

                            $index++;
                            $result .= '<tr>'
                                    . '<td  style="text-align: center;">' . $index . '</td>'
                                    . '<td>' . $feature_parent->name . '</td>'
                                    . '<td>' . $feature_group->name . '</td>'
                                    . '<td>' . $feature_function->name . '</td>'
                                    . $this->handle_feature($feature_function)
                                    . '</tr>';
                            // Add onClick
                        }
                    } else {

                        $index++;
                        $result .= '<tr>'
                                . '<td  style="text-align: center;">' . $index . '</td>'
                                . '<td>' . $feature_parent->name . '</td>'
                                . '<td colspan="2">' . $feature_group->name . '</td>'
                                . $this->handle_feature($feature_group)
                                . '</tr>';
                    }
                }
            } else {
                $index++;
                $result .= '<tr>'
                        . '<td  style="text-align: center;">' . $index . '</td>'
                        . '<td colspan="3">' . $feature_parent->name . '</td>'
                        . $this->handle_feature($feature_parent)
                        . '</tr>';
            }
        }

        return $result;
    }

    private function handle_feature($feature) {
        $task = $this->project_model->get_task($feature->feature_id);
        if ($task) {
            return '<td style="text-align: center;">' . $task->hour_expected . '</td>'
                    . '<td style="text-align: center;">' . $task->hour_consumed . '</td>';
        }
    }

    public function get_platforms($project_id) {
        
    }

    function projecttabel() {
        $getproject = $this->project_model->get_projecttabel();
        echo json_encode($getproject);
    }

    function saveproject() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        // echo json_encode($data);
        // echo json_encode(
        //     array(
        //         'status' => 0,
        //         'type' => 'project',
        //         'msg' => json_encode($data)
        //     )
        // );
        // return;
        if ($data['project_id'] == '') {
            if (!empty($_FILES['filename']['name'])) {
                $dataupload = array(
                    'path' => 'assets/uploadfile/',
                    'nama' => $data['nama']
                );
                $uploadfile = $this->db_load->uploadfile($dataupload);

                if ($uploadfile['status'] == 0) {
                    echo json_encode($uploadfile);
                    die(0);
                }
                $data['path'] = $uploadfile['path'];
            } else {
                $data['path'] = '';
            }

            /* echo json_encode(array(
              'status' => 0,
              'msg' => json_encode($data)
              ));
              die(); */
            $hasil = $this->project_model->addproject($data);
        } else {
            $hasil = $this->project_model->updateproject($data);
        }
        echo json_encode($hasil);
    }

    function detailproject($id) {
        $getdetailproject = $this->project_model->get_detailproject($id);
        echo json_encode($getdetailproject);
    }

    function tabelfeature() {
        $data = array();
        foreach ($_GET as $key => $value) {
            $data[$key] = $value;
        }
        $getfeature = $this->project_model->get_featuretabel($data);
        echo json_encode($getfeature);
    }

    function savefeature() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }

        if ($data['feature_id'] == '' && $data['idfeature'] == '') {
            $saveproject = $this->project_model->addfeature($data);
            $hasil = $saveproject;
        } /*else {
            $updateproject = $this->project_model->updatefeature($data);
            $hasil = $updateproject;
        }*/
        if ($data['idfeature'] != '') {
            $updateproject = $this->project_model->updatefeature($data);
            $hasil = $updateproject;
        }
        echo json_encode($hasil);
    }
    function deletefeaturedProject($id){
        $datadelete = $this->project_model->deleteprojectfeatured($id);
        $hasil = $datadelete;
        echo json_encode($hasil);
    }
    function detailfeature($id) {
        $getdetailfeature = $this->project_model->get_detailfeature($id);
//        $getdetailfeature['url']=''
        echo json_encode($getdetailfeature);
    }

    function tabelgroup() {
        $data = array();
        foreach ($_GET as $key => $value) {
            $data[$key] = $value;
        }
        $gettabel = $this->project_model->get_grouptabel($data);
        echo json_encode($gettabel);
    }

    function savegroup() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }

        if ($data['group_id'] == '' && $data['idgroup'] == '') {
            $save = $this->project_model->addgroup($data);
            $hasil = $save;
        } /*else {
            $update = $this->project_model->updategroup($data);
            $hasil = $update;
        }*/
        if ($data['idgroup'] != '') {
            $save = $this->project_model->updategroup($data);
            $hasil = $save;
        }
        echo json_encode($hasil);
    }
    function deletegroupProject($id){
        $datadelete = $this->project_model->deleteprojectgroup($id);
        $hasil = $datadelete;
        echo json_encode($hasil);
    }

    function tabelitem() {
        $data = array();
        foreach ($_GET as $key => $value) {
            $data[$key] = $value;
        }
        $gettabel = $this->project_model->get_Item($data);
        echo json_encode($gettabel);
    }

    function changeitemstatus() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
//        echo json_encode($data);
        $update = $this->project_model->updatestatusclosed($data);
        echo json_encode($update);
    }

    /** ADDING STATUS PROJECT FOM CHNAGE ALL ITEM **/
    function changeitemstatusPerProject() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        //echo json_encode($data);
        $update = $this->project_model->updatestatusclosedPerProject($data);
        echo json_encode($update);
    }
    /** END ADDING STATUS PROJECT FOM CHNAGE ALL ITEM **/
    function saveitem() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }

        if ($data['item_id'] == '' && $data['iditem'] == '') {
            $save = $this->project_model->addItem($data);
            $hasil = $save;
        } /*else {
            $update = $this->project_model->updategroup($data);
            $hasil = $update;
        }*/
        if ($data['iditem'] != '') {
            $update = $this->project_model->updateitem($data);
            $hasil = $update;
        }
        echo json_encode($hasil);
    }

    function deleteitemProject($id){
        $datadelete = $this->project_model->deleteprojectitem($id);
        $hasil = $datadelete;
        echo json_encode($hasil);
    }
    function detailgroup($id) {
        $getdetailgroup = $this->project_model->get_detailgroup($id);
        echo json_encode($getdetailgroup);
    }

    function linkback() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        $chek = $this->project_model->chekdataforlink($data);

        if ($data['type'] == 'item') {
            $html = "<button type=\"button\"  class=\"btn btn-link\"><<<a href=\"" . base_url() . "home/projectMaster\" style=\"margin:none;\">Project</a></button><button type=\"button\" onclick=\"detailproject('" . $chek['proplat_id'] . "')\" class=\"btn btn-link\" style=\"margin:none;\"><< Feature</button><button type=\"button\" onclick=\"detailfeature('" . $chek['feature_id'] . "')\" class=\"btn btn-link\" style=\"margin:none;\"><< Group</button>";
        } else if ($data['type'] == 'group') {
            $html = "<button type=\"button\"  class=\"btn btn-link\"><<<a href=\"" . base_url() . "home/projectMaster\" style=\"margin:none;\">Project</a></button><button type=\"button\" onclick=\"detailproject('" . $chek['proplat_id'] . "')\" class=\"btn btn-link\" style=\"margin:none;\"><< Feature</button>";
        } else {
            $html = "<button type=\"button\"  class=\"btn btn-link\"><<<a href=\"" . base_url() . "home/projectMaster\" style=\"margin:none;\">Project</a></button>";
        }
        echo $html;
    }

    function get_projectid_bygroupid($groupid) {
        return $this->project_model->get_projectid_bygroupid($groupid);
    }

    function getaddMemberModal() {
        $data = array();
        $groupid = $_POST['groupid'];
        $project_id = $this->get_projectid_bygroupid($groupid);
        //echo $project_id;
        $data['member'] = $this->project_model->get_project_member($project_id);
       // echo json_encode($data);
        echo $this->load->view('item_addmember_modal', $data, FALSE);
    }

    function getmemberpersonal($groupid, $itemid){
        $project_id = $this->get_projectid_bygroupid($groupid);
        //echo $project_id;
        $query = "select * from t_project_member where project_id={$project_id}";
        $members = $this->db->query($query, array($project_id));
        //var_dump($members->result());exit;
       if ($members->num_rows() > 0) {
            $memberarray = '';
            foreach ($members->result_array() as $member) {
                if ($memberarray != '') {
                    $memberarray .= ',';
                }
                $memberarray .= "'" . $member['member_id'] . "'";
            }
            //$query_member_name = "select * from tperson where person_id in(" . $memberarray . ")";
            $query_member_name = "
                    select tp.user_id, tp.salutation, tp.firstname, t2.user_id as user_id_ttask 
                        from tperson as tp LEFT JOIN
                            (select * from t_task WHERE item_id = '$itemid') as t2
                                 ON t2.user_id = tp.user_id where person_id in(" . $memberarray . ")
                                    GROUP BY t2.user_id, tp.user_id, tp.firstname order by tp.firstname ASC";
            $queryperson = $this->db->query($query_member_name);
            //print_r($queryperson->result());exit;
            $a = 0;
            if ($queryperson->num_rows() > 0) {
                 foreach($queryperson->result() as $indeks => $p) {
                        if ($p->user_id_ttask!=NULL) {
                            $checked = "checked";
                        }
                        else{
                            $checked = "";
                    }
                    $data[] = array(
                        '',
                        '',
                        '
                        <input type="checkbox" name="id_member_e['.$a++.']" value="'.$p->user_id.'" '.$checked.' />
                        
                        <span class="lbl">'.$p->salutation.' '.$p->firstname.'</span>'
                        );
                    }

                //return $queryperson->result_array();
            }
        }
        if($data){
        echo json_encode($data);exit;
        }
        //echo $this->load->view('setupprojects_view', $data, FALSE);
    }

    function saveMemberAssignment() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        //$data = {"member":["4","5"]}
//        $addassignment_result = $this->project_model->add_assignment($data);

        $addassignment_result = $this->project_model->addtabletask($data);
        echo json_encode($addassignment_result);
    }

     function saveMemberAssignment2() {
        $addmember_group_id = $this->input->post('addmember_group_id');
        $id_member_e      = $this->input->post('id_member_e');
        $addmember_itemid = $this->input->post('addmember_itemid');
        $updateassignment_result = $this->project_model->updatetabletask($id_member_e, $addmember_itemid, $addmember_group_id);

        echo json_encode($updateassignment_result);
    }

    function remove_assignment() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        //data is user_id, item_id
        $result = $this->project_model->remove_assignment($data);
        echo json_encode($result);
    }

    function deleteproject($project_id) {
        $delete = $this->project_model->harddeleteproject($project_id);
        echo json_encode($delete);
    }

    function getdashboard() {
        $content = array();
        $content['task'] = $this->timesheet_model->gettaskfordashboard();
        echo $this->load->view("dashboardview", $content);
    }

    function updatemanhours() {
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
//        echo "cde";die(0);
        $balikan = $this->timesheet_model->updatetimesheet($data);
    }

    function sendemail($id) {

        $sql = 'select pro.name as proname,pro.client,pro.userid, i.item_id,i.name,x.user_id,t.hours,g.name as grup,f.name as fitur,person.firstname,person.lastname,person.salutation '
                . 'from item i left join t_task x on x.item_id = i.item_id'
                . ' left join `group` g on g.group_id=i.group_id  left '
                . 'join feature f on g.feature_id=f.feature_id left join project_platform p on f.proplat_id=p.proplat_id '
                . 'left join project_platform pp on f.proplat_id = pp.proplat_id
                   left join project pro on pp.project_id = pro.project_id '
                . 'left join tperson person on person.user_id = x.user_id '
                . 'left join (select item_id,sum(main_hour) as "hours" from timesheet group by item_id)t on i.item_id=t.item_id where  pp.project_id = "' . $id . '" and i.isclosed = "0" order by x.user_id';

        $query = $this->db->query($sql)->result_array();
        $previous = "0";
        $message = "Dear " ;
        $name = "";
        $isi = "";
        $penutup = "<br>";
        foreach ($query as $value) {
            echo $value['user_id'];echo "-";
            echo $previous;echo $penutup;
//            $previous = $value['user_id'];
            if($value['user_id'] == $previous){
                $name = $value['salutation']." ".$value['firstname']." ".$value['lastname'];
                $isi = "<br> anda telah terpilih untuk mengerjakan project". $value['proname'];
                
            }else{
                $message = $message.$name.$penutup;
                 echo $message;
                 echo $name = "";
            }
            $previous = $value['user_id'];
        }
       
    }

    /** putre **/
    function getprojectFeatures($id){
        $this->db->where('feature_id',$id);
        $data = $this->db->get('feature')->row();
        echo json_encode($data);
    }
    function getprojectGroup($id){
        $this->db->where('group_id',$id);
        $data = $this->db->get('group')->row();
        echo json_encode($data);
    }
    function getprojectItem($id){
        $this->db->where('item_id',$id);
        $data = $this->db->get('item')->row();
        echo json_encode($data);
    }
    function addnewline($countDetail){
        $no = 1;
        $no = $countDetail+1;
        $manager = $this->db_load->getmember();
        $peran   = $this->db_load->getrolewithouthob();

        $html = '<div id="divMemberdiv'.$no.'">
                        <br><br/><select name="member[]" id="member[]" class="form-control" style="width:270px !important;">';           
                            if (isset($manager)) {
                                   foreach ($manager as $value) {
                                   $html .= '<option value="'.$value['userid'].'">'.$value['salutation'] . " " . $value["firstname"] . " " . $value['lastname'].' </option>';
                                }
                            }                    
        $html .= '</select><br/><br/>
                        <select name="peran[]" id="peran[]" class="form-control" style="width:270px !important;">';                              
                            if (isset($peran)) {
                                 foreach ($peran as $value) {

        $html .= '<option value="'.$value['role_id'].'">'.$value['name'].'</option>';
                                }
                            }
                                   
        $html .= '</select>&nbsp;<button type="button" class="btn btn-danger btn-sm" onClick="remove('.$no.')"><span class="glyphicon glyphicon-remove"></span></button></div>';
        echo $html;
    }
    function addnewlineedit($countDetail){
        $no = 1;
        $no = $countDetail+1;
        $manager = $this->db_load->getmember();
        $peran   = $this->db_load->getrolewithouthob();

        $html = '<div id="divMemberdiv'.$no.'">
                        <br><select name="member2[]" id="member2[]" class="form-control" style="width:270px !important;">';           
                            if (isset($manager)) {
                                   foreach ($manager as $value) {
                                   $html .= '<option value="'.$value['userid'].'">'.$value['salutation'] . " " . $value["firstname"] . " " . $value['lastname'].' </option>';
                                }
                            }                    
        $html .= '</select><br/><br/>
                        <select name="peran2[]" id="peran2[]" class="form-control" style="width:270px !important;">';                              
                            if (isset($peran)) {
                                 foreach ($peran as $value) {

        $html .= '<option value="'.$value['role_id'].'">'.$value['name'].'</option>';
                                        }
                                 }
                                   
        $html .= '</select>&nbsp;<button type="button" class="class="btn btn-danger btn-sm" onClick="remove('.$no.')"><span class="glyphicon glyphicon-remove"></span></button></div>';
        echo $html;
    }
    function geteditproject(){
        $project_id = $this->input->post('project_id');
        $geteditproject = $this->project_model->get_editproject($project_id);
        echo json_encode($geteditproject);
        //echo $geteditproject;
    }

}
