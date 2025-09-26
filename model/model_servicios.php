<?php
// model/model_servicios.php
session_start();
header('Content-Type: application/json; charset=utf-8');

// === Conexión ===
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

// === Helpers ===
function normalize_items($s)
{
    $s = str_replace(["\r", "\n", ","], ";", (string)$s);
    $s = preg_replace('/\s*;\s*/', ';', $s);
    $s = preg_replace('/;{2,}/', ';', $s);
    $s = preg_replace('/^\s*;\s*|\s*;\s*$/', '', $s);
    return trim($s);
}

function ensure_upload_dir($dir)
{
    if (!is_dir($dir)) {
        if (!@mkdir($dir, 0775, true)) {
            throw new Exception("No fue posible crear el directorio de imágenes");
        }
    }
    if (!is_writable($dir)) {
        throw new Exception("El directorio de imágenes no es escribible");
    }
}

function save_uploaded_image($inputName, $uploadDirAbs)
{
    if (empty($_FILES[$inputName]) || !is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
        return null;
    }

    $file = $_FILES[$inputName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error al subir la imagen (código {$file['error']})");
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("La imagen supera 5MB");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($allowed[$mime])) {
        throw new Exception("Formato no permitido ($mime). Solo jpg, png, webp.");
    }
    $extByMime = $allowed[$mime];

    // nombre original (sanitizado)
    $orig = $file['name'];
    $origBase = pathinfo($orig, PATHINFO_FILENAME);
    $origExt  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $safeBase = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $origBase);
    $safeBase = trim($safeBase, '._-');
    if ($safeBase === '') {
        $safeBase = 'archivo';
    }

    $finalExt = $origExt === $extByMime ? $origExt : $extByMime;
    $finalName = $safeBase . '.' . $finalExt;

    ensure_upload_dir($uploadDirAbs);

    $destAbs = rtrim($uploadDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalName;
    $i = 1;
    while (file_exists($destAbs)) {
        $finalName = $safeBase . '-' . $i . '.' . $finalExt;
        $destAbs = rtrim($uploadDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalName;
        $i++;
    }

    if (!@move_uploaded_file($file['tmp_name'], $destAbs)) {
        throw new Exception("No fue posible guardar la imagen en el servidor");
    }
    @chmod($destAbs, 0644);

    return $finalName;
}

function delete_old_image_if_exists($uploadDirAbs, $filename)
{
    if (!$filename) return;
    $path = rtrim($uploadDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (is_file($path)) {
        @unlink($path);
    }
}

// === Parámetros POST ===
$id            = isset($_POST['id']) ? trim($_POST['id']) : '';
$id_categoria  = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : null;
$nombre        = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$descripcion   = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$items         = isset($_POST['items']) ? normalize_items($_POST['items']) : '';
$valor         = isset($_POST['valor']) ? (int)$_POST['valor'] : 0;
$agenda        = isset($_POST['agenda']) ? (int)$_POST['agenda'] : 0;
$pago          = isset($_POST['pago']) ? (int)$_POST['pago'] : 0;
$estado        = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;
$imagen_actual = isset($_POST['imagen']) ? trim($_POST['imagen']) : '';
$usu_crea      = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : 0;

// Directorio de imágenes
$UPLOAD_DIR_ABS = realpath('../public/assets/img');
if ($UPLOAD_DIR_ABS === false) {
    $UPLOAD_DIR_ABS = '../public/assets/img';
}

$conn->begin_transaction();

try {
    if ($nombre === '' || $descripcion === '') {
        throw new Exception("Nombre y descripción son obligatorios");
    }

    $nuevoFilename = save_uploaded_image('srv_imagen_file', $UPLOAD_DIR_ABS);

    if ($id !== '') {
        // UPDATE
        $idI = (int)$id;
        $oldFile = null;
        if ($nuevoFilename !== null) {
            $sqlOld = "SELECT imagen FROM servicios WHERE id=?";
            $st = $conn->prepare($sqlOld);
            $st->bind_param('i', $idI);
            $st->execute();
            $res = $st->get_result();
            if ($res && ($r = $res->fetch_assoc())) {
                $oldFile = $r['imagen'] ?? null;
            }
            $st->close();
        }

        if ($nuevoFilename !== null) {
            $sql = "UPDATE servicios
                       SET id_categoria=?, nombre=?, descripcion=?, items=?, valor=?, agenda=?, pago=?, estado=?, imagen=?
                     WHERE id=?";
            $st = $conn->prepare($sql);
            $st->bind_param(
                "isssiiiisi",
                $id_categoria,
                $nombre,
                $descripcion,
                $items,
                $valor,
                $agenda,
                $pago,
                $estado,
                $nuevoFilename,
                $idI
            );
        } else {
            $sql = "UPDATE servicios
                       SET id_categoria=?, nombre=?, descripcion=?, items=?, valor=?, agenda=?, pago=?, estado=?
                     WHERE id=?";
            $st = $conn->prepare($sql);
            $st->bind_param(
                "isssiiiii",
                $id_categoria,
                $nombre,
                $descripcion,
                $items,
                $valor,
                $agenda,
                $pago,
                $estado,
                $idI
            );
        }

        if (!$st->execute()) {
            throw new Exception("Error al actualizar servicio: " . $st->error);
        }
        $st->close();

        if ($nuevoFilename !== null && $oldFile && $oldFile !== $nuevoFilename) {
            delete_old_image_if_exists($UPLOAD_DIR_ABS, $oldFile);
        }
    } else {
        // INSERT
        $imagenAGuardar = $nuevoFilename !== null ? $nuevoFilename : $imagen_actual;

        $sql = "INSERT INTO servicios
                (id_categoria, nombre, descripcion, items, valor, agenda, pago, estado, imagen, usu_crea)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $st = $conn->prepare($sql);
        $st->bind_param(
            "isssiiiisi",
            $id_categoria,
            $nombre,
            $descripcion,
            $items,
            $valor,
            $agenda,
            $pago,
            $estado,
            $imagenAGuardar,
            $usu_crea
        );

        if (!$st->execute()) {
            throw new Exception("Error al insertar servicio: " . $st->error);
        }
        $st->close();
    }

    $conn->commit();
    echo json_encode(['codigo' => 0, 'mensaje' => 'Servicio guardado con éxito']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Error en el proceso', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
