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
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'ID inválido']);
    exit;
}

$conn->begin_transaction();
try {
    $st = $conn->prepare("UPDATE multimedia SET estado=2 WHERE id=?");
    $st->bind_param("i", $id);
    if (!$st->execute()) throw new Exception("Error al eliminar: " . $st->error);
    $st->close();
    $conn->commit();
    echo json_encode(['codigo' => 0, 'mensaje' => 'Multimedia eliminada']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Error', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
