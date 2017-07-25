<script>
    $(document).ready(function () {

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
            "sAjaxSource": '<?php echo base_url(); ?>report/getloginreport',
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
    
    function checkdetail(data){
        alert(data);
    }
</script>

<div class="container">
    <div class="row">
        <div class="col-lg-12" id="divdataproject">
            <h4>Report Project</h4>
            <div id="divproject">

                <table id="tabelproject" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Name</th>
                    <!--<th>Platform</th>-->
                   
                    <th>Last Login</th>
                     <th>Last Activity</th>

                    <!--<th>Project Manager</th>-->

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
