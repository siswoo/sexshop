<?php
@session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../resources/PHPMailer/PHPMailer/src/Exception.php';
require '../resources/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../resources/PHPMailer/PHPMailer/src/SMTP.php';
include('conexion.php');
include('conexion2.php');
$condicion = $_POST["condicion"];
$datetime = date('Y-m-d H:i:s');
$empresa = $_SESSION["camaleonapp_empresa"];
$fecha_creacion = date('Y-m-d');
$fecha_modificacion = date('Y-m-d');
include("../js/funciones1.php");

if($condicion=='table1'){
	$pagina = $_POST["pagina"];
	$consultasporpagina = $_POST["consultasporpagina"];
	$filtrado = $_POST["filtrado"];
	$link1 = $_POST["link1"];
	$sede = $_POST["sede"];
	$link1 = explode("/",$link1);
	$link1 = $link1[3];

	if($pagina==0 or $pagina==''){
		$pagina = 1;
	}

	if($consultasporpagina==0 or $consultasporpagina==''){
		$consultasporpagina = 10;
	}

	if($filtrado!=''){
		$filtrado = ' and (nombre1 LIKE "%'.$filtrado.'%" or nombre2 LIKE "%'.$filtrado.'%" or apellido1 LIKE "%'.$filtrado.'%" or apellido2 LIKE "%'.$filtrado.'%" or documento_numero LIKE "%'.$filtrado.'%" or us.correo_personal LIKE "%'.$filtrado.'%" or telefono LIKE "%'.$filtrado.'%")';
	}

	if($sede!=''){
		$sede = ' and (dpa.sede = '.$sede.') ';
	}

	$limit = $consultasporpagina;
	$offset = ($pagina - 1) * $consultasporpagina;

	$sql1 = "SELECT 
		us.id as usuario_id,
		dpa.id as pasante_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_pasantes as estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		dpa.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_pasantes dpa
		ON us.id = dpa.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN paises pa
		ON us.id_pais = pa.id
		INNER JOIN sedes se
		ON dpa.sede = se.id 
		WHERE us.id != 0 
		".$filtrado." 
		".$sede."
	";

	$sql2 = "SELECT 
		us.id as usuario_id,
		dpa.id as pasante_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_pasantes as estatus,
		dpa.estatus as pasantes_estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		dpa.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_pasantes dpa
		ON us.id = dpa.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON dpa.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON pa.id = us.id_pais
		WHERE us.id != 0 
		".$filtrado." 
		".$sede." 
		ORDER BY dpa.fecha_creacion DESC LIMIT ".$limit." OFFSET ".$offset."
	";

	$proceso1 = mysqli_query($conexion,$sql1);
	$proceso2 = mysqli_query($conexion,$sql2);
	$conteo1 = mysqli_num_rows($proceso1);
	$paginas = ceil($conteo1 / $consultasporpagina);

	$html = '';

	$html .= '
		<div class="col-xs-12">
	        <table class="table table-bordered">
	            <thead>
	            <tr>
	                <th class="text-center">T Doc</th>
	                <th class="text-center">N Doc</th>
	                <th class="text-center">Nombre</th>
	                <th class="text-center">Género</th>
	                <th class="text-center">Correo</th>
	                <th class="text-center">Teléfono</th>
	                <th class="text-center">Estatus</th>
	                <th class="text-center">Sede</th>
	                <th class="text-center">Ingreso</th>
	                <th class="text-center">Opciones</th>
	            </tr>
	            </thead>
	            <tbody>
	';
	if($conteo1>=1){
		while($row2 = mysqli_fetch_array($proceso2)) {
			if($row2["pasantes_estatus"]==1){
				$pasante_estatus = "Proceso";
			}else if($row2["pasantes_estatus"]==2){
				$pasante_estatus = "Aceptado";
			}else if($row2["pasantes_estatus"]==3){
				$pasante_estatus = "Rechazado";
			}
			$html .= '
		                <tr id="tr_'.$row2["pasante_id"].'">
		                    <td style="text-align:center;">'.$row2["documento_tipo"].'</td>
		                    <td style="text-align:center;">'.$row2["documento_numero"].'</td>
		                    <td>'.$row2["nombre1"]." ".$row2["nombre2"]." ".$row2["apellido1"]." ".$row2["apellido2"].'</td>
		                    <td style="text-align:center;">'.$row2["genero"].'</td>
		                    <td style="text-align:center;">'.$row2["correo"].'</td>
		                    <td style="text-align:center;">'.$row2["telefono"].'</td>
		                    <td  style="text-align:center;">'.$pasante_estatus.'</td>
		                    <td style="text-align:center;">'.$row2["sede"].'</td>
		                    <td nowrap="nowrap">'.$row2["fecha_creacion"].'</td>
		                    <td class="text-center" nowrap="nowrap">
			';

			if($row2["pasantes_estatus"]==1){
				$html .= '
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#modificar1" onclick="editar1('.$row2["pasante_id"].','.$row2["usuario_id"].');">Editar</button>
								<button type="button" class="btn btn-success" onclick="aceptar1('.$row2["usuario_id"].');">A</button>
								<button type="button" class="btn btn-danger" onclick="rechazar1('.$row2["usuario_id"].');">R</button>
				';
			}else if($row2["pasantes_estatus"]==2){
				$html .= '
								<button type="button" class="btn btn-info" disabled="disabled">'.$pasante_estatus.'</button>
								<!--<button class="btn btn-danger" onclick="rechazar1('.$row2["usuario_id"].');">Rechazar</button>-->
				';
			}else if($row2["pasantes_estatus"]==3){
				$html .= '
								<button type="button" class="btn btn-info" disabled="disabled">'.$pasante_estatus.'</button>
								<!--<button class="btn btn-success" onclick="aceptar1('.$row2["usuario_id"].');">Aceptar</button>-->
				';
			}
		    
		    $html .= '		</td>
		    			</tr>
		    ';

		}
	}else{
		$html .= '<tr><td colspan="10" class="text-center" style="font-weight:bold;font-size:20px;">Sin Resultados</td></tr>';
	}

	$html .= '
	            </tbody>
	        </table>
	        <nav>
	            <div class="row">
	                <div class="col-xs-12 col-sm-4 text-center">
	                    <p>Mostrando '.$consultasporpagina.' de '.$conteo1.' Datos disponibles</p>
	                </div>
	                <div class="col-xs-12 col-sm-4 text-center">
	                    <p>Página '.$pagina.' de '.$paginas.' </p>
	                </div> 
	                <div class="col-xs-12 col-sm-4">
			            <nav aria-label="Page navigation" style="float:right; padding-right:2rem;">
							<ul class="pagination">
	';
	
	if ($pagina > 1) {
		$html .= '
								<li class="page-item">
									<a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#">
										<span aria-hidden="true">Anterior</span>
									</a>
								</li>
		';
	}

	$diferenciapagina = 3;
	
	/*********MENOS********/
	if($pagina==2){
		$html .= '
		                		<li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#">
			                            '.($pagina-1).'
			                        </a>
			                    </li>
		';
	}else if($pagina==3){
		$html .= '
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-2).');" href="#"">
			                            '.($pagina-2).'
			                        </a>
			                    </li>
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#"">
			                            '.($pagina-1).'
			                        </a>
			                    </li>
	';
	}else if($pagina>=4){
		$html .= '
		                		<li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-3).');" href="#"">
			                            '.($pagina-3).'
			                        </a>
			                    </li>
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-2).');" href="#"">
			                            '.($pagina-2).'
			                        </a>
			                    </li>
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#"">
			                            '.($pagina-1).'
			                        </a>
			                    </li>
		';
	} 

	/*********MAS********/
	$opcionmas = $pagina+3;
	if($paginas==0){
		$opcionmas = $paginas;
	}else if($paginas>=1 and $paginas<=4){
		$opcionmas = $paginas;
	}
	
	for ($x=$pagina;$x<=$opcionmas;$x++) {
		$html .= '
			                    <li class="page-item 
		';

		if ($x == $pagina){ 
			$html .= '"active"';
		}

		$html .= '">';

		$html .= '
			                        <a class="page-link" onclick="paginacion1('.($x).');" href="#"">'.$x.'</a>
			                    </li>
		';
	}

	if ($pagina < $paginas) {
		$html .= '
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina+1).');" href="#"">
			                            <span aria-hidden="true">Siguiente</span>
			                        </a>
			                    </li>
		';
	}

	$html .= '

						</ul>
					</nav>
				</div>
	        </nav>
	    </div>
	';

	$datos = [
		"estatus"	=> "ok",
		"html"	=> $html,
		"sql2"	=> $sql2,
	];
	echo json_encode($datos);
}

if($condicion=='cambio_estatus1'){
	$id = $_POST['id'];
	$estatus = $_POST['estatus'];

	$sql2 = "SELECT * FROM datos_pasantes WHERE id = ".$id." and (estatus = 2 or estatus = 3)";
	$proceso2 = mysqli_query($conexion,$sql2);
	$contador2 = mysqli_num_rows($proceso2);
	if($contador2==0){
		$sql3 = "SELECT * FROM datos_pasantes WHERE id = ".$id;
		$proceso3 = mysqli_query($conexion,$sql3);
		while($row3 = mysqli_fetch_array($proceso3)) {
			$id_usuarios = $row3["id_usuarios"];
			$sql4 = "SELECT * FROM usuarios WHERE id = ".$id_usuarios;
			$proceso4 = mysqli_query($conexion,$sql4);
			while($row4 = mysqli_fetch_array($proceso4)) {
				$correo_personal = $row4["correo_personal"];
			}
		}
		$sql1 = "UPDATE datos_pasantes SET estatus = ".$estatus." WHERE id = ".$id;
		$proceso1 = mysqli_query($conexion,$sql1);
		if($estatus==2){
			$html = '';

			/***************APARTADO DE CORREO*****************/
			$mail = new PHPMailer(true);
			try {
			    $mail->isSMTP();
			    $mail->CharSet = "UTF-8";
			    $mail->Host = 'mail.camaleonpruebas.com';
			    $mail->SMTPAuth = true;
			    $mail->Username = 'test1@camaleonpruebas.com';
			    $mail->Password = 'juanmaldonado123';
			    $mail->SMTPSecure = 'tls';
			    $mail->Port = 587;

			    $mail->setFrom('test1@camaleonpruebas.com');
			    $mail->addAddress($correo_personal);
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

			    $mail->isHTML(true);
			    $mail->Subject = 'Aprobacion Camaleon!';
			    $mail->Body    = $html;
			    $mail->AltBody = 'Este es el contenido del mensaje en texto plano';
			 
			    $mail->send();
			} catch (Exception $e) {}
			/**************************************************/

		}
		$datos = [
			"estatus"	=> "ok",
		];
	}else{
		$datos = [
			"estatus"	=> "repetidos",
		];
	}



	echo json_encode($datos);
}

if($condicion=='aceptar_pasante1'){
	$turno = $_POST["turno"];
	$sede = $_POST["sede"];
	$usuario_id = $_POST["usuario_id"];
	$pasante_id = $_POST["pasante_id"];

	$sql1 = "SELECT * FROM usuarios WHERE id = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($proceso1)) {
		$estatus_pasantes = $row1["estatus_pasantes"];
		$usuario_estatus_modelo = $row1["estatus_modelo"];
		$correo = $row1["correo_personal"];
		$telefono = $row1["telefono"];
		$id_pais = $row1["id_pais"];

		if($usuario_estatus_modelo>=1){

			$datos = [
				"estatus"	=> "error",
				"msg"	=> "Ya tiene un perfil de Modelo Creado!",
			];
		
			echo json_encode($datos);
			exit;

		}else if($usuario_estatus_modelo==0){
			$sql2 = "SELECT * FROM datos_modelos WHERE id_usuarios = ".$usuario_id;
			$proceso2 = mysqli_query($conexion,$sql2);
			$conteo2 = mysqli_num_rows($proceso2);

			if($conteo2>=1){
				$datos = [
					"estatus"	=> "error",
					"msg"	=> "Ya tiene un perfil de Modelo Creado!",
				];
				echo json_encode($datos);
				exit;
			}else if($conteo2==0){
				$sql3 = "INSERT INTO datos_modelos (id_usuarios,turno,sede,estatus,fecha_creacion) VALUES (".$usuario_id.",".$turno.",".$sede.",2,'".$fecha_creacion."')";
				$proceso3 = mysqli_query($conexion,$sql3);
				$sql4 = "UPDATE usuarios SET estatus_pasantes = 1, estatus_modelo = 1, fecha_modificacion = '".$fecha_modificacion."' WHERE id = ".$usuario_id;
				$proceso4 = mysqli_query($conexion,$sql4);
				$sql5 = "UPDATE datos_pasantes SET estatus = 2, fecha_modificacion = '".$fecha_modificacion."' WHERE id = ".$pasante_id;
				$proceso5 = mysqli_query($conexion,$sql5);

				$sql6 = "SELECT * FROM paises WHERE id = ".$id_pais;
				$proceso6 = mysqli_query($conexion,$sql6);
				while($row6 = mysqli_fetch_array($proceso6)) {
					$codigo_pais = $row6["codigo"];
				}

				/*****************APARTADO DE WHATSAPP************/
				$msg = "Felicitaciones tu perfil ha sido aprobado para formar parte de la familia Camaleón!
				El siguiente paso es completar tu formulario de contacto, puedes ingresar al sistema en el siguiente link https://www.camaleonmg.com";
				$phone = $codigo_pais.$telefono;
				$result = sendMessage($phone,$msg);
				if($result !== false){
					if($result->sent == 1){}else{}
				}else{
					var_dump($result);
				}
				/***************************************************/

				/***************APARTADO DE CORREO*****************/
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
				            Felicitaciones tu perfil ha sido aprobado para iniciar como modelo.
				            El siguiente paso es completar tu formulario de contacto, puedes ingresar al sistema en el siguiente link https://www.camaleonmg.com
				        </h2>
				    ";

				    $mail->isHTML(true);
				    $mail->Subject = 'Aprobacion Camaleon!';
				    $mail->Body    = $html;
				    $mail->AltBody = 'Este es el contenido del mensaje en texto plano';
				 
				    $mail->send();
				} catch (Exception $e) {}
				/**************************************************/

				$datos = [
					"estatus"	=> "ok",
					"msg"	=> "Estatus Cambiado!",
				];
				echo json_encode($datos);
				exit;
			}
		}
	}
}

if($condicion=='rechazar_pasante1'){
	$usuario_id = $_POST["usuario_id"];
	$pasante_id = $_POST["pasante_id"];

	$sql1 = "SELECT * FROM usuarios WHERE id = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($proceso1)) {
		$estatus_pasantes = $row1["estatus_pasantes"];
		$usuario_estatus_modelo = $row1["estatus_modelo"];

		if($usuario_estatus_modelo>=1){

			$datos = [
				"estatus"	=> "error",
				"msg"	=> "Ya tiene un perfil de Modelo Creado!",
			];
		
			echo json_encode($datos);
			exit;

		}else if($usuario_estatus_modelo==0){
			$sql2 = "SELECT * FROM datos_modelos WHERE id_usuarios = ".$usuario_id;
			$proceso2 = mysqli_query($conexion,$sql2);
			$conteo2 = mysqli_num_rows($proceso2);

			if($conteo2>=1){
				$datos = [
					"estatus"	=> "error",
					"msg"	=> "Ya tiene un perfil de Modelo Creado!",
				];
				echo json_encode($datos);
				exit;
			}else if($conteo2==0){
				$sql5 = "UPDATE datos_pasantes SET estatus = 3 WHERE id = ".$pasante_id;
				$proceso5 = mysqli_query($conexion,$sql5);
				$datos = [
					"estatus"	=> "ok",
					"msg"	=> "Estatus Cambiado!",
				];
				echo json_encode($datos);
				exit;
			}
		}
	}
}

if($condicion=='consulta1'){
	$usuario_id = $_POST['usuario_id'];

	$sql1 = "SELECT us.nombre1 as nombre1, us.nombre2 as nombre2, us.apellido1 as apellido1, us.apellido2 as apellido2, us.documento_tipo as documento_tipo, dti.nombre as documento_tipo_nombre, us.documento_numero as documento_numero, us.correo_personal as correo_personal, us.telefono as telefono, us.genero as genero, us.direccion as direccion, us.id_pais as id_pais, pa.nombre as pais_nombre, dpa.id as pasante_id, dpa.fecha_creacion as fecha_creacion, dpa.sede as sede, dpa.estatus as estatus, dpa.turno as turno, se.nombre as sede_nombre, tu.nombre as turno_nombre FROM usuarios us 
	INNER JOIN datos_pasantes dpa 
	ON us.id = dpa.id_usuarios 
	INNER JOIN documento_tipo as dti 
	ON us.documento_tipo = dti.id 
	INNER JOIN paises as pa 
	ON us.id_pais = pa.id 
	INNER JOIN sedes as se 
	ON dpa.sede = se.id 
	INNER JOIN turnos as tu 
	ON dpa.turno = tu.id 
	WHERE us.id = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);
	$contador1 = mysqli_num_rows($proceso1);
	if($contador1==0){
		$datos = [
			"sql1"	=> $sql1,
			"estatus"	=> "error",
			"msg"	=> "No existe dicho registro",
		];
	}else{
		while($row1 = mysqli_fetch_array($proceso1)) {
			$nombre1 = $row1["nombre1"];
			$nombre2 = $row1["nombre2"];
			$apellido1 = $row1["apellido1"];
			$apellido2 = $row1["apellido2"];
			$documento_tipo = $row1["documento_tipo"];
			$documento_tipo_nombre = $row1["documento_tipo_nombre"];
			$documento_numero = $row1["documento_numero"];
			$correo_personal = $row1["correo_personal"];
			$telefono = $row1["telefono"];
			$genero = $row1["genero"];
			$direccion = $row1["direccion"];
			$id_pais = $row1["id_pais"];
			$pais_nombre = $row1["pais_nombre"];
			$pasante_id = $row1["pasante_id"];
			$fecha_creacion = $row1["fecha_creacion"];
			$sede = $row1["sede"];
			$estatus = $row1["estatus"];
			$turno = $row1["turno"];
			$sede_nombre = $row1["sede_nombre"];
		}
		$datos = [
			"estatus"	=> "ok",
			"sql1"	=> $sql1,
			"nombre1"	=> $nombre1,
			"nombre2"	=> $nombre2,
			"apellido1"	=> $apellido1,
			"apellido2"	=> $apellido2,
			"documento_tipo"	=> $documento_tipo,
			"documento_tipo_nombre"	=> $documento_tipo_nombre,
			"documento_numero"	=> $documento_numero,
			"correo_personal"	=> $correo_personal,
			"telefono"	=> $telefono,
			"genero"	=> $genero,
			"direccion"	=> $direccion,
			"id_pais"	=> $id_pais,
			"pais_nombre"	=> $pais_nombre,
			"pasante_id"	=> $pasante_id,
			"fecha_creacion"	=> $fecha_creacion,
			"sede"	=> $sede,
			"turno"	=> $turno,
			"sede_nombre"	=> $sede_nombre,
		];
	}



	echo json_encode($datos);
}

if($condicion=='modificar1'){
	$pasante_id = $_POST['pasante_id'];
	$usuario_id = $_POST['usuario_id'];
	$nombre1 = $_POST['nombre1'];
	$nombre2 = $_POST['nombre2'];
	$apellido1 = $_POST['apellido1'];
	$apellido2 = $_POST['apellido2'];
	$documento_tipo = $_POST['documento_tipo'];
	$documento_numero = $_POST['documento_numero'];
	$correo = $_POST['correo'];
	$telefono = $_POST['telefono'];
	$genero = $_POST['genero'];
	$direccion = $_POST['direccion'];
	$pais = $_POST['pais'];
	$sede = $_POST['sede'];
	$turno = $_POST['turno'];

	$sql1 = "UPDATE usuarios SET nombre1 = '".$nombre1."', nombre2 = '".$nombre2."', apellido1 = '".$apellido1."', apellido2 = '".$apellido2."', documento_tipo = ".$documento_tipo.", documento_numero = '".$documento_numero."', correo_personal = '".$correo."', telefono = '".$telefono."', genero = ".$genero.", direccion = '".$direccion."', id_pais = ".$pais.", fecha_modificacion = '".$fecha_creacion."' WHERE id = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$sql2 = "UPDATE datos_pasantes SET sede = ".$sede.", turno = ".$turno.", fecha_modificacion = '".$fecha_creacion."' WHERE id = ".$pasante_id;
	$proceso2 = mysqli_query($conexion,$sql2);

	$datos = [
		"estatus"	=> "ok",
		"sql1"	=> $sql1,
		"sql2"	=> $sql2,
		"msg" => "Se ha modificado exitosamente",
	];
	echo json_encode($datos);
}

if($condicion=='aceptar_pasante2'){
	$usuario_id = $_POST["usuario_id"];

	$sql1 = "SELECT * FROM usuarios WHERE id = ".$usuario_id." LIMIT 1";
	$proceso1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($proceso1)) {
		$estatus_pasantes = $row1["estatus_pasantes"];
		$usuario_estatus_modelo = $row1["estatus_modelo"];
		$correo = $row1["correo_personal"];
		$telefono = $row1["telefono"];
		$id_pais = $row1["id_pais"];

		$sql2 = "SELECT * FROM datos_modelos WHERE id_usuarios = ".$usuario_id;
		$proceso2 = mysqli_query($conexion,$sql2);
		$contador2 = mysqli_num_rows($proceso2);
		if($contador2>=1){
			$sql4 = "UPDATE datos_modelos SET estatus = 2, fecha_modificacion = '".$fecha_modificacion."' WHERE id_usuarios = ".$usuario_id;
			$proceso4 = mysqli_query($conexion,$sql4);
		}else if($contador2==0){
			$sql3 = "SELECT * FROM datos_pasantes WHERE id_usuarios = ".$usuario_id." LIMIT 1";
			$proceso3 = mysqli_query($conexion,$sql3);
			while($row3 = mysqli_fetch_array($proceso3)) {
				$pasante_sede = $row3["sede"];
				$pasante_turno = $row3["turno"];
			}
			$sql4 = "INSERT INTO datos_modelos (id_usuarios,turno,estatus,sede,fecha_creacion) VALUES ($usuario_id,$pasante_turno,2,$pasante_sede,'".$fecha_creacion."')";
			$proceso4 = mysqli_query($conexion,$sql4);
		}
		
		$sql5 = "UPDATE datos_pasantes SET estatus = 2, fecha_modificacion = '".$fecha_modificacion."' WHERE id_usuarios = ".$usuario_id;
		$proceso5 = mysqli_query($conexion,$sql5);

		$sql6 = "UPDATE usuarios SET estatus_modelo = 1, fecha_modificacion = '".$fecha_modificacion."' WHERE id = ".$usuario_id;
		$proceso6 = mysqli_query($conexion,$sql6);

		$sql7 = "SELECT * FROM paises WHERE id = ".$id_pais;
		$proceso7 = mysqli_query($conexion,$sql7);
		while($row7 = mysqli_fetch_array($proceso7)) {
			$codigo_pais = $row7["codigo"];
		}

		/*****************APARTADO DE WHATSAPP************/
		$msg = "Felicitaciones tu perfil ha sido aprobado para formar parte de la familia Camaleón!
		El siguiente paso es completar tu formulario de contacto, puedes ingresar al sistema en el siguiente link https://www.camaleonmg.com";
		$phone = $codigo_pais.$telefono;
		$result = sendMessage($phone,$msg);
		if($result !== false){
			if($result->sent == 1){}else{}
		}else{
			var_dump($result);
		}
		/***************************************************/

		/***************APARTADO DE CORREO*****************/
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
				Felicitaciones tu perfil ha sido aprobado para iniciar como modelo.
				El siguiente paso es completar tu formulario de contacto, puedes ingresar al sistema en el siguiente link https://www.camaleonmg.com
				</h2>
			";

			$mail->isHTML(true);
			$mail->Subject = 'Aprobacion Camaleon!';
			$mail->Body    = $html;
			$mail->AltBody = 'Este es el contenido del mensaje en texto plano';		 
			$mail->send();
		} catch (Exception $e) {}
		/**************************************************/

		$datos = [
			"estatus"	=> "ok",
			"msg"	=> "Estatus Cambiado!",
		];
		echo json_encode($datos);
		exit;
	}
}

if($condicion=='rechazar_pasante2'){
	$usuario_id = $_POST["usuario_id"];

	$sql1 = "SELECT * FROM usuarios WHERE id = ".$usuario_id." LIMIT 1";
	$proceso1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($proceso1)) {
		$estatus_pasantes = $row1["estatus_pasantes"];
		$usuario_estatus_modelo = $row1["estatus_modelo"];
		$correo = $row1["correo_personal"];
		$telefono = $row1["telefono"];
		$id_pais = $row1["id_pais"];

		$sql7 = "SELECT * FROM datos_modelos WHERE id_usuarios = ".$usuario_id;
		$proceso7 = mysqli_query($conexion,$sql7);
		$contador7 = mysqli_num_rows($proceso7);
		if($contador7>=1){
			$sql4 = "UPDATE datos_modelos SET estatus = 3, fecha_modificacion = '".$fecha_modificacion."' WHERE id_usuarios = ".$usuario_id;
			$proceso4 = mysqli_query($conexion,$sql4);
		}

		$sql5 = "UPDATE datos_pasantes SET estatus = 3, fecha_modificacion = '".$fecha_modificacion."' WHERE id_usuarios = ".$usuario_id;
		$proceso5 = mysqli_query($conexion,$sql5);

		$sql6 = "SELECT * FROM paises WHERE id = ".$id_pais;
		$proceso6 = mysqli_query($conexion,$sql6);
		while($row6 = mysqli_fetch_array($proceso6)) {
			$codigo_pais = $row6["codigo"];
		}

		/*****************APARTADO DE WHATSAPP************/
		$msg = "Tu solicitud para modelo en nuestra empresa ha sido rechazada";
		$phone = $codigo_pais.$telefono;
		$result = sendMessage($phone,$msg);
		if($result !== false){
			if($result->sent == 1){}else{}
		}else{
			var_dump($result);
		}
		/***************************************************/

		/***************APARTADO DE CORREO*****************/
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
				Tu solicitud para modelo en nuestra empresa ha sido rechazada
				</h2>
			";

			$mail->isHTML(true);
			$mail->Subject = 'Aprobacion Camaleon!';
			$mail->Body    = $html;
			$mail->AltBody = 'Este es el contenido del mensaje en texto plano';		 
			$mail->send();
		} catch (Exception $e) {}
		/**************************************************/

		$datos = [
			"estatus"	=> "ok",
			"msg"	=> "Estatus Cambiado!",
		];
		echo json_encode($datos);
		exit;
	}
}

if($condicion=='table2'){
	$pagina = $_POST["pagina"];
	$consultasporpagina = $_POST["consultasporpagina"];
	$filtrado = $_POST["filtrado"];
	$link1 = $_POST["link1"];
	$sede = $_POST["sede"];
	$link1 = explode("/",$link1);
	$link1 = $link1[3];

	if($pagina==0 or $pagina==''){
		$pagina = 1;
	}

	if($consultasporpagina==0 or $consultasporpagina==''){
		$consultasporpagina = 10;
	}

	if($filtrado!=''){
		$filtrado = ' and (nombre1 LIKE "%'.$filtrado.'%" or nombre2 LIKE "%'.$filtrado.'%" or apellido1 LIKE "%'.$filtrado.'%" or apellido2 LIKE "%'.$filtrado.'%" or documento_numero LIKE "%'.$filtrado.'%" or us.correo_personal LIKE "%'.$filtrado.'%" or telefono LIKE "%'.$filtrado.'%")';
	}

	if($sede!=''){
		$sede = ' and (dpa.sede = '.$sede.') ';
	}

	$limit = $consultasporpagina;
	$offset = ($pagina - 1) * $consultasporpagina;

	$sql1 = "SELECT 
		us.id as usuario_id,
		dpa.id as pasante_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_pasantes as estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		dpa.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_pasantes dpa
		ON us.id = dpa.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN paises pa
		ON us.id_pais = pa.id
		INNER JOIN sedes se
		ON dpa.sede = se.id 
		WHERE us.id != 0 
		".$filtrado." 
		".$sede."
	";

	$sql2 = "SELECT 
		us.id as usuario_id,
		dpa.id as pasante_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_pasantes as estatus,
		dpa.estatus as pasantes_estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		dpa.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_pasantes dpa
		ON us.id = dpa.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON dpa.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON pa.id = us.id_pais
		WHERE us.id != 0 
		".$filtrado." 
		".$sede." 
		ORDER BY dpa.fecha_creacion DESC LIMIT ".$limit." OFFSET ".$offset."
	";

	$proceso1 = mysqli_query($conexion,$sql1);
	$proceso2 = mysqli_query($conexion,$sql2);
	$conteo1 = mysqli_num_rows($proceso1);
	$paginas = ceil($conteo1 / $consultasporpagina);

	$html = '';

	$html .= '
		<div class="col-xs-12">
	        <table class="table table-bordered">
	            <thead>
	            <tr>
	                <th class="text-center">T Doc</th>
	                <th class="text-center">N Doc</th>
	                <th class="text-center">Nombre</th>
	                <th class="text-center">Género</th>
	                <th class="text-center">Correo</th>
	                <th class="text-center">Teléfono</th>
	                <th class="text-center">Estatus</th>
	                <th class="text-center">Sede</th>
	                <th class="text-center">Ingreso</th>
	            </tr>
	            </thead>
	            <tbody>
	';
	if($conteo1>=1){
		while($row2 = mysqli_fetch_array($proceso2)) {
			if($row2["pasantes_estatus"]==1){
				$pasante_estatus = "Proceso";
			}else if($row2["pasantes_estatus"]==2){
				$pasante_estatus = "Aceptado";
			}else if($row2["pasantes_estatus"]==3){
				$pasante_estatus = "Rechazado";
			}
			$html .= '
		                <tr id="tr_'.$row2["pasante_id"].'">
		                    <td style="text-align:center;">'.$row2["documento_tipo"].'</td>
		                    <td style="text-align:center;">'.$row2["documento_numero"].'</td>
		                    <td>'.$row2["nombre1"]." ".$row2["nombre2"]." ".$row2["apellido1"]." ".$row2["apellido2"].'</td>
		                    <td style="text-align:center;">'.$row2["genero"].'</td>
		                    <td style="text-align:center;">'.$row2["correo"].'</td>
		                    <td style="text-align:center;">'.$row2["telefono"].'</td>
		                    <td  style="text-align:center;">'.$pasante_estatus.'</td>
		                    <td style="text-align:center;">'.$row2["sede"].'</td>
		                    <td nowrap="nowrap">'.$row2["fecha_creacion"].'</td>
						</tr>
			';
		}
	}else{
		$html .= '<tr><td colspan="10" class="text-center" style="font-weight:bold;font-size:20px;">Sin Resultados</td></tr>';
	}

	$html .= '
	            </tbody>
	        </table>
	        <nav>
	            <div class="row">
	                <div class="col-xs-12 col-sm-4 text-center">
	                    <p>Mostrando '.$consultasporpagina.' de '.$conteo1.' Datos disponibles</p>
	                </div>
	                <div class="col-xs-12 col-sm-4 text-center">
	                    <p>Página '.$pagina.' de '.$paginas.' </p>
	                </div> 
	                <div class="col-xs-12 col-sm-4">
			            <nav aria-label="Page navigation" style="float:right; padding-right:2rem;">
							<ul class="pagination">
	';
	
	if ($pagina > 1) {
		$html .= '
								<li class="page-item">
									<a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#">
										<span aria-hidden="true">Anterior</span>
									</a>
								</li>
		';
	}

	$diferenciapagina = 3;
	
	/*********MENOS********/
	if($pagina==2){
		$html .= '
		                		<li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#">
			                            '.($pagina-1).'
			                        </a>
			                    </li>
		';
	}else if($pagina==3){
		$html .= '
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-2).');" href="#"">
			                            '.($pagina-2).'
			                        </a>
			                    </li>
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#"">
			                            '.($pagina-1).'
			                        </a>
			                    </li>
	';
	}else if($pagina>=4){
		$html .= '
		                		<li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-3).');" href="#"">
			                            '.($pagina-3).'
			                        </a>
			                    </li>
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-2).');" href="#"">
			                            '.($pagina-2).'
			                        </a>
			                    </li>
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina-1).');" href="#"">
			                            '.($pagina-1).'
			                        </a>
			                    </li>
		';
	} 

	/*********MAS********/
	$opcionmas = $pagina+3;
	if($paginas==0){
		$opcionmas = $paginas;
	}else if($paginas>=1 and $paginas<=4){
		$opcionmas = $paginas;
	}
	
	for ($x=$pagina;$x<=$opcionmas;$x++) {
		$html .= '
			                    <li class="page-item 
		';

		if ($x == $pagina){ 
			$html .= '"active"';
		}

		$html .= '">';

		$html .= '
			                        <a class="page-link" onclick="paginacion1('.($x).');" href="#"">'.$x.'</a>
			                    </li>
		';
	}

	if ($pagina < $paginas) {
		$html .= '
			                    <li class="page-item">
			                        <a class="page-link" onclick="paginacion1('.($pagina+1).');" href="#"">
			                            <span aria-hidden="true">Siguiente</span>
			                        </a>
			                    </li>
		';
	}

	$html .= '

						</ul>
					</nav>
				</div>
	        </nav>
	    </div>
	';

	$datos = [
		"estatus"	=> "ok",
		"html"	=> $html,
		"sql2"	=> $sql2,
	];
	echo json_encode($datos);
}

?>