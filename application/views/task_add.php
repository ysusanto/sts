<script type="text/javascript">

    var selectedFeature = 0;
    var selectedStartDate = '';

    function selectFeature(feature_id, type) {
        var project_id = $('#select-project').val();

        feature_id = typeof feature_id !== 'undefined' ? feature_id : '';

        $.ajax({
            type: 'post',
            url: '<?php echo site_url(); ?>tasks/get_project_features',
            data: 'project_id=' + project_id + '&parent_id=' + feature_id,
            success: function (html) {
                if (type == 0) {
                    $('#select-feature-parent').html(html);
                } else if (type == 1) {
                    $('#select-feature-group').html(html);
                } else if (type == 2) {
                    $('#select-feature-function').html(html);
                }

                if (html == '') {
                    selectedFeature = feature_id;
                } else {
                    selectedFeature = 0;
                }

                document.getElementById('label-feature').style.display = 'initial';
                document.getElementById('select-feature-parent').style.display = 'initial';
                document.getElementById('select-feature-group').style.display = 'initial';
                document.getElementById('select-feature-function').style.display = 'initial';
                if (type == 0) {
                    if (html == '') {
                        document.getElementById('select-feature-parent').style.display = 'none';
                    }
                    document.getElementById('select-feature-group').style.display = 'none';
                    document.getElementById('select-feature-function').style.display = 'none';
                } else if (type == 1) {
                    if (html == '') {
                        document.getElementById('select-feature-group').style.display = 'none';
                    }
                    document.getElementById('select-feature-function').style.display = 'none';
                } else if (type == 2 && html == '') {
                    document.getElementById('select-feature-function').style.display = 'none';
                }
            }
        });
    }

    $(function () {
        $("#datepicker").datepicker({
            onSelect: function (date) {
                $('#start-date').val(date);
                selectedStartDate = date;
            }
        });
    });

    function submitTask() {
        var project_id = $('#select-project').val();
        var time_required = $('#input-time').val();

        console.log('selected project: ' + project_id);
        console.log('selected feature: ' + selectedFeature);
        console.log('selected time required: ' + time_required);
        console.log('selected start date: ' + selectedStartDate);

        var alert = document.getElementById('alert');

        var errorMsg;
        if (project_id == '') {
            errorMsg = 'Please select a project';
        } else if (selectedFeature == 0) {
            errorMsg = 'Please select a feature';
        } else if (selectedStartDate == '') {
            errorMsg = 'Please select a start date'
        }

        if (errorMsg != undefined) {
            alert.style.display = 'block';
            alert.innerHTML = errorMsg;
            $(window).scrollTop(0);

        } else {
            $.ajax({
                type: 'post',
                url: '<?php echo site_url(); ?>tasks/get_project_features',
                data: 'project_id=' + project_id + '&parent_id=' + feature_id,
                success: function (html) {
                }});
        }
    }
</script>
<div class="container">
    <div class="box">
        <h2 class="form-header text-center">Add Task</h2>
        <form class="form-task" method="POST" action="javascript:submitTask()">
            <div style="display: none" id="alert" class="alert alert-danger" role="alert"></div>
            <label>Project</label>
            <select id="select-project" class="form-control input-task input-lg" onchange="selectFeature(undefined, 0)">
                <option value="">Please Select</option>
                <?php
                if (isset($projects)) {
                    foreach ($projects as $project) {
                        echo '<option value="' . $project->project_id . '">';
                        if ($project->alias) {
                            echo $project->alias;
                        } else {
                            echo $project->name;
                        }
                        echo '</option>';
                    }
                }
                ?>
            </select>
            <label style="display: none" id="label-feature">Feature</label>
            <select style="display: none" id="select-feature-parent" class="form-control input-lg input-task" onchange="selectFeature(this.value, 1)">
            </select>
            <select style="display: none" id="select-feature-group" class="form-control input-lg input-task" onchange="selectFeature(this.value, 2)">
            </select>
            <select style="display: none" id="select-feature-function" class="form-control input-task input-lg input-task" onchange="selectFeature(this.value, 3)">
            </select>
            <label>Time Required (man-hour)</label>
            <input id="input-time" type="number" class="form-control input-task" name="time_required" placeholder="e.g. 0.5, 1, 2, ..." min="0.5" max="1000" step="0.5" required>
            <label>Start Date</label>
            <input readonly type="text" class="form-control" id="start-date">
            <div class="input-task form-control" id="datepicker"></div>
            <input type="submit" value="Submit" class="btn btn-lg btn-primary btn-block">
        </form>
    </div>
</div> <!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->
</body>
</html>