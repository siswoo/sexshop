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
		$sede = ' and (dno.sede = '.$sede.') ';
	}

	if($m_estatus!=''){
		$m_estatus = " and (dno.estatus = ".$m_estatus.")";
	}

	$limit = $consultasporpagina;
	$offset = ($pagina - 1) * $consultasporpagina;

	$sql1 = "SELECT 
		us.id as usuario_id,
		us.fecha_nacimiento as fecha_nacimiento,
		dno.id as nomina_id,
		dno.estatus as nomina_estatus,
		dno.fecha_creacion as nomina_fecha_creacion,
		dno.turno as nomina_turno,
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
		se.nombre as sede,
		se.id as id_sede,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_nominas dno
		ON us.id = dno.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON dno.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		WHERE us.id != 0 
		".$filtrado." 
		".$sede."
		".$m_estatus." 
	";
	
	$sql2 = "SELECT 
		us.id as usuario_id,
		us.fecha_nacimiento as fecha_nacimiento,
		dno.id as nomina_id,
		dno.estatus as nomina_estatus,
		dno.fecha_creacion as nomina_fecha_creacion,
		dno.turno as nomina_turno,
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
		se.nombre as sede,
		se.id as id_sede,
		us.id_empresa as usuario_empresa
		FROM usuarios us
		INNER JOIN datos_nominas dno
		ON us.id = dno.id_usuarios 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id
		INNER JOIN genero ge
		ON us.genero = ge.id
		INNER JOIN sedes se
		ON dno.sede = se.id 
		INNER JOIN empresas em
		ON us.id_empresa = em.id 
		WHERE us.id != 0 
		".$filtrado." 
		".$sede." 
		".$m_estatus." 
		ORDER BY dno.id DESC LIMIT ".$limit." OFFSET ".$offset."
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
	                <th class="text-center">Estatus</th>
	                <th class="text-center">Turno</th>
	                <th class="text-center">Sede</th>
	                <th class="text-center">Cargo</th>
	                <th class="text-center">Ingreso</th>
	                <th class="text-center">Nacimiento</th>
	                <th class="text-center">Opciones</th>
	            </tr>
	            </thead>
	            <tbody>
	';

	if($conteo1>=1){
		while($row2 = mysqli_fetch_array($proceso2)) {

			if($row2["nomina_estatus"]==1){
				$nomina_estatus = "Proceso";
			}else if($row2["nomina_estatus"]==2){
				$nomina_estatus = "Aceptado";
			}else if($row2["nomina_estatus"]==3){
				$nomina_estatus = "Rechazado";
			}

			$sql3 = "SELECT * FROM turnos WHERE id = ".$row2["nomina_turno"];
			$proceso3 = mysqli_query($conexion,$sql3);
			$contador3 = mysqli_num_rows($proceso3);

			if($contador3>=1){
				while($row3 = mysqli_fetch_array($proceso3)) {
					$nomina_tuno = $row3["nombre"];
				}
			}else{
				$nomina_turno = "Ninguno";
			}

			$html .= '
		                <tr id="tr_'.$row2["nomina_id"].'">
		                    <td style="text-align:center;">'.$row2["documento_tipo"].'</td>
		                    <td style="text-align:center;">'.$row2["documento_numero"].'</td>
		                    <td>'.$row2["nombre1"]." ".$row2["nombre2"]." ".$row2["apellido1"]." ".$row2["apellido2"].'</td>
		                    <td  style="text-align:center;">'.$nomina_estatus.'</td>
		                    <td style="text-align:center;">'.$nomina_turno.'</td>
		                    <td style="text-align:center;">'.$row2["telefono"].'</td>
		                    <td style="text-align:center;">'.$row2["sede"].'</td>
		                    <td nowrap="nowrap">'.$row2["nomina_fecha_creacion"].'</td>
		                    <td nowrap="nowrap">'.$row2["fecha_nacimiento"].'</td>
		                    <td class="text-center" nowrap="nowrap">
		                    	Pendiente... (PRIMERO SEXSHOP)
		                    	<!--
		                    	<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#personales1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="fas fa-user-edit"></i></button>
		                    	<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#emergencia1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="fas fa-first-aid"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#corporales1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="fas fa-child"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#documentos1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="far fa-folder-open"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#fotos1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="fas fa-images"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#bancarios1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="fas fa-money-check-alt"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#empresa1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="far fa-building"></i></button>
								<button type="button" class="btn btn-primary" style="cursor:pointer;" data-toggle="modal" data-target="#subirdocumentos1" onclick="editar1('.$row2["nomina_id"].','.$row2["usuario_id"].');"><i class="fas fa-cloud-upload-alt"></i></button>
								-->
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

?>