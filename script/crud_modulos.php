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
$responsable = $_SESSION['camaleonapp_id'];

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
		$filtrado = ' and (mo.nombre LIKE "%'.$filtrado.'%" or us.nombre1 LIKE "%'.$filtrado.'%" or us.nombre2 LIKE "%'.$filtrado.'%" or us.apellido1 LIKE "%'.$filtrado.'%" or us.apellido2 LIKE "%'.$filtrado.'%" or em.nombre LIKE "%'.$filtrado.'%")';
	}

	if($sede!=''){
		$sede = ' and (mem.id_empresas = '.$sede.') ';
	}

	$limit = $consultasporpagina;
	$offset = ($pagina - 1) * $consultasporpagina;

	$sql1 = "SELECT 
		mem.id as modulos_empresas_id,
		mo.nombre as modulo_nombre,
		em.nombre as empresa_nombre,
		mem.estatus as mem_estatus,
		mem.fecha_creacion as mem_fecha_creacion,
		mem.fecha_modificacion as mem_fecha_modificacion
		FROM modulos_empresas mem 
		INNER JOIN modulos mo 
		ON mem.id_modulos = mo.id 
		INNER JOIN empresas em 
		ON mem.id_empresas = em.id 
		WHERE mem.id_modulos != 6 
		".$filtrado." 
		".$sede." 
		ORDER BY mo.fecha_creacion DESC LIMIT ".$limit." OFFSET ".$offset."
	";
	
	$sql2 = "SELECT 
		mem.id as modulos_empresas_id,
		mo.nombre as modulo_nombre,
		em.nombre as empresa_nombre,
		mem.estatus as mem_estatus,
		mem.fecha_creacion as mem_fecha_creacion,
		mem.fecha_modificacion as mem_fecha_modificacion
		FROM modulos_empresas mem 
		INNER JOIN modulos mo 
		ON mem.id_modulos = mo.id 
		INNER JOIN empresas em 
		ON mem.id_empresas = em.id 
		WHERE mem.id_modulos != 6 
		".$filtrado." 
		".$sede." 
		ORDER BY mo.fecha_creacion DESC LIMIT ".$limit." OFFSET ".$offset."
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
	                <th class="text-center">Modulo</th>
	                <th class="text-center">Empresa</th>
	                <th class="text-center">Estatus</th>
	                <th class="text-center">Fecha Creación</th>
	                <th class="text-center">Fecha Modificación</th>
	                <th class="text-center">Opciones</th>
	            </tr>
	            </thead>
	            <tbody>
	';
	if($conteo1>=1){
		while($row2 = mysqli_fetch_array($proceso2)) {
			if($row2["mem_estatus"]==1){
				$mem_estatus = "Activo";
			}else if($row2["mem_estatus"]==0){
				$mem_estatus = "Inactivo";
			}
			$html .= '
		                <tr id="tr_'.$row2["modulos_empresas_id"].'">
		                    <td style="text-align:center;">'.$row2["modulo_nombre"].'</td>
		                    <td style="text-align:center;">'.$row2["empresa_nombre"].'</td>
		                    <td style="text-align:center;">'.$mem_estatus.'</td>
		                    <td style="text-align:center;">'.$row2["mem_fecha_creacion"].'</td>
		                    <td style="text-align:center;">'.$row2["mem_fecha_modificacion"].'</td>
		   ';

			if($row2["mem_estatus"]==1){
				$html .= '
					<td class="text-center" nowrap="nowrap">
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#inactivar1" onclick="inactivar1('.$row2["modulos_empresas_id"].');">Inactivar</button>
					</td>
				';
			}else if($row2["mem_estatus"]==0){
				$html .= '
		                    <td class="text-center" nowrap="nowrap">
					    		<button type="button" class="btn btn-success" data-toggle="modal" data-target="#activar1" onclick="activar1('.$row2["modulos_empresas_id"].');">Activar</button>
		    		 		</td>
		    	';
		    }
		    	$html .= '
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

if($condicion=='activar1'){
	$modulos_empresas_id = $_POST["modulos_empresas_id"];

	$sql1 = "UPDATE modulos_empresas SET estatus = 1, fecha_modificacion = '".$fecha_creacion."' WHERE id = ".$modulos_empresas_id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus"	=> "ok",
		"sql1"	=> $sql1,
		"msg"	=> "Se ha cambiado el estatus exitosamente!",
	];
	echo json_encode($datos);
}

if($condicion=='inactivar1'){
	$modulos_empresas_id = $_POST["modulos_empresas_id"];

	$sql1 = "UPDATE modulos_empresas SET estatus = 0, fecha_modificacion = '".$fecha_creacion."' WHERE id = ".$modulos_empresas_id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus"	=> "ok",
		"sql1"	=> $sql1,
		"msg"	=> "Se ha cambiado el estatus exitosamente!",
	];
	echo json_encode($datos);
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
		$filtrado = ' and (us.nombre1 LIKE "%'.$filtrado.'%" or us.nombre2 LIKE "%'.$filtrado.'%" or us.apellido1 LIKE "%'.$filtrado.'%" or us.apellido2 LIKE "%'.$filtrado.'%" or em.nombre LIKE "%'.$filtrado.'%" or pa.nombre LIKE "%'.$filtrado.'%")';
	}

	if($sede!=''){
		$sede = ' and (em.nombre = '.$sede.') ';
	}

	$limit = $consultasporpagina;
	$offset = ($pagina - 1) * $consultasporpagina;

	$sql1 = "SELECT 
		us.id as usuario_id,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		us.documento_tipo as documento_tipo,
		us.documento_numero as documento_numero,
		us.correo_empresa as correo_empresa,
		us.estatus_nomina as usuario_nomina_estatus,
		us.id_empresa as usuario_id_empresa,
		pa.nombre as pais_nombre,
		pa.codigo as pais_codigo,
		dti.id as dti_id,
		dti.nombre as dti_nombre 
		FROM usuarios us 
		INNER JOIN empresas em 
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON us.id_pais = pa.id 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id 
		WHERE us.estatus_nomina = 1  
		".$filtrado." 
		".$sede." 
		ORDER BY us.id DESC LIMIT ".$limit." OFFSET ".$offset."
	";
	
	$sql2 = "SELECT 
		us.id as usuario_id,
		us.nombre1 as nombre1,
		us.nombre2 as nombre2,
		us.apellido1 as apellido1,
		us.apellido2 as apellido2,
		us.documento_tipo as documento_tipo,
		us.documento_numero as documento_numero,
		us.correo_empresa as correo_empresa,
		us.estatus_nomina as usuario_nomina_estatus,
		us.id_empresa as usuario_id_empresa,
		pa.nombre as pais_nombre,
		pa.codigo as pais_codigo,
		dti.id as dti_id,
		dti.nombre as dti_nombre,
		em.nombre as empresa_nombre 
		FROM usuarios us 
		INNER JOIN empresas em 
		ON us.id_empresa = em.id 
		INNER JOIN paises pa
		ON us.id_pais = pa.id 
		INNER JOIN documento_tipo dti
		ON us.documento_tipo = dti.id 
		WHERE us.estatus_nomina = 1  
		".$filtrado." 
		".$sede." 
		ORDER BY us.id DESC LIMIT ".$limit." OFFSET ".$offset."
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
	                <th class="text-center">Usuario</th>
	                <th class="text-center">T Documento</th>
	                <th class="text-center">N Documento</th>
	                <th class="text-center">Correo</th>
	                <th class="text-center">Estatus</th>
	                <th class="text-center">Empresa</th>
	                <th class="text-center">Pais</th>
	                <th class="text-center">Opciones</th>
	            </tr>
	            </thead>
	            <tbody>
	';
	if($conteo1>=1){
		while($row2 = mysqli_fetch_array($proceso2)) {
			if($row2["usuario_nomina_estatus"]==1){
				$usuario_nomina_estatus = "Activo";
			}else if($row2["usuario_nomina_estatus"]==0){
				$usuario_nomina_estatus = "Inactivo";
			}
			$html .= '
		                <tr id="tr_'.$row2["usuario_id"].'">
		                    <td style="text-align:center;">'.$row2["nombre1"]." ".$row2["nombre2"]." ".$row2["apellido1"]." ".$row2["apellido2"].'</td>
		                    <td style="text-align:center;">'.$row2["dti_nombre"].'</td>
		                    <td style="text-align:center;">'.$row2["documento_numero"].'</td>
		                    <td style="text-align:center;">'.$row2["correo_empresa"].'</td>
		                    <td style="text-align:center;">'.$usuario_nomina_estatus.'</td>
		                    <td style="text-align:center;">'.$row2["empresa_nombre"].'</td>
		                    <td style="text-align:center;">'.$row2["pais_nombre"].'</td>
		                    <td style="text-align:center;" nowrap="nowrap">
		                    	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#opciones1" onclick="opciones1('.$row2["usuario_id"].');">Crear</button>
		                    	<button type="button" class="btn btn-info" data-toggle="modal" data-target="#opciones2" onclick="opciones2('.$row2["usuario_id"].');">Ver</button>
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

if($condicion=='modulo_dependible1'){
	$modulo_id = $_POST["modulo_id"];
	if($modulo_id!=''){
		$sql1 = "SELECT * FROM modulos_sub WHERE id_modulos = ".$modulo_id." and estatus = 1";
		$proceso1 = mysqli_query($conexion,$sql1);
		$conteo1 = mysqli_num_rows($proceso1);
		$html = '';
		if($conteo1>=1){
			while($row1 = mysqli_fetch_array($proceso1)) {
				$html .= '
					<option value="'.$row1["id"].'">'.$row1["nombre"].'</option>
				';
			}
		}else{
			$html .= '
				<option value="">No tiene</option>
			';
		}
	}else{
		$html = '<option value="">Seleccione</option>';
	}

	$datos = [
		"html"	=> $html,
	];
	echo json_encode($datos);
}

if($condicion=='submodulo_usuario1'){
	$modulo = $_POST["modulo"];
	$submodulo = $_POST["submodulo"];
	$usuario_id = $_POST["usuario_id"];

	$sql1 = "SELECT * FROM modulos_sub_usuarios WHERE id_usuarios = $usuario_id and id_modulos_sub = $submodulo LIMIT 1";
	$proceso1 = mysqli_query($conexion,$sql1);
	$conteo1 = mysqli_num_rows($proceso1);

	if($conteo1==0){
		$sql2 = "INSERT INTO modulos_sub_usuarios (id_modulos_sub,id_usuarios,estatus,responsable,fecha_creacion) VALUES 
		($submodulo,$usuario_id,1,$responsable,$fecha_creacion)";
		$proceso2 = mysqli_query($conexion,$sql2);

		$estatus = "ok";
		$msg = "Agregado satisfactoriamente!";
	}else{
		$estatus = "error";
		$msg = "Ya tenia agregado dicho submodulo!";
	}

	$datos = [
		"estatus"	=> $estatus,
		"msg"	=> $msg,
	];
	echo json_encode($datos);
}

if($condicion=='submodulo_usuario2'){
	$usuario_id = $_POST["usuario_id"];

	$html = '';

	$html .= '
		<input type="hidden" name="usuario_id2" id="usuario_id2">
		<div class="col-12 form-group form-check">
			<label for="modulo2" style="font-weight: bold;">Modulo</label>
		</div>
	';

	$sql1 = "SELECT * FROM modulos_sub_usuarios WHERE id_usuarios = $usuario_id and id_modulos_sub = $submodulo LIMIT 1";
	$proceso1 = mysqli_query($conexion,$sql1);
	$conteo1 = mysqli_num_rows($proceso1);

	if($conteo1==0){
		$sql2 = "INSERT INTO modulos_sub_usuarios (id_modulos_sub,id_usuarios,estatus,responsable,fecha_creacion) VALUES 
		($submodulo,$usuario_id,1,$responsable,$fecha_creacion)";
		$proceso2 = mysqli_query($conexion,$sql2);

		$estatus = "ok";
		$msg = "Agregado satisfactoriamente!";
	}else{
		$estatus = "error";
		$msg = "Ya tenia agregado dicho submodulo!";
	}

	$datos = [
		"estatus"	=> $estatus,
		"msg"	=> $msg,
	];
	echo json_encode($datos);
}

if($condicion=='opciones2'){
	$usuario_id = $_POST['usuario_id'];

	$sql1 = "SELECT 
	mos.id as mos_id,
	mos.nombre as mos_nombre,
	mo.id as mo_id,
	mo.nombre as mo_nombre, 
	msu.id as msu_id 
	FROM modulos_sub_usuarios msu
	INNER JOIN usuarios us 
	ON us.id = msu.id_usuarios 
	INNER JOIN modulos_sub mos 
	ON msu.id_modulos_sub = mos.id
	INNER JOIN modulos mo 
	ON mo.id = mos.id_modulos  
	WHERE msu.id_usuarios = ".$usuario_id;
	$proceso1 = mysqli_query($conexion,$sql1);
	$conteo1 = mysqli_num_rows($proceso1);
	$html = '';
	$contador = 1;
	if($conteo1>=1){
		while($row1 = mysqli_fetch_array($proceso1)) {
			$mos_nombre = $row1["mos_nombre"];
			$mos_id = $row1["mos_id"];
			$mo_id = $row1["mo_id"];
			$mo_nombre = $row1["mo_nombre"];
			$msu_id = $row1["msu_id"];
			$html .= '
			<div class="col-12 mt-3" id="opciones2_row_'.$msu_id.'">
				<div class="row">
					<div class="col-8">
						#'.$contador.': 
						Modulo: '.$mo_nombre.'/'.$mos_nombre.'
					</div>
					<div class="col-4">
						<button type="button" class="btn btn-danger" onclick="revocar1('.$msu_id.')">Revocar</button>
					</div>
				</div>
			</div>
			';
			$contador = $contador+1;
		}
	}

	$datos = [
		"html"	=> $html,
		"sql1"	=> $sql1,
	];
	echo json_encode($datos);
}

if($condicion=='revocar1'){
	$id = $_POST["modulos_sub_usuarios_id"];
	$sql1 = "DELETE FROM modulos_sub_usuarios WHERE id = ".$id;
	$proceso1 = mysqli_query($conexion,$sql1);

	$datos = [
		"estatus"	=> "ok",
		"msg"	=> "Se ha eliminado exitosamente!",
	];
	echo json_encode($datos);
}


?>