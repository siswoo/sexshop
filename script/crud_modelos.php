<?php
@session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../resources/PHPMailer/PHPMailer/src/Exception.php';
require '../resources/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../resources/PHPMailer/PHPMailer/src/SMTP.php';
include('conexion.php');
include('conexion2.php');
include('../js/funciones1.php');
$condicion = $_POST["condicion"];
$datetime = date('Y-m-d H:i:s');
$empresa = $_SESSION["camaleonapp_empresa"];
$fecha_creacion = date('Y-m-d');
$fecha_modificacion = date('Y-m-d');
$responsable = $_SESSION['camaleonapp_id'];

if($condicion=='consultar_personal1'){
	$id = $_POST['id'];

	$sql1 = "SELECT * FROM usuarios WHERE id = ".$id;
	$proceso1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($proceso1)) {
		$nombre = $row1["nombre1"]." ".$row1["nombre2"]." ".$row1["apellido1"]." ".$row1["apellido2"];
		$documento_tipo = $row1["documento_tipo"];
		$documento_numero = $row1["documento_numero"];
		$correo_empresa = $row1["correo_empresa"];
		$correo_personal = $row1["correo_personal"];
		$telefono = $row1["telefono"];
		$genero = $row1["genero"];
		$sql2 = "SELECT * FROM datos_modelos WHERE id_usuarios = ".$id;
		$proceso2 = mysqli_query($conexion,$sql2);
		while($row2 = mysqli_fetch_array($proceso2)) {
			$banco_cedula = $row2["banco_cedula"];
			$banco_nombre = $row2["banco_nombre"];
			$banco_tipo = $row2["banco_tipo"];
			$banco_numero = $row2["banco_numero"];
			$banco_banco = $row2["banco_banco"];
			$banco_bcpp = $row2["banco_bcpp"];
			$banco_tipo_documento = $row2["banco_tipo_documento"];
			$altura = $row2["altura"];
			$peso = $row2["peso"];
			$tpene = $row2["tpene"];
			$tsosten = $row2["tsosten"];
			$tbusto = $row2["tbusto"];
			$tcintura = $row2["tcintura"];
			$tcaderas = $row2["tcaderas"];
			$tipo_cuerpo = $row2["tipo_cuerpo"];
			$pvello = $row2["pvello"];
			$color_cabello = $row2["color_cabello"];
			$color_ojos = $row2["color_ojos"];
			$ptattu = $row2["ptattu"];
			$ppiercing = $row2["ppiercing"];
			$turno = $row2["turno"];
			$sede = $row2["sede"];
			$estatus = $row2["estatus"];
			$fecha_creacion = $row2["fecha_creacion"];
		}

		$sql3 = "SELECT * FROM documento_tipo WHERE id = ".$documento_tipo;
		$proceso3 = mysqli_query($conexion,$sql3);
		while($row3 = mysqli_fetch_array($proceso3)) {
			$documento_tipo_nombre = $row3["nombre"];
		}

		$sql4 = "SELECT * FROM sedes WHERE id = ".$sede;
		$proceso4 = mysqli_query($conexion,$sql4);
		while($row4 = mysqli_fetch_array($proceso4)) {
			$sedes_nombre = $row4["nombre"];
		}

		$sql5 = "SELECT * FROM documento_tipo WHERE id = ".$banco_tipo_documento;
		$proceso5 = mysqli_query($conexion,$sql5);
		while($row5 = mysqli_fetch_array($proceso5)) {
			$banco_tipo_documento_nombre = $row5["nombre"];
		}
	}

	$datos = [
		"estatus"	=> "ok",
		"nombre" => $nombre,
		"documento_tipo" => $documento_tipo_nombre,
		"documento_numero" => $documento_numero,
		"correo_personal" => $correo_personal,
		"telefono" => $telefono,
		"genero" => $genero,
		"sede" => $sedes_nombre,

		"banco_cedula" => $banco_cedula,
		"banco_nombre" => $banco_nombre,
		"banco_tipo" => $banco_tipo,
		"banco_numero" => $banco_numero,
		"banco_banco" => $banco_banco,
		"banco_bcpp" => $banco_bcpp,
		"banco_tipo_documento" => $banco_tipo_documento_nombre,
		"altura" => $altura,
		"peso" => $peso,
		"tpene" => $tpene,
		"tsosten" => $tsosten,
		"tbusto" => $tbusto,
		"tcintura" => $tcintura,
		"tcaderas" => $tcaderas,
		"tipo_cuerpo" => $tipo_cuerpo,
		"pvello" => $pvello,
		"color_cabello" => $color_cabello,
		"color_ojos" => $color_ojos,
		"ptattu" => $ptattu,
		"ppiercing" => $ppiercing,
		"turno" => $turno,
		"estatus" => $estatus,
		"fecha_creacion" => $fecha_creacion,
	];
	echo json_encode($datos);
}

if($condicion=='table1'){
	$pagina = $_POST["pagina"];
	$consultasporpagina = $_POST["consultasporpagina"];
	$filtrado = $_POST["filtrado"];
	$link1 = $_POST["link1"];
	$sede = $_POST["sede"];
	$m_estatus = $_POST["m_estatus"];
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
		$sede = ' and (mo.sede = '.$sede.') ';
	}

	if($m_estatus!=''){
		$m_estatus = " and (mo.estatus = ".$m_estatus.")";
	}

	$limit = $consultasporpagina;
	$offset = ($pagina - 1) * $consultasporpagina;

	$sql1 = "SELECT 
		us.id as usuario_id,
		mo.id as modelo_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_modelo as estatus,
		mo.estatus as modelo_estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		mo.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_modelos mo
		ON us.id = mo.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON mo.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON pa.id = us.id_pais
		WHERE us.id != 0 
		".$filtrado." 
		".$sede."
		".$m_estatus." 
	";
	
	$sql2 = "SELECT 
		us.id as usuario_id,
		mo.id as modelo_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_modelo as estatus,
		mo.estatus as modelo_estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		mo.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_modelos mo
		ON us.id = mo.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON mo.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON pa.id = us.id_pais
		WHERE us.id != 0 
		".$filtrado." 
		".$sede." 
		".$m_estatus." 
		ORDER BY mo.id DESC LIMIT ".$limit." OFFSET ".$offset."
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
	                <!--<th class="text-center">Correo</th>-->
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
			if($row2["modelo_estatus"]==1){
				$modelo_estatus = "Proceso";
			}else if($row2["modelo_estatus"]==2){
				$modelo_estatus = "Aceptado";
			}else if($row2["modelo_estatus"]==3){
				$modelo_estatus = "Rechazado";
			}
			$html .= '
		                <tr id="tr_'.$row2["modelo_id"].'">
		                    <td style="text-align:center;">'.$row2["documento_tipo"].'</td>
		                    <td style="text-align:center;">'.$row2["documento_numero"].'</td>
		                    <td>'.$row2["nombre1"]." ".$row2["nombre2"]." ".$row2["apellido1"]." ".$row2["apellido2"].'</td>
		                    <td style="text-align:center;">'.$row2["genero"].'</td>
		                    <!--<td style="text-align:center;">'.$row2["correo"].'</td>-->
		                    <td style="text-align:center;">'.$row2["telefono"].'</td>
		                    <td  style="text-align:center;">'.$modelo_estatus.'</td>
		                    <td style="text-align:center;">'.$row2["sede"].'</td>
		                    <td nowrap="nowrap">'.$row2["fecha_creacion"].'</td>
		                    <td class="text-center" nowrap="nowrap">
		                    	<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#personales1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-user-edit"></i></button>
		                    	<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#emergencia1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-first-aid"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#corporales1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-child"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#documentos1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="far fa-folder-open"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#fotos1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-images"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#bancarios1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-money-check-alt"></i></button>
								<!--
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#modificar1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="far fa-user"></i></button>
								-->
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#empresa1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="far fa-building"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#subirdocumentos1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-cloud-upload-alt"></i></button>
			';

			if($row2["modelo_estatus"]==1){
				$html .= '
								<button type="button" class="btn btn-success" onclick="aceptar1('.$row2["usuario_id"].');">A</button>
								<button type="button" class="btn btn-danger" onclick="rechazar1('.$row2["usuario_id"].');">R</button>
				';
			}else if($row2["modelo_estatus"]==2){
				$html .= '
								<button type="button" class="btn btn-danger" onclick="rechazar1('.$row2["usuario_id"].');">Rechazar</button>
				';
			}else if($row2["modelo_estatus"]==3){
				$html .= '

								<button type="button" class="btn btn-success" onclick="aceptar1('.$row2["usuario_id"].');">Aceptar</button>
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

if($condicion=='solicitar1'){
	$modelo_id = $_POST["modelo_id"];
	$usuario_id = $_POST["usuario_id"];
	$cambiar = $_POST["cambiar"];
	$texto = $_POST["texto"];
	$modulo_id = $_POST["modulo_id"];

	$sql1 = "SELECT us.id_empresa as usuario_empresa, dmo.sede as modelo_sede FROM usuarios us 
	INNER JOIN datos_modelos dmo 
	ON us.id = dmo.id_usuarios";
	$proceso1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($proceso1)) {
		$usuario_empresa = $row1["usuario_empresa"];
		$modelo_sede = $row1["modelo_sede"];
	}

	$sql2 = "INSERT INTO solicitudes (texto,id_modulos,id_cambiar,id_sedes,id_usuarios,id_empresas,responsable,estatus,fecha_creacion) VALUES 
	('$texto',$modulo_id,$cambiar,$modelo_sede,$usuario_id,$usuario_empresa,$responsable,1,'$fecha_creacion')";
	$proceso2 = mysqli_query($conexion,$sql2);

	$datos = [
		"estatus"	=> "ok",
		"sql1"	=> $sql1,
		"sql2"	=> $sql2,
		"msg"	=> "Se ha guardado la solicitud exitosamente!",
	];
	echo json_encode($datos);
}

if($condicion=='table2'){
	$pagina = $_POST["pagina"];
	$consultasporpagina = $_POST["consultasporpagina"];
	$filtrado = $_POST["filtrado"];
	$link1 = $_POST["link1"];
	$sede = $_POST["sede"];
	$m_estatus = $_POST["m_estatus"];
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
		$sede = ' and (mo.sede = '.$sede.') ';
	}

	if($m_estatus!=''){
		$m_estatus = " and (mo.estatus = ".$m_estatus.")";
	}

	$limit = $consultasporpagina;
	$offset = ($pagina - 1) * $consultasporpagina;

	$sql1 = "SELECT 
		us.id as usuario_id,
		mo.id as modelo_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_modelo as estatus,
		mo.estatus as modelo_estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		mo.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_modelos mo
		ON us.id = mo.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON mo.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON pa.id = us.id_pais
		WHERE us.id != 0 
		".$filtrado." 
		".$sede."
		".$m_estatus." 
	";
	
	$sql2 = "SELECT 
		us.id as usuario_id,
		mo.id as modelo_id,
		dti.nombre as documento_tipo,
		us.documento_numero as documento_numero,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		ge.nombre as genero,
		us.correo_personal as correo,
		us.telefono as telefono,
		us.estatus_modelo as estatus,
		mo.estatus as modelo_estatus,
		pa.nombre as pais,
		pa.codigo as pais_codigo,
		se.nombre as sede,
		se.id as id_sede,
		mo.fecha_creacion as fecha_creacion,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_modelos mo
		ON us.id = mo.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON mo.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON pa.id = us.id_pais
		WHERE us.id != 0 
		".$filtrado." 
		".$sede." 
		".$m_estatus." 
		ORDER BY mo.id DESC LIMIT ".$limit." OFFSET ".$offset."
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
	                <!--<th class="text-center">Correo</th>-->
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
			if($row2["modelo_estatus"]==1){
				$modelo_estatus = "Proceso";
			}else if($row2["modelo_estatus"]==2){
				$modelo_estatus = "Aceptado";
			}else if($row2["modelo_estatus"]==3){
				$modelo_estatus = "Rechazado";
			}
			$html .= '
		                <tr id="tr_'.$row2["modelo_id"].'">
		                    <td style="text-align:center;">'.$row2["documento_tipo"].'</td>
		                    <td style="text-align:center;">'.$row2["documento_numero"].'</td>
		                    <td>'.$row2["nombre1"]." ".$row2["nombre2"]." ".$row2["apellido1"]." ".$row2["apellido2"].'</td>
		                    <td style="text-align:center;">'.$row2["genero"].'</td>
		                    <td style="text-align:center;">'.$row2["telefono"].'</td>
		                    <td  style="text-align:center;">'.$modelo_estatus.'</td>
		                    <td style="text-align:center;">'.$row2["sede"].'</td>
		                    <td nowrap="nowrap">'.$row2["fecha_creacion"].'</td>
		                    <td class="text-center" nowrap="nowrap">
		                    	<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#personales1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-user-edit"></i></button>
		                    	<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#emergencia1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-first-aid"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#corporales1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-child"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#documentos1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="far fa-folder-open"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#fotos1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-images"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#bancarios1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="fas fa-money-check-alt"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#empresa1" onclick="editar1('.$row2["modelo_id"].','.$row2["usuario_id"].');"><i class="far fa-building"></i></button>
							</td>
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

if($condicion=='subir_fotos1'){
	$id_documentos = $_POST['id_documentos'];
	$id_modelos = $_POST['id_modelos'];
	$imagen_temporal = $_FILES['file']['tmp_name'];
	$imagen_nombre = $_FILES['file']['name'];

	if(file_exists('../resources/documentos/modelos/archivos/'.$id_modelos)){}else{
    	mkdir('../resources/documentos/modelos/archivos/'.$id_modelos, 0777);
	}

	/***************FUNCIONES****************/
	function redimensionar_imagen($nombreimg, $rutaimg, $xmax, $ymax){
	    $ext = explode(".", $nombreimg);
	    $ext = $ext[count($ext)-1];

	    if($ext!="jpg" && $ext!="jpeg" && $ext!="png"){
	        echo 'error';
	        exit;
	    }

	    if($ext == "jpg" || $ext == "jpeg")  
	        $imagen = imagecreatefromjpeg($rutaimg);
	    elseif($ext == "png")  
	        $imagen = imagecreatefrompng($rutaimg);

	    $x = imagesx($imagen);  
	    $y = imagesy($imagen);  
	          
	    if($x <= $xmax && $y <= $ymax){
	        //echo "<center>Esta imagen ya esta optimizada para los maximos que deseas.<center>";
	        return $imagen;  
	    }
	      
	    if($x >= $y) {  
	        $nuevax = $xmax;  
	        $nuevay = $nuevax * $y / $x;  
	    }  
	    else {  
	        $nuevay = $ymax;  
	        $nuevax = $x / $y * $nuevay;  
	    }  

	    $img2 = imagecreatetruecolor($nuevax, $nuevay);
	    imagecopyresized($img2, $imagen, 0, 0, 0, 0, floor($nuevax), floor($nuevay), $x, $y);
	    //echo "<center>La imagen se ha optimizado correctamente.</center>";
	    return $img2;
	}
	/*******************************************/

	if($id_documentos==2){
	    $location = "../resources/documentos/modelos/archivos/".$id_modelos."/documento_identidad.jpg";
	}else if($id_documentos==8){
	    $location = "../resources/documentos/modelos/archivos/".$id_modelos."/foto_cedula_con_cara.jpg";
	}else if($id_documentos==9){
	    $location = "../resources/documentos/modelos/archivos/".$id_modelos."/foto_cedula_parte_frontal_cara.jpg";
	}else if($id_documentos==10){
	    $location = "../resources/documentos/modelos/archivos/".$id_modelos."/foto_cedula_parte_respaldo.jpg";
	}else if($id_documentos==12){
	    $imagen_nombre = $_FILES['file']['name'];
	    $extension = explode(".", $imagen_nombre);
	    $extension = $extension[count($extension)-1];
	    if($extension=='pdf'){}else if($extension=='jpg'){}else{
	        $extension='jpg';
	    }

	    $sql2 = "INSERT INTO modelos_documentos (id_documentos,id_usuarios,tipo,responsable,fecha_creacion) VALUES ('$id_documentos','$id_modelos','$extension',$responsable,'$fecha_creacion')";
	    $registro1 = mysqli_query($conexion,$sql2);
	    $id_modelos_documentos = mysqli_insert_id($conexion);

	    $location = "../resources/documentos/modelos/archivos/".$id_modelos."/extras_".$id_modelos_documentos.".jpg";
	}else{
	    echo 'error';
	    exit;
	}

	$imagen_nombre = $_FILES['file']['name'];
	$extension = explode(".", $imagen_nombre);
	$extension = $extension[count($extension)-1];

	if($extension == 'pdf'){
	    @unlink($location);
	    move_uploaded_file ($_FILES['file']['tmp_name'],$location);
	}else{
	    $imagen = getimagesize($_FILES['file']['tmp_name']);
	    $ancho = $imagen[0];
	    $alto = $imagen[1];

	    if($ancho>$alto){
	        //echo 'Mas ancho por Alto';
	        $imagen_optimizada = redimensionar_imagen($imagen_nombre,$imagen_temporal,1920,1080);
	        @unlink($location);
	        imagejpeg($imagen_optimizada, $location);
	    }else if($ancho<$alto){
	        //echo 'Mas Alto por Ancho';
	        $imagen_optimizada = redimensionar_imagen($imagen_nombre,$imagen_temporal,1080,1920);
	        @unlink($location);
	        imagejpeg($imagen_optimizada, $location);
	    }else{
	        //echo 'Cuadrado';
	        $imagen_optimizada = redimensionar_imagen($imagen_nombre,$imagen_temporal,1080,1080);
	        @unlink($location);
	        imagejpeg($imagen_optimizada, $location);
	    }
	}

	$sql3 = "DELETE FROM modelos_documentos WHERE id_documentos = ".$id_documentos." and id_modelos = ".$id_modelos;
    $eliminar1 = mysqli_query($conexion,$sql3);

    if($extension=='pdf'){}else if($extension=='jpg'){}else{
        $extension='jpg';
    }

    $sql2 = "INSERT INTO modelos_documentos (id_documentos,id_usuarios,tipo,responsable,fecha_creacion) VALUES ('$id_documentos','$id_modelos','$extension',$responsable,'$fecha_creacion')";
    $registro1 = mysqli_query($conexion,$sql2);
}

if($condicion=='ver_documentos1'){
	$html_documentos1='';
	$html_firma1='';
	$contador_extra1 = 1;
	$modelo_id = $_POST['usuario_id'];
	$sql2 = "SELECT * FROM modelos_documentos WHERE id_usuarios = ".$modelo_id;
	$consulta3 = mysqli_query($conexion,$sql2);
	while($row5 = mysqli_fetch_array($consulta3)) {
		$modelos_documentos_id = $row5['id'];
		$modelos_documentos_id_documento = $row5['id_documentos'];
		$modelos_documentos_tipo = $row5['tipo'];

		if($modelos_documentos_id_documento==1){
			$sql3 = "SELECT * FROM documentos WHERE id = ".$modelos_documentos_id_documento;
			$consulta4 = mysqli_query($conexion,$sql3);
			while($row6 = mysqli_fetch_array($consulta4)) {
				$html_firma1.='
					<div class="col-12 form-group text-center">
						<div>
							<button type="button" id="documento1" value="0" onclick="bottonMostrar1(this.id,value);" class="btn btn-info">Firma</button>
						</div>
						<img id="div_documento1" src="../resources/documentos/modelos/archivos/'.$modelo_id.'/firma_digital.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px; display:none;">
						<hr style="background-color:black;">
					</div>
				';
			}
		}

		if($modelos_documentos_id_documento==3){
			$sql3 = "SELECT * FROM documentos WHERE id = ".$modelos_documentos_id_documento;
			$consulta4 = mysqli_query($conexion,$sql3);
			while($row7 = mysqli_fetch_array($consulta4)) {
				if($modelos_documentos_tipo=='pdf'){
					$html_documentos1.= '
						<div class="col-12 form-group text-center">
							<div>
								<!--<label for="" style="text-transform: capitalize;">Pasaporte</label>-->
								<button type="button" id="documento2" value="0" onclick="bottonMostrar1(this.id,value);" class="btn btn-info">Pasaporte</button>
							</div>
							<embed id="div_documento2" src="../resources/documentos/modelos/archivos/'.$modelo_id.'/pasaporte.'.$modelos_documentos_tipo.'#toolbar=0" type="application/pdf" width="100%" height="300px" style="display:none;" />
							<hr style="background-color:black;">
						</div>
					';
				}else{
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div><label for="" style="text-transform: capitalize;">Pasaporte</label></div>
							<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/pasaporte.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
							<hr style="background-color:black;">
						</div>
					';
				}
			}
		}

		if($modelos_documentos_id_documento==4){
			$sql3 = "SELECT * FROM documentos WHERE id = ".$modelos_documentos_id_documento;
			$consulta4 = mysqli_query($conexion,$sql3);
			while($row6 = mysqli_fetch_array($consulta4)) {
				if($modelos_documentos_tipo=='pdf'){
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div>
								<!--<label for="" style="text-transform: capitalize;">RUT</label>-->
								<button type="button" id="documento3" value="0" onclick="bottonMostrar1(this.id,value);" class="btn btn-info">RUT</button>
							</div>
							<embed id="div_documento3" src="../resources/documentos/modelos/archivos/'.$modelo_id.'/rut.'.$modelos_documentos_tipo.'#toolbar=0" type="application/pdf" width="100%" height="300px" style="display:none;" />
								<hr style="background-color:black;">
						</div>
					';
				}else{
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div><label for="" style="text-transform: capitalize;">RUT</label></div>
							<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/rut.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
							<hr style="background-color:black;">
						</div>
					';
				}
			}
		}

		if($modelos_documentos_id_documento==5){
			$sql3 = "SELECT * FROM documentos WHERE id = ".$modelos_documentos_id_documento;
			$consulta4 = mysqli_query($conexion,$sql3);
			while($row6 = mysqli_fetch_array($consulta4)) {
				if($modelos_documentos_tipo=='pdf'){
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div>
								<!--<label for="" style="text-transform: capitalize;">Certificación Bancaria</label>-->
								<button type="button" id="documento4" value="0" onclick="bottonMostrar1(this.id,value);" class="btn btn-info">Certificación Bancaria</button>
							</div>
							<embed id="div_documento4" src="../resources/documentos/modelos/archivos/'.$modelo_id.'/certificacion_bancaria.'.$modelos_documentos_tipo.'#toolbar=0" type="application/pdf" width="100%" height="300px" style="display:none;" />
											    	<hr style="background-color:black;">
						</div>
					';
				}else{
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div><label for="" style="text-transform: capitalize;">Certificación Bancaria</label></div>
							<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/certificacion_bancaria.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
							<hr style="background-color:black;">
						</div>
					';
				}
			}
		}

		if($modelos_documentos_id_documento==6){
			$sql3 = "SELECT * FROM documentos WHERE id = ".$modelos_documentos_id_documento;
			$consulta4 = mysqli_query($conexion,$sql3);
			while($row6 = mysqli_fetch_array($consulta4)) {
				if($modelos_documentos_tipo=='pdf'){
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div>
								<!--<label for="" style="text-transform: capitalize;">EPS</label>-->
								<button type="button" id="documento5" value="0" onclick="bottonMostrar1(this.id,value);" class="btn btn-info">EPS</button>
							</div>
							<embed id="div_documento5" src="../resources/documentos/modelos/archivos/'.$modelo_id.'/eps.'.$modelos_documentos_tipo.'#toolbar=0" type="application/pdf" width="100%" height="300px" style="display:none;"/>
							<hr style="background-color:black;">
						</div>
					';
				}else{
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div><label for="" style="text-transform: capitalize;">EPS</label></div>
							<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/eps.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
							<hr style="background-color:black;">
						</div>
					';
				}
			}
		}

		if($modelos_documentos_id_documento==7){
			$sql3 = "SELECT * FROM documentos WHERE id = ".$modelos_documentos_id_documento;
			$consulta4 = mysqli_query($conexion,$sql3);
			while($row6 = mysqli_fetch_array($consulta4)) {
				if($modelos_documentos_tipo=='pdf'){
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div>
								<!--<label for="" style="text-transform: capitalize;">Antecedentes Disciplinarios</label>-->
								<button type="button" id="documento6" value="0" onclick="bottonMostrar1(this.id,value);" class="btn btn-info">Antecedentes Disciplinarios</button>
							</div>
							<embed id="div_documento6" src="../resources/documentos/modelos/archivos/'.$modelo_id.'/antecedentes_disciplinarios.'.$modelos_documentos_tipo.'#toolbar=0" type="application/pdf" width="100%" height="300px" style="display:none;" />
							<hr style="background-color:black;">
						</div>
					';
				}else{
					$html_documentos1.='
						<div class="col-12 form-group text-center">
							<div><label for="" style="text-transform: capitalize;">Antecedentes Disciplinarios</label></div>
							<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/antecedentes_disciplinarios.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
							<hr style="background-color:black;">
						</div>
					';
				}
			}
		}
	}

	$html_matriz = $html_firma1.$html_documentos1;

	if($html_matriz==''){
		$html_matriz = '
			<div class="col-12 form-group text-center">
				<div><label for="" style="text-transform: capitalize;">Sin Documentos cargados</label></div>
				<hr style="background-color:black;">
			</div>
		';
	}

	$datos = [
		"html_matriz" => $html_matriz,
	];

	echo json_encode($datos);
}

if($condicion=='ver_fotos1'){
	$html_documento_identidad='';
	$html_foto_cedula_con_cara='';
	$html_foto_cedula_parte_frontal_cara='';
	$html_foto_cedula_parte_respaldo='';
	$html_antecedentes_penales='';
	$html_extras1='';
	$html_fotos1='';
	$html_fotos2='';
	$modelo_id = $_POST['variable'];
	$contador_extra1 = 0;
	$contador_fotos1 = 0;
	$sql2 = "SELECT * FROM modelos_documentos WHERE id_usuarios = ".$modelo_id;
	$consulta3 = mysqli_query($conexion,$sql2);
	while($row5 = mysqli_fetch_array($consulta3)) {
		$modelos_documentos_id = $row5['id'];
		$modelos_documentos_id_documento = $row5['id_documentos'];
		$modelos_documentos_tipo = $row5['tipo'];
		$modelos_documentos_fecha_inicio = $row5['fecha_creacion'];

		if($modelos_documentos_id_documento==2){
			$html_documento_identidad.='
				<div class="col-12 form-group text-center" id="documento_'.$modelos_documentos_id.'">
					<div><label for="" style="text-transform: capitalize;">Foto Documento de Identidad</label></div>
					<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/documento_identidad.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
					<p class="mt-3"><button type="button" class="btn btn-danger" id="'.$modelo_id.'" value="documento_identidad.'.$modelos_documentos_tipo.'" onclick="eliminar_foto1(this.id,value,'.$modelos_documentos_id.')">Borrar</button></p>
					<hr style="background-color:black;">
				</div>
			';
		}

		if($modelos_documentos_id_documento==8){
			$html_foto_cedula_con_cara.='
				<div class="col-12 form-group text-center" id="documento_'.$modelos_documentos_id.'">
					<div><label for="" style="text-transform: capitalize;">Foto cédula con cara</label></div>
					<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/foto_cedula_con_cara.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
					<p class="mt-3"><button type="button" class="btn btn-danger" id="'.$modelo_id.'" value="foto_cedula_con_cara.'.$modelos_documentos_tipo.'" onclick="eliminar_foto1(this.id,value,'.$modelos_documentos_id.')">Borrar</button></p>
					<hr style="background-color:black;">
				</div>
			';
		}

		if($modelos_documentos_id_documento==9){
			$html_foto_cedula_parte_frontal_cara.='
				<div class="col-12 form-group text-center" id="documento_'.$modelos_documentos_id.'">
					<div><label for="" style="text-transform: capitalize;">Foto cédula parte frontal con cara</label></div>
					<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/foto_cedula_parte_frontal_cara.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
					<p class="mt-3"><button type="button" class="btn btn-danger" id="'.$modelo_id.'" value="foto_cedula_parte_frontal_cara.'.$modelos_documentos_tipo.'" onclick="eliminar_foto1(this.id,value,'.$modelos_documentos_id.')">Borrar</button></p>
					<hr style="background-color:black;">
				</div>
			';
		}

		if($modelos_documentos_id_documento==10){
			$html_foto_cedula_parte_respaldo.='
				<div class="col-12 form-group text-center" id="documento_'.$modelos_documentos_id.'">
					<div><label for="" style="text-transform: capitalize;">Foto Cédula Parte Respaldo</label></div>
					<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/foto_cedula_parte_respaldo.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
					<p class="mt-3"><button type="button" class="btn btn-danger" id="'.$modelo_id.'" value="foto_cedula_parte_respaldo.'.$modelos_documentos_tipo.'" onclick="eliminar_foto1(this.id,value,'.$modelos_documentos_id.')">Borrar</button></p>
					<hr style="background-color:black;">
				</div>
			';
		}

		if($modelos_documentos_id_documento==12){
			if($contador_extra1==0){
				$html_extras1.='
					<div class="col-12 form-group text-center">
						<div><label for="" style="text-transform: capitalize;">Extras</label></div>
				';
				$contador_extra1 = $contador_extra1 + 1;
			}
			$html_extras1.='
						<div class="mt-3 mb-3">
							<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/extras_'.$modelos_documentos_id.'.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
							<p class="mt-3" style="font-weight:bold;">('.$modelos_documentos_fecha_inicio.')</p>
							<p><button type="button" class="btn btn-danger mt-1" value="'.$modelos_documentos_id.'" id="'.$modelo_id.'" onclick="borrar_extra(this.value,this.id);">Borrar</button></p>
						</div>
						<br>
			';
		}

		if($modelos_documentos_id_documento==13){
			if($contador_fotos1==0){
				$html_fotos1.='
					<div class="col-12 form-group text-center">
						<div><label for="" style="text-transform: capitalize;">Fotos Sensuales</label></div>
				';
				$contador_fotos1 = $contador_fotos1 + 1;
			}
			$html_fotos1.='
						<div class="mt-3 mb-3">
							<img src="../resources/documentos/modelos/archivos/'.$modelo_id.'/sensuales_'.$modelos_documentos_id.'.jpg" style="width:250px;border-radius:5px;">
							<p class="mt-3" style="font-weight:bold;">('.$modelos_documentos_fecha_inicio.')</p>
							<p><button type="button" class="btn btn-danger mt-1" value="'.$modelos_documentos_id.'" id="'.$modelo_id.'" onclick="borrar_sensual(this.value,this.id);">Borrar</button></p>
						</div>
						<br>
			';
		}
	}

	if($contador_extra1>=1){
		$html_extras1.='
				<hr style="background-color:black;">
			</div>
		';
	}

	if($contador_fotos1>=1){
		$html_fotos1.='
				<hr style="background-color:black;">
			</div>
		';
	}

	$html_matriz = $html_documento_identidad.$html_foto_cedula_con_cara.$html_foto_cedula_parte_frontal_cara.$html_foto_cedula_parte_respaldo.$html_extras1.$html_fotos1;

	if($html_matriz==''){
		$html_matriz = '
			<div class="col-12 form-group text-center">
				<div><label for="" style="text-transform: capitalize;">Sin Fotos cargados</label></div>
				<hr style="background-color:black;">
			</div>
		';
	}

	$datos = [
		"html_matriz" => $html_matriz,
	];

	echo json_encode($datos);
}

if($condicion=="consultar_cuentas1"){
	$modelo_id = $_POST['variable'];
	$html = "";
	$sql1="SELECT * FROM paginas ORDER BY id";
	$consulta1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($consulta1)) {
		$pagina_id = $row1['id'];
		$pagina_nombre = $row1['nombre'];
		$html.="<div class='col-12'>";
		if($pagina_id!=1){ $html.= '<hr>'; }
		$html.= "<p style='font-weight:bold; text-align:center;'>".$pagina_nombre."</p>";
		$sql2="SELECT * FROM modelos_cuentas WHERE id_usuarios = ".$modelo_id." and id_paginas = ".$pagina_id;
		$consulta2 = mysqli_query($conexion,$sql2);
		$fila1 = mysqli_num_rows($consulta2);
		if($fila1==0){
			$html.="<p><small>No ha registrado cuenta</small></p>";
		}
		$contador1=1;
		while($row2 = mysqli_fetch_array($consulta2)) {
			$modelo_cuenta_id = $row2['id'];
			$modelo_usuario = $row2['usuario'];
			$modelo_clave = $row2['clave'];
			$modelo_correo = $row2['correo'];
			$modelo_link = $row2['link'];
			$modelo_estatus = $row2['estatus'];
			$modelo_nickname_xlove = $row2['nickname_xlove'];
			$modelo_usuario_bonga = $row2['usuario_bonga'];
			if($modelo_estatus=='Proceso'){
				$html.= "<button type='button' class='btn btn-info' style='margin-bottom:1rem;' value='hidden_cuenta_".$pagina_nombre."_".$contador1."' onclick='hidden_cuentas1(this.value);'>Cuenta #".$contador1." (Proceso)</button>";	
			}else if($modelo_estatus=='Aprobada'){
				$html.= "<button type='button' class='btn btn-success' style='margin-bottom:1rem;' value='hidden_cuenta_".$pagina_nombre."_".$contador1."' onclick='hidden_cuentas1(this.value);'>Cuenta #".$contador1." (Aprobada)</button>";	
			}else{
				$html.= "<button type='button' class='btn btn-danger' style='margin-bottom:1rem;' value='hidden_cuenta_".$pagina_nombre."_".$contador1."' onclick='hidden_cuentas1(this.value);'>Cuenta #".$contador1." (Rechazada)</button>";	
			}

			if($pagina_id==4){
				$html.="
				<input type='hidden' id='hidden_cuenta_".$pagina_nombre."_".$contador1."' value='0'>
				<div class='row' id='div_hidden_cuenta_".$pagina_nombre."_".$contador1."' style='display:none;'>
					<div class='col-12'>
						<div class='input-group'>
							<span style='margin-top:1rem; width:100px;'>Usuario Pago: &nbsp;</span>
							<input class='form-control mb-2 mt-2' type='text' value='".$modelo_usuario."' id='edit_cuenta_usuario_".$modelo_cuenta_id."' name='edit_cuenta_usuario_".$modelo_cuenta_id."'> 
						</div>
					</div>
					<div class='col-12'>
						<div class='input-group'>
							<span style='margin-top:1rem; width:100px;'>Usuario Login: &nbsp;</span>
							<input class='form-control mb-2 mt-2' type='text' value='".$modelo_usuario_bonga."' id='edit_usuario_bonga_".$modelo_cuenta_id."' name='edit_usuario_bonga_".$modelo_cuenta_id."'> 
						</div>
					</div>

					<div class='col-12'>
						<div class='input-group'>
							<span style='margin-top:1rem; width:100px;'>Clave: &nbsp;</span>
							<input class='form-control mb-2 mt-2' type='text' value='".$modelo_clave."' id='edit_cuenta_clave_".$modelo_cuenta_id."' name='edit_cuenta_clave_".$modelo_cuenta_id."'> 
						</div>
					</div>
					";
					if($modelo_correo!=''){
					$html.= "
						<div class='col-12'>
							<div class='input-group'>
								<span style='margin-top:1rem; width:100px;'>Correo: &nbsp;</span>
								<input class='form-control mb-2 mt-2' type='text' value='".$modelo_correo."' id='edit_cuenta_correo_".$modelo_cuenta_id."' name='edit_cuenta_correo_".$modelo_cuenta_id."'> 
							</div>
						</div>	
					";	
					}
					if($modelo_link!=''){
					$html.= "
						<div class='col-12'>
							<div class='input-group'>
								<span style='margin-top:1rem; width:100px;'>Link: &nbsp;</span>
								<input class='form-control mb-2 mt-2' type='text' value='".$modelo_link."' id='edit_cuenta_link_".$modelo_cuenta_id."' name='edit_cuenta_link_".$modelo_cuenta_id."'>
							</div>
						</div>
					";	
					}

					if($pagina_id==11){
						$html.= "
						<div class='col-12'>
							<div class='input-group'>
								<span style='margin-top:1rem; width:140px;'>NickName Xlove: &nbsp;</span>
								<input class='form-control mb-2 mt-2' type='text' value='".$modelo_nickname_xlove."' id='edit_cuenta_nickname_xlove_".$modelo_cuenta_id."' name='edit_cuenta_nickname_xlove_".$modelo_cuenta_id."'>
							</div>
						</div>
						";
					}

					if($modelo_estatus=='Aprobada'){
						$html.= "
						<div class='col-12 text-center mt-2'>
							<button type='button' class='btn btn-danger' value='".$pagina_nombre."' id='Rechazada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Rechazada</button>
							<button type='button' class='btn btn-dark' value='".$pagina_nombre."' id='Eliminar' onclick='cuenta_eliminar(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Eliminar Cuenta</button>
							<button type='button' class='btn btn-warning' value='".$pagina_nombre."' id='Eliminar' style='color:white;' onclick='cuenta_editar(".$modelo_cuenta_id.",".$pagina_id.");'>Editar Cuenta</button>
						</div>
						";
					}

					if($modelo_estatus=='Proceso'){
						$html.= "
							<div class='col-12 text-center'>
								<button type='button' class='btn btn-success' value='".$pagina_nombre."' id='Aprobada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Aprobada</button>
								<button type='button' class='btn btn-danger' value='".$pagina_nombre."' id='Rechazada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Rechazada</button>
								<button type='button' class='btn btn-dark' value='".$pagina_nombre."' id='Eliminar' onclick='cuenta_eliminar(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Eliminar Cuenta</button>
								<button type='button' class='btn btn-warning' value='".$pagina_nombre."' id='Eliminar' style='color:white;' onclick='cuenta_editar(".$modelo_cuenta_id.",".$pagina_id.");'>Editar Cuenta</button>
							</div>
						";
					}

					if($modelo_estatus=='Rechazada'){
						$html.= "
							<div class='col-12 text-center'>
								<button type='button' class='btn btn-success' value='".$pagina_nombre."' id='Aprobada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Aprobada</button>
								<button type='button' class='btn btn-dark' value='".$pagina_nombre."' id='Eliminar' onclick='cuenta_eliminar(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Eliminar Cuenta</button>
								<button type='button' class='btn btn-warning' value='".$pagina_nombre."' id='Eliminar' style='color:white;' onclick='cuenta_editar(".$modelo_cuenta_id.",".$pagina_id.");'>Editar Cuenta</button>
							</div>
						";
					}
					$html.= "
					<div style='margin-bottom:10px;'>&nbsp;</div>
				</div>
			";
			}else{
				$html.="
				<input type='hidden' id='hidden_cuenta_".$pagina_nombre."_".$contador1."' value='0'>
				<div class='row' id='div_hidden_cuenta_".$pagina_nombre."_".$contador1."' style='display:none;'>
					<div class='col-12'>
						<div class='input-group'>
							<span style='margin-top:1rem; width:100px;'>Usuario: &nbsp;</span>
							<input class='form-control mb-2 mt-2' type='text' value='".$modelo_usuario."' id='edit_cuenta_usuario_".$modelo_cuenta_id."' name='edit_cuenta_usuario_".$modelo_cuenta_id."'> 
						</div>
					</div>
					<div class='col-12'>
						<div class='input-group'>
							<span style='margin-top:1rem; width:100px;'>Clave: &nbsp;</span>
							<input class='form-control mb-2 mt-2' type='text' value='".$modelo_clave."' id='edit_cuenta_clave_".$modelo_cuenta_id."' name='edit_cuenta_clave_".$modelo_cuenta_id."'> 
						</div>
					</div>
					";
					if($modelo_correo!=''){
					$html.= "
						<div class='col-12'>
							<div class='input-group'>
								<span style='margin-top:1rem; width:100px;'>Correo: &nbsp;</span>
								<input class='form-control mb-2 mt-2' type='text' value='".$modelo_correo."' id='edit_cuenta_correo_".$modelo_cuenta_id."' name='edit_cuenta_correo_".$modelo_cuenta_id."'> 
							</div>
						</div>	
					";	
					}
					if($modelo_link!=''){
					$html.= "
						<div class='col-12'>
							<div class='input-group'>
								<span style='margin-top:1rem; width:100px;'>Link: &nbsp;</span>
								<input class='form-control mb-2 mt-2' type='text' value='".$modelo_link."' id='edit_cuenta_link_".$modelo_cuenta_id."' name='edit_cuenta_link_".$modelo_cuenta_id."'>
							</div>
						</div>
					";	
					}

					if($pagina_id==11){
						$html.= "
						<div class='col-12'>
							<div class='input-group'>
								<span style='margin-top:1rem; width:140px;'>NickName Xlove: &nbsp;</span>
								<input class='form-control mb-2 mt-2' type='text' value='".$modelo_nickname_xlove."' id='edit_cuenta_nickname_xlove_".$modelo_cuenta_id."' name='edit_cuenta_nickname_xlove_".$modelo_cuenta_id."'>
							</div>
						</div>
						";
					}

					if($modelo_estatus=='Aprobada'){
						$html.= "
						<div class='col-12 text-center mt-2'>
							<!--<button type='button' class='btn btn-primary' onclick='alerta_cuenta1(".$modelo_id.",".$modelo_cuenta_id.");'>Alertar a Modelo</button>-->
							<button type='button' class='btn btn-danger' value='".$pagina_nombre."' id='Rechazada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Rechazada</button>
							<button type='button' class='btn btn-dark' value='".$pagina_nombre."' id='Eliminar' onclick='cuenta_eliminar(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Eliminar Cuenta</button>
							<button type='button' class='btn btn-warning' value='".$pagina_nombre."' id='Eliminar' style='color:white;' onclick='cuenta_editar(".$modelo_cuenta_id.",".$pagina_id.");'>Editar Cuenta</button>
						</div>
						";
					}
					if($modelo_estatus=='Proceso'){
						$html.= "
							<div class='col-12 text-center'>
								<button type='button' class='btn btn-success' value='".$pagina_nombre."' id='Aprobada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Aprobada</button>
								<button type='button' class='btn btn-danger' value='".$pagina_nombre."' id='Rechazada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Rechazada</button>
								<button type='button' class='btn btn-dark' value='".$pagina_nombre."' id='Eliminar' onclick='cuenta_eliminar(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Eliminar Cuenta</button>
								<button type='button' class='btn btn-warning' value='".$pagina_nombre."' id='Eliminar' style='color:white;' onclick='cuenta_editar(".$modelo_cuenta_id.",".$pagina_id.");'>Editar Cuenta</button>
							</div>
						";
					}

					if($modelo_estatus=='Rechazada'){
						$html.= "
							<div class='col-12 text-center'>
								<button type='button' class='btn btn-success' value='".$pagina_nombre."' id='Aprobada' onclick='cuenta_estatus(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Cuenta Aprobada</button>
								<button type='button' class='btn btn-dark' value='".$pagina_nombre."' id='Eliminar' onclick='cuenta_eliminar(this.id,this.value,".$modelo_id.",".$pagina_id.",".$modelo_cuenta_id.");'>Eliminar Cuenta</button>
								<button type='button' class='btn btn-warning' value='".$pagina_nombre."' id='Eliminar' style='color:white;' onclick='cuenta_editar(".$modelo_cuenta_id.",".$pagina_id.");'>Editar Cuenta</button>
							</div>
						";
					}
					$html.= "
					<div style='margin-bottom:10px;'>&nbsp;</div>
				</div>
			";
			}

			$contador1=$contador1+1;
		}
		$html.="</div>";

		$datos = [
			"sql1" 	=> $sql1,
			"sql2" 	=> $sql2,
			"html" 	=> $html,
		];

	}

	echo json_encode($datos);
}

if($condicion=="modelos_cuentas_estatus1"){
	$estatus = $_POST['estatus'];
	$pagina = $_POST['pagina'];
	$id = $_POST['id'];
	$pagina_id = $_POST['pagina_id'];
	$modelo_cuenta_id = $_POST['modelo_cuenta_id'];

	$sql2 = "SELECT * FROM modelos WHERE id =".$id;
	$consulta1 = mysqli_query($conexion,$sql2);
	while($row1 = mysqli_fetch_array($consulta1)) {
		$correo = $row1['correo'];
		$sede = $row1['sede'];
	}
	
	$sql1 = "UPDATE modelos_cuentas SET estatus = '$estatus' WHERE id = ".$modelo_cuenta_id;
	$modificar1 = mysqli_query($conexion,$sql1);

	if($estatus=='Aprobada'){

		$mail = new PHPMailer(true);
		try {
		    $mail->isSMTP();
		    $mail->Host = 'mail.camaleonmg.com';
		    $mail->SMTPAuth = true;
		    $mail->Username = 'contactosmodelos@camaleonmg.com';
		    $mail->Password = 'juanmaldonado123';
		    $mail->SMTPSecure = 'tls';
		    $mail->Port = 587;

		    $mail->setFrom('contactosmodelos@camaleonmg.com');
		    $mail->addAddress($correo);
		    $mail->AddEmbeddedImage("../img/alerta_habilitada.png", "my-attach", "alerta_habilitada.png");
		    $html = "
		        <h2 style='color:#3F568A; text-align:center; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;'>
		            <p>Tu cuenta en la página de ".$pagina."</p>
		            <p>Si tienes alguna duda, consultar con tu monitor de confianza.</p>
		        </h2>
		        <div style='text-align:center;'>
		        	<img alt='PHPMailer' src='cid:my-attach'>
		        </div>
		    ";

		    $mail->isHTML(true);
		    $mail->Subject = 'Camaleon Models!';
		    $mail->Body    = $html;
		    $mail->AltBody = 'Cuenta Aprobada!';
		 
		    $mail->send();

	    } catch (Exception $e) {}
	}

	$datos = [
		"resultado" => "ok",
	];

	echo json_encode($datos);
}

if($condicion=='consulta1'){
	$usuario_id = $_POST["usuario_id"];
	$html_documentos1='';
	$html_fotos1='';
	$html_fotos2='';
	$relleno_fotos2='';

	$sql1 = "SELECT us.nombre1 as nombre1, us.nombre2 as nombre2, us.apellido1 as apellido1, us.apellido2 as apellido2, us.documento_tipo as documento_tipo, dti.nombre as documento_tipo_nombre, us.documento_numero as documento_numero, us.correo_personal as correo_personal, us.telefono as telefono, us.genero as genero, us.direccion as direccion, us.id_pais as id_pais, pa.nombre as pais_nombre, dmo.id as modelo_id, dmo.fecha_creacion as fecha_creacion, dmo.sede as sede, dmo.estatus as estatus, dmo.turno as turno, se.nombre as sede_nombre, dmo.altura as altura, dmo.peso as peso, dmo.tpene as tpene, dmo.tsosten as tsosten, dmo.tbusto as tbusto, dmo.tcintura as tcintura, dmo.tcaderas as tcaderas, dmo.tipo_cuerpo as tipo_cuerpo, dmo.pvello as pvello, dmo.color_cabello as color_cabello, dmo.color_ojos as color_ojos, dmo.ptattu as ptattu, dmo.ppiercing as ppiercing, dmo.banco_cedula  as banco_cedula, dmo.banco_nombre as banco_nombre, dmo.banco_tipo as banco_tipo, dmo.banco_numero as banco_numero, dmo.banco_banco as banco_banco, dmo.banco_bcpp as banco_bcpp, dmo.banco_tipo_documento as banco_tipo_documento, dmo.emergencia_nombre as emergencia_nombre, dmo.emergencia_telefono as emergencia_telefono, dmo.emergencia_parentesco as emergencia_parentesco FROM usuarios us 
	INNER JOIN datos_modelos dmo 
	ON us.id = dmo.id_usuarios 
	INNER JOIN documento_tipo dti 
	ON us.documento_tipo = dti.id 
	INNER JOIN genero ge 
	ON us.genero = ge.id 
	INNER JOIN paises pa 
	ON us.id_pais = pa.id 
	INNER JOIN sedes se 
	ON dmo.sede = se.id 
	WHERE us.id = ".$usuario_id;
	$consulta1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($consulta1)) {
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
		$modelo_id = $row1["modelo_id"];
		$fecha_creacion = $row1["fecha_creacion"];
		$sede = $row1["sede"];
		$estatus = $row1["estatus"];
		$turno = $row1["turno"];
		$sede_nombre = $row1["sede_nombre"];

		$altura = $row1["altura"];
		$peso = $row1["peso"];
		$pene = $row1["tpene"];
		$sosten = $row1["tsosten"];
		$busto = $row1["tbusto"];
		$cintura = $row1["tcintura"];
		$caderas = $row1["tcaderas"];
		$cuerpo = $row1["tipo_cuerpo"];
		$vello = $row1["pvello"];
		$cabello = $row1["color_cabello"];
		$ojos = $row1["color_ojos"];
		$tattu = $row1["ptattu"];
		$piercing = $row1["ppiercing"];

		$banco_cedula = $row1["banco_cedula"];
		$banco_nombre = $row1["banco_nombre"];
		$banco_tipo = $row1["banco_tipo"];
		$banco_numero = $row1["banco_numero"];
		$banco_banco = $row1["banco_banco"];
		$banco_bcpp = $row1["banco_bcpp"];
		$banco_tipo_documento = $row1["banco_tipo_documento"];

		$emergencia_nombre = $row1["emergencia_nombre"];
		$emergencia_telefono = $row1["emergencia_telefono"];
		$emergencia_parentesco = $row1["emergencia_parentesco"];

		$sql2 = "SELECT * FROM modelos_documentos WHERE (id_documentos = 1 or id_documentos = 2 or id_documentos = 3 or id_documentos = 4 or id_documentos = 5 or id_documentos = 6 or id_documentos = 7 or id_documentos = 8 or id_documentos = 9 or id_documentos = 10 or id_documentos = 11 or id_documentos = 14) and id_usuarios = ".$usuario_id;
		$proceso2 = mysqli_query($conexion,$sql2);
		$contador2 = mysqli_num_rows($proceso2);
		if($contador2>=1){
			while($row2 = mysqli_fetch_array($proceso2)) {
				$modelos_documentos_id = $row2['id'];
				$id_documento = $row2['id_documentos'];
				$modelos_documentos_tipo = $row2['tipo'];

				$sql3 = "SELECT * FROM documentos WHERE id = ".$id_documento;
				$proceso3 = mysqli_query($conexion,$sql3);
				while($row3 = mysqli_fetch_array($proceso3)) {
					$documento_nombre = $row3["nombre"];
					$documento_ruta = $row3["ruta"];
				}

				if($modelos_documentos_tipo=='pdf' or $modelos_documentos_tipo=='PDF'){
					$html_documentos1 .= '
						<div class="col-12 form-group text-center" id="divmacro_documento_'.$modelos_documentos_id.'">
							<p>
								<button type="button" id="documento_'.$modelos_documentos_id.'" value="1" onclick="documento_mostrar1(this.id,value);" class="btn btn-info">Ver '.$documento_nombre.'</button>
							</p>
							<embed id="div_documento_'.$modelos_documentos_id.'" src="../resources/documentos/modelos/archivos/'.$usuario_id.'/'.$documento_ruta.'.'.$modelos_documentos_tipo.'" type="application/pdf" width="100%" height="300px" style="display: none;">
							<hr style="background-color:black;">
						</div>
					';
				}else{
					$html_documentos1 .= '
						<div class="col-12 form-group text-center">
							<p>'.$documento_nombre.'</p>
							<img id="div_documento1" src="../resources/documentos/modelos/archivos/'.$usuario_id.'/'.$documento_ruta.'.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
							<hr style="background-color:black;">
						</div>
					';
				}

			}
		}else if($contador2==0){
			$html_documentos1 = '
				<div class="col-12 form-group text-center">
					<div><label for="" style="text-transform: capitalize;">Sin Documentos cargados</label></div>
					<hr style="background-color:black;">
				</div>
			';
		}

		$sql4 = "SELECT * FROM modelos_documentos WHERE (id_documentos = 12 or id_documentos = 13) and id_usuarios = ".$usuario_id;
		$proceso4 = mysqli_query($conexion,$sql4);
		$contador4 = mysqli_num_rows($proceso4);
		if($contador4>=1){
			while($row2 = mysqli_fetch_array($proceso4)) {
				$modelos_documentos_id = $row2['id'];
				$id_documento = $row2['id_documentos'];
				$modelos_documentos_tipo = $row2['tipo'];

				$sql3 = "SELECT * FROM documentos WHERE id = ".$id_documento;
				$proceso3 = mysqli_query($conexion,$sql3);
				while($row3 = mysqli_fetch_array($proceso3)) {
					$documento_nombre = $row3["nombre"];
					$documento_ruta = $row3["ruta"];
				}

				$html_fotos1 .= '
					<div class="col-12 form-group text-center">
						<p>'.$documento_nombre.'</p>
						<img id="div_documento1" src="../resources/documentos/modelos/archivos/'.$usuario_id.'/'.$documento_ruta.'.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
						<hr style="background-color:black;">
					</div>
				';
			}

			$relleno_fotos2 = '
				<div class="col-12 text-center">
					<img id="div_documento1" src="../resources/documentos/modelos/archivos/'.$usuario_id.'/'.$documento_ruta.'.'.$modelos_documentos_tipo.'" style="width:250px;border-radius:5px;">
					<hr style="background-color:black;">
				</div>
			';

		}else if($contador4==0){
			$html_fotos1 = '
				<div class="col-12 form-group text-center">
					<div><label for="" style="text-transform: capitalize;">No tiene Registro de Fotos</label></div>
					<hr style="background-color:black;">
				</div>
			';
		}
	}

	if($contador4<=4){
		$cupo_fotos2 = 5-$contador4;
		$html_fotos2 = '
			<div class="col-12 text-center" style="font-weight: bold;">
				<hr style="background-color:black;">
				REGISTRAR FOTOS SENSUALES 
				<p>(Le faltan '.$cupo_fotos2.')</p>
			</div>
			<div class="col-12 text-center" style="font-weight: bold;">
				<input type="file" class="form-control" name="subirdocumentos1_foto1" id="subirdocumentos1_foto1" style="margin-left: 18px; margin-right: 16px;" required>
				<hr style="background-color:black;">
			</div>
		';

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
		"modelo_id"	=> $modelo_id,
		"fecha_creacion"	=> $fecha_creacion,

		"altura" => $altura,
		"peso" => $peso,
		"pene" => $pene,
		"sosten" => $sosten,
		"busto" => $busto,
		"cintura" => $cintura,
		"caderas" => $caderas,
		"cuerpo" => $cuerpo,
		"vello" => $vello,
		"cabello" => $cabello,
		"ojos" => $ojos,
		"tattu" => $tattu,
		"piercing" => $piercing,

		"sede"	=> $sede,
		"turno"	=> $turno,
		"sede_nombre"	=> $sede_nombre,

		"html_documentos1" => $html_documentos1,
		"html_fotos1" => $html_fotos1,
		"html_fotos2" => $html_fotos2,

		"banco_cedula"	=> $banco_cedula,
		"banco_nombre"	=> $banco_nombre,
		"banco_tipo"	=> $banco_tipo,
		"banco_numero"	=> $banco_numero,
		"banco_banco"	=> $banco_banco,
		"banco_bcpp"	=> $banco_bcpp,
		"banco_tipo_documento"	=> $banco_tipo_documento,

		"emergencia_nombre"	=> $emergencia_nombre,
		"emergencia_telefono"	=> $emergencia_telefono,
		"emergencia_parentesco"	=> $emergencia_parentesco,
	];

	echo json_encode($datos);
}

if($condicion=='aceptar_modelos1'){
	$usuario_id = $_POST["usuario_id"];

	$sql1 = "UPDATE datos_modelos SET estatus = 2 WHERE id_usuarios = ".$usuario_id;
	$consulta1 = mysqli_query($conexion,$sql1);

	$sql2 = "UPDATE datos_pasantes SET estatus = 2 WHERE id_usuarios = ".$usuario_id;
	$consulta2 = mysqli_query($conexion,$sql2);

	$datos = [
		"sql1" 	=> $sql1,
		"sql2" 	=> $sql2,
		"estatus" 	=> "ok",
		"msg" 	=> "Se ha cambiado el estatus ha aceptado!",
	];
	echo json_encode($datos);
}

if($condicion=='rechazar_modelos1'){
	$usuario_id = $_POST["usuario_id"];

	$sql1 = "UPDATE datos_modelos SET estatus = 3 WHERE id_usuarios = ".$usuario_id;
	$consulta1 = mysqli_query($conexion,$sql1);

	$sql2 = "UPDATE datos_pasantes SET estatus = 3 WHERE id_usuarios = ".$usuario_id;
	$consulta2 = mysqli_query($conexion,$sql2);

	$datos = [
		"sql1" 	=> $sql1,
		"sql2" 	=> $sql2,
		"estatus" 	=> "ok",
		"msg" 	=> "Se ha cambiado el estatus ha rechazado!",
	];
	echo json_encode($datos);
}

if($condicion=='modificar_personales1'){
	$usuario_id = $_POST["usuario_id"];
	$nombre1 = $_POST["nombre1"];
	$nombre2 = $_POST["nombre2"];
	$apellido1 = $_POST["apellido1"];
	$apellido2 = $_POST["apellido2"];
	$documento_tipo = $_POST["documento_tipo"];
	$documento_numero = $_POST["documento_numero"];
	$correo_personal = $_POST["correo"];
	$telefono = $_POST["telefono"];
	$genero = $_POST["genero"];
	$direccion = $_POST["direccion"];
	$pais = $_POST["pais"];

	$sql1 = "SELECT * FROM usuarios WHERE documento_numero = '".$documento_numero."' and id != ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);
	$contador1 = mysqli_num_rows($proceso1);
	if($contador1==0){
		$sql2 = "UPDATE usuarios SET nombre1 = '$nombre1', nombre2 = '$nombre2', apellido1 = '$apellido1', apellido2 = '$apellido2', documento_tipo = '$documento_tipo', documento_numero = '$documento_numero', correo_personal = '$correo_personal', telefono = '$telefono', genero = '$genero', direccion = '$direccion', id_pais = '$pais' WHERE id = ".$usuario_id;
		$proceso2 = mysqli_query($conexion,$sql2);

		$datos = [
			"estatus"	=> "ok",
			"msg"	=> "Se ha modificado correctamente!",
			"sql1"	=> $sql1,
			"sql2"	=> $sql2,
		];
		echo json_encode($datos);
	}else{
		$datos = [
			"estatus"	=> "error",
			"msg"	=> "Ya existe un usuario con dicho documento!",
			"sql1"	=> $sql1,
		];
		echo json_encode($datos);
	}
}

if($condicion=='modificar_corporales1'){
	$usuario_id = $_POST["usuario_id"];
	$altura = $_POST["altura"];
	$peso = $_POST["peso"];
	$pene = $_POST["pene"];
	$sosten = $_POST["sosten"];
	$busto = $_POST["busto"];
	$cintura = $_POST["cintura"];
	$caderas = $_POST["caderas"];
	$cuerpo = $_POST["cuerpo"];
	$vello = $_POST["vello"];
	$cabello = $_POST["cabello"];
	$ojos = $_POST["ojos"];
	$tattu = $_POST["tattu"];
	$piercing = $_POST["piercing"];

	$sql1 = "UPDATE datos_modelos SET altura = '$altura', peso = '$peso', tpene = '$pene', tsosten = '$sosten',tbusto = '$busto', tcintura = '$cintura', tcaderas = '$caderas', tipo_cuerpo = '$cuerpo', pvello = '$vello', color_cabello = '$cabello', color_ojos = '$ojos', ptattu = '$tattu', ppiercing = '$piercing' WHERE id_usuarios = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus"	=> "ok",
		"msg"	=> "Se ha modificado correctamente!",
		"sql1"	=> $sql1,
	];
	echo json_encode($datos);
}

if($condicion=='modificar_bancarios1'){
	$usuario_id = $_POST["usuario_id"];
	$banco_cedula = $_POST["banco_cedula"];
	$banco_nombre = $_POST["banco_nombre"];
	$banco_tipo = $_POST["banco_tipo"];
	$banco_numero = $_POST["banco_numero"];
	$banco_banco = $_POST["banco_banco"];
	$banco_bcpp = $_POST["banco_bcpp"];
	$banco_tipo_documento = $_POST["banco_tipo_documento"];

	$sql1 = "UPDATE datos_modelos SET banco_cedula = '$banco_cedula', banco_nombre = '$banco_nombre', banco_tipo = '$banco_tipo', banco_numero = '$banco_numero',banco_banco = '$banco_banco', banco_bcpp = '$banco_bcpp', banco_tipo_documento = '$banco_tipo_documento' WHERE id_usuarios = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus"	=> "ok",
		"msg"	=> "Se ha modificado correctamente!",
		"sql1"	=> $sql1,
	];
	echo json_encode($datos);
}

if($condicion=='modificar_empresa1'){
	$usuario_id = $_POST["usuario_id"];
	$turno = $_POST["turno"];
	$sede = $_POST["sede"];

	$sql1 = "UPDATE datos_modelos SET turno = '$turno', sede = '$sede' WHERE id_usuarios = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus"	=> "ok",
		"msg"	=> "Se ha modificado correctamente!",
		"sql1"	=> $sql1,
	];
	echo json_encode($datos);
}

if($condicion=='subirdocumentos1'){
	$id_documento = $_POST['id_documento'];
	$usuario_id = $_POST['usuario_id'];
	$imagen_temporal = $_FILES['file']['tmp_name'];
	$imagen_nombre = $_FILES['file']['name'];

	if(file_exists('../resources/documentos/modelos/archivos/'.$usuario_id)){}else{
	    mkdir('../resources/documentos/modelos/archivos/'.$usuario_id, 0777);
	}

	$sql1 = "SELECT * FROM documentos WHERE id = ".$id_documento;
	$consulta1 = mysqli_query($conexion,$sql1);
	while($row1 = mysqli_fetch_array($consulta1)) {
		$documento_nombre = $row1["ruta"];
	}

	$imagen_nombre = $_FILES['file']['name'];
	$extension = explode(".", $imagen_nombre);
	$extension = $extension[count($extension)-1];

	if($extension!='pdf' and $extension!='jpg' and $extension!='jpeg' and $extension!='png'){
		$datos = [
			"estatus"	=> "error",
			"msg"	=> "Formatos validos JPG, JPEG, PDF, PNG",
			"sql1"	=> $sql1,
		];
		echo json_encode($datos);
		exit;
	}

	$location = "../resources/documentos/modelos/archivos/".$usuario_id."/".$documento_nombre.".".$extension;

	if($extension=='pdf'){
		@unlink($location);
		move_uploaded_file ($_FILES['file']['tmp_name'],$location);
	}else if($extension!='pdf'){
	    $imagen = getimagesize($_FILES['file']['tmp_name']);
	    $ancho = $imagen[0];
	    $alto = $imagen[1];

	    if($ancho>$alto){
	        $imagen_optimizada = redimensionar_imagen($imagen_nombre,$imagen_temporal,1920,1080);
	        @unlink($location);
	        imagejpeg($imagen_optimizada, $location);
	    }else if($ancho<$alto){
	        $imagen_optimizada = redimensionar_imagen($imagen_nombre,$imagen_temporal,1080,1920);
	        @unlink($location);
	        imagejpeg($imagen_optimizada, $location);
	    }else{
	        $imagen_optimizada = redimensionar_imagen($imagen_nombre,$imagen_temporal,1080,1080);
	        @unlink($location);
	        imagejpeg($imagen_optimizada, $location);
	    }
	}

	$sql2 = "DELETE FROM modelos_documentos WHERE id_documentos = ".$id_documento." and id_usuarios = ".$usuario_id;
	$proceso2 = mysqli_query($conexion,$sql2);

	if($extension!='pdf' and $extension!='jpg' and $extension!='png' and $extension!='jpeg'){
		$extension='jpg';
	}

	$sql3 = "INSERT INTO modelos_documentos (id_documentos,id_usuarios,tipo,responsable,fecha_creacion) VALUES ($id_documento,$usuario_id,'$extension',$responsable,'$fecha_creacion')";
	$proceso3 = mysqli_query($conexion,$sql3);

	$datos = [
		"estatus"	=> "ok",
		"msg"	=> "Se ha subido el archivo satisfactoriamente",
		"sql1"	=> $sql1,
		"sql2"	=> $sql2,
		"sql3"	=> $sql3,
	];
	echo json_encode($datos);
}

if($condicion=='eliminar_documento1'){
	$id = $_POST['id'];

	$sql1 = "SELECT mdoc.id as id, mdoc.id_documentos as mdoc_id_documentos, mdoc.id_usuarios as mdoc_id_usuarios, mdoc.tipo as mdoc_tipo, doc.nombre as doc_nombre, doc.ruta as doc_ruta FROM modelos_documentos mdoc 
	INNER JOIN documentos doc 
	ON mdoc.id_documentos = doc.id 
	WHERE mdoc.id = ".$id;
	$proceso1 = mysqli_query($conexion,$sql1);

	while($row1 = mysqli_fetch_array($proceso1)) {
		$usuario_id = $row1['mdoc_id_usuarios'];
		$extension = $row1['mdoc_tipo'];
		$documento_nombre = $row1['doc_nombre'];
	}

	$location = "../resources/documentos/modelos/archivos/".$usuario_id."/".$documento_nombre.".".$extension;
	@unlink($location);

	$sql2 = "DELETE FROM modelos_documentos WHERE id = ".$id;
	$proceso2 = mysqli_query($conexion,$sql2);

	$datos = [
		"estatus"	=> "ok",
		"msg"	=> "Se ha eliminado el documento satisfactoriamente",
		"sql1"	=> $sql1,
		"sql2"	=> $sql2,
	];
	echo json_encode($datos);
}

if($condicion=='modificar_emergencia1'){
	$modelo_id = $_POST["modelo_id"];
	$usuario_id = $_POST["usuario_id"];
	$emergencia_nombre = $_POST["emergencia_nombre"];
	$emergencia_telefono = $_POST["emergencia_telefono"];
	$emergencia_parentesco = $_POST["emergencia_parentesco"];

	$sql1 = "UPDATE datos_modelos SET emergencia_nombre = '$emergencia_nombre', emergencia_telefono = '$emergencia_telefono', emergencia_parentesco = '$emergencia_parentesco' WHERE id = ".$modelo_id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus"	=> "ok",
		"msg"	=> "Se ha modificado satisfactoriamente!",
		"sql1"	=> $sql1,
	];
	echo json_encode($datos);
}

?>