<?php
session_start();

$conn = new mysqli($_SESSION['servername'], $_SESSION['username'], $_SESSION['password'], $_SESSION['dbname'], $_SESSION['port']);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo 'aqui';
$sql = "SELECT  
    u.id,
    u.rut,
    u.nombre,
    u.apellidos,
    u.contacto AS fono,
    u.email AS correo,
    u.estado,
    CASE 
        WHEN u.estado = 1 THEN 'Activo' 
        ELSE 'Inactivo' 
    END AS estado_glosa,
    tu.id AS rol_id,
    tu.glosa AS rol
FROM usuarios u
INNER JOIN tipos_usuario tu ON tu.id = u.tipo;
";
$result = $conn->query($sql);

$filas = [];


if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {


        $filas[] = $row;
    }
}
// print_r($filas);
if (empty($filas)) {
    echo json_encode(['data' => '']);
} else {

    echo json_encode(['data' => $filas]);
}
