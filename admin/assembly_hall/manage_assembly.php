<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `assembly_hall` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Actualizar ": "Crear Nueva " ?> Sala</h3>
	</div>
	<div class="card-body">
		<form action="" id="assembly-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="form-group">
				<label for="room_name" class="control-label">Nombre de Sala</label>
                <input name="room_name" id="" class="form-control form no-resize" value="<?php echo isset($room_name) ? $room_name : ''; ?>" />
			</div>
			<div class="form-group">
				<label for="location" class="control-label">Ubiación</label>
                <textarea name="location" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($location) ? $location : ''; ?></textarea>
			</div>
			<div class="form-group">
				<label for="description" class="control-label">Descripción</label>
                <textarea name="description" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($description) ? $description : ''; ?></textarea>
			</div>
            <div class="form-group">
				<label for="status" class="control-label">Estado</label>
               	<select name="status" id="status" class="custom-select">
					   <option value="1" <?php echo isset($status) && $status == 1 ? "selected" : "" ?>>Activa</option>
					   <option value="0" <?php echo isset($status) && $status == 10 ? "selected" : "" ?>>Inactiva</option>
				   </select>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="assembly-form">Guardar</button>
		<a class="btn btn-flat btn-default" href="?page=assembly_hall">Cancelar</a>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#assembly-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_assembly",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("Ocurrió un error",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.href = "./?page=assembly_hall";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("Ocurrió un error",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

	})
</script>