<script type="text/javascript">
    $(function () {
        var project_id = "<?php if (isset($project)) echo $project->project_id ?>"
        if (project_id != "") {
//            $('#time-limit').html(html);
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url(); ?>projects/get_platforms',
                data: 'project_id='project_id,
                success: function (data, textStatus, jqXHR) {
                    
                }
            });
        }
    });
</script>
<div class="container">
    <div class="project-name">
        <?php
        if (isset($project_name)) {
            echo '<div class="project-text-name">' . $project_name . '</div>';
        }
        if (isset($project_alias)) {
            echo '<div class="project-text-alias">' . $project_alias . '</div>';
        }
        ?>
    </div>

    <div class="panel-status">
        <div id="time-limit" class="status-title">Estimated Time Limit</div>
        <div class="status-content">0 <span class="status-misc">Hour(s)</span></div>
    </div>

    <div class="panel-status">
        <div class="status-title">Actual Time Limit</div>
        <div class="status-content">0 <span class="status-misc">Hour(s)</span></div>
    </div>

    <div class="panel-status">
        <div class="status-title">Unassigned Task(s)</div>
        <div class="status-content">0 <span class="status-misc">Feature(s)</span></div>
    </div>

    <div class="dropdown">
        <button class="btn button-dropdown" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">
            Platform
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
        </ul>
    </div>

    <div class="panel-table">
        <!-- Table -->
        <table class="table table-bordered table-hover">
            <?php
            if (isset($features)) {
//                echo $features;
            }
            ?>
        </table>
    </div>
</div>
<!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->
</body>
</html>