<div class="navbar navbar-static-top " >
    <div class="container">
        <div class="navbar-header" >
            <a class="navbar-brand" href="<?php echo site_url() ?>">Time Sheet</a>
        </div>
        <ul class="nav navbar-nav">
            <!--<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>-->

            <?php
//            echo json_encode($menu);die(0);
            if (isset($menu)) {
                if (strcasecmp($menu['roleid'], "9")!=0) {
                    foreach ($menu['menu'] as $key) {
                        echo '<li><a class="navbar-item" href="' . base_url() . $key['subdomainfirst'] . '/' . $key['subdomainsecond'] . '">' . $key['description'] . '</a></li>';
                    }
                } else {
                    foreach ($menu['menu'] as $key) {
                        if (strcasecmp("loginreport", $key['subdomainsecond']) == 0) {
                            echo '<li><a class="navbar-item" href="' . base_url() . $key['subdomainfirst'] . '/' . $key['subdomainsecond'] . '">' . $key['description'] . '</a></li>';
                        }
                        if (strcasecmp("timesheetreport", $key['subdomainsecond']) == 0) {
                            echo '<li><a class="navbar-item" href="' . base_url() . $key['subdomainfirst'] . '/' . $key['subdomainsecond'] . '">' . $key['description'] . '</a></li>';
                        }
                    }
                }
            }
//                $roleid = $this->session->userdata('roleid');
//                if ($roleid == 0 || $chekpm == 1) {
//                    echo '<li><a class="navbar-item" href="' . base_url() . 'home/projectMaster">Setup Project Master</a></li>';
//                }
//                if ($roleid == 0 || $roleid == 1) {
////                echo '<li><a  class="navbar-item" href="' . base_url() . 'home/projectMember">Setup Project Member</a></li>';
//                    echo '<li><a class="navbar-item" href="' . base_url() . 'home/timesheet">Timesheet</a></li>';
//                }
//                if ($roleid == 0 || $roleid == 2 || $chekpm == 1) {
//                    echo '<li><a class="navbar-item" href="' . base_url() . 'home/report">Report</a></li>';
//                }
//            }
            ?>
            <!--<li><a href="#">Link</a></li>-->
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <?php
            if (isset($datauser)) {
                if (!isset($datauser['personname'])) {
                    echo '<li><p class = "navbar-text">Welcome, no-name</p></li>' . "\n";
                } else {
                    echo '<li><p class = "navbar-text">Welcome, ' . $datauser['personname'] . '</p></li>' . "\n";
                }
            }
            ?>
            <li><a class="navbar-item" href="<?php echo base_url() ?>home/signout" role="button">Sign Out</a></li>
        </ul>
    </div>
</div>
