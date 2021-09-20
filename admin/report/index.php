<?php 
$dstart = isset($_GET['dstart']) ? $_GET['dstart'] : date("Y-m-d", strtotime(date("Y-m-d")." -1 week"));
$dend = isset($_GET['dend']) ? $_GET['dend'] : date("Y-m-d");
$rid = isset($_GET['aid']) ? $_GET['aid'] : 0;
?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Reporte de Reservas</h3>
		<div class="card-tools">
			
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <form action="" id="filter">
            <div class="d-flex  h-100 d-flex align-items-end">
                <div class="form-group col-3">
                    <label for="start" class="control-label">Fecha Inicio</label>
                    <input type="date" name="start" id="start" value="<?php echo $dstart ?>" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="end" class="control-label">Fecha Fin</label>
                    <input type="date" name="end" id="end" value="<?php echo $dend ?>" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="aid" class="control-label">Sala</label>
                    <select class="custom-select select2" name="aid" id="aid">
                        <option value="0" <?php echo $rid ==  0 ? "selected" : "" ?>>All</option>
                        <?php 
                        $aqry = $conn->query("SELECT * FROM `assembly_hall` order by room_name asc");
                        while($row= $aqry->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo $rid ==  $row['id']  ? "selected" : "" ?>><?php echo $row['room_name'] ?></option>

                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-3">
                    <button class="btn btn-flat btn-primary"><span class="fas fa-filter"></span>  Filtro</button>
                    <button type="button" id="print_now" class="btn btn-flat btn-success"><span class="fas fa-print"></span>  Imprimir</button>
                </div>
            </div>
        </form>
        <div class="container-fluid" id="print_out">
			<table class="table table-bordered table-stripped" id="report-table">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="20%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Fecha</th>
						<th>Sala</th>
						<th>Descripción</th>
						<th>Reservado por</th>
						<th>Observaciones</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
                        $where = "";
                        $rname = "";
                        if($rid > 0){
                            $where = " and a.id = '{$rid}'";
                        }
						$qry = $conn->query("SELECT s.*,a.room_name, a.description from `schedule_list` s inner join `assembly_hall` a on a.id = s.assembly_hall_id where ((date(datetime_start) BETWEEN '{$dstart}' and '{$dend}' ) OR (date(datetime_end) BETWEEN '{$dstart}' and '{$dend}' )) {$where} order by unix_timestamp(datetime_start) asc, unix_timestamp(datetime_end) asc ");
						while($row = $qry->fetch_assoc()):
                            if($rid > 0){
                                $rname = $row['room_name'];
                            }
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td>
                                <p class="m-0">
                                    <small><b>Inicio:</b> <?php echo date("M d, Y h:i A",strtotime($row['datetime_start'])) ?></small><br>
                                    <small><b>Fin:</b> <?php echo date("M d, Y h:i A",strtotime($row['datetime_end'])) ?></small>
                                </p>
                            </td>
							<td ><?php echo $row['room_name'] ?></td>
							<td ><?php echo $row['description'] ?></td>
							<td ><?php echo ucwords($row['reserved_by']) ?></td>
							<td ><?php echo $row['schedule_remarks'] ?></td>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
    window.DT_INIT = function(){
        $('#report-table').dataTable({
            "lengthMenu":[ [ 50, 100, -1], [ 50, 100, "All"]],
            "oreder": [[0,"asc"]]
        })
    }
    $(function(){
        $('#filter').submit(function(e){
            e.preventDefault()
            var dstart = $('[name="start"]').val()
            var dend = $('[name="end"]').val()
            var aid = $('[name="aid"]').val()
            location.href = "?page=report&dstart="+dstart+"&dend="+dend+"&aid="+aid;
        })
        $('.select2').select2()
        DT_INIT()
        // console.log($.fn.DataTable.isDataTable( '#report-table' ))
        $('#print_now').click(function(){
            start_loader()
            if($.fn.DataTable.isDataTable( '#report-table' ) == true){
                $('#report-table').DataTable().destroy()
            }
            var _h = $('head').clone()
            var _p = $('#print_out').clone()
            var _el = $('<div>')
                _el.append(_h)
                _el.append("<style>html, body, .wrapper {  min-height: inherit !important; }</style>")
                _el.append("<h3 class='text-center'><?php echo $_settings->info('name') ?></h3>")
                _el.append("<h4 class='text-center'>ConfiguroWeb</h4>")
                if('<?php echo $dstart ?>' == '<?php echo $dend ?>')
                    _el.append("<p class='text-center m-0'>Fecha: <?php echo date("F d, Y", strtotime($dstart)) ?></p>");
                else
                    _el.append("<p class='text-center m-0'>Fecha: <?php echo date("F d, Y", strtotime($dstart)) ?> - <?php echo date("F d, Y", strtotime($dend)) ?></p>");
                if('<?php echo $rid ?>' > 0)
                _el.append("<p class='text-center m-0'>For: <?php echo $rname ?></p>");
                _el.append("<hr/>")
                _el.append(_p)
            var nw = window.open("","_blank","width=5000,heigth=5000,top=0,left=0")
                nw.document.write(_el.html())
                nw.document.close()
                setTimeout(() => {
                nw.print()
                    setTimeout(() => {
                        nw.close()
                        DT_INIT()
                        end_loader()
                    }, 200);
                }, 500);
        })
    })
</script>