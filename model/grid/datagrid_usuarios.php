<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli(
    $_SESSION['servername'],
    $_SESSION['username'],
    $_SESSION['password'],
    $_SESSION['dbname'],
    $_SESSION['port']
);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "
SELECT
  u.id,
  u.rut,
  u.nombre,
  u.apellidos,
  u.contacto AS fono,
  u.email    AS correo,
  u.estado,
  CASE WHEN u.estado = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado_glosa,
  tu.id      AS rol_id,
  tu.glosa   AS rol
FROM usuarios u
INNER JOIN tipos_usuario tu ON tu.id = u.tipo
ORDER BY u.id DESC
";

$res = $conn->query($sql);
$data = [];
if ($res) {
    while ($r = $res->fetch_assoc()) $data[] = $r;
    $res->free();
}

echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
$conn->close();
