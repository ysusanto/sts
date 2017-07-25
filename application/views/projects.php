<script>
    $(document).ready(function () {
        loaddashboard();
    });

    function loaddashboard() {
        $.ajax({
            method: "POST",
            url: "<?php echo base_url('projects/getdashboard') ?>",
            data: {},
            dataType: 'text'
        }).done(function (html) {
            $("#dashboardcontainer").html(html);
//            alert(html);
        });
    }

</script>

<div class="container">
    <!--<a href="<?php echo site_url() ?>projects/import" class="btn btn-default" role="button">Import a project</a>-->
    <div class="col-md-2"></div>
    <div class="col-md-8" id="dashboardcontainer">




        <?php
//        if (isset($projects)) {
//            foreach ($projects as $project) {
//                echo '<a href="' . site_url(uri_string()) . '/project/' . $project->project_id . '" class="list-group-item">
//                    <h4 class="list-group-item-heading">' . $project->name . '';
//                
//                if ($project->alias) {
//                    echo ' <span class="list-group-item-subtitle">(' . $project->alias . ')</span>';
//                }
//                
//                $feature_count = isset($project->feature_count) ? $project->feature_count : 0;
//                $feature_count > 1 ? $feature_count = $feature_count . ' Features' : $feature_count = $feature_count . ' Feature';
//                echo '</h4>
//                    <p class="list-group-item-text">' . $feature_count . '</p>
//                    </a>';
//            }
//        }
        ?>
    </div>
    <div class="col-md-2"></div>
</div>
<!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->
</body>
</html>