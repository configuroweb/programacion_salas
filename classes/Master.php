<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			if(isset($sql))
			$resp['sql'] = $sql;
			return json_encode($resp);
			exit;
		}
	}
	function save_assembly(){
		extract($_POST);
		$data = "";
		$_POST['description'] = addslashes(htmlentities($_POST['description']));
		foreach($_POST as $k=> $v){
			if($k != 'id'){
				if(!empty($data)) $data.=", ";
				$data.=" {$k} = '{$v}'";
			}
		}
		$check = $this->conn->query("SELECT * FROM `assembly_hall` where `room_name` = '{$room_name}' ".(!empty($id) ? "and id != {$id}" : ''))->num_rows;
		$this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Assembly Hall/Room Already Exists.";
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `assembly_hall` set $data";
				$save = $this->conn->query($sql);
			}else{
				$sql = "UPDATE `assembly_hall` set $data where id = {$id}";
				$save = $this->conn->query($sql);
			}
			$this->capture_err();

			if($save){
				$resp['status'] = "success";
				$this->settings->set_flashdata('success'," Assembly Hall/Room Successfully Saved");
			}else{
				$resp['status'] = "failed";
				$resp['sql'] = $sql;
			}
		}
		return json_encode($resp);
	}

	function delete_assembly_hall(){
		$sql = "DELETE FROM `assembly_hall` where id = '{$_POST['id']}' ";
		$delete = $this->conn->query($sql);
		$this->capture_err();
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Assembly Hall/Room Successfully Deleted");
		}else{
			$resp['status'] = "failed";
			$resp['sql'] = $sql;
		}
		return json_encode($resp);
	}

	function save_schedule(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k=> $v){
			if($k != 'id'){
				if(!empty($data)) $data.=", ";
				$data.=" {$k} = '{$v}'";
			}
		}
		if(strtotime($datetime_end) < strtotime($datetime_start)){
			$resp['status'] = 'failed';
			$resp['err_msg'] = "Date and Time Schedule is Invalid.";
		}else{
			$d_start = strtotime($datetime_start);
			$d_end = strtotime($datetime_end);
			$chk = $this->conn->query("SELECT * FROM `schedule_list` where (('{$d_start}' Between unix_timestamp(datetime_start) and unix_timestamp(datetime_end)) or ('{$d_end}' Between unix_timestamp(datetime_start) and unix_timestamp(datetime_end))) ".(($id > 0) ? " and id !='{$id}' " : ""))->num_rows;
			if($chk > 0){
				$resp['status'] = 'failed';
				$resp['err_msg'] = "The schedule is conflict with other schedules.";
			}else{
				if(empty($id)){
					$sql = "INSERT INTO `schedule_list` set {$data}";
				}else{
					$sql = "UPDATE `schedule_list` set {$data} where id = '{$id}'";
				}
				$save = $this->conn->query($sql);
				if($save){
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success', " Schedule successfully saved.");
				}else{
					$resp['status'] = 'failed';
					$resp['sql'] = $sql;
					$resp['qry_error'] = $this->conn->error;
					$resp['err_msg'] = "There's an error while submitting the data.";
				}
			}
		}
		return json_encode($resp);
	}
	function delete_sched(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `schedule_list` where id = '{$id}'");
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Schedule successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_assembly':
		echo $Master->save_assembly();
	break;
	case 'delete_assembly_hall':
		echo $Master->delete_assembly_hall();
	break;
	case 'save_schedule':
		echo $Master->save_schedule();
	break;
	case 'delete_sched':
		echo $Master->delete_sched();
	break;
	default:
		// echo $sysset->index();
		break;
}