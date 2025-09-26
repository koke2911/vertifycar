<?php
// model/servicios_cambiar_estado.php
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
    die(json_encode([
        'codigo'  => 2,
        'mensaje' => 'Error de conexión',
        'error'   => $conn->connect_error
    ]));
}

$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$estado = isset($_POST['estado']) ? (int)$_POST['estado'] : -1;

if ($id <= 0 || !in_array($estado, [0, 1], true)) {
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Parámetros inválidos']);
    exit;
}

$conn->begin_transaction();

try {
    $sql = "UPDATE servicios SET estado = ? WHERE id = ?";
    $st  = $conn->prepare($sql);
    if (!$st) {
        throw new Exception("Error en prepare: " . $conn->error);
    }
    $st->bind_param("ii", $estado, $id);
    if (!$st->execute()) {
        throw new Exception("Error al actualizar estado: " . $st->error);
    }
    $st->close();

    $conn->commit();
    echo json_encode(['codigo' => 0, 'mensaje' => 'Estado actualizado con éxito']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode([
        'codigo'  => 2,
        'mensaje' => 'Error en el proceso',
        'error'   => $e->getMessage()
    ]);
} finally {
    $conn->close();
}
