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
    <form action="" id="edit_sched">
        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
        <div class="form-group">
            <label for="assembly_hall_id" class="control-label">Assembly Hall/Room</label>
            <select name="assembly_hall_id" id="assembly_hall_id" class="custom-select select2" required>
                <option value=""></option>
                <?php 
                $hall_qry = $conn->query("SELECT * FROM `assembly_hall` where status =1  order by `room_name` asc");
                while($row = $hall_qry->fetch_assoc()):
                ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo isset($assembly_hall_id) && $assembly_hall_id == $row['id'] ? "selected" : "" ?>><?php echo $row['room_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="reserved_by" class="control-label">Reserved By:</label>
            <input type="text" class="form-control" name="reserved_by" id="reserved_by" value="<?php echo isset($reserved_by) ? $reserved_by : "" ?>" required>
        </div>
        <div class="form-group">
            <label for="datetime_start" class="control-label">DateTime Start:</label>
            <input type="datetime-local" class="form-control" name="datetime_start" value="<?php echo isset($datetime_start) ? date("Y-m-d\TH:i",strtotime($datetime_start)) : "" ?>" id="datetime_start">
        </div>
        <div class="form-group">
            <label for="datetime_end" class="control-label">DateTime End:</label>
            <input type="datetime-local" class="form-control" name="datetime_end" value="<?php echo isset($datetime_end) ? date("Y-m-d\TH:i",strtotime($datetime_end)) : "" ?>" id="datetime_end">
        </div>
        <div class="form-group">
            <label for="schedule_remarks" class="control-label">Remarks:</label>
            <textarea rows="3" class="form-control" name="schedule_remarks" id="schedule_remarks"><?php echo isset($schedule_remarks) ? $schedule_remarks : "" ?></textarea>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button class="btn btn-flat btn-primary mr-2" form="edit_sched">Update</button>
    <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
</div>
<script>
    $(function(){
        $('#edit_sched').submit(function(e){
			e.preventDefault()
			start_loader()
			$('#edit_sched .err-msg').remove()
			$.ajax({
				url:_base_url_+'classes/Master.php?f=save_schedule',
				method:"POST",
				data: $(this).serialize(),
				dataType:"json",
				error:err=>{
					console.log(err)
					end_loader()
					alert_toast("An error occured","error");
				},
				success:function(resp){
					if(resp.status == 'success'){
						location.reload()
					}else if(resp.status == 'failed' && !!resp.err_msg){
						var el = $('<div class="err-msg alert alert-danger mb-1">')
							el.text(resp.err_msg)
						$('#edit_sched').prepend(el)
							el.show('slow')
					}else{
						console.log(resp)
						alert_toast("An error occured","error");
					}
					end_loader();
				}
			})
		})
        $('#uni_modal').on('hidden.bs.modal',function(){
            if($(this).find('form#edit_sched').length > 0)
            uni_modal("Schedule Details","schedules/view_details.php?id=<?php echo $_GET['id'] ?>")
        })
    })
</script>
