<?php
include('conexion.php');
$datetime = date('Y-m-d H:i:s');
$fecha_creacion = date('Y-m-d');
$hora_creacion = date('H:i:s');
$url = "https://shop.camaleonmg.com/terminosycondiciones.php";
$recorte = "camaleonmg.com/tyc.php";

$sql1 = "INSERT INTO recorte1 (url,recorte,fecha_creacion) VALUES ('$url','$recorte','$fecha_creacion')";
$proceso1 = mysqli_query($conexion,$sql1);

header('Location: '.$url);

?>