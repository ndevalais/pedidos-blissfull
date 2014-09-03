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
$sql = "SELECT	id_lineas, lineas
		FROM lineas
		ORDER BY orden";

$query=mysql_query($sql) or die(mysql_error());

$arr = array();
while($obj = mysql_fetch_object($query)) {
    $arr[] = $obj;
}
if (!$arr) 
	$arr = array("error" => "Error" , "mensaje" => "No existen registros para mostrar");

# JSON-encode the response
echo $json_response = json_encode($arr);
?>