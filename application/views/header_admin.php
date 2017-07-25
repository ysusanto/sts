<div class="navbar navbar-static-top " >
    <div class="container">
        <div class="navbar-header" >
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo site_url() ?>">Time Sheet</a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a class="navbar-item" href="<?php echo base_url() ?>home/signout" role="button">Sign Out
                <?php
                if (isset($datauser)) {
                    echo ' as ' . $datauser->username."(Sign Out)";
                }
                ?>
                    </a></li>
            </ul>
        </div>
    </div>
</div>
