<?php
session_start();
include("../config/variables.php");
$userName = $_SESSION['rut_usuario'] . ' ' . $_SESSION['nombre_usuario'];

?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel | TuMarca</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../public/assets/js/libs/bootstrap-4.6/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/js/libs/vendor/datepicker/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="../public/assets/js/libs/vendor/datatables/dataTables.cellEdit.css">
    <link href="../public/assets/js/libs/js/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css">
    <script src="../public/assets/js/libs/js/fontawesome.all.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --brand: #0E6FFF;
            --ink: #111827;
        }

        body {
            background: #f6f8fb;
            color: #1f2937;
        }

        .navbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }

        .brand-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--brand);
            display: inline-block;
            vertical-align: middle;
            margin-right: .5rem;
        }

        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: calc(100vh - 56px);
        }

        @media (max-width: 991.98px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: relative;
                z-index: 2;
            }
        }

        .sidebar {
            background: #fff;
            border-right: 1px solid #e5e7eb;
        }

        .sidebar .nav-link {
            color: #334155;
            border-radius: .5rem;
            margin: .15rem 0;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #eef2ff;
            color: #1d4ed8;
        }

        .content {
            padding: 1rem;
            background: #f6f8fb;
        }

        .card {
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
        }

        iframe#appframe {
            width: 100%;
            height: calc(100vh - 56px - 2rem);
            border: 0;
            background: #fff;
            border-radius: .5rem;
        }

        .userbadge {
            background: #eef2ff;
            color: #1d4ed8;
            border-radius: 999px;
            padding: .15rem .6rem;
            font-size: .85rem;
        }

        /* Toggle sidebar en móviles */
        .btn-sidebar {
            display: none;
        }

        @media (max-width: 991.98px) {
            .btn-sidebar {
                display: inline-flex;
            }

            .sidebar {
                display: none;
            }

            .sidebar.show {
                display: block;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg px-3">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <span class="brand-dot"></span>
            <span class="font-weight-bold">Tu<span class="text-primary">Marca</span> · Admin</span>
        </a>

        <button class="btn btn-outline-secondary btn-sm ml-auto btn-sidebar" id="btnSidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="ml-auto d-flex align-items-center">
            <span id="who" class="userbadge mr-2">
                <i class="fas fa-user-circle mr-1"></i><?php echo htmlspecialchars($userName); ?>
            </span>
            <button id="btnLogout" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt mr-1"></i>Salir
            </button>
        </div>
    </nav>
    <!-- LAYOUT -->
    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar p-3" id="sidebar">
            <div class="mb-2 text-muted small">Menú principal</div>
            <nav class="nav flex-column">
                <a href="#" class="nav-link active" data-target="dashboard.html"><i class="fas fa-tachometer-alt mr-2"></i>Resumen</a>
                <a href="#" class="nav-link" data-target="usuarios.html"><i class="fas fa-users mr-2"></i>Usuarios</a>
                <a href="#" class="nav-link" data-target="servicios.html"><i class="fas fa-tools mr-2"></i>Servicios</a>
                <a href="#" class="nav-link" data-target="solicitudes.html"><i class="fas fa-clipboard-check mr-2"></i>Solicitudes</a>
                <a href="#" class="nav-link" data-target="multimedia.html"><i class="far fa-image mr-2"></i>Multimedia</a>
            </nav>

            <hr>

        </aside>

        <!-- CONTENT -->
        <main class="content">
            <div class="card p-2">
                <!-- IFRAME que carga las páginas internas -->
                <iframe id="appframe" src="" title="Aplicación"></iframe>
            </div>
        </main>
    </div>

    <!-- JS -->
    <script src="../public/assets/js/libs/bootstrap-4.6/jquery-3.6.0.min.js"></script>
    <script src="../public/assets/js/libs/bootstrap-4.6/bootstrap.bundle.min.js"></script>

    <script type="text/javascript" src="../public/assets/js/libs/vendor/datepicker/moment.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datepicker/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datepicker/bootstrap-datetimepicker.es.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/js/dataTables.select.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/buttons.html5.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/buttons.print.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/buttons.colVis.min.js"></script>

    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/jszip.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/pdfmake.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/vfs_fonts.js"></script>

    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/js/fnReloadAjax.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/dataTables.cellEdit.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/vendor/datatables/fnFindCellRowNodes.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/js/jquery-validation/dist/jquery.validate.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/js/sweetalert2/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/js/Multiple-Select/dist/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/js/Multiple-Select/dist/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript" src="../public/assets/js/libs/js/bootstrap-select/js/bootstrap-select.min.js"></script>

    <script src="../public/assets/js/principal.js?v=<?php echo 5; ?>"></script>


</body>

</html>