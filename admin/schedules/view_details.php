<?php
require_once('../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT s.*,a.room_name FROM `schedule_list` s inner join assembly_hall a on a.id = s.assembly_hall_id where s.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
#uni_modal .modal-content>.modal-footer{
    display:none;
}
#uni_modal .modal-body{
    padding:0 !important;
}
</style>
<div class="container-fluid p-2">
    <p><b>Room/Hall Name:</b> <?php echo $room_name ?></p>
    <p><b>Reserved By:</b> <?php echo ucwords($reserved_by) ?></p>
    <p><b>Date/Time Start:</b> <?php echo date("M d, Y h:i A",strtotime($datetime_start)) ?></p>
    <p><b>Date/Time End:</b> <?php echo date("M d, Y h:i A",strtotime($datetime_end)) ?></p>
    <p><b>Remarks:</b><br> <span><?php echo $schedule_remarks ?></span></p>
</div>
<div class="modal-footer">
    <button type="button" id="update" class="btn btn-primary btn-flat" data-id="<?php echo $_GET['id'] ?>">Edit</button>
    <button type="button" id="delete" class="btn btn-danger btn-flat" data-id="<?php echo $_GET['id'] ?>">Delete</button>
    <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
</div>
<script>
    $(function(){
        $('#update').click(function(){
            uni_modal("Edit Schedule","schedules/edit_schedule.php?id=<?php echo $_GET['id'] ?>")
        })
        $('#delete').click(function(){
			_conf("Are you sure to delete this schedule permanently?","delete_sched",[$(this).attr('data-id')])
		})
    })
    function delete_sched($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_sched",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
