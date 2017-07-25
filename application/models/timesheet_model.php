<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of timesheet_model
 *
 * @author ASUS
 */
class timesheet_model extends CI_Model {

    //put your code here

    function get_projecttabel() {
        $userid = $this->session->userdata('user_id');
//        $sql = 'select a.project_id,a.name,a.client,b.username from project a left join tuser b on a.userid=b.userid order by a.created_date desc';
        $sql = "SELECT 
                t_project_member.project_member_id,
                t_project_member.member_id,
                t_project_member.project_id,
                project.project_id,
                project.name,
                project.created_by,
                project.client FROM `t_project_member` 
                left join project on t_project_member.project_id = project.project_id  
                WHERE t_project_member.member_id =" . $userid;

        $query = $this->db->query($sql);
        $platform = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $htmlplatform = '';

            foreach ($query->result_array() as $value) {
//                echo json_encode($value);die(0);
                $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id  where a.project_id='" . $value['project_id'] . "'";
                $query2 = $this->db->query($sql2);
                $htmlplatform = '<ul>';
                foreach ($query2->result_array() as $row) {
                    $htmlplatform .='<li>' . $row['name'] . '</li>';
//                    array_push($platform,$row['name']);
                }
                $htmlplatform .='</ul>';

                $namedetail = '<a href="#" onclick="detailproject(' . $value['project_id'] . ')">' . $value['name'] . '</a>';
                $json['aaData'][] = array($x, $namedetail, $htmlplatform, $value['client'], $value['created_by']);
                $x++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    function get_detailproject($id) {
        $sql = 'select a.project_id,a.name,a.client from project a where project_id="' . $id . '"';
        $query = $this->db->query($sql);
        $hasil = array();
        $x = 1;
        if ($query->num_rows() > 0) {
            $htmlplatform = '';
            $value = $query->row_array();
//            foreach ($query->result_array() as $value) {
            $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id where a.project_id='" . $value['project_id'] . "'";
            $query2 = $this->db->query($sql2);
            $html = '';
            foreach ($query2->result_array() as $row) {
                $html .='<option value="' . $row['proplat_id'] . '">' . $row['name'] . '</option>';
//                    array_push($platform,$row);
            }
            $value['platform'] = $html;

            $hasil = $value;
//                array_push($hasil,$value);
//            }
        }
//        print_r($hasil);die(0);
        return $hasil;
    }

    function getdetailtimesheet($id) {
        $sql = "select i.item_id,i.group_id,i.name,i.hour,g.name as 'group' , f.name as 'feature' from item i left join `group` g on i.group_id=g.group_id left join feature f on g.feature_id=f.feature_id where f.project_id='" . $id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                
            }
        }
    }

    function gettimesheettableall() {
        error_reporting(0);
        $userid = $this->session->userdata('user_id');
        $sql = 'select pro.name as proname,pro.client, i.item_id,i.name,x.user_id,t.hours,g.name as grup,f.name as fitur '
                . 'from item i left join t_task x on x.item_id = i.item_id'
                . ' left join `group` g on g.group_id=i.group_id  left '
                . 'join feature f on g.feature_id=f.feature_id left join project_platform p on f.proplat_id=p.proplat_id '
                . 'left join project_platform pp on f.proplat_id = pp.proplat_id
                   left join project pro on pp.project_id = pro.project_id '
                . 'left join (select item_id,sum(main_hour) as "hours" from timesheet group by item_id)t on i.item_id=t.item_id where  x.user_id = "' . $userid . '"';

//        echo $sql;die(0);

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $q = 1;
            foreach ($query->result_array() as $row) {

//                if ($row)
                $hour = "<span class=\"hoursout\" style=\"margin-right:20%\"><button class=\"btn btn-link\" onclick=\"detailmainhours('" . $row['item_id'] . "')\"> " . $row['hours'] . "</button></span>
                ";
                 /** EDITED COMMAS **/
                error_reporting(0);
                $explode  = explode('.', $row['hours']);
                $strlen   = $explode[1];
                $strlen_d = strlen($strlen);
                //$length_actual_h = strlen(explode('.', $row['actual']));
                $length_hours = $strlen_d;
                //$length_hours = strlen(explode('.', $row['hours'])[1]);
                $row_hours = $row['hours'];
                if ($length_hours >= 1 && $length_hours <= 5) {
                    $a = explode(".", $row_hours);
                    count($a)>1 and $a[1] = 5;
                    $rowhour_split_val = implode(".", $a);
                    $hour2 = "<span class=\"hoursout\" style=\"margin-right:20%\"><button class=\"btn btn-link\" onclick=\"detailmainhours('" . $row['item_id'] . "')\"> " . $rowhour_split_val . "</button></span>";
                }
                elseif ($length_hours > 5 && $length_hours <= 10) {
                    $a = explode(".", $row_hours);
                    count($a)>1 and $a[1] = 10;
                    $rowhour_split_val = implode(".", $a);
                    $hour2 = "<span class=\"hoursout\" style=\"margin-right:20%\"><button class=\"btn btn-link\" onclick=\"detailmainhours('" . $row['item_id'] . "')\"> " . $rowhour_split_val . "</button></span>";
                }
                elseif ($length_hours > 10){
                    $rowhour_split_val = round($row_hours);
                    $hour2 = "<span class=\"hoursout\" style=\"margin-right:20%\"><button class=\"btn btn-link\" onclick=\"detailmainhours('" . $row['item_id'] . "')\"> " . $rowhour_split_val . "</button></span>";

                }
                else{
                    $hour2 = "<span class=\"hoursout\" style=\"margin-right:20%\"><button class=\"btn btn-link\" onclick=\"detailmainhours('" . $row['item_id'] . "')\"> " . $row['hours'] . "</button></span>";
                }
                /** EDITED COMMAS **/
                $json['aaData'][] = array($q, $row['proname'], $row['client'], $row['fitur'], $row['grup'], $row['name'], $hour2);
                $q++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    function gettimesheettable($data) {
        $userid = $this->session->userdata('user_id');
        $sql = 'select i.item_id,i.name,x.user_id,t.hours,g.name as grup,f.name as fitur '
                . 'from item i left join t_task x on x.item_id = i.item_id'
                . ' left join `group` g on g.group_id=i.group_id  left '
                . 'join feature f on g.feature_id=f.feature_id left join project_platform p on f.proplat_id=p.proplat_id '
                . 'left join (select item_id,sum(main_hour) as "hours" from timesheet group by item_id)t on i.item_id=t.item_id where p.project_id="' .
                $data['project_id'] . '" and p.proplat_id="' . $data['proplat_id'] . '" and x.user_id = "' . $userid . '"';

//        echo $sql;die(0);

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $q = 1;
            foreach ($query->result_array() as $row) {

//                if ($row)
                $hour = "<span class=\"hoursout\" style=\"margin-right:20%\"><button class=\"btn btn-link\" onclick=\"detailmainhours('" . $row['item_id'] . "')\"> " . $row['hours'] . "</button></span><button onclick=\"addmainhours('" . $row['item_id'] . "')\" class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-plus-sign\" aria-hidden=\"true\"></span></button>
                ";
                $json['aaData'][] = array($q, $row['fitur'], $row['grup'], $row['name'], $hour);
                $q++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    function gettaskfordashboard() {
//        echo "abc";die(0);
        $userid = $this->session->userdata('user_id');
        $sql = 'select pro.name as proname,pro.client, i.item_id,i.name,x.user_id,t.hours,g.name as grup,f.name as fitur '
                . 'from item i left join t_task x on x.item_id = i.item_id'
                . ' left join `group` g on g.group_id=i.group_id  left '
                . 'join feature f on g.feature_id=f.feature_id left join project_platform p on f.proplat_id=p.proplat_id '
                . 'left join project_platform pp on f.proplat_id = pp.proplat_id
                   left join project pro on pp.project_id = pro.project_id '
                . 'left join (select item_id,sum(main_hour) as "hours" from timesheet group by item_id)t on i.item_id=t.item_id where  x.user_id = "' . $userid . '" and i.isclosed = "0" order by proname, `name` asc';

//        echo $sql;die(0);

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function updatetimesheet($data) {
//        echo "abc";die(0);
        $username = $this->session->userdata('username');
        $userid = $this->session->userdata('user_id');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d');
        $startdate = $tanggal . " 00:00:00";
        $enddate = $tanggal . " 23:59:59";
        $checkrecordquery = "select timesheet_id,start_date,end_date from timesheet where start_date >= '" . $startdate . "' and end_date <= '" . $enddate . "' and userid = '" . $userid . "' order by end_date desc";
//        echo $checkrecordquery;die(0);
        $executequery = $this->db->query($checkrecordquery);
        $result = $executequery->row_array();

        $startdatetoinsert = $tanggal . " 09:00:00";
        $enddatetoinser = $tanggal . " 18:00:00";

        if (sizeof($result) > 0) {
            $startdatetoinsert = $result['end_date'];
        }
        $timestampstar = strtotime($startdatetoinsert);




        //conversion
        $houradded = $data['nilaimanhour'];
        $minuteadded = "0";
        if (strpos($data['nilaimanhour'], ".") == true) {
            if (strpos(substr($data['nilaimanhour'], 0, 2), ".") == true) {
                $houradded = substr($data['nilaimanhour'], 0, 1);
                $minuteadded = "30";
            } else if (strpos(substr($data['nilaimanhour'], 0, 3), ".") == true) {
                $houradded = substr($data['nilaimanhour'], 0, 1);
                $minuteadded = "30";
            }
        }
        $ms = $houradded * 60 * 60 + $minuteadded * 60;
//        echo $houradded * 60 * 60 * 1000;die(0);
        $timestampend = $timestampstar + $ms;
        
        $startdatetoinsert = date("Y-m-d H:i:s",$timestampstar);
        $enddatetoinser = date("Y-m-d H:i:s",$timestampend);
        





        $datainserttimesheet = array(
            'item_id' => $data['itemid'],
            'start_date' => $startdatetoinsert,
            'end_date' => $enddatetoinser,
            'main_hour' => $data['nilaimanhour'],
            'userid' => $userid,
            'created_by' => $username,
            'created_date' => $tanggal,
            'modified_date' => $tanggal,
            'modified_by' => $username
        );
//        echo json_encode($datainserttimesheet);
//        die(0);
        
        
        $insert = $this->db->insert('timesheet', $datainserttimesheet);
        if ($insert) {
            
            $balikan = array('status' => 1,
                'msg' => 'Main Hour has been saved');
        } else {
            $balikan = array('status' => 0,
                'msg' => 'Failed has been saved');
        }
        return $balikan;
    }

    function savetimesheet($data) {
        $username = $this->session->userdata('username');
        $userid = $this->session->userdata('user_id');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');

        $datainserttimesheet = array(
            'item_id' => $data['item_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'main_hour' => $data['hours'],
            'userid' => $userid,
            'created_by' => $username,
            'created_date' => $tanggal,
            'modified_date' => $tanggal,
            'modified_by' => $username
        );
        $insert = $this->db->insert('timesheet', $datainserttimesheet);
        if ($insert) {
            $balikan = array('status' => 1,
                'msg' => 'Main Hour has been saved');
        } else {
            $balikan = array('status' => 0,
                'msg' => 'Failed has been saved');
        }
        return $balikan;
    }

    function detailmainhours($item_id) {
        $hasil = array();
        $sql = "select a.timesheet_id,a.item_id,a.main_hour,a.userid,b.username,a.start_date,a.end_date,c.name as item_name from timesheet a inner join tuser b on a.userid=b.userid inner join item c on a.item_id=c.item_id where a.item_id='" . $item_id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $hasil = $query->result_array();
        }
        return $hasil;
    }

    function getitemname($item_id) {
        $hasil = array();
        $sql = "select * from item where item_id='" . $item_id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $hasil = $query->row_array();
        }
        return $hasil;
    }

}
