<?php
$directorio = './archivos/'; //Declaramos un  variable con la ruta donde guardaremos los archivos
if(!file_exists($directorio)) mkdir($directorio);// Si no existe la carpeta de destino la creamos
$dir=opendir($directorio); //Abrimos el directorio de destino

//Como el elemento es un arreglo utilizamos foreach para extraer todos los valores
foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name)
{
  //Validamos que el archivo exista
  if($_FILES["archivos"]["name"][$key]) {
    $filename = $_FILES["archivos"]["name"][$key]; //Obtenemos el nombre original del archivo
    $source = $_FILES["archivos"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivo  
    $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivo      
    //Movemos y validamos que el archivo se haya cargado correctamente. El primer campo es el origen y el segundo el destino
    if(move_uploaded_file($source, $target_path)) { 
      echo "El archivo $filename se ha almacenado en forma exitosa.<br>";
      } else {  
      echo "Ha ocurrido un error, por favor inténtelo de nuevo.<br>";
    }
  }
};

$cont = 0;
$elemento = scandir($directorio);
//Se leen todos los archivos que tiene la carpeta archivos
for ($i=0; $i < count($elemento)  ; $i++) { 
  if( $elemento[$i] != "." && $elemento[$i] != ".."){
    $nombresArchivos[$cont] = $elemento[$i];
    $GLOBALS['cont'] = $GLOBALS['cont'] + 1;
  }
}
closedir($dir); //Cerramos el directorio de destino

//aquí empieza la conexión de la base de datos, y el llenado de las tablas
$servername = "localhost";
$operador = "";
$conn = new mysqli($servername, "root", "", "practica");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
//verifica que por cada nombre de los archivos exista una fila llamada de la misma manera en la tabla tablaFrecuencias, sino existe la crea
foreach ($nombresArchivos as $key => $value) {
  $columnas = explode(".", $value); 
  $result = $conn->query("SELECT titulo FROM tablafrecuencias WHERE titulo =  '".$columnas[0]."'"); 
  if (!$result || $result ->num_rows == 0 ) {
    $data = file_get_contents($value);
    $conn->query("INSERT INTO tablafrecuencias VALUES (0,'".$columnas[0]."','".$data."')");
    $nuevasTablas[] = $columnas[0];
  }
}
$conn->close();
?>
