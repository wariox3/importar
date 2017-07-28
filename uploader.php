<?php

$usuario = "root";
$password = "70143086";
//$servidor = "181.49.169.98";
$servidor = "192.168.1.104";
//$servidor = "localhost";
$basededatos = "bdkl";

// creación de la conexión a la base de datos con mysql_connect()
$conexion = mysqli_connect($servidor, $usuario, $password) or die("No se ha podido conectar al servidor de Base de datos");

// Selección del a base de datos a utilizar
$db = mysqli_select_db($conexion, $basededatos) or die("Upps! Pues va a ser que no se ha podido conectar a la base de datos");

$uploadedfileload = "true";
$uploadedfile_size = $_FILES['uploadedfile'][size];
//echo $_FILES[uploadedfile][name];


$file_name = $_FILES[uploadedfile][name];
$add = "/var/www/html/cargas/$file_name";
if ($uploadedfileload == "true") {

    if (move_uploaded_file($_FILES[uploadedfile][tmp_name], $add)) {
        echo " Ha sido subido satisfactoriamente<br />";
    } else {
        echo "Error al subir el archivo";
    }
} else {
    echo $msg;
}
$consulta = "TRUNCATE TABLE guias_importar;";
$resultado = mysqli_query($conexion, $consulta) or die("Algo ha ido mal en la consulta a la base de datos"); 
$fp = fopen($add, "r");
while (!feof($fp)) {
    $linea = fgets($fp);
    $porciones = explode(";", $linea);
    if($porciones[0]) {
        $consulta = "SELECT codigo_ciudad_kit FROM ciudad_interface WHERE codigo_ciudad_interface = " . $porciones[8];
        $resultado = mysqli_query($conexion, $consulta) or die("Algo ha ido mal en la consulta a la base de datos");    
        $ciudades = mysqli_fetch_array( $resultado );
        $codigoCiudad = $ciudades[0];
        $peso = round($porciones[14]);
        $declarado = round($porciones[20]);
        if(!$codigoCiudad){ $codigoCiudad = 0;}        
        $sql = "INSERT INTO guias_importar (Guia, NmDestinatario, DirDestinatario, TelDestinatario, CodigoCiudad, Unidades, Peso, Declarado, DocumentoCliente)
                VALUES ($porciones[0], '$porciones[5]', '$porciones[6]', '$porciones[7]', $codigoCiudad, $porciones[13], $peso, $declarado, '$porciones[22]')";

        if ($conexion->query($sql) === TRUE) {
            echo "Se inserto el registro " . $porciones[22] . "<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conexion->error;
        }        
    }    
}
fclose($fp);

exit;
?>

