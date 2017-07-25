<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        require_once 'assets/PHPExcel.php';
        require_once 'assets/PHPExcel/IOFactory.php';
//        error_reporting(E_ALL);
//        set_error_handler(array($this, "errorHandler"), E_ALL);
        date_default_timezone_set('Asia/Jakarta');
        ini_set('memory_limit', '500M');
        ini_set('max_execution_time', '300000');
    }

    public function get($project_id) {
        $query = $this->db->get_where('tb_project', array('project_id' => $project_id));
        if ($query->num_rows() > 0) {
            return $query->row();
        }
    }

    public function get_projects() {
        $query = $this->db->get('tb_project');
        return $query->result();
    }

    public function feature_count($project_id) {
        $index = 0;
        $features_parent = $this->get_features($project_id);
        foreach ($features_parent as $feature_parent) {
            $features_group = $this->get_features($project_id, $feature_parent->feature_id);
            if (sizeof($features_group) > 0) {
                foreach ($features_group as $feature_group) {
                    $features_function = $this->get_features($project_id, $feature_group->feature_id);
                    if (sizeof($features_function) > 0) {
                        $index += sizeof($features_function);
                    } else {
                        $index++;
                    }
                }
            } else {
                $index++;
            }
        }
        return $index;
    }

    public function get_features($project_id, $parent_id = NULL) {
        $query = $this->db->get_where('tb_feature', array(
            'project_id' => $project_id,
            'parent_id' => $parent_id));

        return $query->result();
    }

    public function get_task($feature_id) {
        $query = $this->db->get_where('tb_task', array('feature_id' => $feature_id));

        return $query->row();
    }

    public function get_tasks($project_id, $withName = false) {
        $query = $this->db->get_where('tb_task', array('project_id' => $project_id));

        $result = $query->result();
        if ($withName) {
            foreach ($result as $task) {
                $this->db->select('name');
                $this->db->where('project_id', $project_id);

                $query_feature = $this->db->get('tb_feature');
                if ($query_feature->num_rows() > 0) {
                    $task->name = $query_feature->row()->name;
                }
            }
        }
        return $result;
    }

    public function get_project_platforms($project_id) {
        
    }

    function getprojectdata() {
        
    }

    function get_projecttabel() {
        $roleid = $this->session->userdata('roleid');
        $sql = 'select a.project_id,a.isclosed, a.name,a.client,a.start_date,a.end_date,b.username from project a left join tuser b on a.userid=b.userid order by a.created_date desc';
        $query = $this->db->query($sql);
        $platform = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $htmlplatform = '';

            foreach ($query->result_array() as $value) {

               
                $sqlproplat = "SELECT MIN(proplat_id) AS proplat_id, project_id FROM project_platform WHERE project_id = '" . $value['project_id'] . "'";
                $queryproplat = $this->db->query($sqlproplat);
                foreach ($queryproplat->result_array() as $valueproplat) {
                    /** ADDING STATUS PROJECT FOM CHNAGE ALL ITEM **/
                    $status = "Open";
                    if (strcasecmp($value['isclosed'], 1)){
                        $status = "Closed";
                    }
                    if ($status == 'Closed') {
                        $status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#EEEEEE !important;"
                            onClick="changeStatusClosedProject(' . $value['project_id'] . ', ' . $valueproplat['proplat_id'] . ')">
                            <span>'.$status.'</span></a>';
                    }
                    else{
                        $status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#26C281 !important; border-color: #26C281 !important; color:#ececec !important;"
                            onClick="changeStatusClosedProject(' . $value['project_id'] . ', ' . $valueproplat['proplat_id'] . ')">
                            <span>'.$status.'</span></a>';
                    }
                    /** END ADDING STATUS PROJECT FOM CHNAGE ALL ITEM **/
                }
                $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id where a.project_id='" . $value['project_id'] . "' and a.is_delete='0'";
                $query2 = $this->db->query($sql2);
                $htmlplatform = '<ul>';
                foreach ($query2->result_array() as $row) {
                    $htmlplatform .='<li>' . $row['name'] . '</li>';
//                    array_push($platform,$row['name']);
                }
                $htmlplatform .='</ul>';
               

                if ($roleid == 0) {
                    $delete = "<button type='button' onclick=\"editproject('" . $value['project_id'] . "')\" class='btn btn-danger btn-xs emailbtn-" . $value['project_id'] . "'>Edit</button>&nbsp;<button type='button' onclick=\"deleteproject('" . $value['project_id'] . "')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
                } else {
                    $delete = "<button type='button' onclick=\"editproject('" . $value['project_id'] . "')\" class='btn btn-success btn-xs emailbtn-" . $value['project_id'] . "'>Edit</button>&nbsp;<button type='button' onclick=\"sendmail('" . $value['project_id'] . "')\" class='btn btn-danger btn-xs emailbtn-" . $value['project_id'] . "'><span class='glyphicon glyphicon-envelope' aria-hidden='true'></span></button>";
                }
                $namedetail = '<a href="#" onclick="detailproject(' . $value['project_id'] . ')">' . $value['name'] . '</a>';
                $json['aaData'][] = array($x, $namedetail, $value['client'], $htmlplatform, $status_btn, $delete);
                $x++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    function addproject($data) {
        $username = $this->session->userdata('username');
        $data['username'] = $username;
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectinsert = array(
            'name' => $data['nama'],
            'client' => $data['client'],
            'userid' => $data['pm_id'],
            'file_path' => $data['path'],
            'created_date' => $tanggal,
            'created_by' => $username,
            'modified_date' => $tanggal,
            'modified_by' => $username,
        );
        $query = $this->db->insert('project', $projectinsert);
        $insert_id = $this->db->insert_id();
 
        for ($m_p = 0; $m_p < sizeof($data['platform_text']); $m_p++) {
                $is_delete_platform = 2;
                if (isset($data['is_delete_platform'][$m_p])) {
                    $is_delete_platform = $data['is_delete_platform'][$m_p];
                }
                $prolatinsert = array(
                    'project_id' => $insert_id,
                    'platform_id'   => $data['platform_text'][$m_p],
                    'created_date'  => $tanggal,
                    'created_by'    => $username,
                    'is_delete'     => $is_delete_platform
                );
                $query2 = $this->db->insert('project_platform', $prolatinsert);
            }
        /*if (isset($data['platform']) && sizeof($data['platform']) > 0) {
            foreach ($data['platform'] as $value) {
                $prolatinsert = array(
                    'project_id' => $insert_id,
                    'platform_id' => $value,
                    'created_date' => $tanggal,
                    'created_by' => $username,
                );
                $query2 = $this->db->insert('project_platform', $prolatinsert);
            }
        }*/

       $this->insertProjectMemberTable($data, $insert_id, $username);

        if ($data['path'] != '') {
            $data['project_id'] = $insert_id;
            $readxlx = $this->readexcellmaindays($data);
        }
        if ($query) {
            $balikan = array(
                'status' => 1,
                'type' => 'project',
                'msg' => 'Project added'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Project failed to save'
            );
        }
        //return $readxlx;
        return $balikan;
    }

    function updateproject($data){
        $username = $this->session->userdata('username');
        $data['username'] = $username;
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectinsert = array(
            'name' => $data['nama'],
            'client' => $data['client'],
            'userid' => $data['pm_id'],
            //'file_path' => $data['path'],
            'modified_date' => $tanggal,
            'modified_by' => $username,
        );
        $this->db->where('project_id', $data['project_id']);
        $query = $this->db->update('project', $projectinsert);

        //$insert_id = $this->db->insert_id();
        /*for ($m_p = 0; $m_p < sizeof($data['platform_text_e']); $m_p++) {
                $is_delete_platform = 2;
                if (isset($data['is_delete_platform_e'][$m_p])) {
                    $is_delete_platform = $data['is_delete_platform_e'][$m_p];
                }
                $platform_id_e = 2;
                if (isset($data['platform_id_e'][$m_p])) {
                    $platform_id_e = $data['platform_id_e'][$m_p];
                }
                    $prolatinsert = array(
                    'is_delete'     => $is_delete_platform
                );
                    $this->db->where('project_id', $data['project_id']);
                    $this->db->where('platform_id', $platform_id_e);
                    $this->db->update('project_platform', $prolatinsert);
            }*/
        for ($m_p = 0; $m_p < sizeof($data['platform_text_e']); $m_p++) {
                $is_delete_platform = 2;
                if (isset($data['is_delete_platform_e'][$m_p])) {
                    $is_delete_platform = $data['is_delete_platform_e'][$m_p];
                }
                $platform_id_e = 2;
                if (isset($data['platform_id_e'][$m_p])) {
                    $platform_id_e = $data['platform_id_e'][$m_p];
                }
                $prolatinsert = array(
                    'is_delete'     => $is_delete_platform
                );
                $isAvailable_data = "select count(*) as count from project_platform where project_id='" . $data['project_id'] . "' and platform_id = '" . $platform_id_e . "'";
                $query_available = $this->db->query($isAvailable_data)->row();
                if ($query_available->count == 1) {
                $platform = array();
                    $this->db->where('project_id', $data['project_id']);
                    $this->db->where('platform_id', $platform_id_e);
                    $this->db->update('project_platform', $prolatinsert);
                }
                else{
                     $prolatinsert = array(
                        'project_id'    => $data['project_id'],
                        'platform_id'   => $platform_id_e,
                        'created_date'  => $tanggal,
                        'created_by'    => $username,
                        'is_delete'     => $is_delete_platform
                    );
                    $this->db->insert('project_platform', $prolatinsert);
                }
            }
            //exit;
        $this->updateProjectMemberTable($data, $username);

        /*if ($data['path'] != '') {
            $data['project_id'] = $insert_id;
            $readxlx = $this->readexcellmaindays($data);
        }*/
        if ($query) {
            $balikan = array(
                'status' => 1,
                'type' => 'project',
                'msg' => 'Project Updated'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Project failed to update'
            );
        }
        //return $readxlx;
        return $balikan;
    }
    function insertProjectMemberTable($data, $insert_id, $username) {
        //Insert into project_member
        if (isset($data['member']) && isset($data['peran'])) {
            for ($m = 0; $m < sizeof($data['member']); $m++) {
                $peran = 2;
                if (isset($data['peran'][$m])) {
                    $peran = $data['peran'][$m];
                }
                $project_member_data = array(
                    'member_id' => $data['member'][$m],
                    'project_id' => $insert_id,
                    'role_id' => $peran,
                    'created_by' => $username
                );
                $queryMember = $this->db->insert('t_project_member', $project_member_data);
            }
        }
    }
    function updateProjectMemberTable($data,$username) {
        // Update into project member
        $this->db->delete('t_project_member',array('project_id' => $data['project_id']));
        if (isset($data['member2']) && isset($data['peran2'])) {
            for ($m = 0; $m < sizeof($data['member2']); $m++) {
                /*$peran = 2;
                if (isset($data['peran'][$m])) {
                    $peran = $data['peran'][$m];
                }*/
                $project_member_data = array(
                    'member_id' => $data['member2'][$m],
                    'project_id' => $data['project_id'],
                    'role_id' => $data['peran2'][$m],
                    'created_by' => $username
                );
                $queryMember = $this->db->insert('t_project_member', $project_member_data);
            }
        }
    }

    function get_featuretabel($data) {
        $sql = "select feature_id,proplat_id,name, created_date, created_by, modified_date, modified_by from feature where proplat_id='" . $data['proplat_id'] . "' and is_delete = '0'";
        $query = $this->db->query($sql);
        $platform = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $htmlplatform = '';

            foreach ($query->result_array() as $value) {
                $namedetail = '<a href="#" onclick="detailfeature(' . $value['feature_id'] . ')">' . $value['name'] . '</a>';
                $action     = '<button class="btn btn-primary btn-xs" onclick="editfeature('.$value['feature_id'].')">Edit</button>&nbsp;<button class="btn btn-danger btn-xs" onclick="deletefeatured('.$value['feature_id'].', '.$value['proplat_id'].')">Delete</button>';
                $created = '<span style="text-align: center !important;">'.$value['created_date'].'<br>'.$value['created_by'].'</span>';
                $modified = $value['modified_date'].'<br>'.$value['modified_by'];

                $json['aaData'][] = array($x, $namedetail, $created, $modified, $action);
                $x++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    function get_detailproject($id) {
        $sql = 'select a.project_id,a.name,a.client from project a where project_id=?';
        $query = $this->db->query($sql, array($id));
        $platform = $hasil = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $htmlplatform = '';
            $value = $query->row_array();
            $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id where a.project_id='" . $value['project_id'] . "' and a.is_delete = '0' ";
            $query2 = $this->db->query($sql2);
            $html = '';
            foreach ($query2->result_array() as $row) {
                $html .='<option value="' . $row['proplat_id'] . '">' . $row['name'] . '</option>';
            }
            $value['platform'] = $html;

            $hasil = $value;
        }
        return $hasil;
    }

    function addfeature($data) {
        $username = $this->session->userdata('username');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectinsert = array(
            'proplat_id' => $data['proplatid'],
            'name' => $data['nama'],
            'created_date' => $tanggal,
            'created_by' => $username,
            'modified_date' => $tanggal,
            'modified_by' => $username,
        );
        $query = $this->db->insert('feature', $projectinsert);
        $insert_id = $this->db->insert_id();


        if ($query) {
            $balikan = array(
                'status' => 1,
                'proplat_id' => $data['proplatid'],
                'type' => 'feature',
                'msg' => 'Feature added'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Feature failed to save'
            );
        }
        return $balikan;
    }

    function updatefeature($data){
        $username = $this->session->userdata('username');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectupdate = array(
                //'proplat_id' => $data['proplatid'],
                'name' => $data['nama'],
                //'created_date' => $tanggal,
                //'created_by' => $username,
                'modified_date' => $tanggal,
                'modified_by' => $username,
         );
        //$data = array ('deleted' => '1');
        $this->db->where('feature_id', $data['idfeature']);
        $query = $this->db->update('feature', $projectupdate);

        if ($query) {
            $balikan = array(
                'status' => 1,
                'proplat_id' => $data['proplatid'],
                'type' => 'feature',
                'msg' => 'Feature Edited'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Feature failed to deleted'
            );
        }
        return $balikan;
    }
    function deleteprojectfeatured($id){
        //$querydelete = $this->db->query('delete from feature where feature_id="' . $id . '"');
        $this->db->where('feature_id', $id);
        $dataisdelete = array ('is_delete' => '1');
        $querydelete = $this->db->update('feature', $dataisdelete);
        if ($querydelete) {
            $balikan = array(
                'status' => 1,
                //'proplat_id' => $data['proplatid'],
                'type' => 'feature',
                'msg' => 'Feature Deleted'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Feature failed to Deleted'
            );
        }
        return $balikan;
    }
    function get_detailfeature($id) {
        $sql = 'select a.feature_id,a.name from feature a where feature_id=?';
        $query = $this->db->query($sql, array($id));
        $platform = $hasil = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $value = $query->row_array();
            $hasil = $value;
        }
        return $hasil;
    }

    function get_grouptabel($data) {
        $sql = "SELECT group_id,feature_id,name, created_date, create_by, modified_date, modified_by FROM `group` where feature_id='" . $data['feature_id'] . "' and is_delete ='0'";
        $query = $this->db->query($sql);
        $platform = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $htmlplatform = '';

            foreach ($query->result_array() as $value) {


                $namedetail = '<a href="#" onclick="detailgroup(' . $value['group_id'] . ')">' . $value['name'] . '</a>';
                $action     = '<button class="btn btn-primary btn-xs" onclick="editgroup('.$value['group_id'].')">Edit</button>&nbsp;<button class="btn btn-danger btn-xs" onclick="deletegroup('.$value['group_id'].', '.$value['feature_id'].')">Delete</button>';
                $created = '<span style="text-align: center !important;">'.$value['created_date'].'<br>'.$value['create_by'].'</span>';
                $modified = $value['modified_date'].'<br>'.$value['modified_by'];
                $json['aaData'][] = array($x, $namedetail, $created, $modified, $action);
                $x++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }
    
    function updatestatusclosed($data){
        
        $updatedvalue = "0";
//        echo "a";die(0);
        $checkcurrent = "select isclosed from item where item_id ='".$data['itemid']."'";
        $doquery = $this->db->query($checkcurrent)->row_array();
//        echo "a";die(0);
        
        if (sizeof($doquery)>0){
            if(strcasecmp($doquery["isclosed"], "0") == 0)
            {
                $updatedvalue = "1";
            }
            
            $updatequery = "update item set isclosed = '".$updatedvalue."' where item_id ='".$data['itemid']."'";
//            echo $updatequery;die(0);
            $doupdate = $this->db->query($updatequery);
            
            if($doupdate){
                return "Update Sukses";
            } else{
                return "Update Gagal";
                
            }
        }
    }

    /** ADDING STATUS PROJECT FOM CHNAGE ALL ITEM **/
    function updatestatusclosedPerProjectqq($data){
        
        $updatedvalue = "0";
//        echo "a";die(0);
        $checkcurrent = "select isclosed from project where project_id ='".$data['project_id']."'";
        $doquery = $this->db->query($checkcurrent)->row_array();
//        echo "a";die(0);
        
        if (sizeof($doquery)>0){
            if(strcasecmp($doquery["isclosed"], "0") == 0)
            {
                $updatedvalue = "1";
            }
            
            $updatequery = "update project set isclosed = '".$updatedvalue."' where project_id ='".$data['project_id']."'";
//            echo $updatequery;die(0);
            $doupdate = $this->db->query($updatequery);
            
            if($doupdate){
                return "Update Sukses";
            } else{
                return "Update Gagal";
                
            }
        }
    }

    function updatestatusclosedPerProject($data){
        
         $updatedvalue = "0";
//        echo "a";die(0);

        $checkcurrent_proplat = "select proplat_id from project_platform where project_id ='".$data['project_id']."'";
        $doquery_proplat = $this->db->query($checkcurrent_proplat);

        //return $doquery_proplat;exit;
        if ($doquery_proplat->num_rows() > 0) {
                $platform_id = '';
                foreach ($doquery_proplat->result_array() as $proplat) {
                    if ($platform_id != '') {
                        $platform_id .= ',';
                    }
                    $platform_id .= "'" . $proplat['proplat_id'] . "'";
                }
                //return $platform_id;exit;
            }

        $checkcurrent = "select isclosed from project where project_id ='".$data['project_id']."'";
        $doquery = $this->db->query($checkcurrent)->row_array();
        //echo "a";die(0);
        
        if (sizeof($doquery)>0){
            if(strcasecmp($doquery["isclosed"], "0") == 0)
            {
                $updatedvalue = "1";
            }
            $checkcurrent_feature = "select feature_id from `feature` where proplat_id IN (" . $platform_id . ")";
            //$checkcurrent_feature = "select feature_id from feature where proplat_id =?";
            $doquery_feature = $this->db->query($checkcurrent_feature);

            if ($doquery_feature->num_rows() > 0) {
                $featurearray = '';
                foreach ($doquery_feature->result_array() as $feature) {
                    if ($featurearray != '') {
                        $featurearray .= ',';
                    }
                    $featurearray .= "'" . $feature['feature_id'] . "'";
                }
                //return $featurearray;exit;
            }
            //return $doquery_feature->result();exit;
            if (sizeof($doquery_feature->row_array())>0){
                foreach ($doquery_feature->result_array() as $valuefeature) {
                        $checkcurrent_group = "select group_id from `group` where feature_id IN (" . $featurearray . ")";
                        $doquery_group = $this->db->query($checkcurrent_group);

                        if ($doquery_group->num_rows() > 0) {
                            $grouparray = '';
                            foreach ($doquery_group->result_array() as $group) {
                                if ($grouparray != '') {
                                    $grouparray .= ',';
                                }
                                $grouparray .= "'" . $group['group_id'] . "'";
                            }
                            //return $grouparray;exit;
                        }

                        //return $doquery_group->result();exit;
                        if (sizeof($doquery_group->row_array())>0){
                            foreach ($doquery_group->result_array() as $valuegroup) {
                                $checkcurrent_item = "select item_id from item where group_id IN (" . $grouparray . ")";
                                $doquery_item = $this->db->query($checkcurrent_item);
                                    if (sizeof($doquery_item->row_array())>0){
                                        /*foreach ($doquery_item->result_array() as $valueitem) {
                                            $updatequery = "update item set isclosed = '".$updatedvalue."' where group_id ='".$valuegroup['group_id']."'";
                                        }*/
                                        $updatequery = "update item set isclosed = '".$updatedvalue."' where group_id IN (" . $grouparray . ")";            
                                        $doupdate_item = $this->db->query($updatequery);

                                        $updatequeryproject = "update project set isclosed = '".$updatedvalue."' where project_id ='".$data['project_id']."'";
                                        $doupdate = $this->db->query($updatequeryproject);
                                    
                                        if($doupdate && $doupdate_item){
                                            return "Update Sukses";
                                        } else{
                                            return "Update Gagal";
                                            
                                        }

                                    }
                                    else{
                                        $msg = "Update project";
                                    }
                              }
                        }
                        else{
                            $msg = "Update project";
                        }
                }
            }
            else{
                $msg = "Update project";
            }
            if ($msg == "Update project") {
                        $updatequery = "update project set isclosed = '".$updatedvalue."' where project_id ='".$data['project_id']."'";
                        //echo $updatequery;die(0);
                        $doupdate = $this->db->query($updatequery);
                    
                        if($doupdate){
                            return "Update Sukses";
                        } else{
                            return "Update Gagal";
                            
                        }
            }

        }
    }

    /** END ADDING STATUS PROJECT FOM CHNAGE ALL ITEM **/
    function addgroup($data) {
        $username = $this->session->userdata('username');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectinsert = array(
            'feature_id' => $data['featureid'],
            'name' => $data['nama'],
            'created_date' => $tanggal,
            'create_by' => $username,
            'modified_date' => $tanggal,
            'modified_by' => $username,
        );
        $query = $this->db->insert('group', $projectinsert);
        $insert_id = $this->db->insert_id();


        if ($query) {
            $balikan = array(
                'status' => 1,
                'id' => $data['featureid'],
                'type' => 'group',
                'msg' => 'Group added'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Group failed to save'
            );
        }
        return $balikan;
    }
    function updategroup($data) {
        $username = $this->session->userdata('username');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectupdate = array(
                //'feature_id' => $data['featureid'],
                'name' => $data['nama'],
                //'created_date' => $tanggal,
                //'create_by' => $username,
                'modified_date' => $tanggal,
                'modified_by' => $username,
         );
        //$data = array ('deleted' => '1');
        $this->db->where('group_id', $data['idgroup']);
        $query = $this->db->update('group', $projectupdate);


        if ($query) {
            $balikan = array(
                'status' => 1,
                'id' => $data['featureid'],
                'type' => 'group',
                'msg' => 'Group Edited'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Group failed to edit'
            );
        }
        return $balikan;
    }

     function deleteprojectgroup($id){
        //$querydelete = $this->db->query('delete from feature where feature_id="' . $id . '"');
        $this->db->where('group_id', $id);
        $dataisdelete = array ('is_delete' => '1');
        $querydelete = $this->db->update('group', $dataisdelete);
        if ($querydelete) {
            $balikan = array(
                'status' => 1,
                //'proplat_id' => $data['proplatid'],
                'type' => 'feature',
                'msg' => 'Group Deleted'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Group failed to Deleted'
            );
        }
        return $balikan;
    }

    function get_Item($data) {
        $sql = "SELECT item_id,group_id,name,hour,isclosed FROM `item` where group_id='" . $data['group_id'] . "' and is_delete='0'";
        $query = $this->db->query($sql);
        $platform = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $query = $query->result_array();
            foreach ($query as $value) {
//                echo json_encode($value);die(0);
                $status = "Open";
                if (strcasecmp($value['isclosed'], 1)){
                    $status = "Closed";
                }
                if ($status == 'Closed') {
                    $status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#EEEEEE !important;"
                        onClick="changeStatusClosed(' . $value['group_id'] . ', ' . $value['item_id'] . ')">
                        <span>'.$status.'</span></a>';
                }
                else{
                    $status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#26C281 !important; border-color: #26C281 !important; color:#ececec !important;"
                        onClick="changeStatusClosed(' . $value['group_id'] . ', ' . $value['item_id'] . ')">
                        <span>'.$status.'</span></a>';
                }
                $members = $this->get_assignment($value['item_id']);
                $member_names = $this->get_member_name($members);
                $member_list = '';
                if (sizeof($member_names) > 0) {
                    $member_list = '<ul>';
                    //This should be from assignment later
                    foreach ($member_names as $member) {
                        $member_list .= '<li style="cursor:pointer;" onclick="remove_assignment(' . $member['user_id'] . ', ' .
                                $value['item_id'] . ', ' . $value['group_id'] . ')">' .
                                $member['salutation'] . ' ' . $member['firstname'] . '</li>';
                    }
                    $member_list .= '</ul>';
                }
                $action     = '<button class="btn btn-primary btn-xs" onclick="edititem('.$value['item_id'].')">Edit</button>&nbsp;<button class="btn btn-danger btn-xs" onclick="deleteitem('.$value['item_id'].', '.$value['group_id'].')">Delete</button> <span style="color:transparent !important;>'.$value['item_id'].'</span>';
                $json['aaData'][] = array($x,
                    $value['name'],
                    $value['hour'],
                    $member_list,
                    '
                        &nbsp;<a class="btn btn-default" tabindex="0" style="float: right;"
                        onClick="viewAddMemberModal2(' . $value['group_id'] . ', ' . $value['item_id'] . ')">
                        <span>Management Personel</span></a>', $status_btn,
                 $action);
                $x++;
            }
            /*<a class="btn btn-default" tabindex="0" style="float: right;"
                        onClick="viewAddMemberModal(' . $value['group_id'] . ', ' . $value['item_id'] . ')">
                        <span>Add Personel</span></a>*/
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    //Add members to assignment table
    //Add member to item
    function add_assignment($data) {
        $members = '';
        $datenow = date('Y-m-d H:i:s');
        // echo json_encode($data);return;
        // return array(
        //     'status' => 0,
        //     'msg' => json_encode($data)
        // );
        if (isset($data['member']) && isset($data['addmember_itemid'])) {
            foreach ($data['member'] as $m_id) {
                $assignment_table_data = array(
                    'user_id' => $m_id,
                    'item_id' => $data['addmember_itemid'],
                    'startdate' => '',
                    'efd' => '',
                    'efh' => '',
                    'efm' => '',
                    'created_by' => '',
                    'created_date' => $datenow,
                    'modified_by' => '',
                    'modified_date' => $datenow
                );
                $insertresult = $this->db->insert('assignment', $assignment_table_data);
                if ($insertresult) {
                    return array(
                        'status' => 1,
                        'type' => 'item',
                        'id' => $data['addmember_group_id'],
                        'msg' => 'Success'
                    );
                } else {
                    return array(
                        'status' => 0,
                        'type' => 'item',
                        'id' => $data['addmember_group_id'],
                        'msg' => ($this->db->_error_number() == 1062 ? 'Cant put the same personnel again' : $this->db->_error_message())
                    );
                }
            }
        }

        return array(
            'status' => 0,
            'msg' => 'Cant find member or item id'
        );
    }

    function remove_assignment($data) {
        //data is user_id, item_id
        $query = "delete from assignment where  user_id=? and item_id=?";
        $query_result = $this->db->query($query, array($data['user_id'], $data['item_id']));
        if ($query_result) {
            return 'Remove personel success';
        } else {
            return 'Failed to remove personnel';
        }
    }

    //Get list of user ids in assignment
    function get_assignment($itemid) {
        $user_ids = '';
        //previous code
//        $query = "select * from assignment where item_id=?";
        $query = "select * from t_task where item_id=?";
        $assignment_result = $this->db->query($query, array($itemid));
        if ($assignment_result->num_rows() > 0) {
            $assignment_result = $assignment_result->result_array();
            foreach ($assignment_result as $r) {
                if ($user_ids != '') {
                    $user_ids .= ',';
                }
                $user_ids .= "'" . $r['user_id'] . "'";
            }
        }
        return $user_ids;
    }

    //Get member name using a string list of user_id
    function get_member_name($user_ids) {
        if ($user_ids == "") {
            return array();
        }
        $query = "select * from tperson where user_id in (" . $user_ids . ") group by user_id";
        return $this->db->query($query)->result_array();
    }

    // Get all involved member in a project
    function get_project_member($projectid) {
        $query = "select * from t_project_member where project_id=?";
        $members = $this->db->query($query, array($projectid));
        if ($members->num_rows() > 0) {
            $memberarray = '';
            foreach ($members->result_array() as $member) {
                if ($memberarray != '') {
                    $memberarray .= ',';
                }
                $memberarray .= "'" . $member['member_id'] . "'";
            }
            $query_member_name = "select * from tperson where person_id in(" . $memberarray . ")";
            $queryperson = $this->db->query($query_member_name);
            if ($queryperson->num_rows() > 0) {
                return $queryperson->result_array();
            }
        }
    }
    function get_project_member_new($projectid) {
        $query = "select * from t_project_member where project_id=?";
        $members = $this->db->query($query, array($projectid));
        if ($members->num_rows() > 0) {
            $memberarray = '';
            foreach ($members->result_array() as $member) {
                if ($memberarray != '') {
                    $memberarray .= ',';
                }
                $memberarray .= "'" . $member['member_id'] . "'";
            }
            $query_member_name = "select * from tperson where person_id in(" . $memberarray . ")";
            $queryperson = $this->db->query($query_member_name);
            $a = 0;
            if ($queryperson->num_rows() > 0) {
                 foreach($queryperson->result() as $indeks => $p) {
                        if ($p->user_id!=NULL) {
                            $checked = "checked";
                        }
                        else{
                            $checked = "";
                    }
                    $data[] = array(
                        '',
                        '',
                        '
                        <input type="checkbox" name="id_menu_e['.$a++.']" value="'.$p->user_id.'" '.$checked.' />
                        <span class="lbl">' . $p['salutation'] . ' ' . $p['firstname'] . '</span>'
                        );
                    }

                return $queryperson->result_array();
            }
        }
    }

    // Get project id from item id
    function get_projectid($itemid) {
        $query = "select `project_platform`.project_id from `item`
            left join `group` on `item`.group_id=`group`.group_id
            left join `feature` on `feature`.feature_id=`group`.feature_id
            left join `project_platform` on `feature`.proplat_id=`project_platform`.proplat_id
            where `item`.item_id=?";
        $query_result = $this->db->query($query, array($itemid));
        if ($query_result->num_rows() > 0) {
            $query_array = $query_result->row_array();
            return $query_array['project_id'];
        } else {
            return 0;
        }
    }

    // Get project id from group id
    function get_projectid_bygroupid($groupid) {
        $query = "select `project_platform`.project_id
            from `group`
            left join `feature` on `feature`.feature_id=`group`.feature_id
            left join `project_platform` on `feature`.proplat_id=`project_platform`.proplat_id
            where `group`.group_id=?";
        $query_result = $this->db->query($query, array($groupid));
        if ($query_result->num_rows() > 0) {
            $query_array = $query_result->row_array();
            return $query_array['project_id'];
        } else {
            return 0;
        }
    }

    function addItem($data) {
        $checkcurrent_group = "select feature_id from `group` where group_id = '".$data['group_id']."'";
        $doquery_group = $this->db->query($checkcurrent_group);
        //return $doquery_group->result();exit;
        if (sizeof($doquery_group->row_array())>0){
            foreach ($doquery_group->result_array() as $valuegroup) {
                        $checkcurrent_feature = "select feature_id, proplat_id from `feature` where feature_id = '".$valuegroup['feature_id']."'";
                         $doquery_feature = $this->db->query($checkcurrent_feature);
                         //return $doquery_feature->result();exit;
                          if (sizeof($doquery_feature->row_array())>0){
                          foreach ($doquery_feature->result_array() as $valuefeature) {
                              $checkcurrent_proplat = "select project_id, proplat_id from `project_platform` where proplat_id = '".$valuefeature['proplat_id']."'";
                              $doquery_proplat = $this->db->query($checkcurrent_proplat);   
                                //return $doquery_proplat->result();exit;
                                  if (sizeof($doquery_proplat->row_array())>0){
                                  foreach ($doquery_proplat->result_array() as $valueproplat) {
                                        $checkcurrent_project = "select isclosed, project_id from `project` where project_id = '".$valueproplat['project_id']."'";
                                        $doquery_project = $this->db->query($checkcurrent_project);

                                        if ($doquery_project->num_rows() > 0) {
                                            $isclosed_project = $doquery_project->row()->isclosed;
                                            //return $isclosed_project;exit;  
                                        }
                                  } 
                          }
                      }
               }
           }
       }
        $username = $this->session->userdata('username');
        $tanggal = date('Y-m-d H:i:s');
        $projectinsert = array(
            'group_id' => $data['group_id'],
            'name' => $data['nama'],
            'hour' => $data['hour'],
            'created_date' => $tanggal,
            'created_by' => $username,
            'modified_date' => $tanggal,
            'modified_by' => $username,
            'isclosed'    => $isclosed_project,
        );
        $query = $this->db->insert('item', $projectinsert);
        $insert_id = $this->db->insert_id();

        if ($query) {
            $balikan = array(
                'status' => 1,
                'item_id' => $data['group_id'],
                'id' => $data['group_id'],
                'type' => 'item',
                'msg' => 'Item added'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Item failed to save'
            );
        }
        return $balikan;
    }

    function updateitem($data) {
        /*$username = $this->session->userdata('username');
        $tanggal = date('Y-m-d H:i:s');
        $projectinsert = array(
            'group_id' => $data['group_id'],
            'name' => $data['nama'],
            'hour' => $data['hour'],
            'created_date' => $tanggal,
            'created_by' => $username,
            'modified_date' => $tanggal,
            'modified_by' => $username,
        );
        $query = $this->db->insert('item', $projectinsert);
        $insert_id = $this->db->insert_id();*/

        $checkcurrent_group = "select feature_id from `group` where group_id = '".$data['group_id']."'";
        $doquery_group = $this->db->query($checkcurrent_group);
        //return $doquery_group->result();exit;
        if (sizeof($doquery_group->row_array())>0){
            foreach ($doquery_group->result_array() as $valuegroup) {
                        $checkcurrent_feature = "select feature_id, proplat_id from `feature` where feature_id = '".$valuegroup['feature_id']."'";
                         $doquery_feature = $this->db->query($checkcurrent_feature);
                         //return $doquery_feature->result();exit;
                          if (sizeof($doquery_feature->row_array())>0){
                          foreach ($doquery_feature->result_array() as $valuefeature) {
                              $checkcurrent_proplat = "select project_id, proplat_id from `project_platform` where proplat_id = '".$valuefeature['proplat_id']."'";
                              $doquery_proplat = $this->db->query($checkcurrent_proplat);   
                                //return $doquery_proplat->result();exit;
                                  if (sizeof($doquery_proplat->row_array())>0){
                                  foreach ($doquery_proplat->result_array() as $valueproplat) {
                                        $checkcurrent_project = "select isclosed, project_id from `project` where project_id = '".$valueproplat['project_id']."'";
                                        $doquery_project = $this->db->query($checkcurrent_project);

                                        if ($doquery_project->num_rows() > 0) {
                                            $isclosed_project = $doquery_project->row()->isclosed;
                                            //return $isclosed_project;exit;  
                                        }
                                  } 
                          }
                      }
               }
           }
       }

        $username = $this->session->userdata('username');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectupdate = array(
            //'group_id' => $data['group_id'],
            'name' => $data['nama'],
            'hour' => $data['hour'],
            //'created_date' => $tanggal,
            //'created_by' => $username,
            'modified_date' => $tanggal,
            'modified_by' => $username,
         );
        //$data = array ('deleted' => '1');
        $this->db->where('item_id', $data['iditem']);
        $query = $this->db->update('item', $projectupdate);


        if ($query) {
            $balikan = array(
                'status' => 1,
                'item_id' => $data['group_id'],
                'id' => $data['group_id'],
                'type' => 'item',
                'msg' => 'Item Edited',
                'isclosed'    => $isclosed_project,
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Item failed to edit'
            );
        }
        return $balikan;
    }

     function deleteprojectitem($id){
        //$querydelete = $this->db->query('delete from feature where feature_id="' . $id . '"');
        $this->db->where('item_id', $id);
        $dataisdelete = array ('is_delete' => '1');
        $querydelete = $this->db->update('item', $dataisdelete);
        if ($querydelete) {
            $balikan = array(
                'status' => 1,
                //'proplat_id' => $data['proplatid'],
                'type' => 'feature',
                'msg' => 'Item Deleted'
            );
        } else {
            $balikan = array(
                'status' => 0,
                'msg' => 'Item failed to Deleted'
            );
        }
        return $balikan;
    }

    function addtabletask($data) {
        $username = $this->session->userdata('username');
        $tanggal = date('Y-m-d H:i:s');
        $failuretoinsert = array();
        if (isset($data['member']) && isset($data['addmember_itemid'])) {
            foreach ($data['member'] as $m_id) {
                $assignment_table_data = array(
                    'user_id' => $m_id,
                    'item_id' => $data['addmember_itemid'],
                    'hour_spent' => '0',
                    'is_done' => '0',
                    'created_by' => $username,
                    'created_date' => $tanggal,
                    'modified_by' => '',
                    'modified_date' => $tanggal
                );
                $query = "select task_id from t_task where user_id = '" . $m_id . "' and item_id = '" . $data['addmember_itemid'] . "'";
                $sqlcheck = $this->db->query($query)->row_array();
//                echo sizeof($sqlcheck);die(0);
                if (sizeof($sqlcheck) == 0) {
                    $insertresult = $this->db->insert('t_task', $assignment_table_data);
                } else {
                    $query = "select salutation,firstname,lastname from tperson where user_id ='" . $m_id . "'";
                    $query_result = $this->db->query($query)->row_array();
                    array_push($failuretoinsert, $query_result['salutation'] . " " . $query_result['firstname'] . " " . $query_result['lastname']);
                }
            }
            if (sizeof($failuretoinsert) > 0) {
                $failurelist = "";
                foreach ($failuretoinsert as $value) {
                    $failurelist = $failurelist . $value . " ";
                }
                $balikan = array(
                    'status' => 0,
                    'type' => 'item',
                    'id' => $data['addmember_group_id'],
                    'msg' => $failurelist . " already Registered For This Item"
                );
            } else {
                $balikan = array(
                    'status' => 1,
                    'type' => 'item',
                    'id' => $data['addmember_group_id'],
                    'msg' => 'Personel Berhasil Didaftarkan'
                );
            }
        }

        return $balikan;
    }

    function updatetabletask($id_member_e, $addmember_itemid, $addmember_group_id){
        $username = $this->session->userdata('username');
        $tanggal = date('Y-m-d H:i:s');
        if($id_member_e != ''){
            $this->db->delete('t_task',array('item_id' => $addmember_itemid));
            foreach ($id_member_e as $p) {
                if($p != '0'){                  
                    $result_update = $this->db->insert('t_task', array('user_id' => $p,'item_id' => $addmember_itemid, 'hour_spent' => '0', 'is_done' => '0', 'modified_date' => $tanggal, 'modified_by' => '' ,'created_by' => $username, 'created_date' => $tanggal));
                }           
            }
            //die('test');exit;
            if ($result_update) {
                $balikan = array(
                    'status' => 1,
                    'type'   => 'item',
                    'id'     => $addmember_group_id,
                    'msg'    => 'Personel Berhasil DiPerbaharui'
                );
            }
            else{
                $balikan = array(
                    'status' => 0,
                    'type'   => 'item',
                    'id'     =>  $addmember_group_id,
                    'msg'    =>  'Gagal Update'
                );
            }
            return $balikan;
        }
        else{
            $result_update = $this->db->delete('t_task',array('item_id' => $addmember_itemid));
             if ($result_update) {
                $balikan = array(
                    'status' => 1,
                    'type'   => 'item',
                    'id'     => $addmember_group_id,
                    'msg'    => 'Personel Berhasil DiPerbaharui'
                );
            }
            else{
                $balikan = array(
                    'status' => 0,
                    'type'   => 'item',
                    'id'     =>  $addmember_group_id,
                    'msg'    =>  'Gagal Update'
                );
            }
            return $balikan;
        }
    }
    function get_detailgroup($id) {
        $sql = 'select a.group_id,a.name from `group` a where group_id=?';
        $query = $this->db->query($sql, array($id));
        $platform = $hasil = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $value = $query->row_array();
            $hasil = $value;
        }
        return $hasil;
    }

    function chekdataforlink($data) {
        if ($data['type'] == 'item') {
            $sql = 'select a.group_id,a.name,a.feature_id,b.proplat_id from  `group` a left join feature b on a.feature_id=b.feature_id where a.group_id="' . $data['id'] . '"';
        } else if ($data['type'] == 'group') {
            $sql = 'select feature_id,proplat_id from feature where feature_id="' . $data['id'] . '"';
        } else {
            $sql = 'select feature_id,proplat_id from feature where proplat_id="' . $data['id'] . '"';
        }

        $query = $this->db->query($sql);
        $row = $query->row_array();
        return $row;
    }

    function harddeleteproject($id) {
        $sql = "select i.item_id,i.group_id,f.feature_id,i.name,i.hour,g.name as 'group' , f.name as 'feature' from item i  join `group` g on i.group_id=g.group_id  join feature f on g.feature_id=f.feature_id  join project_platform pp on f.proplat_id=pp.proplat_id where pp.project_id=?";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $deleteitem = $this->db->query('delete from item where item_id="' . $row['item_id'] . '"');
                $deletegroup = $this->db->query('delete from `group` where group_id="' . $row['group_id'] . '"');
                $deletefitur = $this->db->query('delete from feature where feature_id="' . $row['feature_id'] . '"');
                $deleteassignment = $this->db->query('delete from `assignment` where item_id=?', array($row['item_id']));
            }
        }
        // No need to unlink file no more
        // $qry = $this->db->query("select * from project where project_id=?", array($id));
        // if ($qry->num_rows() > 0) {
        // $r = $qry->row_array();
        // unlink($r['file_path']);
        // }
        $deletemapping = $this->db->query('delete from project_platform where project_id="' . $id . '"');
        $deleteproject = $this->db->query('delete from project where project_id="' . $id . '"');
        if ($deleteproject) {
            return array('status' => 1, 'msg' => 'Project has been deleted');
        } else {
            return array('status' => 0, 'msg' => 'Delete Project Failed');
        }
    }

    function readexcellmaindays($data) {
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
        $cacheSettings = array('memcacheServer' => 'localhost',
            'memcachePort' => 11211,
            'cacheTime' => 600,
            'memoryCacheSize' => '32MB'
        );
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $fileType = PHPExcel_IOFactory::identify($data['path']);
        if ($fileType == 'CSV') {
            $uploadid = $this->session->userdata('uploadid');
            if ($uploadid != null && trim($uploadid) != '') {
                $cekdel = $this->checkdeletefile($uploadid, true);
                $this->deletefile($uploadid, $cekdel);
                $this->session->unset_userdata('uploadid');
            }
            echo "<script>alert('Unrecognized file format " .
            "\\nMake sure Your file is a proper Excel file\\n Resave the file if possible\\nYour File type is: $mimeType" .
            "'); </script>";
            die();
        }
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $sheetNames = $objReader->listWorksheetNames($data['path']);

        $ExcelObj = $objReader->load($data['path']);
        $indexnumber = 0;
        $sheet['web'] = "";
        $sheet['ws'] = "";
        $sheet['android'] = "";
        $sheet['ios'] = "";
        $sheet['inisialisasi'] = "";
        $dataExcelBySheet = "";

        foreach ($sheetNames as $sheetname) {
            if ((trim(strtolower($sheetname)) == "web")) {
                $sheet['web'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
            } else if ((trim(strtolower($sheetname)) == "ws")) {
                $sheet['ws'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
            } else if ((trim(strtolower($sheetname)) == "android")) {
                $sheet['android'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
            } else if ((trim(strtolower($sheetname)) == "ios")) {
                $sheet['ios'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
            } else if ((trim(strtolower($sheetname)) == "inisialisasi")) {
                $sheet['inisialisasi'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
            }
            $indexnumber++;
        }

        if ($sheet['web'] != '') {
            $condata = array(
                'project_id' => $data['project_id'],
                'platform_id' => 1,
                'username' => $data['username']
            );
            $chekproject = $this->chekprojectplatform($condata);
            if (sizeof($chekproject) <= 0) {
                $chekproject['proplat_id'] = $this->addprojectplatform($condata);
            }
            $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['web']);
        }
        if ($sheet['ws'] != '') {
            $condata = array(
                'project_id' => $data['project_id'],
                'platform_id' => 4,
                'username' => $data['username']
            );
            $chekproject = $this->chekprojectplatform($condata);
            if (sizeof($chekproject) <= 0) {
                $chekproject['proplat_id'] = $this->addprojectplatform($condata);
            }
            $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['ws']);
        }
        if ($sheet['android'] != '') {
            $condata = array(
                'project_id' => $data['project_id'],
                'platform_id' => 3,
                'username' => $data['username']
            );
            $chekproject = $this->chekprojectplatform($condata);
            if (sizeof($chekproject) <= 0) {
                $chekproject['proplat_id'] = $this->addprojectplatform($condata);
            }
            $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['android']);
        }

        if ($sheet['ios'] != '') {
            $condata = array(
                'project_id' => $data['project_id'],
                'platform_id' => 2,
                'username' => $data['username']
            );
            $chekproject = $this->chekprojectplatform($condata);
            if (sizeof($chekproject) <= 0) {
                $chekproject['proplat_id'] = $this->addprojectplatform($condata);
            }
            $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['ios']);
        }
        if ($sheet['inisialisasi'] != '') {
            $condata = array(
                'project_id' => $data['project_id'],
                'platform_id' => 5,
                'username' => $data['username']
            );
            $chekproject = $this->chekprojectplatform($condata);
            if (sizeof($chekproject) <= 0) {
                $chekproject['proplat_id'] = $this->addprojectplatform($condata);
            }
            $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['inisialisasi']);
        }
    }

    function chekprojectplatform($data) {
        $hasil = array();
        $sql = "select * from project_platform where project_id='" . $data['project_id'] . "' and platform_id='" . $data['platform_id'] . "' ";
        $qry = $this->db->query($sql);
        if ($qry->num_rows() > 0) {
            $hasil = $qry->row_array();
        }
        return $hasil;
    }

    //Insert to table project platform
    function addprojectplatform($data) {
        $prolatinsert = array(
            'project_id' => $data['project_id'],
            'platform_id' => $data['platform_id'],
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => $data['username']
        );
        $query2 = $this->db->insert('project_platform', $prolatinsert);
        if ($query2) {
            //proplat_id
            return $this->db->insert_id();
        }
    }

    //No longer temp table, just the function name
    function inserttemptimesheet($data, $proplat_id, $sheet) {
        $username = $this->session->userdata('username');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $projectinsert = array();
        foreach ($sheet as $sheetdata) {
            $sheetdata['A'] = (strtolower($sheetdata['A']) != 'Task Assigment' || $sheetdata['A'] != null ? $sheetdata['A'] : '');
            $sheetdata['B'] = (strtolower($sheetdata['B']) != 'Task Assigment' || $sheetdata['B'] != null ? $sheetdata['B'] : '');
            $sheetdata['C'] = (strtolower($sheetdata['C']) != 'Task Assigment' || $sheetdata['C'] != null ? $sheetdata['C'] : '');

            $sheetdata['D'] = ($sheetdata['D'] != 'hour' || $sheetdata['D'] != null ? $sheetdata['D'] : 0);
            if ($sheetdata['D'] != null && $sheetdata['C'] != null && $sheetdata['B'] != null && ($sheetdata['A'] != null || strtolower($sheetdata['C']) != 'Task Assigment')) {
                $projectinsert[] = array(
                    'proplat_id' => $proplat_id,
                    'feature' => $sheetdata['A'],
                    'group' => $sheetdata['B'],
                    'item' => $sheetdata['C'],
                    'hour' => $sheetdata['D'],
                    'created_date' => $tanggal
                );
                //$query = $this->db->insert('temp_uploadtimesheet', $projectinsert);
            }
        }
        $this->db->insert_batch('temp_uploadtimesheet', $projectinsert);
        $this->insertfeatureExcel($data, $projectinsert);
        $this->insertgroupExcell($data, $projectinsert);
    }

    /* function insertfeatureExcel($data) {
      $username = $this->session->userdata('username');
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      $sql = "insert into feature(proplat_id,name,created_date,created_by,modified_date,modified_by) SELECT tu.proplat_id,tu.feature,'" . $tanggal . "' as 'created_date','" . $username . "' as 'created_by','" . $tanggal . "' as 'modified_date','" . $username . "' as 'modified_by' FROM `temp_uploadtimesheet` tu left join project_platform pp on tu.proplat_id=pp.proplat_id where pp.project_id='" . $data['project_id'] . "' and pp.platform_id='" . $data['platform_id'] . "' group by tu.proplat_id,tu.feature order by 1 ";
      $qryfeature = $this->db->query($sql);
      } */

    function insertfeatureExcel($data, $projectdata) {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        $sql = "insert into feature(proplat_id,name,created_date,created_by,modified_date,modified_by) values";
        if (sizeof($projectdata) > 0) {
            foreach ($projectdata as $key => $p) {
                if ($key > 0) {
                    $sql .= ",";
                }
                $sql .= "('" . $p['proplat_id'] . "', '" . $p['feature'] . "', '$tanggal', '" . $data['username'] . "', '$tanggal', '" . $data['username'] . "')";
            }
        }
        $qryfeature = $this->db->query($sql);
    }

    /* function insertgroupExcell($data){
      $username = $this->session->userdata('username');
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      $sql = "insert into group(proplat_id,name,created_date,created_by,modified_date,modified_by) SELECT tu.proplat_id,tu.feature,'" . $tanggal . "' as 'created_date','" . $username . "' as 'created_by','" . $tanggal . "' as 'modified_date','" . $username . "' as 'modified_by' FROM `temp_uploadtimesheet` tu left join project_platform pp on tu.proplat_id=pp.proplat_id where pp.project_id='" . $data['project_id'] . "' and pp.platform_id='" . $data['platform_id'] . "' group by tu.proplat_id,tu.feature order by 1 ";
      $qryfeature = $this->db->query($sql);
      } */

    function insertgroupExcell($data, $projectdata) {
        date_default_timezone_set('Asia/Jakarta');
        $projectid = $data['project_id'];
        $username = $data['username'];
        $tanggal = date('Y-m-d H:i:s');

        //$query_select = "select * from project_platform where project_id = '" . $projectid . "'";
        /* if (sizeof($projectdata) > 0) {
          foreach ($projectdata as $p) [

          }
          } */

        //$sql = "insert into group(proplat_id,feature_id,name,created_date,create_by,modified_date,modified_by) values('" . $projectdata['proplat_id'] . "')";
        //$qryfeature = $this->db->query($sql);
    }

    function get_editproject2($id){
        //$sql = "SELECT a.*, b.name FROM t_project_member a JOIN project b ON a.project_id = b.project_id";
        $sql = "SELECT tuser.userid,tuser.username,tuser.email,tuser.role_id,role.name,tperson.salutation,tperson.firstname,tperson.lastname "
                . "FROM tuser LEFT JOIN tperson ON tuser.userid = tperson.user_id 
                LEFT JOIN role ON tuser.role_id = role.role_id 
                JOIN t_project_member ON tuser.userid = t_project_member.member_id
                JOIN project ON t_project_member.project_id = project.project_id
                WHERE tuser.isdelete = '0' AND tuser.role_id!=0 AND project.project_id = '$id'";
        $query = $this->db->query($sql);
        //var_dump($query->result_array());exit;
        $platform = array();
        $x = 1;
        if ($query->num_rows() > 0) {                
            foreach ($query->result() as $value) {
                $html = '<select name="member[]" id="member[]" class="form-control" >';
                $html .= '<option value="'.$value['userid'].'">'.$value['salutation'] . " " . $value["firstname"] . " " . $value['lastname'].' </option>'; 
                $html .= '</select>';       
            }
                
        } else {
            $html .= 'empty';
        }
        return $html;exit;
    }
    function get_editproject($project_id){
         $sql = " SELECT tuser.userid,tuser.username,tuser.email,tuser.role_id,role.name,tperson.salutation,tperson.firstname,t_project_member.project_member_id,project.project_id,project.name as name_project,project.client, tperson.lastname "
             . "FROM tuser LEFT JOIN tperson ON tuser.userid = tperson.user_id 
             LEFT JOIN role ON tuser.role_id = role.role_id 
             JOIN t_project_member ON tuser.userid = t_project_member.member_id
             JOIN project ON t_project_member.project_id = project.project_id
             WHERE tuser.isdelete = '0' AND tuser.role_id!=0 AND project.project_id ='".$project_id."'";
        $query = $this->db->query($sql);
        $row_query = $query->row();

        $query_member = $this->db->query("SELECT tuser.userid,tuser.username,tuser.email,tuser.role_id,role.name,tperson.salutation,tperson.firstname,tperson.lastname "
                    . "FROM tuser LEFT JOIN tperson on tuser.userid = tperson.user_id LEFT JOIN role on tuser.role_id = role.role_id where tuser.isdelete = '0' and tuser.role_id!=0");
        $query_role = $this->db->query('select * from role where role_id !=0');
        //var_dump($query->result());exit;
        $platform = array();
        $no = 1;

        $count = count($query->result());
        $html = '';

        foreach ($query->result() as $rowz) {
            $html .= '<div id="divMemberdiv'.$no.'"><br><select name="member2[]" id="member2[]" class="form-control" style="width:270px !important;">';
                    /*$html .= '<option value="">-- Pilih Option ---</option>';*/
                    $userid = $rowz->userid;
                    foreach ($query_member->result() as $row) {
                        if ($row->userid != $rowz->userid) {
                            $selected = '';
                        }
                        else{
                            $selected = 'selected="selected"';
                        }

                        $html .= '<option value="'.$row->userid.'"'.$selected.'>'.$row->salutation . " " . $row->firstname . " " . $row->lastname.' </option>';
                    }
                    
                $html .= '</select><br/>'; 

              $html .= '<br/><select name="peran2[]" id="peran2[]" class="form-control" style="width:270px !important;">';
                    /*$html .= '<option value="">-- Pilih Option ---</option>';*/
                    $role_id = $rowz->role_id;
                    foreach ($query_role->result() as $row2) {
                        if ($row2->role_id != $rowz->role_id) {
                            $selected = '';
                        }
                        else{
                            $selected = 'selected="selected"';
                        }

                        $html .= '<option value="'.$row2->role_id.'"'.$selected.'>'.$row2->name.' </option>';
                    }
                    
                $html .= '</select>&nbsp;<button type="button" class="btn btn-danger btn-sm" onClick="remove('.$no.')"><span class="glyphicon glyphicon-remove"></span></button></div><input type="hidden" value="'.$count.'" class="valmemberedit">'; 

        $no++;
        //echo $html;   
        
          
        }
        $query_member_name = "
                    SELECT p.platform_id as plat_id, pp.is_delete, p.name, pp.platform_id, pp.proplat_id FROM platform AS p LEFT JOIN
                        (SELECT * FROM project_platform WHERE project_id = '".$project_id."' and is_delete='0') AS pp
                            ON p.platform_id = pp.platform_id ";
            $queryperson = $this->db->query($query_member_name);
            //print_r($queryperson->result());exit;
            $a = 0;
            $no_tp = '1';
            if ($queryperson->num_rows() > 0) {
                 foreach($queryperson->result() as $indeks => $p) {
                        if ($p->platform_id!=NULL) {
                            $checked = "checked";
                        }
                        else{
                            $checked = "";
                        }
                        if ($p->is_delete == null) {
                            $is_delete = '1';
                        }
                        else{
                             $is_delete = '0';
                        }
                    $data[] = array(
                        '
                        <input type="hidden" name="platform_text_e[]" value="'.$p->proplat_id.'">
                        <input type="hidden" name="platform_id_e[]" value="'.$p->plat_id.'">
                        <input type="hidden" name="is_delete_platform_e[]" value="'.$is_delete.'" class="is_delete_platform'.$no_tp.'">
                        <input type="hidden" name="proplat_id_e[]" value="'.$p->proplat_id.'"">
                        <input class="checkbox_platform'.$no_tp.'" onClick="is_delete_platform('.$no_tp.')" type="checkbox" name="id_platform_e['.$a++.']" value="'.$p->plat_id.'" '.$checked.'/>
                        
                        <span class="lbl">'.$p->name.'</span>
                        '
                        );
                    $no_tp++;
                    }
            }
        $balikan = array(
                    'datahtml'      => $html,
                    'item'          => $row_query,
                    'dataplatform'  => $data
                );
        return $balikan; 

        
    }

}
