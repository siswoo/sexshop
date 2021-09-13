<?php

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

/**********SUBIR ARCHIVOS**********/
	function redimensionar_imagen($nombreimg, $rutaimg, $xmax, $ymax){
	    $ext = explode(".", $nombreimg);
	    $ext = $ext[count($ext)-1];

	    /*
	    if($ext!="jpg" && $ext!="jpeg" && $ext!="png"){
	        echo 'error';
	        exit;
	    }
	    */

	    if($ext == "jpg" || $ext == "jpeg")  
	        $imagen = imagecreatefromjpeg($rutaimg);
	    elseif($ext == "png")  
	        $imagen = imagecreatefrompng($rutaimg);

	    $x = imagesx($imagen);
	    $y = imagesy($imagen);
	          
	    if($x <= $xmax && $y <= $ymax){
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
	    return $img2;
	}
/***********************************************************/

?>