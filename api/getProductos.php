<?php 
require_once 'db.php'; // The mysql database connection script
$id_lineas = '1';
if(isset($_GET['id_lineas'])){
	$id_lineas = $_GET['id_lineas'];
}
$id_idioma = 'ES';
if(isset($_GET['id_idioma'])){
	$id_idioma = $_GET['id_idioma'];
}
$sql = "SELECT	vw_productos.id_producto,
				vw_productos.id_coleccion,
				vw_productos.item,
				vw_productos.descripcion,
				vw_productos.colores,
				vw_productos.talles,
				vw_productos.precio,
				vw_productos.pagina_cat_desde,
				vw_productos.pagina_cat_hasta,
				vw_productos.imagen_cat,
				vw_productos.coleccion,
				vw_productos.orden,
				vw_productos.costo_trasp_ship,
				vw_productos.costo_parcial,
				vw_productos.ganancias,
				vw_productos.precio_publico,
				vw_productos.id_lineas,
				vw_productos.lineas
		FROM vw_productos
		WHERE id_lineas = ".$id_lineas." ORDER BY vw_productos.orden ASC";

$query=mysql_query($sql) or die(mysql_error());

# Collect the results
$arr = array();
while($obj = mysql_fetch_object($query)) {
    $arr[] = $obj;
}
mysql_free_result($query);
if (!$arr) 
	$arr = array("error" => "Error" , "mensaje" => "No existen registros para mostrar");
	
# JSON-encode the response
echo $json_response = json_encode($arr);
?>