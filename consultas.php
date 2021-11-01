<?php

$searchData = $_GET["buscar"];
//$searchData = "Olympics 2020";
$servername = "localhost";
$operador = "";
$activadoPATRON = false;
$consulta = "";

// Create connection
$conn = new mysqli($servername, "root", "", "practica");
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//dividimos la cadena por espacios
$palabras = explode(" ", $searchData);
	foreach ($palabras as $clave => $valor) {
		if ($activadoPATRON) {
			if ($valor == ")") $activadoPATRON = false;
				else $consulta = $consulta."*".$valor."*";
		} elseif ($valor == "and" || $valor == "AND") {
			$consulta = $consulta."+";
		} elseif ($valor == "not" || $valor == "NOT") { 
			$consulta = $consulta."-";
		} elseif ($valor == "PATRON(") {
			$activadoPATRON = true;
		} 
		else{
			if ($valor == "or" || $valor == "OR" || $valor != "") $consulta = $consulta.$valor." ";
		}
	};
//se imprime y devulve el resultado final de la busqueda
$result = $conn->query("SELECT titulo, contenido, MATCH(titulo,contenido) AGAINST('".$consulta."' IN BOOLEAN MODE) AS rel FROM tablafrecuencias ORDER BY rel Desc");
if (!$result || $result ->num_rows == 0 ) {
	echo "No hay resultados";
} else {
	while ($row = $result->fetch_assoc()) {
		if($row['rel'] != 0){
			$contenido[$row["titulo"]][] = str_split($row['contenido'], 70);
			echo "Archivo: ".$row["titulo"]. ".txt - Descripción: ".$contenido[$row["titulo"]][0][0]." - Frecuencia: ".$row['rel'];
			echo " <br>";
		}
	}
}  
$conn->close();
?>