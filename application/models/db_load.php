<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db_load
 *
 * @author ASUS
 */
class db_load extends CI_Model {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->load->database();
         date_default_timezone_set('Asia/Jakarta');
        $this->load->library('session');
        $this->load->helper('date');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('image_lib');
//         $this->load->library('image_lib');
        $this->load->library('upload');
        $this->load->library('email');
    }

    public function getplatform() {
        $query = $this->db->query('select * from platform');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }
    
    public function getrole() {
        $query = $this->db->query('select * from role');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }
    
    public function getrolewithouthob() {
        $query = $this->db->query('select * from role where role_id !=0');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }
    
    

    public function getmember() {
        $query = $this->db->query("SELECT tuser.userid,tuser.username,tuser.email,tuser.role_id,role.name,tperson.salutation,tperson.firstname,tperson.lastname "
                . "FROM tuser LEFT JOIN tperson on tuser.userid = tperson.user_id LEFT JOIN role on tuser.role_id = role.role_id where tuser.isdelete = '0' and tuser.role_id!=0");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function chekpm($user_id) {
        $query = $this->db->query('select userid from project where userid="' . $user_id . '"');
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function uploadfile($data) {


        $path = $data['path'];

        $file = $_FILES['filename']['name'];
        $type = 'application/octet-stream|xls|xlsx|application/vnd.ms-excel|application/excel|application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|application/vnd.oasis.opendocument.spreadsheet';
        $name = 'filename';
        /*$return = array(
            'status' => 0,
            'message' => 'Upload gagal',
            'msg' => $_FILES['filename']['type']
        );
        return $return;*/

        if (!empty($file)) {
            $pathfile = pathinfo($file);
            $config['upload_path'] = $path; //'assets/img/boat/';
            $config['allowed_types'] = $type;
            $config['file_name'] = $data['nama'] . '_' . date('dmy') . '.' . $pathfile['extension'];
            $config['max_size'] = '2000';
//            $config['max_width'] = '500';
//            $config['max_height'] = '600';
            $config['overwrite'] = TRUE;
//            $config['encrypt_name'] = FALSE;
            $config['remove_spaces'] = TRUE;
            $this->upload->initialize($config);
            if ($this->upload->do_upload($name)) {
                $image = $this->upload->data();
//                print_r($image);
//                die(0);
                $imagepath = $path . $image['file_name'];
                $names = $image['file_name'];
//                $data['thumbimage'] = 'assets/img/'.$image['file_name'];
//                $data['namaimage'] = $image['orig_name'];
////                if($this->image_lib->resize()){
////                    $data['thumbimage'] = 'assets/shoppict/thumb/'.$image['raw_name'].'_thumb'.$image['file_ext'];
////                }
////                $return = $this->webshop_model->updateCover($data);
//                $images = file_get_contents($data['mainimage']);
////                $base64 = 'data:image/' . $pathfile['extension'] . ';base64,' . base64_encode($images);
//                $base64=base64_encode($images);
                $return = array(
                    'status' => 1,
                    'path' => $imagepath,
                    'name' => $names
                );
                return $return;
            } else {
                $return = array(
                    'status' => 0,
                    'message' => 'Upload gagal',
                    'msg' => $this->upload->display_errors()
                );

                return $return;
            }
        }
    }

}
