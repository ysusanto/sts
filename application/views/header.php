<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="icon" href="<?php echo base_url() ?>assets/checkbox_checked.png">

        <title>seatechmobile Time Sheet</title>

        <!-- Bootstrap core CSS -->
        <link href="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/css/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/sts.css">
        <!--<link rel="stylesheet" href="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.css">-->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/jquery.dataTables.min.css">
        <!--<link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/dataTables.bootstrap.min.css">-->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/buttons.bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.css">
        <script src="<?php echo base_url() ?>assets/jquery-1.11.3.min.js"></script>
        <script src="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
        <script src="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/js/bootstrap.min.js" ></script>
        <script src="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/js/bootstrap-timepicker.min.js"></script>
        <script src="<?php echo base_url() ?>assets/datatables/js/jquery.dataTables.min.js"></script>
        <!--<script src="<?php echo base_url() ?>assets/datatables/js/dataTables.bootstrap.min.js"></script>-->
        <script src="<?php echo base_url() ?>assets/datatables/js/dataTables.buttons.min.js"></script>
        <script src="<?php echo base_url() ?>assets/datatables/js/buttons.bootstrap.min.js"></script>
        <script src="<?php echo base_url() ?>assets/jquery.form.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--        [if lt IE 9]>
                  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                <![endif]-->
    </head>

    <body>
        <?php
        $user_id = $this->session->userdata('user_id');

        if ($user_id > 0) {
            $data['datauser'] = $this->user_model->get_user($user_id);
            $data['menu'] = $this->user_model->get_menu($data['datauser']['role_id']);
            $username = $data['datauser']['username'];
            $data['chek_pm'] = $chekpm;
//            if ($user->role_id == 0) {
//                $this->load->view('header_admin', array('datauser' => $user));
//            } else {
            $this->load->view('header_user', $data);
//            }
        } else {
            $this->load->view('header_default');
        }
        ?>