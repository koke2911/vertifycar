<?php
// model/model_multimedia.php
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

function normalize_sc($s)
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
        if (!@mkdir($dir, 0775, true)) throw new Exception("No fue posible crear el directorio: $dir");
    }
    if (!is_writable($dir)) throw new Exception("Directorio no escribible: $dir");
}
function sanitize_name($name)
{
    $base = pathinfo($name, PATHINFO_FILENAME);
    $ext  = pathinfo($name, PATHINFO_EXTENSION);
    $base = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $base);
    $base = trim($base, '._-');
    if ($base === '') $base = 'archivo';
    return [$base, strtolower($ext)];
}
function save_uploaded_media($inputName, $tipo, $dirImg, $dirVid)
{
    if (empty($_FILES[$inputName]) || !is_uploaded_file($_FILES[$inputName]['tmp_name'])) return [null, null];

    $file = $_FILES[$inputName];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception("Error al subir (código {$file['error']})");

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

    $isImage = in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);
    $isVideo = in_array($mime, ['video/mp4', 'video/webm', 'video/ogg'], true);

    if ($tipo === 'imagen' && !$isImage) throw new Exception("El archivo no es una imagen válida");
    if ($tipo === 'video'  && !$isVideo) throw new Exception("El archivo no es un video válido");

    // límites sugeridos
    if ($isImage && $file['size'] > 5 * 1024 * 1024)   throw new Exception("Imagen supera 5MB");
    if ($isVideo && $file['size'] > 200 * 1024 * 1024) throw new Exception("Video supera 200MB");

    list($safeBase, $origExt) = sanitize_name($file['name']);
    // aseguremos extensión por MIME si difiere
    if ($isImage) {
        $extByMime = ($mime === 'image/jpeg' ? 'jpg' : ($mime === 'image/png' ? 'png' : 'webp'));
    } else {
        $extByMime = ($mime === 'video/mp4' ? 'mp4' : ($mime === 'video/webm' ? 'webm' : 'ogg'));
    }
    $finalExt = ($origExt === $extByMime ? $origExt : $extByMime);

    $uploadDirAbs = ($tipo === 'imagen')
        ? realpath('../public/assets/media/imagenes')
        : realpath('../public/assets/media/videos');

    if ($uploadDirAbs === false) {
        $uploadDirAbs = ($tipo === 'imagen')
            ? ('../public/assets/media/imagenes')
            : ('../public/assets/media/videos');
    }

    ensure_upload_dir($uploadDirAbs);

    $finalName = $safeBase . '.' . $finalExt;
    $destAbs   = rtrim($uploadDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalName;

    $i = 1;
    while (file_exists($destAbs)) {
        $finalName = $safeBase . '-' . $i . '.' . $finalExt;
        $destAbs   = rtrim($uploadDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalName;
        $i++;
        if ($i > 500) throw new Exception("No fue posible generar un nombre único");
    }

    if (!@move_uploaded_file($file['tmp_name'], $destAbs)) throw new Exception("No fue posible mover el archivo al servidor");
    @chmod($destAbs, 0644);

    return [$finalName, $uploadDirAbs]; // nombre guardado + ruta
}
function delete_old_media_if_exists($dir, $filename)
{
    if (!$dir || !$filename) return;
    $path = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (is_file($path)) @unlink($path);
}

$id           = trim($_POST['id'] ?? '');
$id_categoria = $_POST['id_categoria'] !== '' ? (int)$_POST['id_categoria'] : null;
$tipo         = $_POST['tipo'] ?? 'imagen'; // 'imagen'|'video'
$titulo       = trim($_POST['titulo'] ?? '');
$descripcion  = trim($_POST['descripcion'] ?? '');
$tags         = normalize_sc($_POST['tags'] ?? '');
$fuente       = $_POST['fuente'] ?? 'file'; // 'file'|'url'
$url          = trim($_POST['url'] ?? '');
$archivoActual = trim($_POST['archivo'] ?? ''); // por si no se reemplaza el archivo
$estado       = (int)($_POST['estado'] ?? 1);
$usu_crea     = (int)($_SESSION['id_usuario'] ?? 0);

$conn->begin_transaction();

try {
    if ($titulo === '') throw new Exception("El título es obligatorio");
    if (!in_array($tipo, ['imagen', 'video'], true)) throw new Exception("Tipo inválido");
    if (!in_array($fuente, ['file', 'url'], true)) throw new Exception("Fuente inválida");
    if ($fuente === 'url' && $url === '') throw new Exception("Debe indicar la URL del recurso");

    // Subida de archivo si corresponde
    $nuevoNombre = null;
    $uploadDirUsed = null;
    if ($fuente === 'file') {
        list($nuevoNombre, $uploadDirUsed) = save_uploaded_media('media_file', $tipo, null, null); // dirs resueltos dentro
    }

    if ($id !== '') {
        // UPDATE
        $idI = (int)$id;

        // Si hay nuevo archivo, buscar el anterior para borrarlo
        $oldFile = null;
        $oldTipo = null;
        $oldFuente = null;
        if ($fuente === 'file' && $nuevoNombre !== null) {
            $st = $conn->prepare("SELECT archivo, tipo, fuente FROM multimedia WHERE id=?");
            $st->bind_param('i', $idI);
            $st->execute();
            $res = $st->get_result();
            if ($res && ($r = $res->fetch_assoc())) {
                $oldFile = $r['archivo'] ?? null;
                $oldTipo = $r['tipo'] ?? null;
                $oldFuente = $r['fuente'] ?? null;
            }
            $st->close();
        }

        if ($fuente === 'file') {
            if ($nuevoNombre !== null) {
                $sql = "UPDATE multimedia SET id_categoria=?, tipo=?, titulo=?, descripcion=?, tags=?, fuente='file', archivo=?, url=NULL, estado=? WHERE id=?";
                $st = $conn->prepare($sql);
                $st->bind_param("isssssii", $id_categoria, $tipo, $titulo, $descripcion, $tags, $nuevoNombre, $estado, $idI);
            } else {
                $sql = "UPDATE multimedia SET id_categoria=?, tipo=?, titulo=?, descripcion=?, tags=?, fuente='file', archivo=?, estado=? WHERE id=?";
                $st = $conn->prepare($sql);
                $arch = $archivoActual ?: null;
                $st->bind_param("isssssii", $id_categoria, $tipo, $titulo, $descripcion, $tags, $arch, $estado, $idI);
            }
        } else { // fuente=url
            $sql = "UPDATE multimedia SET id_categoria=?, tipo=?, titulo=?, descripcion=?, tags=?, fuente='url', archivo=NULL, url=?, estado=? WHERE id=?";
            $st = $conn->prepare($sql);
            $st->bind_param("isssssii", $id_categoria, $tipo, $titulo, $descripcion, $tags, $url, $estado, $idI);
        }

        if (!$st->execute()) throw new Exception("Error al actualizar: " . $st->error);
        $st->close();

        // Borrar archivo anterior si se subió uno nuevo (y antes era file)
        if ($fuente === 'file' && $nuevoNombre !== null && $oldFuente === 'file' && $oldFile) {
            $oldDir = ($oldTipo === 'imagen')
                ? (__DIR__ . '/../public/assets/media/imagenes')
                : (__DIR__ . '/../public/assets/media/videos');
            delete_old_media_if_exists($oldDir, $oldFile);
        }
    } else {
        // INSERT
        if ($fuente === 'file' && $nuevoNombre === null && $archivoActual === '') {
            throw new Exception("Debe adjuntar un archivo");
        }

        $sql = "INSERT INTO multimedia (id_categoria,tipo,titulo,descripcion,tags,fuente,archivo,url,estado,usu_crea)
          VALUES (?,?,?,?,?,?,?,?,?,?)";
        $st = $conn->prepare($sql);

        $archivoAGuardar = ($fuente === 'file') ? ($nuevoNombre ?? $archivoActual) : null;
        $urlAGuardar     = ($fuente === 'url')  ? $url : null;

        $st->bind_param(
            "issssssssi",
            $id_categoria,
            $tipo,
            $titulo,
            $descripcion,
            $tags,
            $fuente,
            $archivoAGuardar,
            $urlAGuardar,
            $estado,
            $usu_crea
        );
        if (!$st->execute()) throw new Exception("Error al insertar: " . $st->error);
        $st->close();
    }

    $conn->commit();
    echo json_encode(['codigo' => 0, 'mensaje' => 'Multimedia guardada con éxito']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Error en el proceso', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
