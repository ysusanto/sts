<script>
    $(document).ready(function () {
        $('#divdataprojectdetail, #divdatadetailprojectplatform').hide();
        viewreport();
    })
    function viewreport() {
        
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
            "sAjaxSource": '<?php echo base_url(); ?>report/getprojectreport',
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
    
    function checkdetail(projectid){
        $('#mymodal').modal('show');
        
        viewdetailreport(projectid);
    }
    
    function viewdetailreport(projectid){
        var table = $('#tabelprojectdetail').dataTable({
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
            "sAjaxSource": '<?php echo base_url(); ?>report/getdetailproject',

        });
    }

    function showDetailRepProject(projectid){
        $('#divdataprojectlist').hide();
        $('#divdataprojectdetail').show();
        $('#divdatadetailprojectplatform').hide();
        linkbackproj('detailproj', projectid);
        $('#tabelprojectdetailProject').DataTable( {
            "scrollY": 200,
            "scrollX": true,
            "sPaginationType": "full_numbers",
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"}
              ],
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>report/getprojectdetail/'+projectid,
        });

    }

    function viewdetailProj(project_id){

    }
    function linkbackproj(jenis, id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>report/linkback',
            data: 'type=' + jenis+ '&id=' + id,
            success: function (msg) {
                if (jenis == 'detailproj') {
                    $('#linkbackdetailproj').html(msg);
                }
                else if(jenis == 'detailproj2'){
                    $('#linkbackdetailproj2').html(msg);
                }
            }
        });
    }
    function showdetail_proj(id_platform, id_project){
        $('#divdataprojectdetail').hide();
        $('#divdatadetailprojectplatform').show();
        linkbackproj('detailproj2', id_project)
         $('#tabelprojectplatform').DataTable( {
            "sPaginationType": "full_numbers",
            //"scrollY": 200,
            "scrollX": true,
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": false,
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"}
              ],
            "bLengthChange": false,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>report/getdetailprojectplatform/'+id_platform+'/'+id_project,
        });
        //$('#modaldetailprojectplatform').modal('show');
    }

    function detailmainhours(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>report/detailmainhours/' + id,
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
    function changeStatusClosed(itemid, id_platform, id_project) {
        <?php
            $role = $this->session->userdata('roleid');
            /*if ($role <= 20) {*/
            if ($role <= 8) {
        ?>
        $.ajax({
            type: 'post',
            url:  '<?php echo base_url(); ?>report/changeitemstatus/',
            data: '&itemid=' + itemid,
            success: function (msg) {
                alert(msg);
                showdetail_proj(id_platform, id_project);
            }
        });
        <?php } else { ?>
            alert("Sorry You don't have Auth, Please call administrator");
        <?php } ?>
    }
</script>

<div class="container">
    <div class="row">
        <div class="col-lg-12" id="divdataprojectlist">
            <h4>Report Project</h4>
            <div id="divproject">

                <table id="tabelproject" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Project Manager</th>
                    <th>Total Plan</th>
                    <th>Total Actual</th>
                    <!--<th>Project Manager</th>-->

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12" id="divdataprojectdetail">
            <h4>Detail Project</h4>
            <div id="divproject">
            <br>
                <div id="linkbackdetailproj"></div>
                <table id="tabelprojectdetailProject" class="display nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="font-size: 12px !important; ">No.</th>
                            <th style="font-size: 12px !important;">Project Name</th>     
                            <th style="font-size: 12px !important;">General Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">Web Service Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">Web Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">Android Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">IOS Development Plan & Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12" id="divdatadetailprojectplatform">
            <h4>Detail Project</h4>
            <div id="divproject">
            <br>
                <div id="linkbackdetailproj2"></div>
                <table id="tabelprojectplatform" class="display nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="font-size: 12px !important; ">No.</th>
                            <th style="font-size: 12px !important;">Name Feauture</th>     
                            <th style="font-size: 12px !important;">Name Group</th>
                            <th style="font-size: 12px !important;">Name Item</th>
                            <th style="font-size: 12px !important;">Plan</th>
                            <th style="font-size: 12px !important;">Actual</th>
                            <th style="font-size: 12px !important;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal fade" tabindex="-1" role="dialog" id="mymodal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Project Detal</h4>
      </div>
      <div class="modal-body">
        <table id="tabelprojectdetail" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Project Name</th>     
                    <th>General Development Plan</th>
                    <th>General Development Actual</th>
                    <th>IOS Development Plan</th>
                    <th>IOS Development Actual</th>
                    <th>Android Development Plan</th>
                    <th>Android Development Actual</th>
                    <th>Web Development Plan</th>
                    <th>Web Development Actual</th>
                    <th>Web Service Development Plan</th>
                    <th>Web service Development Actual</th>
                    <th>Project Manager</th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> -->


<!-- <div class="modal fade" tabindex="-1" role="dialog" id="mymodalDetailPro">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Project Detail</h4>
      </div>
      <div class="modal-body">
                 <table id="tabelprojectdetailProject2" class="display nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="font-size: 12px !important; ">No.</th>
                            <th style="font-size: 12px !important;">Project Name</th>     
                            <th style="font-size: 12px !important;">General Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">Web Service Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">Web Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">Android Development Plan & Actual</th>
                            <th style="font-size: 12px !important;">IOS Development Plan & Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> -->

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


<div class="modal fade" tabindex="-1" role="dialog" id="modaldetailprojectplatform">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Project Detail</h4>
      </div>
      <div class="modal-body">
                 <table id="tabelprojectplatform" class="display nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="font-size: 12px !important; ">No.</th>
                            <th style="font-size: 12px !important;">Name Feauture</th>     
                            <th style="font-size: 12px !important;">Name Group</th>
                            <th style="font-size: 12px !important;">Name Item</th>
                            <th style="font-size: 12px !important;">Plan</th>
                            <th style="font-size: 12px !important;">Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
