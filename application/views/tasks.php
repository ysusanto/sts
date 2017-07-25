<script type="text/javascript" src="chrome-extension://ghbmnnjooekpmoecnnnilnnbdlolhkhi/page_embed_script.js"></script>
<div class="container">
    <div class="list-group">
        <a href="<?php echo site_url() ?>tasks/add" class="btn btn-default" role="button">Add Task</a>
        <?php
        if (isset($tasks)) {
            foreach ($tasks as $project) {
                echo '<a href="' . site_url(uri_string()) . '/project/' . $project->project_id . '" class="list-group-item">
                    <h4 class="list-group-item-heading">' . $project->name . '';

                if ($project->alias) {
                    echo ' <span class="list-group-item-subtitle">(' . $project->alias . ')</span>';
                }

                $feature_count = isset($project->feature_count) ? $project->feature_count : 0;
                $feature_count > 1 ? $feature_count = $feature_count . ' Features' : $feature_count = $feature_count . ' Feature';
                echo '</h4>
                    <p class="list-group-item-text">' . $feature_count . '</p>
                    </a>';
            }
        }
        ?>
    </div>
</div>
<!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->
</body>
</html>