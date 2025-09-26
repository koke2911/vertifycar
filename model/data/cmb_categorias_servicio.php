<?php
session_start();

$conn = new mysqli($_SESSION['servername'], $_SESSION['username'], $_SESSION['password'], $_SESSION['dbname'], $_SESSION['port']);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, nombre FROM categorias where estado=1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        $filas[] = [
            'id' => ($row['id']),
            'glosa' => ($row['nombre'])
        ];
    }
}

if (empty($filas)) {
    echo json_encode([]);
} else {

    echo json_encode($filas);
}
