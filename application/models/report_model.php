<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of report_model
 *
 * @author ASUS
 */
class report_model extends CI_Model {

    //put your code here

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    function getreportuser() {

        $sql = "select tuser.userid, tperson.salutation,tperson.firstname, tperson.lastname,tuser.last_activity,tuser.last_login from tuser left join tperson on tuser.userid =  tperson.user_id WHERE tuser.isdelete='0'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $x = 1;
            foreach ($query->result_array() as $row) {  
                $fullname = $row['salutation']." ". $row['firstname'] . " " . $row['lastname'];
                $lastactivity = $row['last_activity'] == "" ? "-" :$row['last_activity']; 
                $json['aaData'][] = array($row['userid'], $fullname, $row['last_login']
                        ,$lastactivity);
                
                $x++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    function getreportproject() {
        error_reporting(0);
        $sql = "SELECT p.project_id,p.name,p.client,SUM(i.hour)AS plan,SUM(ts.hours)AS actual,u.firstname AS pm FROM item i 
                LEFT JOIN `group` g ON i.group_id=g.group_id  
                LEFT JOIN feature f ON g.feature_id=f.feature_id
                LEFT JOIN project_platform pp ON f.proplat_id=pp.proplat_id
                RIGHT JOIN project p ON pp.project_id=p.project_id
                LEFT JOIN tperson u ON p.userid=u.user_id
                LEFT JOIN (SELECT item_id,SUM(main_hour)AS hours FROM `timesheet` GROUP BY item_id)ts ON ts.item_id=i.item_id
                GROUP BY project_id ORDER BY p.created_date DESC";
        $query = $this->db->query($sql);
        //return $query->result_array();exit;
        if ($query->num_rows() > 0) {
            $x = 1;
            foreach ($query->result_array() as $row) {  
                /*$sql2 = "
                     SELECT p.platform_id, p.name, i.hour AS 'plan',SUM(t.main_hour) AS 'actual' FROM platform AS p 
                        LEFT JOIN (SELECT * FROM project_platform WHERE project_id ='".$row['project_id']."') AS pp 
                            ON pp.platform_id = p.platform_id
                        LEFT JOIN (SELECT * FROM feature) AS f 
                            ON f.proplat_id=pp.proplat_id 
                        LEFT JOIN (SELECT * FROM `group`) AS g
                            ON g.feature_id=f.feature_id 
                        LEFT JOIN (SELECT * FROM item) AS i
                            ON i.group_id=g.group_id 
                        LEFT JOIN (SELECT * FROM timesheet) AS t
                            ON t.item_id=i.item_id 
                        GROUP BY p.platform_id, p.name
                ";*/
                /*$sql2 = "
                        SELECT distinct i.item_id,i.group_id,i.name,g.name AS 'group' , f.name AS 'feature',i.hour AS 'plan',SUM(t.main_hour) AS 'actual',
                        pp.platform_id,pl.name AS 'platform' FROM item i 
                        LEFT JOIN `group` g ON i.group_id=g.group_id 
                        LEFT JOIN feature f ON g.feature_id=f.feature_id 
                        LEFT JOIN timesheet t ON i.item_id=t.item_id 
                        LEFT JOIN project_platform pp ON pp.proplat_id=f.proplat_id 
                        LEFT JOIN platform pl ON pl.platform_id=pp.platform_id 
                        WHERE pp.project_id='".$row['project_id']."' GROUP BY i.item_id ORDER BY 1
                        ";
                $query_platform = $this->db->query($sql2);
                $htmlplatform = '<ul>';
                foreach ($query_platform->result_array() as $row_platform) {
                         $htmlplatform .='<li>' . $row_platform['platform'] . '</li>';
                }
                $htmlplatform .='</ul>';*/

                //$rowactual = '<span id="rowactual" onClick="showDetailRepProject('.$row['project_id'].')">'.$row['actual'].'</span>';
                /** EDITED COMMAS **/
                error_reporting(0);
                $explode  = explode('.', $row['actual']);
                $strlen   = $explode[1];
                $strlen_d = strlen($strlen);
                //$length_actual_h = strlen(explode('.', $row['actual']));
                $length_actual = $strlen_d;
                $row_actual = $row['actual'];
                if ($length_actual >= 1 && $length_actual <= 5) {
                    $a = explode(".", $row_actual);
                    count($a)>1 and $a[1] = 5;
                    $rowactual_split_val = implode(".", $a);
                    $rowactual_split = '<a title="Show Detail" href="#" onclick="showDetailRepProject('.$row['project_id'].')">'. $rowactual_split_val.'</a>';
                }
                elseif ($length_actual > 5 && $length_actual <= 10) {
                    $a = explode(".", $row_actual);
                    count($a)>1 and $a[1] = 10;
                    $rowactual_split_val = implode(".", $a);
                    $rowactual_split = '<a title="Show Detail" href="#" onclick="showDetailRepProject('.$row['project_id'].')">'. $rowactual_split_val.'</a>';
                }
                elseif ($length_actual > 10){
                    $rowactual_split_val = round($row_actual);
                    $rowactual_split = '<a title="Show Detail" href="#" onclick="showDetailRepProject('.$row['project_id'].')">'. $rowactual_split_val.'</a>';
                }
                else{
                    $rowactual_split = '<a title="Show Detail" href="#" onclick="showDetailRepProject('.$row['project_id'].')">'. $row['actual'] .'</a>';
                }
                /** EDITED COMMAS **/

                $rowactual = '<a title="Show Detail" href="#" onclick="showDetailRepProject(' . $row['project_id'] . ')">' . $row['actual'] . '</a>';
                $json['aaData'][] = array($x, $row['name'], $row['client'], $row['pm']
                        ,$row['plan'], $rowactual_split);
                
                $x++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;
    }

    function getdetailreportdata($project_id) {
      error_reporting(0);
        $hasil=array();
        /*$sql = "select i.item_id,i.group_id,i.name,g.name as 'group' , f.name as 'feature',i.hour as 'plan',sum(t.main_hour) as 'actual',pp.platform_id,pl.name as 'platform' from item i left join `group` g on i.group_id=g.group_id left join feature f on g.feature_id=f.feature_id left join timesheet t on i.item_id=t.item_id left join project_platform pp on pp.proplat_id=f.proplat_id left join platform pl on pl.platform_id=pp.platform_id where pp.project_id='1' group by i.item_id order by 1";*/
        /*$sql2 = "
                SELECT 
                       proj.name AS nama_project, proj.project_id,

                       MAX(CASE WHEN p.platform_id = 1 THEN pp.platform_id END) 'id_general',
                       SUM(CASE WHEN p.platform_id = 1 THEN i.hour END) 'General plan',
                       SUM(CASE WHEN p.platform_id = 1 THEN t.main_hour END) 'General Actual',  
                       
                       MAX(CASE WHEN p.platform_id = 2 THEN pp.platform_id END) 'id_dev_web_service',
                       SUM(CASE WHEN p.platform_id = 2 THEN i.hour END) 'Development Web Service Plan',
                       SUM(CASE WHEN p.platform_id = 2 THEN t.main_hour END) 'Development Web Service Actual',
                       
                       MAX(CASE WHEN p.platform_id = 3 THEN pp.platform_id END) 'id_dev_web',
                       SUM(CASE WHEN p.platform_id = 3 THEN i.hour END) 'Development Web Plan',
                       SUM(CASE WHEN p.platform_id = 3 THEN t.main_hour END) 'Development Web Actual',
                       
                       MAX(CASE WHEN p.platform_id = 4 THEN pp.platform_id END) 'id_dev_android',
                       SUM(CASE WHEN p.platform_id = 4 THEN i.hour END) 'Development Android Plan',
                       SUM(CASE WHEN p.platform_id = 4 THEN t.main_hour END) 'Development Android Actual',
                       
                       MAX(CASE WHEN p.platform_id = 5 THEN pp.platform_id END) 'id_dev_ios',
                       SUM(CASE WHEN p.platform_id = 5 THEN i.hour END) 'Development IOS Plan',
                       SUM(CASE WHEN p.platform_id = 5 THEN t.main_hour END) 'Development IOS Actual'
                       
                FROM platform AS p
                LEFT JOIN (SELECT * FROM project_platform WHERE project_id ='".$project_id."') AS pp 
                    ON pp.platform_id = p.platform_id
                LEFT JOIN (SELECT * FROM feature) AS f 
                    ON f.proplat_id=pp.proplat_id 
                LEFT JOIN (SELECT * FROM `group`) AS g
                    ON g.feature_id=f.feature_id 
                LEFT JOIN (SELECT * FROM item) AS i
                    ON i.group_id=g.group_id 
                LEFT JOIN (SELECT * FROM timesheet) AS t
                    ON t.item_id=i.item_id 
                LEFT JOIN project AS proj
                    ON proj.project_id=pp.project_id 
        
        ";*/
        $sql2 = "
                SELECT 
                       proj.name AS nama_project, proj.project_id,
            
                       MAX(CASE WHEN p.platform_id = 1 THEN pp.platform_id END) 'id_general',
                       SUM(CASE WHEN p.platform_id = 1 THEN i.hour END) 'General plan',
                       SUM(CASE WHEN p.platform_id = 1 THEN t.main_hour END) 'General Actual',  
                       
                       MAX(CASE WHEN p.platform_id = 2 THEN pp.platform_id END) 'id_dev_web_service',
                       SUM(CASE WHEN p.platform_id = 2 THEN i.hour END) 'Development Web Service Plan',
                       SUM(CASE WHEN p.platform_id = 2 THEN t.main_hour END) 'Development Web Service Actual',
                       
                       MAX(CASE WHEN p.platform_id = 3 THEN pp.platform_id END) 'id_dev_web',
                       SUM(CASE WHEN p.platform_id = 3 THEN i.hour END) 'Development Web Plan',
                       SUM(CASE WHEN p.platform_id = 3 THEN t.main_hour END) 'Development Web Actual',
                       
                       MAX(CASE WHEN p.platform_id = 4 THEN pp.platform_id END) 'id_dev_android',
                       SUM(CASE WHEN p.platform_id = 4 THEN i.hour END) 'Development Android Plan',
                       SUM(CASE WHEN p.platform_id = 4 THEN t.main_hour END) 'Development Android Actual',
                       
                       MAX(CASE WHEN p.platform_id = 5 THEN pp.platform_id END) 'id_dev_ios',
                       SUM(CASE WHEN p.platform_id = 5 THEN i.hour END) 'Development IOS Plan',
                       SUM(CASE WHEN p.platform_id = 5 THEN t.main_hour END) 'Development IOS Actual'
                     
                       
                FROM platform AS p
                LEFT JOIN (SELECT * FROM project_platform WHERE project_id ='".$project_id."') AS pp 
                    ON pp.platform_id = p.platform_id
                LEFT JOIN project AS proj
                    ON proj.project_id=pp.project_id  
                LEFT JOIN (SELECT * FROM feature) AS f 
                    ON f.proplat_id=pp.proplat_id 
                LEFT JOIN (SELECT * FROM `group`) AS g
                    ON g.feature_id=f.feature_id 
                LEFT JOIN (SELECT * FROM item) AS i
                    ON i.group_id=g.group_id 
                 LEFT JOIN (SELECT item_id,SUM(main_hour) AS `main_hour` FROM timesheet GROUP BY item_id) AS t
                    ON t.item_id=i.item_id
        ";
        $query = $this->db->query($sql2);


        if ($query->num_rows() > 0) {
            $x = 1;
            foreach ($query->result_array() as $row) {  
                //$rowactual = '<span id="rowactual">'.$row['actual'].'</span>';
               error_reporting(0);
               $explode_general_plan    = explode('.', $row['General plan']);
               $strlen_general_plan     = $explode_general_plan[1];
               $general_plan            = strlen($strlen_general_plan);

               $explode_general_actual  = explode('.', $row['General Actual']);
               $strlen_general_actual   = $explode_general_actual[1];
               $general_actual          = strlen($strlen_general_actual);
               $general_plan_ori   = $row['General plan'];
               $general_actual_ori = $row['General Actual'];
               /*$general_plan   = strlen(explode('.', $row['General plan'])[1]);
               $general_actual = strlen(explode('.', $row['General Actual'])[1]);
               $general_plan_ori   = $row['General plan'];
               $general_actual_ori = $row['General Actual'];*/

               $explode_webservice_plan    = explode('.', $row['Development Web Service Plan']);
               $strlen_webservice_plan     = $explode_webservice_plan[1];
               $webservice_plan            = strlen($strlen_webservice_plan);
               
               $explode_webservice_actual  = explode('.', $row['General Actual']);
               $strlen_webservice_actual   = $explode_webservice_actual[1];
               $webservice_actual          = strlen($strlen_webservice_actual);

               /*$webservice_plan   = strlen(explode('.', $row['Development Web Service Plan'])[1]);
               $webservice_actual = strlen(explode('.', $row['Development Web Service Actual'])[1]);*/
               $webservice_plan_ori   = $row['Development Web Service Plan'];
               $webservice_actual_ori = $row['Development Web Service Actual'];

               $explode_webdev_plan        = explode('.', $row['Development Web Plan']);
               $strlen_webdev_actual       = $explode_webdev_plan[1];
               $webdev_plan                = strlen($strlen_webdev_actual);
               
               $explode_webdev_actual  = explode('.', $row['Development Web Actual']);
               $strlen_webdev_actual   = $explode_webdev_actual[1];
               $webdev_actual          = strlen($strlen_webdev_actual);

               /*$webdev_plan   = strlen(explode('.', $row['Development Web Plan'])[1]);
               $webdev_actual = strlen(explode('.', $row['Development Web Actual'])[1]);*/
               $webdev_plan_ori = $row['Development Web Plan'];
               $webdev_actual_ori = $row['Development Web Actual'];

               $explode_android_plan        = explode('.', $row['Development Android Plan']);
               $strlen_android_plan         = $explode_android_plan[1];
               $android_plan                = strlen($strlen_android_plan);
               
               $explode_android_actual  = explode('.', $row['Development Android Actual']);
               $strlen_android_actual   = $explode_android_actual[1];
               $android_actual          = strlen($strlen_android_actual);

               /*$android_plan   = strlen(explode('.', $row['Development Android Plan'])[1]);
               $android_actual = strlen(explode('.', $row['Development Android Actual'])[1]);*/
               $android_plan_ori = $row['Development Android Plan'];
               $android_actual_ori = $row['Development Android Actual'];

               $explode_ios_plan        = explode('.', $row['Development IOS Plan']);
               $strlen_ios_plan         = $explode_ios_plan[1];
               $ios_plan                = strlen($strlen_ios_plan);
               
               $explode_ios_actual  = explode('.', $row['Development IOS Actual']);
               $strlen_ios_actual   = $explode_ios_actual[1];
               $ios_actual          = strlen($strlen_ios_actual);

               /*$ios_plan   = strlen(explode('.', $row['Development IOS Plan'])[1]);
               $ios_actual = strlen(explode('.', $row['Development IOS Actual'])[1]);*/
               $ios_plan_ori = $row['Development IOS Plan'];
               $ios_actual_ori = $row['Development IOS Actual'];

                 if ($general_plan >= 1 && $general_plan <= 5 || $general_actual >= 1 && $general_actual <= 5 || $webservice_plan >= 1 && $webservice_plan <= 5 || $webservice_actual >= 1 && $webservice_actual <= 5 || $webdev_plan >= 1 && $webdev_plan <= 5 || $webdev_actual >= 1 && $webdev_actual <= 5 || $android_plan >= 1 && $android_plan <= 5 || $android_actual >= 1 && $android_actual <= 5 || $ios_plan >= 1 && $ios_plan <= 5 || $ios_actual >= 1 && $ios_actual <= 5 ) {
                    /** GENERAL **/
                    $a = explode(".", $general_plan_ori);
                    count($a)>1 and $a[1] = 5;
                    $generalplan_split_val = implode(".", $a);
                    $a2 = explode(".", $general_actual_ori);
                    count($a2)>1 and $a2[1] = 5;
                    $generalactual_split_val = implode(".", $a2);
                    $general2 = '<a title="Show Detail General Development" href="#" onclick="showdetail_proj('.$row['id_general'].', '.$row['project_id'].')">' . $generalplan_split_val.' - '.$generalactual_split_val . '</a>';
                    /** GENERAL **/

                    /** Webservice **/
                    $a_ws = explode(".", $webservice_plan_ori);
                    count($a_ws)>1 and $a_ws[1] = 5;
                    $wsplan_split_val = implode(".", $a_ws);
                    $a_ws2 = explode(".", $webservice_actual_ori);
                    count($a_ws2)>1 and $a_ws2[1] = 5;
                    $wsactual_split_val = implode(".", $a_ws2);
                    $webservice2 = '<a title="Show Detail Web Service Development" href="#" onclick="showdetail_proj('.$row['id_dev_web_service'].', '.$row['project_id'].')">' . $wsplan_split_val.' - '.$wsactual_split_val . '</a>'; 
                    /** Webservice **/

                    /** Webdev **/
                    $a_webdev = explode(".", $webdev_plan_ori);
                    count($a_webdev)>1 and $a_webdev[1] = 5;
                    $webdevplan_split_val = implode(".", $a_webdev);
                    $a_webdev2 = explode(".", $webdev_actual_ori);
                    count($a_webdev2)>1 and $a_webdev2[1] = 5;
                    $webdevactual_split_val = implode(".", $a_webdev2);
                    $webdev2     = '<a title="Show Detail Web Development" href="#" onclick="showdetail_proj('.$row['id_dev_web'].', '.$row['project_id'].')">' . $webdevplan_split_val.' - '.$webdevactual_split_val . '</a>'; 
                    /** Webdev **/

                    /** Android **/
                    $a_android = explode(".", $android_plan_ori);
                    count($a_android)>1 and $a_android[1] = 5;
                    $androidplan_split_val = implode(".", $a_android);
                    $a_android2 = explode(".", $android_actual_ori);
                    count($a_android2)>1 and $a_android2[1] = 5;
                    $androidactual_split_val = implode(".", $a_android2);
                    $android2     = '<a title="Show Detail Android Development" href="#" onclick="showdetail_proj('.$row['id_dev_android'].', '.$row['project_id'].')">' . $androidplan_split_val.' - '.$androidactual_split_val . '</a>'; 
                    /** Android **/

                    /** ios **/
                    $a_ios = explode(".", $ios_plan_ori);
                    count($a_ios)>1 and $a_ios[1] = 5;
                    $iosplan_split_val = implode(".", $a_ios);
                    $a_ios2 = explode(".", $ios_actual_ori);
                    count($a_ios2)>1 and $a_ios2[1] = 5;
                    $iosactual_split_val = implode(".", $a_ios2);
                    $ios2  = '<a title="Show Detail IOS Development" href="#" onclick="showdetail_proj('.$row['id_dev_ios'].', '.$row['project_id'].')">' . $iosplan_split_val.' - '.$iosactual_split_val . '</a>'; 
                    /** IOS **/
                 }
              if ($general_plan > 5 && $general_plan <= 10 || $general_actual > 5 && $general_actual <= 10 || $webservice_plan > 5 && $webservice_plan <= 10 || $webservice_actual > 5 && $webservice_actual <= 10 || $webdev_plan > 5 && $webdev_plan <= 10 || $webdev_actual > 5 && $webdev_actual <= 10 || $android_plan > 5 && $android_plan <= 10 || $android_actual > 5 && $android_actual <= 10 || $ios_plan > 5 && $ios_plan <= 10 || $ios_actual > 5 && $ios_actual <= 10 ) {

                      /** GENERAL **/
                    $a = explode(".", $general_plan_ori);
                    count($a)>1 and $a[1] = 10;
                    $generalplan_split_val = implode(".", $a);
                    $a2 = explode(".", $general_actual_ori);
                    count($a2)>1 and $a2[1] = 10;
                    $generalactual_split_val = implode(".", $a2);
                    $general2 = '<a title="Show Detail General Development" href="#" onclick="showdetail_proj('.$row['id_general'].', '.$row['project_id'].')">' . $generalplan_split_val.' - '.$generalactual_split_val . '</a>';
                    /** GENERAL **/

                    /** Webservice **/
                    $a_ws = explode(".", $webservice_plan_ori);
                    count($a_ws)>1 and $a_ws[1] = 10;
                    $wsplan_split_val = implode(".", $a_ws);
                    $a_ws2 = explode(".", $webservice_actual_ori);
                    count($a_ws2)>1 and $a_ws2[1] = 10;
                    $wsactual_split_val = implode(".", $a_ws2);
                    $webservice2 = '<a title="Show Detail Web Service Development" href="#" onclick="showdetail_proj('.$row['id_dev_web_service'].', '.$row['project_id'].')">' . $wsplan_split_val.' - '.$wsactual_split_val . '</a>'; 
                    /** Webservice **/

                    /** Webdev **/
                    $a_webdev = explode(".", $webdev_plan_ori);
                    count($a_webdev)>1 and $a_webdev[1] = 5;
                    $webdevplan_split_val = implode(".", $a_webdev);
                    $a_webdev2 = explode(".", $webdev_actual_ori);
                    count($a_webdev2)>1 and $a_webdev2[1] = 5;
                    $webdevactual_split_val = implode(".", $a_webdev2);
                    $webdev2     = '<a title="Show Detail Web Development" href="#" onclick="showdetail_proj('.$row['id_dev_web'].', '.$row['project_id'].')">' . $webdevplan_split_val.' - '.$webdevactual_split_val . '</a>'; 
                    /** Webdev **/

                    /** Android **/
                    $a_android = explode(".", $android_plan_ori);
                    count($a_android)>1 and $a_android[1] = 10;
                    $androidplan_split_val = implode(".", $a_android);
                    $a_android2 = explode(".", $android_actual_ori);
                    count($a_android2)>1 and $a_android2[1] = 10;
                    $androidactual_split_val = implode(".", $a_android2);
                    $android2     = '<a title="Show Detail Android Development" href="#" onclick="showdetail_proj('.$row['id_dev_android'].', '.$row['project_id'].')">' . $androidplan_split_val.' - '.$androidactual_split_val . '</a>'; 
                    /** Android **/

                    /** ios **/
                    $a_ios = explode(".", $ios_plan_ori);
                    count($a_ios)>1 and $a_ios[1] = 10;
                    $iosplan_split_val = implode(".", $a_ios);
                    $a_ios2 = explode(".", $ios_actual_ori);
                    count($a_ios2)>1 and $a_ios2[1] = 10;
                    $iosactual_split_val = implode(".", $a_ios2);
                    $ios2  = '<a title="Show Detail IOS Development" href="#" onclick="showdetail_proj('.$row['id_dev_ios'].', '.$row['project_id'].')">' . $iosplan_split_val.' - '.$iosactual_split_val . '</a>'; 
                    /** IOS **/

               }
               elseif ($general_plan > 10 || $general_actual > 10 || $webservice_plan > 10 || $webservice_actual > 10 || $webdev_plan > 10 || $webdev_actual > 10 || $android_plan > 10 || $android_actual > 10 || $ios_plan > 10 || $ios_actual > 10){
                    $general_plan_split_val        = round($general_plan_ori);
                    $general_actual_split_val      = round($general_actual_ori);
                    $general2 = '<a title="Show Detail General Development" href="#" onclick="showdetail_proj('.$row['id_general'].', '.$row['project_id'].')">' . $general_plan_split_val.' - '.$general_actual_split_val . '</a>';

                    $webservice_plan_split_val     = round($webservice_plan_ori);
                    $webservice_actual_split_val   = round($webservice_actual_ori);
                    $webservice2 = '<a title="Show Detail Web Service Development" href="#" onclick="showdetail_proj('.$row['id_dev_web_service'].', '.$row['project_id'].')">' . $webservice_plan_split_val.' - '.$webservice_actual_split_val . '</a>'; 

                    $webdev_plan_split_val         = round($webdev_plan_ori);
                    $webdev_actual_split_val       = round($webdev_actual_ori);
                    $webdev2     = '<a title="Show Detail Web Development" href="#" onclick="showdetail_proj('.$row['id_dev_web'].', '.$row['project_id'].')">' . $webdev_plan_split_val.' - '.$webdev_actual_split_val . '</a>'; 

                    $android_plan_split_val         = round($android_plan_ori);
                    $android_actual_split_val       = round($android_actual_ori);
                    $android2 = '<a title="Show Detail Android Development" href="#" onclick="showdetail_proj('.$row['id_dev_android'].', '.$row['project_id'].')">' . $android_plan_split_val.' - '.$android_actual_split_val . '</a>'; 

                    $ios_plan_split_val            = round($ios_plan_ori);
                    $ios_actual_split_val          = round($ios_actual_ori);
                    $ios2 = '<a title="Show Detail IOS Development" href="#" onclick="showdetail_proj('.$row['id_dev_ios'].', '.$row['project_id'].')">' . $ios_plan_split_val.' - '.$ios_actual_split_val . '</a>'; 
               }
               else{
                 $general2 = '<a title="Show Detail General Development" href="#" onclick="showdetail_proj('.$row['id_general'].', '.$row['project_id'].')">' . $row['General plan'].' - '.$row['General Actual'] . '</a>';
                 $webservice2 = '<a title="Show Detail Web Service Development" href="#" onclick="showdetail_proj('.$row['id_dev_web_service'].', '.$row['project_id'].')">' . $row['Development Web Service Plan'].' - '.$row['Development Web Service Actual'] . '</a>'; 
                 $webdev2     = '<a title="Show Detail Web Development" href="#" onclick="showdetail_proj('.$row['id_dev_web'].', '.$row['project_id'].')">' . $row['Development Web Plan'].' - '.$row['Development Web Actual'] . '</a>'; 
                 $android2     = '<a title="Show Detail Android Development" href="#" onclick="showdetail_proj('.$row['id_dev_android'].', '.$row['project_id'].')">' . $row['Development Android Plan'].' - '.$row['Development Android Actual'] . '</a>'; 
                 $ios2  = '<a title="Show Detail IOS Development" href="#" onclick="showdetail_proj('.$row['id_dev_ios'].', '.$row['project_id'].')">' . $row['Development IOS Plan'].' - '.$row['Development IOS Actual'] . '</a>'; 
               }

               $nama_project = $row['nama_project'];
               $general = '<a title="Show Detail General Development" href="#" onclick="showdetail_proj('.$row['id_general'].', '.$row['project_id'].')">' . $row['General plan'].' - '.$row['General Actual'] . '</a>';
               $webservice = '<a title="Show Detail Web Service Development" href="#" onclick="showdetail_proj('.$row['id_dev_web_service'].', '.$row['project_id'].')">' . $row['Development Web Service Plan'].' - '.$row['Development Web Service Actual'] . '</a>'; 
               $webdev     = '<a title="Show Detail Web Development" href="#" onclick="showdetail_proj('.$row['id_dev_web'].', '.$row['project_id'].')">' . $row['Development Web Plan'].' - '.$row['Development Web Actual'] . '</a>'; 
               $android     = '<a title="Show Detail Android Development" href="#" onclick="showdetail_proj('.$row['id_dev_android'].', '.$row['project_id'].')">' . $row['Development Android Plan'].' - '.$row['Development Android Actual'] . '</a>'; 
               $ios  = '<a title="Show Detail IOS Development" href="#" onclick="showdetail_proj('.$row['id_dev_ios'].', '.$row['project_id'].')">' . $row['Development IOS Plan'].' - '.$row['Development IOS Actual'] . '</a>'; 
                $json['aaData'][] = array($x, $nama_project, $general2, $webservice2, $webdev2, $android2, $ios2);
                //$json['aaData'][] = array($x, $nama_project, $general, $webservice, $webdev, $android, $ios);
                
                $x++;
            }
        } else {
            $json['aaData'] = array();
        }
        return $json;


        /*return $query->result_array();exit;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                array_push($hasil,$row);
            }
        }
        return $hasil;exit;*/
    }
    
   function getdetailreportprojectplatform($id_platform, $id_project){
    $sql = "
            SELECT i.item_id,i.group_id,i.name,g.name AS 'group' , f.name AS 'feature',i.hour AS 'plan', i.isclosed, SUM(t.main_hour) AS 'actual',
            pp.platform_id,pl.name AS 'platform', p.`project_id` FROM item i 
            LEFT JOIN `group` g ON i.group_id=g.group_id 
            LEFT JOIN feature f ON g.feature_id=f.feature_id 
            LEFT JOIN timesheet t ON i.item_id=t.item_id 
            LEFT JOIN project_platform pp ON pp.proplat_id=f.proplat_id 
            LEFT JOIN platform pl ON pl.platform_id=pp.platform_id 
            LEFT JOIN project p ON p.project_id=pp.project_id 
            WHERE pp.project_id='".$id_project."' and pp.platform_id  = '".$id_platform."' GROUP BY i.item_id ORDER BY pl.name ASC
        ";
    $query = $this->db->query($sql);
    if ($query->num_rows() > 0) {
        $x = 1;
            foreach ($query->result_array() as $row) {  
                //$btn_actual = '<a title="Show Detail IOS Development" href="#" onclick="detailmainhours('""')">'.$row['actual'].'</a>'; 
                $btn_actual = "<span class=\"hoursout\" style=\"margin-right:20%\"><button class=\"btn btn-link\" onclick=\"detailmainhours('" . $row['item_id'] . "')\"> " . $row['actual'] . "</button></span>
                ";
                $status = "Open";
                if (strcasecmp($row['isclosed'], 1)){
                    $status = "Closed";
                }
                if ($status == 'Closed') {
                    $status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#EEEEEE !important;"
                        onClick="changeStatusClosed(' . $row['item_id'] . ', ' . $row['platform_id'] . ', ' . $row['project_id'] . ')">
                        <span>'.$status.'</span></a>';
                    /*$status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#EEEEEE !important;cursor:default;">
                        <span>'.$status.'</span></a>';*/
                }
                else{
                    $status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#26C281 !important; border-color: #26C281 !important; color:#ececec !important;"
                        onClick="changeStatusClosed(' . $row['item_id'] . ', ' . $row['platform_id'] . ', ' . $row['project_id'] . ')">
                        <span>'.$status.'</span></a>';
                    /*$status_btn = '<a class="btn btn-default" tabindex="0" style="float: right; background-color:#26C281 !important; border-color: #26C281 !important; color:#ececec !important;cursor:default;">
                        <span>'.$status.'</span></a>';*/
                }
                $json['aaData'][] = array($x, $row['feature'], $row['group'], $row['name'], $row['plan'], $btn_actual, $status_btn);
                
                $x++;
        }
    }
    else{
       $json['aaData'] = array(); 
    }
     return $json;
   }

   function chekdataforlink($data) {
        if ($data['type'] == 'detailproj2') {
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

}
