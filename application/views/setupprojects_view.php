<style>
    #addModal .modal-dialog  {width:50%;}
</style>
<script>
    $(document).ready(function () {
        $('#divdatafeature,#divdatagroup,#divdataitem').hide();
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
            "sAjaxSource": '<?php echo base_url(); ?>projects/projecttabel',
//            "fnServerParams": function (aoData) {
//                aoData.push({'name': 'tug_id', 'value': tug},
//                {'name': 'datefrom', 'value': datefrom},
//                {'name': 'dateto', 'value': dateto},
//                {'name': 'owner_id', 'value': owner},
//                {'name': 'searchkey', 'value': search}
//                );
//            }
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add Project',
                    action: function (e, dt, node, config) {
                    <?php
                    $role = $this->session->userdata('roleid');
                    /*if ($role <= 20) {*/
                    if ($role <= 8) {
                        ?>
                                                $('#divboxeditproject').hide();
                                                $('.divboxaddproject').show();
                                                $('#addModal').modal('show');
                    <?php } else { ?>
                                                alert("Sorry You don't have Auth, Please call administrator");
                    <?php } ?>
                    }
                }
            ]
        });
    }
    function editproject(project_id){
        <?php
            $role = $this->session->userdata('roleid');
            /*if ($role <= 20) {*/
            if ($role <= 8) {
        ?>
         $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/geteditproject',
            data: 'project_id=' + project_id,
            success: function (msg) {
                $('#diveditprojectmember').html('');
                var data = JSON.parse(msg);
                $('#project_id').val(data.item.project_id);
                $('#nama').val(data.item.name_project);
                $('#client').val(data.item.client);
                $('#project_member_id').val(data.item.project_member_id)
                $('#diveditprojectmember').append(data.datahtml);
                var platformedit = $('#platformedit').dataTable({
                      "bPaginate": false,
                      "bLengthChange": false,
                      "bFilter": false,
                      "bSort": false,
                      "bInfo": false,
                      "bAutoWidth": false,
                      "bDestroy": true
                });
                platformedit.fnClearTable();
                platformedit.fnAddData(data.dataplatform);

                $('#divboxeditproject').show();
                $('.divboxaddproject').hide();
                $('#addModal').modal('show');
            }
        });
        <?php } else { ?>
            alert("Sorry You don't have Auth, Please call administrator");
        <?php } ?>

    }
    function getaddMemberModal(groupid) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/getaddMemberModal',
            data: 'groupid=' + groupid,
            success: function (msg) {
                $('#addMemberModal').html(msg);
            }
        });
    }
    function linkback(jenis, id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/linkback',
            data: 'type=' + jenis + '&id=' + id,
            success: function (msg) {
//                var data = JSON.parse(msg);

                if (jenis == 'item') {
                    $('#linkbackitem').html(msg);
                } else if (jenis == 'group') {
                    $('#linkbackgroup').html(msg);
                } else {
                    $('#linkbackfeature').html(msg);
                }

//                $('#addModal').modal('show');
            }
        });
    }
    function detailproject(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/detailproject/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#proplat').html(data.platform);
                $('#projectname').text(data.name);
                $('#divdatafeature').show();
                $('#divdataproject,#divdatagroup,#divdataitem').hide();
                linkback('feature', id)
                viewfeature(id);
//                $('#addModal').modal('show');
            }
        });
    }
    function viewfeature(id) {
        var proplat = $('#proplat').val();
        $('#proplatid').val(proplat);
        
        var table = $('#tabelfeature').dataTable({
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
            "sAjaxSource": '<?php echo base_url(); ?>projects/tabelfeature',
            "fnServerParams": function (aoData) {
                aoData.push({'name': 'project_id', 'value': id},
                        {'name': 'proplat_id', 'value': proplat}

                );
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add Feature',
                    action: function (e, dt, node, config) {
                        $('#idfeature').val('');
                        $('.namafeature').val('');
                        $('#addModalfeature').modal('show');
                    }
                }
            ]
        });
    }

    function detailfeature(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/detailfeature/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#featureid').val(id);
                $('#featurename').text(data.name);
                $('#divdatafeature').hide();
                $('#divdataproject,#divdataitem').hide();
                $('#divdatagroup').show();
                linkback('group', id)
                viewgroup(id);
//                $('#addModal').modal('show');
            }
        });
    }
    function viewgroup(id) {

        var table = $('#tabelgroup').dataTable({
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
            "sAjaxSource": '<?php echo base_url(); ?>projects/tabelgroup',
            "fnServerParams": function (aoData) {
                aoData.push({'name': 'feature_id', 'value': id}
//                {'name': 'proplat_id', 'value': proplat}

                );
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add group',
                    action: function (e, dt, node, config) {
                        $('#idgroup').val('');
                        $('.namagroup').val('');
                        $('#addModalgroup').modal('show');
                    }
                }
            ]
        });
    }
    function detailgroup(id) {
        getaddMemberModal(id);
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/detailgroup/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#group_id_item').val(id);
                $('#groupname').text(data.name);
                $('#divdatafeature').hide();
                $('#divdataproject').hide();
                $('#divdatagroup').hide();
                $('#divdataitem').show();
                linkback('item', id)
                viewitem(id);
//                $('#addModal').modal('show');
            }
        });
    }
    function viewitem(id) {

        var table = $('#tabelitem').dataTable({
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
            "sAjaxSource": '<?php echo base_url(); ?>projects/tabelitem',
            "fnServerParams": function (aoData) {
                aoData.push({'name': 'group_id', 'value': id}
//                {'name': 'proplat_id', 'value': proplat}

                );
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add Item',
                    action: function (e, dt, node, config) {
                         $('#iditem').val('');
                         $('.namaitem').val('');
                         $('.hourval').val('');
                        $('#addModalitem').modal('show');
                    }
                }
            ]
        });
    }

    function viewAddMemberModal(group_id, itemid) {
        $('#viewAddMemberModalHidden').val(group_id);
        $('#viewAddMemberModalItemidHidden').val(itemid);
        $('#addMemberModal').modal('show');
    }

    function viewAddMemberModal2(group_id, itemid){
        
        $('#viewAddMemberModalHidden2').val(group_id);
        $('#viewAddMemberModalItemidHidden2').val(itemid);
        $('#addMemberModal').modal('show');

        /*$('#id_name_users_group_data').val(id_users_group);
        $('#name_users_group_data').val(name_users_group);
        $('#myModalExchangeGroup').modal('show');*/

        $.getJSON('<?php echo base_url();?>projects/getmemberpersonal/'+group_id+'/'+itemid,function(data){
                var menu2 = $('#menu2').dataTable({
                      "bPaginate": true,
                      "bLengthChange": false,
                      "bFilter": false,
                      "bSort": false,
                      "bInfo": false,
                      "bAutoWidth": false,
                      "bDestroy": true
                });
                menu2.fnClearTable();
                menu2.fnAddData(data);
        });

    }

    function changeStatusClosed(groupid, itemid) {
        <?php
            $role = $this->session->userdata('roleid');
            /*if ($role <= 20) {*/
            if ($role <= 8) {
        ?>
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/changeitemstatus/',
            data: 'groupid=' + groupid + '&itemid=' + itemid,
            success: function (msg) {
                alert(msg);
                viewitem(groupid);
            }
        });
        <?php } else { ?>
            alert("Sorry You don't have Auth, Please call administrator");
        <?php } ?>
    }

    function changeStatusClosedProject(project_id, proplat_id){
            <?php
                 $role = $this->session->userdata('roleid');
                 if ($role <= 8) {
            ?>
                 $.ajax({
                    type: 'post',
                    url: '<?php echo base_url(); ?>projects/changeitemstatusPerProject/',
                    data: 'project_id=' + project_id + '&proplat_id=' + proplat_id,
                    success: function (msg) {
                        alert(msg);
                        viewproject();
                    }
                });

            <?php } else { ?>
                 alert("Sorry You don't have Auth, Please call administrator");
            <?php } ?>
    }

    function remove_assignment(user_id, item_id, group_id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/remove_assignment/',
            data: 'user_id=' + user_id + '&item_id=' + item_id,
            success: function (msg) {
                alert(msg);
                viewitem(group_id);
            }
        });
    }

    function deleteproject(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/deleteproject/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);

                if (data.status == 1) {
                    alert(data.msg);
                    viewproject();
                } else {
                    alert(data.msg);
                }
//                $('#addModal').modal('show');
            }
        });
    }

    //This variable is to increment cloned div below
    var divCloneThisIndex = 1;
    function addmore() {
        var divClonedVar = $('#divCloneThis' + divCloneThisIndex).clone();
        var divMemberLabelVar = divClonedVar.find('#divMemberLabel' + ((divCloneThisIndex == 1) ? '' : divCloneThisIndex));
        divMemberLabelVar.css('visibility', 'hidden');
        divMemberLabelVar.attr('id', 'divMemberLabel' + (divCloneThisIndex + 1));
        divClonedVar.appendTo('#divContainerMember').attr('id', 'divCloneThis' + (divCloneThisIndex + 1));
        divCloneThisIndex++;
    }
    
    function addmores(type){
        if (type == 'add') {
            var count = 1;
            $.post('<?php echo base_url();?>projects/addnewline/'+count,function(newline){
                $('.divmember').append(newline);
                count++;
            }); 
        }
        else{
             var count = $('.valmemberedit').val();
             $.post('<?php echo base_url();?>projects/addnewlineedit/'+count,function(newline){
                $('.divmemberedit').append(newline);
                count++;
            });
        }
        
    }
    function remove(num){
        $('#divMemberdiv'+num).remove();
    }
    function sendmail(id){
        var btnid = ".emailbtn-"+id;
        $(btnid).prop("disabled",true);
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/sendemail/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);

                if (data.status == 1) {
                    alert(data.msg);
                    $(btnid).prop("disabled",false);
                } else {
                    alert(data.msg);
                }
//                $('#addModal').modal('show');
            }
        });
    }
    /** putra **/
    function editfeature(id){
        
        $.getJSON('<?php echo base_url();?>projects/getprojectFeatures/'+id,function(data){
         $('#idfeature').val(data.feature_id);
         $('.namafeature').val(data.name);
         $('#addModalfeature').modal('show');
    });
    }
    function deletefeatured(feature_id,proplat_id){
        var con = confirm('Are You Sure?');
        if(con==true){
            $.post('<?php echo base_url();?>projects/deletefeaturedProject/'+feature_id,function(){
                viewfeature(proplat_id);
            });
        }
    }
    function editgroup(id){
        
        $.getJSON('<?php echo base_url();?>projects/getprojectGroup/'+id,function(data){
         $('#idgroup').val(data.group_id);
         $('.namagroup').val(data.name);
         $('#addModalgroup').modal('show');
    });
    }
    function deletegroup(group_id,feature_id){
        var con = confirm('Are You Sure?');
        if(con==true){
            $.post('<?php echo base_url();?>projects/deletegroupProject/'+group_id,function(){
                viewgroup(feature_id);
            });
        }
    }
    function edititem(id){
        $.getJSON('<?php echo base_url();?>projects/getprojectItem/'+id,function(data){
         $('#iditem').val(data.item_id);
         $('.namaitem').val(data.name);
         $('.hourval').val(data.hour);
         $('#addModalitem').modal('show');
    });
    }
    function deleteitem(item_id,group_id){
        var con = confirm('Are You Sure?');
        if(con==true){
            $.post('<?php echo base_url();?>projects/deleteitemProject/'+item_id,function(){
                viewitem(group_id);
            });
        }
    }
    function is_delete_platform(no){
        var checkbox = $('.checkbox_platform'+no);
        //$('.is_delete_platform'+no).toggle($('.checkbox_platform'+no).checked);
        if (checkbox.is(':checked')) {
           $('.is_delete_platform'+no).val('0') 
        }
        else{
            $('.is_delete_platform'+no).val('1')
        }
    }
</script>

<style>
    .col-md-8 {
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <!--<a href="<?php echo site_url() ?>projects/import" class="btn btn-default" role="button">Import a project</a>-->
    <div class="list-group">
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

    <div class="row">
        <div class="col-lg-12" id="divdataproject">
            <h4>Project</h4>
            <div id="divtug">

                <table id="tabelproject" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Platform</th>
                    <th>Status</th>
                    <th></th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12" id="divdatafeature">
            <div id="linkbackfeature"></div>
            <h4>Feature</h4>
            <div class="row">
                <div class="col-md-4">
                    <dl class="dl-horizontal">
                        <dt >Project</dt>
                        <dd id="projectname">...</dd>
                        <dt >Platform</dt>
                        <dd ><select name="proplat" id="proplat" class="form-control" onchange="viewfeature(id)">


                            </select></dd>
                    </dl>
                </div>
                <div class="col-md-8"></div>
            </div>
            <div id="divtug" style="width: 100%">


                <table id="tabelfeature" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Feature</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th></th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12" id="divdatagroup">
            <div id="linkbackgroup"></div>
            <h4>Group</h4>
            <div class="row">
                <div class="col-md-4">
                    <dl class="dl-horizontal">
                        <dt >Feature</dt>
                        <dd id="featurename">...</dd>

                    </dl>
                </div>
                <div class="col-md-8"></div>
            </div>
            <div id="divtug" style="width: 100%">


                <table id="tabelgroup" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Group</th>
                    <th>Created</th>
                    <th>Modified</th>

                    <th></th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12" id="divdataitem">
            <div id="linkbackitem"></div>
            <h4>Item</h4>
            <div class="row">
                <div class="col-md-4">
                    <dl class="dl-horizontal">
                        <dt >Group</dt>
                        <dd id="groupname">...</dd>

                    </dl>
                </div>
                <div class="col-md-8"></div>
            </div>
            <div id="divtug" style="width: 100%">
                <table id="tabelitem" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Item</th>
                    <th>Hour</th>
                    <th>Member</th>
                    <th></th>
                    <th>Status</th>
                    <th></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->

<div class="modal fade bs-example-modal-sm" id="addModal" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Project</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/saveproject' id='formsaveproject' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Project Name</label></div>
                        <div class="col-md-8">
                            <input type="hidden" id="project_id" name='project_id' value="">   
                            <input type="hidden" id="project_member_id" name='project_member_id' value="">              
                            <input type="text" class="form-control" id="nama" name='nama'placeholder="Project Name"  required></div>

                    </div>

                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Client Name</label></div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="client" name='client'placeholder="Client Name" ></div>

                    </div>
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;display: none;">
                        <div class="col-md-4"><label for="exampleInputName2">Project Manager</label></div>
                        <div class="col-md-8">
                            <select name="pm_id" id="pm_id" class="form-control" >
                                <?php
                                if (isset($manager)) {
                                    foreach ($manager as $value) {
                                        ?>
                                        <option value="<?php echo $value['userid']; ?>"><?php echo $value['salutation'] . " " . $value["firstname"] . " " . $value['lastname']; ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>

                    </div>
                    <div class="divboxaddproject">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        
                        <div id="divMemberLabel" class="col-md-4"><label for="exampleInputName2">Member</label></div>
                            <div class="col-md-8">
                                <select name="member[]" id="member[]" class="form-control" style="width:270px !important;">
                                    <?php
                                    if (isset($manager)) {
                                        foreach ($manager as $value) {
                                            ?>
                                            <option value="<?php echo $value['userid']; ?>"><?php echo $value['salutation'] . " " . $value["firstname"] . " " . $value['lastname']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>

                                </select>
                                <br><br>
                                <select name="peran[]" id="peran[]" class="form-control" style="width:270px !important;">
                                    <?php
                                    if (isset($peran)) {
                                        foreach ($peran as $value) {
                                            ?>
                                            <option value="<?php echo $value['role_id']; ?>"><?php echo $value['name']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="divmember"></div>
                                <br>
                                <button class="btn btn-primary"type = "button" onclick = "addmores('add')">Add More Member</button>
                            </div>
                            
                            
                    </div>
                    </div>
                    <div id="divboxeditproject">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        
                        <div id="divMemberLabel" class="col-md-4"><label for="exampleInputName2">Member</label></div>

                            <div class="col-md-8">
                                <div id="diveditprojectmember"></div>
                                <div class="divmemberedit"></div>
                                <br>
                                <button class="btn btn-primary"type = "button" onclick = "addmores('edit')">Add More Member</button>
                            </div>
                            
                            
                    </div>
                    <div id="checkboxplatform" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Platform</label></div>
                        <div class="col-md-8">
                            <table id="platformedit" class="table table-hover" style="font-size:9pt !important">
                              <thead>
                                <tr>
                                    <td></td>
                                </tr>
                              </thead>
                                <tbody id="tRow">

                                </tbody>
                              </table>
                        </div>
                    </div>
                    </div>
                    

                    <!-- <div id="divContainerMember" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div id="divCloneThis1">
                            <div id="divMemberLabel" class="col-md-4"><label for="exampleInputName2">Member</label></div>
                            <div class="col-md-8">
                                <select name="member[]" id="member[]" class="form-control" >
                                    <?php
                                    if (isset($manager)) {
                                        foreach ($manager as $value) {
                                            ?>
                                            <option value="<?php echo $value['userid']; ?>"><?php echo $value['salutation'] . " " . $value["firstname"] . " " . $value['lastname']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>

                                </select>

                                <select name="peran[]" id="peran[]" class="form-control" >
                                    <?php
                                    if (isset($peran)) {
                                        foreach ($peran as $value) {
                                            ?>
                                            <option value="<?php echo $value['role_id']; ?>"><?php echo $value['name']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>

                                <button class="btn btn-primary"type = "button" onclick = "addmore();this.style.display = 'none';">Add More Member</button>
                            </div>
                        </div>
                    </div> -->



                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;display: none;">
                        <div class="col-md-4"><label for="exampleInputName2">Select Timesheet Type</label></div>
                        <div class="col-md-8">
                            <select onchange="hideOtherMenu(this.selectedIndex)" name="pm_id" id="pm_id" class="form-control" >
                                <option value="1" selected>Manual</option>

                            </select>
                        </div>

                    </div>
                    <div class="divboxaddproject">
                    <div id="checkboxplatform" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Platform</label></div>
                        <div class="col-md-8">
                            <?php
                            if (isset($platform)) {
                                $no_tp = '1';
                                foreach ($platform as $value) {
                                    ?>
                                    <div class="checkbox" style="margin-left: 5px">
                                        <label>
                                            <input type="hidden" name="platform_text[]" value="<?php echo $value['platform_id']; ?>">
                                            <input type="hidden" name="is_delete_platform[]" value="1" class="is_delete_platform<?php echo $no_tp; ?>">
                                            <input type="checkbox" name="platform[]" value="<?php echo $value['platform_id']; ?>" class="checkbox_platform<?php echo $no_tp; ?>" onClick="is_delete_platform('<?php echo $no_tp; ?>')"><?php echo $value['name']; ?>
                                        </label>
                                    </div> <br>
                                    <?php
                                    $no_tp++;
                                }
                            }
                            ?>
                        </div>

                    </div>
                    </div>
                    <div id="uploadexcel" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Upload Main hours</label></div>
                        <div class="col-md-8">
                            <input type="file" id="filename" name="filename">
                        </div>

                    </div>
<!--                    <input type="text" id="to" class="date hasDatepicker" name="dateTo" placeholder="To" title="input">-->

            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit" >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>

<div class="modal fade bs-example-modal-sm" id="addModalfeature" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Feature</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/savefeature' id='formsavefeature' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Feature Name</label></div>
                        <div class="col-md-8"><input type="hidden" id="feature_id" name='feature_id' value="">
                            <input type="hidden" id="proplatid" name='proplatid' value="">
                            <input type="hidden" name="idfeature" id="idfeature" class="idfeature">
                            <input type="text" class="form-control namafeature" id="nama" name='nama'placeholder="Feature Name"  required></div>
                    </div>
            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit"  >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>

<div class="modal fade bs-example-modal-sm" id="addModalgroup" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Group</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/savegroup' id='formsavegroup' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Group Name</label></div>
                        <div class="col-md-8"><input type="hidden" id="group_id" name='group_id' value="">
                            <input type="hidden" id="featureid" name='featureid' value="">
                            <input type="hidden" id="idgroup" class="idgroup" name="idgroup">
                            <input type="text" class="form-control namagroup" id="nama" name='nama'placeholder="Group Name"  required></div>
                    </div>
            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit"  >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>




<div class="modal fade bs-example-modal-sm" id="addModalitem" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Item</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/saveitem' id='formsaveitem' method="POST" enctype="multipart/form-data">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Item Name</label></div>
                        <div class="col-md-8"><input type="hidden" id="group_id_item" name='group_id' value="">
                            <input type="hidden" id="item_id" name='item_id' value="">
                            <input type="hidden" name="iditem" id="iditem" class="iditem">
                            <input type="text" class="form-control namaitem" id="nama" name='nama'placeholder="Item Name"  required></div>
                    </div>
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Hour</label></div>
                        <div class="col-md-8">
                            <input type="number" class="form-control hourval" id="hour" name='hour' placeholder="Hours"  required style='width: 30%'></div>
                    </div>
            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit"  >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>





<div class="modal fade bs-example-modal-sm" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true"></div>


<script>
    // Attach a submit handler to the form

    var options = {
        beforeSubmit: showRequest,
        success: showResponse,
        dataType: 'json'
    };
    $('#formsaveproject,#formsavefeature,#formsavegroup,#formsaveitem').ajaxForm(options);

    function showRequest(formData, jqForm, options) {
        return true;
    }
    function closemodal() {
        $('addMemberModal').modal('hide');
        $('#addModal').modal('hide');
        $('#formsavetugboat').resetForm();
    }
    //Hide uploadExcel by default
    $('#uploadexcel').hide();
    function hideOtherMenu(type) {
        if (type === 0) {
            $('#checkboxplatform').show();
            $('#uploadexcel').hide();
            $('#filename').val('');
        } else if (type === 1) {
            $("input:checkbox").removeAttr('checked');
            $('#checkboxplatform').hide();
            $('#uploadexcel').show();
        }
    }
    function showResponse(data) {
//        alert(data);
//                        if (data.status == 1) {

//        var msg = JSON.parse(data);
//                        document.write(data.type);

//alert(data.status);
        if (data.status == 1 || data.status == '1') {
            $('#formsaveproject,#formsavefeature,#formsavegroup,#formsaveitem,#formsavemember').resetForm();
            $('#addModal').modal('hide');
            $('#addModalfeature,#addModalgroup,#addModalitem,#addMemberModal').modal('hide');
            alert(data.msg);
            if (data.type == 'project') {
                //viewproject();
                window.location.href = "<?php echo site_url('home/projectMaster'); ?>";
            }
            if (data.type == 'feature') {
                viewfeature(0);
            }
            if (data.type == 'group') {
                viewgroup(data.id)
            }
            if (data.type == 'item') {
                $('#group_id_item').val(data.id);
                viewitem(data.id);
            }
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
</body>
</html>
