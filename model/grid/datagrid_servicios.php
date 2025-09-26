<?php
// model/grid/datagrid_servicios.php
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

// Parámetros
$page     = max(1, (int)($_GET['page'] ?? 1));
$pageSize = (int)($_GET['page_size'] ?? 12);
$pageSize = ($pageSize > 0 && $pageSize <= 100) ? $pageSize : 12;

$q        = trim((string)($_GET['q'] ?? ''));
$estado   = $_GET['estado'] ?? '';            // '0' | '1' | ''
$idCat    = $_GET['id_categoria'] ?? '';      // num | ''
$id       = $_GET['id'] ?? '';
$agenda = $_GET['agenda'] ?? '';   // '0' | '1' | ''
$pago   = $_GET['pago'] ?? '';     // '0' | '1' | ''
// num | ''

$baseSelect = "
    SELECT
        s.id,
        s.id_categoria,
        s.nombre,
        s.descripcion,
        s.items,
        s.valor,
        s.agenda,
        s.pago,
        s.estado,
        s.fecha_crea,
        s.imagen,
        s.usu_crea,
        c.nombre AS categoria
    FROM servicios s
    LEFT JOIN categorias c ON c.id = s.id_categoria
    WHERE 1=1 AND s.estado <> 2
";

// ===== Detalle por ID =====
if ($id !== '' && ctype_digit((string)$id)) {
    $sql = $baseSelect . " AND s.id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $idI = (int)$id;
    $stmt->bind_param('i', $idI);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
        $stmt->close();
        $conn->close();
        exit;
    }
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    echo json_encode(['data' => $row ? [$row] : []], JSON_UNESCAPED_UNICODE);
    $stmt->close();
    $conn->close();
    exit;
}

// ===== Listado con filtros + paginación =====
$where  = '';
$params = [];
$types  = '';

// Búsqueda
if ($q !== '') {
    $where .= " AND (s.nombre LIKE ? OR s.descripcion LIKE ? OR s.items LIKE ?)";
    $like = "%{$q}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types   .= 'sss';
}

// Estado
if ($estado !== '' && ($estado === '0' || $estado === '1')) {
    $where .= " AND s.estado = ?";
    $params[] = (int)$estado;
    $types   .= 'i';
}

// Categoría
if ($idCat !== '' && ctype_digit((string)$idCat)) {
    $where .= " AND s.id_categoria = ?";
    $params[] = (int)$idCat;
    $types   .= 'i';
}

if ($agenda !== '' && ($agenda === '0' || $agenda === '1')) {
    $where .= " AND s.agenda = ?";
    $params[] = (int)$agenda;
    $types   .= 'i';
}

// Pago
if ($pago !== '' && ($pago === '0' || $pago === '1')) {
    $where .= " AND s.pago = ?";
    $params[] = (int)$pago;
    $types   .= 'i';
}

// Conteo
$countSql = "SELECT COUNT(*) AS total FROM servicios s
             LEFT JOIN categorias c ON c.id = s.id_categoria
             WHERE 1=1 {$where}";

$stmt = $conn->prepare($countSql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare count failed: ' . $conn->error], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute count failed: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
    $stmt->close();
    $conn->close();
    exit;
}
$res = $stmt->get_result();
$total = (int)($res->fetch_assoc()['total'] ?? 0);
$stmt->close();

// Datos
$offset  = ($page - 1) * $pageSize;
$dataSql = $baseSelect . $where . " ORDER BY s.id DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($dataSql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare data failed: ' . $conn->error], JSON_UNESCAPED_UNICODE);
    exit;
}
$types2  = $types . 'ii';
$params2 = $params;
$params2[] = $pageSize;
$params2[] = $offset;

$stmt->bind_param($types2, ...$params2);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute data failed: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
    $stmt->close();
    $conn->close();
    exit;
}
$res = $stmt->get_result();
$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

echo json_encode([
    'data' => $rows,
    'page' => $page,
    'total_pages' => max(1, (int)ceil($total / $pageSize))
], JSON_UNESCAPED_UNICODE);

$conn->close();
