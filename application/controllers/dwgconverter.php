<?php

class dwgconverter extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('db_load');
        $this->load->library('upload');
    }

    public function index() {

        $this->load->view('poc');
    }

    public function doupload() {
        $target_dir = base_url() . "assets/dwgsample/";
//        echo $target_dir;
        $target_file = $target_dir . basename($_FILES["dwg"]["name"]);
        $uploadOk = 1;
//        print_r($_FILES);die(0);
//        ini_set("upload_max_filesize","3000M");
//        ini_set("memory_limit","500M");
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if (!empty($_FILES['dwg']['name'])) {
            $pathfile = pathinfo($_FILES['dwg']['name']);
            $config['upload_path'] = 'assets/dwgsample/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|dwg|acad';
            $config['file_name'] = "tesdwg" . '.' . $pathfile['extension'];
            $config['max_size'] = '100000000000';

            $config['overwrite'] = TRUE;
//            $config['encrypt_name'] = FALSE;
            $config['remove_spaces'] = TRUE;
            $this->upload->initialize($config);
            if ($this->upload->do_upload('dwg')) {
                $image = $this->upload->data();
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://54.85.21.160/API/test_api.ashx");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '{
	"convert_type": "dwg2txt",
	"user_email": "bismowirayuda@seatech.com",
	"sendto_email": "bismowirayuda@seatech.com",
	"files": [{
		"name": "tesdwg.dwg",
		"direct_link": "http://api.seatechmobile.com/STS/assets/dwgsample/tesdwg.dwg",
		"source_file_format": "dwg",
		"file_version": "ACAD2010"
	}]
}');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));

                $result = curl_exec($ch);
                echo "Konversi Selesai <br>";
                echo "Silahkan check email bismowirayuda@seatech.com";
            } else {
                echo $this->upload->display_errors();
            }
        }
    }

}

/*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    