<?php
require_once('../../config/database.php');

try {
    $conn = new mysqli($servername, $username, $password, $dbname,$port);
    $conn->set_charset("utf8mb4");
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    echo "Error en la conexi n: " . $e->getMessage();
    exit();
}

$sql="SELECT * FROM regiones where estado=1";

$result = $conn->query($sql);
// $filas = [];


if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {


        $filas[] = [
            'CODIGO' => ($row['codigo']),
            'NOMBRE' => ($row['nombre'])
            
        ];
    }
}
// print_r($filas);

if(empty($filas)){
    echo json_encode([]);
}else{

    echo json_encode( $filas);
}