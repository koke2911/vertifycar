<?php
// model/model_faq.php
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
    die(json_encode(['codigo' => 2, 'mensaje' => 'Error de conexión', 'error' => $conn->connect_error]));
}

function normalize_tags($s)
{
    $s = str_replace(["\r", "\n", ","], ";", (string)$s);
    $s = preg_replace('/\s*;\s*/', ';', $s);
    $s = preg_replace('/;{2,}/', ';', $s);
    $s = preg_replace('/^\s*;\s*|\s*;\s*$/', '', $s);
    return trim($s);
}

$id           = isset($_POST['id']) ? trim($_POST['id']) : '';
$id_categoria = $_POST['id_categoria'] !== '' ? (int)$_POST['id_categoria'] : null;
$pregunta     = trim($_POST['pregunta'] ?? '');
$respuesta    = trim($_POST['respuesta'] ?? '');
$tags         = normalize_tags($_POST['tags'] ?? '');
$orden        = (int)($_POST['orden'] ?? 0);
$estado       = (int)($_POST['estado'] ?? 1);
$usu_crea     = (int)($_SESSION['id_usuario'] ?? 0);

$conn->begin_transaction();
try {
    if ($pregunta === '' || $respuesta === '') {
        throw new Exception("Pregunta y respuesta son obligatorias");
    }

    if ($id !== '') {
        $sql = "UPDATE faq SET id_categoria=?, pregunta=?, respuesta=?, tags=?, orden=?, estado=? WHERE id=?";
        $st = $conn->prepare($sql);
        $st->bind_param("isssiii", $id_categoria, $pregunta, $respuesta, $tags, $orden, $estado, $id);
        if (!$st->execute()) throw new Exception("Error al actualizar: " . $st->error);
        $st->close();
    } else {
        $sql = "INSERT INTO faq (id_categoria,pregunta,respuesta,tags,orden,estado,usu_crea)
          VALUES (?,?,?,?,?,?,?)";
        $st = $conn->prepare($sql);
        $st->bind_param("isssiii", $id_categoria, $pregunta, $respuesta, $tags, $orden, $estado, $usu_crea);
        if (!$st->execute()) throw new Exception("Error al insertar: " . $st->error);
        $st->close();
    }

    $conn->commit();
    echo json_encode(['codigo' => 0, 'mensaje' => 'FAQ guardada con éxito']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Error en el proceso', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
