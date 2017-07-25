<script>
    $(document).ready(function () {
        $(".timepicker").timepicker({
            showInputs: false,
            use24hours: true,
            showSeconds: true,
            showMeridian: false,
            minuteStep: 1
        });

        datetoday();
        viewproject();
    })
    function viewproject() {

        var table = $('#tabelproject').dataTable({
            "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>timesheet/viewproject',
//            "fnServerParams": function (aoData) {
//                aoData.push({'name': 'tug_id', 'value': tug},
//                {'name': 'datefrom', 'value': datefrom},
//                {'name': 'dateto', 'value': dateto},
//                {'name': 'owner_id', 'value': owner},
//                {'name': 'searchkey', 'value': search}
//                );
//            }

        });
    }

//    function viewtimesheet(id) {
//        var table = $('#tabeltimesheet').dataTable({
//            "sPaginationType": "full_numbers",
////            "bJQueryUI": true,
//            "iDisplayLength": 30,
//            "bDestroy": true,
//            "bFilter": true,
//            "bLengthChange": false,
//            "aaSorting": [],
//            "bAutoWidth": true,
//            "bSortable": false,
//            "bSortClasses": true,
//            "sAjaxSource": '<?php echo base_url(); ?>projects/tabelfeature',
//            "fnServerParams": function (aoData) {
//                aoData.push({'name': 'project_id', 'value': id},
//                {'name': 'proplat_id', 'value': proplat}
//
//                );
//            }});
//    }
    function detailproject(id) {
        location.replace('<?php echo base_url(); ?>timesheet/detailproject/' + id);

    }

    function addmainhours(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>timesheet/itemname/' + id,
//            data: 'item_id=' + id + '&hour=' + hour,
            success: function (msg) {
//                var data = JSON.parse(msg);
                $('#itemnameinput').html(msg);
                $('#item_id').val(id);
                $('#timesheetdetailModal').modal('show');
//                $('#addModal').modal('show');
            }
        });


    }

    function addmainhours(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>timesheet/itemname/' + id,
//            data: 'item_id=' + id + '&hour=' + hour,
            success: function (msg) {
//                var data = JSON.parse(msg);
                $('#itemnameinput').html(msg);
                $('#item_id').val(id);
                $('#timesheetdetailModal').modal('show');
//                $('#addModal').modal('show');
            }
        });


    }
    function detailmainhours(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>timesheet/detailmainhours/' + id,
//            data: 'item_id=' + id + '&hour=' + hour,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#itemnametable').text(data.name);
                $('#datamainhours').html(data.table);
                $('#mainhoursmodal').modal('show');
//                $('#addModal').modal('show');
            }
        });
    }

    function datetoday() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }

        if (mm < 10) {
            mm = '0' + mm
        }

        today = yyyy + '-' + mm + '-' + dd;
        $('#date').val(today);
        $('#datetoday').val(today);
    }
    function chekdate() {
        var today = $('#datetoday').val();
        var date = $('#date').val();

        var stDate = new Date(date);
        var enDate = new Date(today);
        var compDate = enDate - stDate;

        if (compDate >= 0)
            return true;
        else
        {
            alert("Please Enter the correct date ");
            return false;
        }
    }

</script>
<div class="container">
    <div class="row">
        <div class="col-lg-12" id="divdataproject">
            <h4>Project</h4>
            <div id="divtug">

                <table id="tabelproject" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Feature</th>
                    <th>Group</th>
                    <th>Item</th>
                    <th>Hours</th>    

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" id="timesheetdetailModal" tabindex="-1" role="dialog" aria-labelledby="timesheetdetailModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Add Hours <span id="itemnameinput"></span></h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>timesheet/savetimesheet' id='formsavetimesheet' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Date</label></div>
                        <div class="col-md-8"><input type="hidden" id="item_id" name='item_id' value="">
                            <input type="hidden" id="datetoday" name='datetoday' value="">
                            <input type="date" class="form-control" id="date" name='date' placeholder="Date" required></div>

                    </div>




                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Range Time</label></div>
                        <div class="col-md-8">
                            <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                                <div class="col-lg-4" style="width: 40%">
                                    <div class="bootstrap-timepicker"> 
                                        <div class="input-group">
                                            <input type="text" class="form-control timepicker" id="start_time" name='start_time' required>
                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                              <!--<i class="glyphicon glyphicon-time"></i>-->
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-4" style="width: 5%">
                                    <p>s/d</p>
                                </div>
                                <div class="col-lg-4" style="width: 40%">
                                    <div class="bootstrap-timepicker">
                                        <div class="input-group">
                                            <input type="text" class="form-control timepicker" id='end_time' name='end_time' required>
                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--<input type="text" class="form-control" id="client" name='client'placeholder="Client Name" ></div>-->
                            </div>
                        </div>
                    </div>
                    <!--                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                                            <div class="col-md-4"><label for="exampleInputName2">Main Hours</label></div>
                                            <div class="col-md-8">
                                                <input type="number" class="form-control" name='hours' id='hours' placeholder="Hours"  required>
                                            </div>
                    
                                        </div>-->

<!--                    <input type="text" id="to" class="date hasDatepicker" name="dateTo" placeholder="To" title="input">-->




            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit"  >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>
<div class="modal fade bs-example-modal-sm" id="mainhoursmodal" tabindex="-1" role="dialog" aria-labelledby="mainhoursmodal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Detail Main Hours <span id="itemnametable"></span></h4>
            </div>

            <div class="modal-body" id='datamainhours'>

            </div>
            <div class = "modal-footer">
                <!--                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                                <button class="btn btn-primary"type = "submit"  >Save</button>-->
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>
<script>
    // Attach a submit handler to the form

    var options = {
        beforeSubmit: showRequest,
        success: showResponse,
        dataType: 'json'
    };
    $('#formsavetimesheet').ajaxForm(options);

    function showRequest(formData, jqForm, options) {




        return true;
    }
    function closemodal() {
        $('#addModal').modal('hide');
        $('#formsavetimesheet').resetForm();

    }
    function showResponse(data) {
//        alert(data);
//                        if (data.status == 1) {

//        var msg = JSON.parse(data);
//                        document.write(data.type);

//alert(data.status);
        if (data.status == 1 || data.status == '1') {
            $('#formsavetimesheet').resetForm();
//        $('#imagepreview').attr('src', '');
//        $('#addModal').modal('hide');
            $('#timesheetdetailModal').modal('hide');
            alert(data.msg);
            timesheet(<?php echo $projectid; ?>);

        } else {
            alert(data.msg);
        }


//                            location.reload();
//                        } else if (data.status == 2) {
//                            alert(data);
//                            window.location.replace("<?php echo base_url(''); ?>");
//                        } else {
//                            alert(data.message);
//                            $('#submitAdd').prop('disabled', false);
//                        }
    }
</script>