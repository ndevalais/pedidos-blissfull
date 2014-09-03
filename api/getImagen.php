<?php 
require_once 'db.php'; // The mysql database connection script
$id_producto = '1';
if(isset($_GET['id_producto'])){
	$id_producto = $_GET['id_producto'];
}

$sql = "SELECT	productos.id_producto,
				productos.pagina_cat_desde,
				productos.pagina_cat_hasta,
				productos_imagenes.imagen,
				productos_imagenes.principal
		FROM	productos
		LEFT JOIN productos_imagenes ON productos.id_producto = productos_imagenes.id_producto
		WHERE productos.id_producto = ".$id_producto." ORDER BY productos_imagenes.principal DESC";

$query=mysql_query($sql) or die(mysql_error());

# Collect the results
$arr = array();
while($obj = mysql_fetch_object($query)) {
	if (is_null($obj->imagen)) {
		// [{"id_producto":"23","pagina_cat_desde":"14","pagina_cat_hasta":"15","imagen":null,"principal":null}]
		if ($obj->pagina_cat_desde<10) {
			$imagen = "image-00".$obj->pagina_cat_desde.".jpg";
		} else {
			$imagen = "image-0".$obj->pagina_cat_desde.".jpg";
		}
		$arr[] = array("id_producto"=>$obj->id_producto, "pagina_cat_desde" => $obj->pagina_cat_desde, "pagina_cat_hasta" => $obj->pagina_cat_hasta, "imagen" => $imagen, "principal" => null);	
	} else {
		$arr[] = $obj;
	}
}
mysql_free_result($query);
if (!$arr) {
	$arr = array("error" => "Error" , "mensaje" => "No existen registros para mostrar");
}
# JSON-encode the response
echo $json_response = json_encode($arr);
?>