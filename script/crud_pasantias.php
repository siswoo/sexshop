<?php
@session_start();
include('conexion.php');
$condicion = $_POST["condicion"];
$datetime = date('Y-m-d H:i:s');
$fecha_creacion = date('Y-m-d');
$hora_creacion = date('H:i:s');
$responsable = $_SESSION["camaleonapp_id"];
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../resources/PHPMailer/PHPMailer/src/Exception.php';
require '../resources/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../resources/PHPMailer/PHPMailer/src/SMTP.php';

if($condicion=='registro1'){
	$documento_tipo = $_POST['documento_tipo'];
	$documento_numero = $_POST['documento_numero'];
	$primer_nombre = $_POST['primer_nombre'];
	$segundo_nombre = $_POST['segundo_nombre'];
	$primer_apellido = $_POST['primer_apellido'];
	$segundo_apellido = $_POST['segundo_apellido'];
	$correo = $_POST['correo'];
	$correo2 = $_POST['correo2'];
	$telefono = $_POST['telefono'];
	$barrio = $_POST['barrio'];
	$direccion = $_POST['direccion'];
	$genero = $_POST['genero'];
	$enterado = $_POST['enterado'];

	if($correo!=$correo2){
		$datos = [
			"estatus" => "error",
			"msg" => "Correos Diferentes!",
		];
		echo json_encode($datos);
		exit;
	}

	$sql1 = "SELECT * FROM usuarios WHERE documento_numero = '".$documento_numero."' and estatus_pasantes = 1";
	$proceso1 = mysqli_query($conexion,$sql1);
	$contador1 = mysqli_num_rows($proceso1);

	if($contador1>=1){
		$datos = [
			"estatus" => "error",
			"msg" => "ya existe pasante!",
		];
		echo json_encode($datos);
		exit;
	}

	$sql10 = "SELECT * FROM usuarios WHERE correo_personal = '".$correo."'";
	$proceso10 = mysqli_query($conexion,$sql10);
	$contador10 = mysqli_num_rows($proceso10);

	if($contador10>=1){
		$datos = [
			"estatus" => "error",
			"msg" => "ya existe un registro con dicho correo!",
		];
		echo json_encode($datos);
		exit;
	}


	/**********CREACION DE LA FUNCION PARA WHATSAPP**********/
	function sendMessage($to,$msg){
		$data = [
			'phone' => $to,
			'body' => $msg,
		];

		include('conexion.php');

		$sql9 = "SELECT * FROM apiwhatsapp";
		$proceso9 = mysqli_query($conexion,$sql9);
		while($row9 = mysqli_fetch_array($proceso9)) {
			$CHAT_URL = $row9["url"];
			$CHAT_TOKEN = $row9["token"];
		}

		$json = json_encode($data);
		$url = 'https://api.chat-api.com/'.$CHAT_URL.'/sendMessage?token='.$CHAT_TOKEN;
		$options = stream_context_create(['http' => [
				'method' => 'POST',
				'header' => 'Content-type: application/json',
				'content' => $json
			]
		]);

		$result = file_get_contents($url, false, $options);
		if($result) return json_decode($result);

		return false;
	}
	/***********************************************************/

	$sql2 = "SELECT * FROM usuarios WHERE documento_numero = '".$documento_numero."' and correo_personal = '".$correo."'";
	$proceso2 = mysqli_query($conexion,$sql2);
	$contador2 = mysqli_num_rows($proceso2);
	if($contador2>=1){

		while($row2 = mysqli_fetch_array($proceso2)) {
			$usuario_id = $row2["usuario_id"];
			$usuario_pasante_estatus = $row2["estatus_pasantes"];
			$id_pais = $row2["id_pais"];
			$sql3 = "SELECT * FROM pais WHERE id = ".$id_pais;
			$proceso3 = mysqli_query($conexion,$sql3);
			while($row3 = mysqli_fetch_array($proceso3)) {
				$codigo_pais = $row3["codigo"];
			}
		}

		if($usuario_pasante_estatus==1){
			$datos = [
				"estatus" => "error",
				"msg" => "Ya te has registrado anteriormente como pasante!",
			];
			echo json_encode($datos);
			exit;
		}

		$sql3 = "UPDATE usuarios SET estatus_pasantes = 1 WHERE id = ".$usuario_id;
		$proceso3 = mysqli_query($conexion,$sql3);

		$sql4 = "SELECT * FROM datos_pasantias WHERE id_usuarios = ".$_SESSION["camaleonapp_id"];
		$proceso4 = mysqli_query($conexion,$sql4);
		while($row4 = mysqli_fetch_array($proceso4)) {
			$usuario_session_sede = $row4["sede"];
		}

		$sql5 = "INSERT INTO datos_pasantes (id_usuarios,sede,estatus,fecha_creacion) VALUES (".$usuario_id.",".$usuario_session_sede.",1,'$fecha_creacion')";
		$proceso5 = mysqli_query($conexion,$sql5);

		$msg = "Ya estas registrado en nuestra APP https://www.camaleonmg.com como pasante";
		$phone = $codigo_pais.$telefono;
		$result = sendMessage($phone,$msg);
		if($result !== false){
			if($result->sent == 1){}else{}
		}else{
			var_dump($result);
		}

		/*************ENVIAR CORREO ELECTRONICO***************/
		$html = '';
		$mail = new PHPMailer(true);
		try {
		    $mail->isSMTP();
		    $mail->CharSet = "UTF-8";
		    $mail->Host = 'mail.camaleonmg.com';
		    $mail->SMTPAuth = true;
		    $mail->Username = 'noreply@camaleonmg.com';
		    $mail->Password = 'juanmaldonado123';
		    $mail->SMTPSecure = 'tls';
		    $mail->Port = 587;

		    $mail->setFrom('noreply@camaleonmg.com');
		    $mail->addAddress($correo);
		    $html = "
		        <h2 style='color:#3F568A; text-align:center; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;'>
		            <p>Felicitaciones tu perfil esta haciendo analizado por nuestro equipo de trabajo.</p>
		            <p>El siguiente paso es esperar una respuesta, gracias por elegir a Camaleón!.</p>
		        </h2>
		    ";
		    /*
		    $mail->AddEmbeddedImage("img/mails/mailing modelo1.png", "my-attach", "mailing modelo1.png");
		    $html = "
		        <h2 style='color:#3F568A; text-align:center; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;'>
		            <p>Felicitaciones tu perfil ha sido aprobado para formar parte de la familia Camaleón!.</p>
		            <p>El siguiente paso es completar tu formulario de contacto, puedes ingresar al sistema con los siguientes datos.</p>
		            <p>Usuario: | Clave: ".$clave_generada." </p>
		            <p>En el link.. https://www.camaleonmg.com</p>
		        </h2>
		        <div style='text-align:center;'>
		        	<img alt='PHPMailer' src='cid:my-attach'>
		        </div>
		    ";
		    */

		    $mail->isHTML(true);
		    $mail->Subject = 'Pasante Camaleón!';
		    $mail->Body    = $html;
		    $mail->AltBody = 'Este es el contenido del mensaje en texto plano';
		 
		    $mail->send();
		} catch (Exception $e) {}
		/*****************************************************/
	}else{
		$clave_generada1 = rand(6,99999999);
		$clave = md5($clave_generada1);

		$id_empresa = $_SESSION['camaleonapp_empresa'];
		$id_pais = 5;
		$codigo_pais = 57;

		$sql6 = "INSERT INTO usuarios (nombre1,nombre2,apellido1,apellido2,documento_tipo,documento_numero,correo_personal,clave,telefono,estatus_pasantes,genero,direccion,responsable,id_empresa,id_pais,fecha_creacion) VALUES ('$primer_nombre','$segundo_nombre','$primer_apellido','$segundo_apellido','$documento_tipo','$documento_numero','$correo','$clave','$telefono',1,'$genero','$direccion','$responsable',$id_empresa,$id_pais,'$fecha_creacion')";
		$proceso6 = mysqli_query($conexion,$sql6);
		$ultimo_id_usuario=mysqli_insert_id($conexion);

		$sql7 = "SELECT * FROM datos_pasantias WHERE id_usuarios = ".$_SESSION["camaleonapp_id"];
		$proceso7 = mysqli_query($conexion,$sql7);
		while($row7 = mysqli_fetch_array($proceso7)) {
			$usuario_session_sede = $row7["sede"];
		}

		$sql8 = "INSERT INTO datos_pasantes (id_usuarios,sede,estatus,fecha_creacion) VALUES ('$ultimo_id_usuario','$usuario_session_sede',1,'$fecha_creacion')";
		$proceso8 = mysqli_query($conexion,$sql8);

		$msg = "Bienvenido a Camaleon Models! 
Datos de tu cuenta Camaleón... recuerda que la página para ingresar es https://www.camaleonmg.com
---------------------------------------------------------
Usuario: ".$correo."
Clave: ".$clave_generada1."
Módulo: Módelo
---------------------------------------------------------";
		$phone = $codigo_pais.$telefono;
		$result = sendMessage($phone,$msg);
		if($result !== false){
			if($result->sent == 1){}else{}
		}else{
			var_dump($result);
		}

		/*************ENVIAR CORREO ELECTRONICO***************/
		$html = '';
		$mail = new PHPMailer(true);
		try {
		    $mail->isSMTP();
		    $mail->CharSet = "UTF-8";
		    $mail->Host = 'mail.camaleonmg.com';
		    $mail->SMTPAuth = true;
		    $mail->Username = 'noreply@camaleonmg.com';
		    $mail->Password = 'juanmaldonado123';
		    $mail->SMTPSecure = 'tls';
		    $mail->Port = 587;

		    $mail->setFrom('noreply@camaleonmg.com');
		    $mail->addAddress($correo);
		    $html = "
		        <h2 style='color:#3F568A; text-align:center; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;'>
		            <p>Felicitaciones tu perfil esta haciendo analizado por nuestro equipo de trabajo.</p>
		            <p>El siguiente paso es esperar una respuesta, gracias por elegir a Camaleón!.</p>
		        </h2>
		    ";

		    $mail->isHTML(true);
		    $mail->Subject = 'Pasante Camaleón!';
		    $mail->Body    = $html;
		    $mail->AltBody = 'Este es el contenido del mensaje en texto plano';
		 
		    $mail->send();
		} catch (Exception $e) {}
		/*****************************************************/
	}

	$datos = [
		"estatus" => "ok",
	];
	echo json_encode($datos);
}

?>