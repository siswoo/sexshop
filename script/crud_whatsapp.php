<?php
@session_start();
include('conexion.php');
$condicion = $_POST["condicion"];
$datetime = date('Y-m-d H:i:s');
$fecha_creacion = date('Y-m-d');
$hora_creacion = date('H:i:s');

if($condicion=='actualizar1'){
	$token = $_POST['token'];
	$url = $_POST['url'];

	$sql1 = "UPDATE apiwhatsapp SET token = '".$token."', url = '".$url."', fecha_creacion = '".$fecha_creacion."', hora_creacion = '".$hora_creacion."'";
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus" => "ok",
	];
	echo json_encode($datos);
}

?>