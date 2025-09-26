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

$sql = "SELECT id AS ID, glosa AS GLOSA FROM tipos_usuario WHERE estado = 1 ORDER BY glosa ASC";
$res = $conn->query($sql);
$out = [];
if ($res) {
    while ($r = $res->fetch_assoc()) $out[] = $r;
    $res->free();
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);
$conn->close();
