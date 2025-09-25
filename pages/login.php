<?php
include("../config/variables.php");
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | Panel</title>
    <!-- Bootstrap 4.6 + vendors que ya usas -->
    <link rel="stylesheet" href="../public/assets/js/libs/bootstrap-4.6/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/js/libs/vendor/datepicker/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="../public/assets/js/libs/vendor/datatables/dataTables.cellEdit.css">
    <link href="../public/assets/js/libs/js/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css">
    <script src="../public/assets/js/libs/js/fontawesome.all.js" crossorigin="anonymous"></script>

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(180deg, #ffffff 0%, #eef6ff 100%);
        }

        .card {
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }

        .brand-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #0E6FFF;
            display: inline-block;
            vertical-align: middle;
            margin-right: .5rem;
        }
    </style>
</head>

<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-5">
                <div class="text-center mb-4">
                    <span class="brand-dot"></span><span class="fs-4 fw-bold">Tu<span
                            class="text-primary">Marca</span></span>
                    <div class="text-muted">Panel de administración</div>
                </div>
                <div class="card">
                    <div class="card-body p-4">
                        <center>
                            <h1 class="h5 mb-3 align-items-center">Iniciar sesión</h1>
                        </center>
                        <form id="frmLogin" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="txt_usuario" placeholder="11111111-1"
                                    required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="txt_password" placeholder="••••••••" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                              
                            </div>
                            <button class="btn btn-primary w-100" type="button" id="btn_entrar">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                            </button>
                        </form>                       
                    </div>
                </div>
                <p class="text-center text-muted small mt-3 mb-0">© <span id="y"></span> TuMarca</p>
            </div>
        </div>
    </div>
    <script src="../public/assets/js/libs/bootstrap-4.6/jquery-3.6.0.min.js"></script>
    <script src="../public/assets/js/libs/bootstrap-4.6/bootstrap.bundle.min.js"></script>
    <script src="../public/assets/js/libs/js/sweetalert2/sweetalert2.all.min.js"></script>

    <script src="../public/assets/js/login.js?v=<?php echo $version; ?>"></script>

</body>

</html>