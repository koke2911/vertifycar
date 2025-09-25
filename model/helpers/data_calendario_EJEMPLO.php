<?php
session_start();
$usuario = $_SESSION['id_usuario'];

$conn = new mysqli($_SESSION['servername'], $_SESSION['username'], $_SESSION['password'], $_SESSION['dbname'], $_SESSION['port']);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$profesional_id = $_GET['profesional_id'];
$dia = $_GET['dia'];
$id_solicitud = $_GET['id_solicitud'];


$sql = "SELECT c.id as ID,
date_format(c.fecha_cita,'%d/%m/%Y') as DIA,
date_format(c.inicio,'%H:%i') as INICIO,
date_format(c.fin,'%H:%i') as FIN,
t.tipo as ESTADO,
se.nombre as MOTIVO,
concat(p.nombre ,' ',p.apellido) as USUARIO,
'registrado' as REGISTRADO,
 observacion as OBS
from citas c 
inner join citas_tipo t on t.id=c.tipo
inner join solicitudes s on s.id=c.id_solicitud
inner join lista_espera l on l.id_solicitud=s.id
inner join servicios se on se.id=l.servicio_id
inner join profesionales p on p.id=c.profesional_id
";


if ($id_solicitud != "") {
    $sql .= " where c.id_solicitud=$id_solicitud and c.estado=1";
} else if ($profesional_id != "") {
    $sql .= " where c.profesional_id=$profesional_id and c.estado=1 ";
} else {
    $sql .= " where c.estado=1 ";
}


$result = $conn->query($sql);

$filas = [];

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        $filas[] = [
            'ID' => $row['ID'],
            'DIA' => $row['DIA'],
            'INICIO' => $row['INICIO'],
            'FIN' => $row['FIN'],
            'ESTADO' => $row['ESTADO'],
            'MOTIVO' => $row['MOTIVO'],
            'USUARIO' => $row['USUARIO'],
            'REGISTRADO' => $row['REGISTRADO'],

            'OBS' => $row['OBS']
        ];
    }
}


if (empty($filas)) {
    echo "{ \"data\":[] }";
} else {
    echo "{ \"data\": " . json_encode($filas) . "}";
}
