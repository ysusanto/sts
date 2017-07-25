<script>
    $('#formsavemember').ajaxForm(options);
</script>

    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Project Member</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/saveMemberAssignment2' id='formsavemember' method="POST" enctype="multipart/form-data">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">

                        <input type="hidden" value="" id="viewAddMemberModalHidden2" name="addmember_group_id" />
                        <input type="hidden" value="" id="viewAddMemberModalItemidHidden2" name="addmember_itemid" />
                        <div id="tablecheckboks"></div>
                        <table id="menu2" class="table table-hover" style="font-size:9pt !important; width: 420px !important; border-color:transparent !important;">
                          <thead>
                            <tr style="border-color:transparent !important;">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                          </thead>
                            <tbody id="tRow">

                            </tbody>
                          </table>
                      
            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit"  >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
