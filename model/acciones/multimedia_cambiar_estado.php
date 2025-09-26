<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
$conn = new mysqli($_SESSION['servername'], $_SESSION['username'], $_SESSION['password'], $_SESSION['dbname'], $_SESSION['port']);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['codigo' => 2, 'mensaje' => 'Error de conexión', 'error' => $conn->connect_error]));
}

$id = (int)($_POST['id'] ?? 0);
$estado = (int)($_POST['estado'] ?? -1);
if ($id <= 0 || !in_array($estado, [0, 1], true)) {
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Parámetros inválidos']);
    exit;
}

$conn->begin_transaction();
try {
    $st = $conn->prepare("UPDATE multimedia SET estado=? WHERE id=?");
    $st->bind_param("ii", $estado, $id);
    if (!$st->execute()) throw new Exception("Error al actualizar: " . $st->error);
    $st->close();
    $conn->commit();
    echo json_encode(['codigo' => 0, 'mensaje' => 'Estado actualizado']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Error', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
