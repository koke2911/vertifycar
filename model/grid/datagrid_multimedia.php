<?php
// model/grid/datagrid_multimedia.php
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
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$page     = max(1, (int)($_GET['page'] ?? 1));
$pageSize = (int)($_GET['page_size'] ?? 12);
$pageSize = ($pageSize > 0 && $pageSize <= 100) ? $pageSize : 12;

$q        = trim((string)($_GET['q'] ?? ''));
$estado   = $_GET['estado'] ?? '';           // 0|1|''
$idCat    = $_GET['id_categoria'] ?? '';     // num|''
$tipo     = $_GET['tipo'] ?? '';             // 'imagen'|'video'|''
$id       = $_GET['id'] ?? '';

$baseSelect = "
  SELECT m.id,m.id_categoria,m.tipo,m.titulo,m.descripcion,m.tags,m.fuente,m.archivo,m.url,m.estado,m.fecha_crea,m.usu_crea,
         c.nombre AS categoria
  FROM multimedia m
  LEFT JOIN multimedia_categorias c ON c.id = m.id_categoria
  WHERE 1=1
";

if ($id !== '' && ctype_digit((string)$id)) {
    $sql = $baseSelect . " AND m.id = ? LIMIT 1";
    $st = $conn->prepare($sql);
    $idI = (int)$id;
    $st->bind_param('i', $idI);
    $st->execute();
    $res = $st->get_result();
    $row = $res->fetch_assoc();
    echo json_encode(['data' => $row ? [$row] : []], JSON_UNESCAPED_UNICODE);
    $st->close();
    $conn->close();
    exit;
}

$where = '';
$params = [];
$types = '';

if ($q !== '') {
    $where .= " AND (m.titulo LIKE ? OR m.descripcion LIKE ? OR m.tags LIKE ?)";
    $like = "%{$q}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}
if ($estado !== '' && ($estado === '0' || $estado === '1')) {
    $where .= " AND m.estado=?";
    $params[] = (int)$estado;
    $types .= 'i';
}
if ($idCat !== '' && ctype_digit((string)$idCat)) {
    $where .= " AND m.id_categoria=?";
    $params[] = (int)$idCat;
    $types .= 'i';
}
if ($tipo === 'imagen' || $tipo === 'video') {
    $where .= " AND m.tipo=?";
    $params[] = $tipo;
    $types .= 's';
}

$where .= " AND m.estado<>2"; // ocultar eliminados

// Conteo
$countSql = "SELECT COUNT(*) total FROM multimedia m LEFT JOIN multimedia_categorias c ON c.id=m.id_categoria WHERE 1=1 {$where}";
$st = $conn->prepare($countSql);
if ($types !== '') $st->bind_param($types, ...$params);
$st->execute();
$res = $st->get_result();
$total = (int)($res->fetch_assoc()['total'] ?? 0);
$st->close();

// Datos
$offset = ($page - 1) * $pageSize;
$dataSql = $baseSelect . $where . " ORDER BY m.id DESC LIMIT ? OFFSET ?";
$st = $conn->prepare($dataSql);
$types2 = $types . 'ii';
$params2 = $params;
$params2[] = $pageSize;
$params2[] = $offset;
$st->bind_param($types2, ...$params2);
$st->execute();
$res = $st->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
$st->close();

echo json_encode(['data' => $rows, 'page' => $page, 'total_pages' => max(1, (int)ceil($total / $pageSize))], JSON_UNESCAPED_UNICODE);
$conn->close();
