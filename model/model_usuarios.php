<?php
session_start();

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

$id                = isset($_POST['txt_id']) ? trim($_POST['txt_id']) : '';
$rut               = isset($_POST['txt_rut']) ? trim($_POST['txt_rut']) : '';
$clave             = isset($_POST['txt_clave']) ? trim($_POST['txt_clave']) : '';
$nombre            = isset($_POST['txt_nombre']) ? trim($_POST['txt_nombre']) : '';
$apellidos         = isset($_POST['txt_apellidos']) ? trim($_POST['txt_apellidos']) : '';
$contacto          = isset($_POST['txt_fono']) ? trim($_POST['txt_fono']) : null;
$email             = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$tipo              = isset($_POST['cmb_rol']) ? trim($_POST['cmb_rol']) : null;
$estado            = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;


$conn->begin_transaction();

try {
    if ($id !== '') {
        // --- UPDATE ---

        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE rut = ? AND id <> ?");
        $stmt->bind_param("si", $rut, $id);
        $stmt->execute();
        $stmt->bind_result($dupRut);
        $stmt->fetch();
        $stmt->close();

        if ($dupRut > 0) {
            throw new Exception("Ya existe un usuario con el RUT $rut");
        }

        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id <> ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->bind_result($dupEmail);
        $stmt->fetch();
        $stmt->close();

        if ($dupEmail > 0) {
            throw new Exception("Ya existe un usuario con el email $email");
        }

        $sql = "UPDATE usuarios 
                SET rut = ?, nombre = ?, apellidos = ?,  contacto = ?, email = ?, tipo = ?, estado = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssii",
            $rut,
            $nombre,
            $apellidos,
            $contacto,
            $email,
            $tipo,
            $estado,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar usuario: " . $stmt->error);
        }
        $stmt->close();
    } else {
        // --- INSERT ---
        // Verificar duplicados de RUT y Email
        $stmt = $conn->prepare("SELECT 
                                    SUM(CASE WHEN rut = ? THEN 1 ELSE 0 END) AS dup_rut,
                                    SUM(CASE WHEN email = ? THEN 1 ELSE 0 END) AS dup_email
                                FROM usuarios");
        $stmt->bind_param("ss", $rut, $email);
        $stmt->execute();
        $stmt->bind_result($dupRut, $dupEmail);
        $stmt->fetch();
        $stmt->close();

        if ((int)$dupRut > 0)  throw new Exception("Ya existe un usuario con el RUT $rut");
        if ((int)$dupEmail > 0) throw new Exception("Ya existe un usuario con el email $email");


        $hash = hash('md5', $rut);

        $sql = "INSERT INTO usuarios (rut, clave, nombre, apellidos, contacto, email, tipo, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssis",
            $rut,
            $hash,
            $nombre,
            $apellidos,
            $contacto,
            $email,
            $tipo,
            $estado
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al insertar usuario: " . $stmt->error);
        }
        $stmt->close();
    }

    $conn->commit();
    echo json_encode(['codigo' => 0, 'mensaje' => 'Usuario guardado con éxito']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['codigo' => 2, 'mensaje' => 'Error en el proceso', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
