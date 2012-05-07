<?php
/**
 * Función para aplicar el efecto polaroid a una imagen.
 *
 * @param string $path Ruta de la imagen
 * @param float $giro Angulo de giro de la imagen
 * @param int $rfondo componente rojo del color de fondo
 * @param int $gfondo componente verde del color de fondo
 * @param int $bfondo componente azul del color de fondo
 * @return resource imagen con el efecto polaroid aplicada
 */
function efectoPolaroid($path, $giro, $rfondo, $gfondo, $bfondo)
{
	ob_start();
	// Cargamos la imagen a la que queremos aplicar el efecto polaroid
	$imgBase = imagecreatefromjpeg($path);
	//Creamos una nueva imagen.
	$img = imagecreatetruecolor(imagesx($imgBase) + 25, imagesy($imgBase) + 65);
	$blanco = imagecolorallocate($img, 255,255,255);
	$gris = imagecolorallocate($img, 2204,204,204);

	//Rellenamos la nueva imagen de blanco
	imagefill($img,0,0, $gris);

	//Copiamos la imagen a la que queremos aplicar el efecto polariod en nuestra nueva imagen.
	imagecopy($img, $imgBase, 11, 11, 0, 0, imagesx($imgBase), imagesy($imgBase));

	//Eliminamos nuestra imagen de memoria, ya que ya no hace falya
	imagedestroy($imgBase);

	//Color del borde
	$color = imagecolorallocate($img, 192,192,192);
	//Le ponemos un borde gris a nuestra imagen.
	imagerectangle($img, 0,0, imagesx($img)-4, imagesy($img)-4, $color);

	//Colores para la sombra
	$gris1 = imagecolorallocate($img, 208,208,208);
	$gris2 = imagecolorallocate($img, 224,224,224);
	$gris3 = imagecolorallocate($img, 240,240,240);

	//Le añadimos una pequeña sombra
	imageline($img, 2, imagesy($img)-3, imagesx($img)-1,imagesy($img)-3,$gris1);
	imageline($img, 4, imagesy($img)-2, imagesx($img)-1,imagesy($img)-2,$gris2);
	imageline($img, 6, imagesy($img)-1, imagesx($img)-1,imagesy($img)-1,$gris3);
	imageline($img, imagesx($img)-3, 2, imagesx($img)-3,imagesy($img)-4,$gris1);
	imageline($img, imagesx($img)-2, 4, imagesx($img)-2,imagesy($img)-4,$gris2);
	imageline($img, imagesx($img)-1, 6, imagesx($img)-1,imagesy($img)-4,$gris3);

	//Rotamos la imagen
	$fondo = imagecolorallocate($img, $rfondo, $gfondo, $bfondo);
	$rotatedImg = imagerotate($img, $giro, $fondo);

	//Destruimos la imagen con la que hemos estado trabajando
	imagejpeg($rotatedImg);
	imagedestroy($img);

	//Devolvemos la imagen rotada
	//return $rotatedImg;
	
	
	//header( "Content-Type: image/jpg" );
	echo $rotatedImg;
	$buffer = ob_get_clean();
	file_put_contents(INST_PATH.'app/webroot/images/p71.jpg', "$buffer") or die('pailas!');
	return NULL;
}

?>
