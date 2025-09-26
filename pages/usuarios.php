<?php
session_start();
include("../config/variables.php");
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mantenedor de Usuarios</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../public/assets/js/libs/bootstrap-4.6/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/js/libs/vendor/datatables/dataTables.bootstrap4.min.css">
    <script src="../public/assets/js/libs/js/fontawesome.all.js" crossorigin="anonymous"></script>

    <style>
        body {
            background: #f9fafb;
        }

        .card {
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
        }

        .form-section-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .help {
            font-size: .85rem;
            color: #6b7280;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            padding: .15rem .5rem;
            font-size: .75rem;
            border-radius: 999px;
            background: #eef2ff;
            color: #1d4ed8;
        }

        .chip.ok {
            background: #ecfdf5;
            color: #065f46;
        }

        .chip.off {
            background: #fef2f2;
            color: #991b1b;
        }

        label.required::after {
            content: " *";
            color: #e11d48;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: .75rem;
        }
    </style>
</head>

<body>

    <div class="container-fluid py-3">
        <h1 class="h5 mb-3"><i class="fas fa-users mr-2"></i>Mantenedor de Usuarios</h1>

        <!-- Formulario -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-section-title">Datos del usuario</div>

                <input type="hidden" id="txt_id">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label class="required">Nombre</label>
                        <input type="text" class="form-control" id="txt_nombre" maxlength="150">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="required">Apellidos</label>
                        <input type="text" class="form-control" id="txt_apellidos" maxlength="150">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="required">Correo</label>
                        <input type="email" class="form-control" id="txt_correo" maxlength="150">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Fono +569</label>
                        <input type="text" class="form-control" id="txt_fono" maxlength="8" placeholder="Ej: 912345678">
                        <small class="text-muted">Solo dígitos (7–12).</small>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="required">RUT (11111111-1)</label>
                        <input type="text" class="form-control" id="txt_rut" maxlength="12" placeholder="Sin puntos">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="required">Rol</label>
                        <select class="custom-select" id="cmb_rol"></select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Estado</label>
                        <select id="cmb_estado" class="custom-select">
                            <option value="">Seleccionar</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap">
                    <button type="button" class="btn btn-primary mr-2 mb-2" id="btn_guardar">
                        <i class="fas fa-save mr-1"></i>Guardar
                    </button>
                    <button type="button" class="btn btn-warning mr-2 mb-2" id="btn_cancelar">
                        <i class="fas fa-times-circle mr-1"></i>Cancelar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="card">
            <div class="card-body">
                <div class="form-section-title mb-2">Listado de usuarios</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="grid_usuarios" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>#ID</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>RUT</th>
                                <th>Fono</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th style="display:none;">Rol_id</th>
                                <th>Estado</th>
                                <th style="display:none;">estado_raw</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="../public/assets/js/libs/bootstrap-4.6/jquery-3.6.0.min.js"></script>
    <script src="../public/assets/js/libs/bootstrap-4.6/bootstrap.bundle.min.js"></script>

    <script src="../public/assets/js/libs/vendor/datatables/jquery.dataTables.js"></script>
    <script src="../public/assets/js/libs/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="../public/assets/js/libs/js/fnReloadAjax.js"></script>
    <script src="../public/assets/js/libs/js/sweetalert2/sweetalert2.all.min.js"></script>

    <script src="../public/assets/js/usuarios.js?v=2"></script>
</body>

</html>