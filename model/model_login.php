<?php
session_start();
require_once('../config/database.php');

$conn = new mysqli($servername, $username, $password, $dbname, $port);
$conn->set_charset("utf8mb4");


$_SESSION['servername'] = $servername;
$_SESSION['username'] = $username;
$_SESSION['password'] = $password;
$_SESSION['dbname'] = $dbname;
$_SESSION['port'] = $port;


$usuario = $_POST['usuario'];
$pass = $_POST['pass'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$tipo_usuario = $_SESSION['tipo'];

$pass = hash('md5', $pass);


$stmt = $conn->prepare("SELECT * FROM usuarios WHERE rut=? AND clave=? ");
$stmt->bind_param("ss", $usuario, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $_SESSION['id_usuario'] = $row['id'];
        $_SESSION['rut_usuario'] = $row['rut'];
        $_SESSION['nombre_usuario'] = $row['nombre'] . ' ' . $row['apellidos'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['tipo'] = $row['tipo'];
        $estado = $row['estado'];
       
    }

    if ($estado == 1) {
        echo 1;
    } else {
        echo 'Usuario Bloqueado';
    }
} else {
    echo 'Usuario o password incorrectos';
}
