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
    <link rel="stylesheet" href="../public/assets/js/libs/vendor/datepicker/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="../public/assets/js/libs/vendor/datatables/dataTables.cellEdit.css">
    <link href="../public/assets/js/libs/js/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css">

    <script src="../public/assets/js/libs/js/fontawesome.all.js" crossorigin="anonymous"></script>


    <style>
        body {
            background: #f9fafb;
        }

        .card {
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
        }

        .section-pad {
            padding: 2rem 0;
        }

        .form-section-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .chip-role {
            border-radius: 999px;
            padding: .15rem .5rem;
            font-size: .8rem;
            background: #eef2ff;
            color: #1d4ed8;
        }

        .dt-buttons .btn {
            margin-right: .25rem;
        }

        .btn-excel {
            background-color: #74a27f !important;
            border-color: #5c8f68 !important;
            color: #fff !important;
        }

        /* Etiquetas y asterisco de obligatorios */
        label.required::after {
            content: " *";
            color: #e11d48;
            font-weight: 600;
        }

        /* Separación vertical más compacta */
        .form-group {
            margin-bottom: .75rem;
        }

        /* Área de acciones siempre visible */
        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        /* Inputs compactos */
        .form-control,
        .custom-select {
            height: calc(1.5em + .75rem + 2px);
        }

        /* Que no se estiren demasiado en pantallas grandes */
        @media (min-width: 992px) {
            .form-max-900 {
                max-width: 900px;
            }
        }

        .blue {
            background-color: #eef2ff;
        }

        .badge-role {
            display: inline-block;
            padding: .25rem .6rem;
            font-size: .8rem;
            font-weight: 600;
            border-radius: 999px;
        }

        .badge-admin {
            background: #eef2ff;
            color: #1d4ed8;
            /* azul */
        }

        .badge-mecanico {
            background: #dfc1a1;
            color: #121313;
            /* verde */
        }

        .badge-estado {
            display: inline-block;
            padding: .25rem .6rem;
            font-size: .8rem;
            font-weight: 600;
            border-radius: 999px;
        }

        .badge-activo {
            background: #a9eece;
            color: #047857;
            /* verde */
        }

        .badge-inactivo {
            background: #f7aa66;
            color: #6b7280;
            /* gris */
        }

        body,
        table.dataTable {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body>

    <div class="">
        <h1 class="h4 mb-4">
            <i class="fas fa-users mr-2"></i>Mantenedor de Usuarios
        </h1>

        <!-- Formulario arriba -->
        <div class="card mb-4 ">
            <div class="card-body d-flex flex-column align-items-center ">

                <div class="form-section-title">Datos del usuario</div>

                <input type="hidden" name="txt_id" id="txt_id">

                <!-- fila 1 -->
                <div class="form-row justify-content-center w-100">
                    <div class="form-group col-md-2">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" name="txt_nombre" id="txt_nombre" required maxlength="150">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Apellidos *</label>
                        <input type="text" class="form-control" name="txt_apellidos" id="txt_apellidos" required maxlength="150">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Correo *</label>
                        <input type="email" class="form-control" name="txt_correo" id="txt_correo" required maxlength="150">
                    </div>
                    <div class="form-group col-md-1">
                        <label>Fono +56</label>
                        <input type="text" class="form-control" name="txt_fono" id="txt_fono" maxlength="8">
                        <small class="text-muted">Solo números</small>
                    </div>
                </div>

                <!-- fila 2 -->
                <div class="form-row justify-content-center w-100">
                    <div class="form-group col-md-2">
                        <label>RUT * 11111111-1</label>
                        <input type="text" class="form-control" name="txt_rut" id="txt_rut" required maxlength="150">
                        <small class="text-muted">Sin puntos y con guion.</small>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Rol *</label>
                        <select class="custom-select" name="cmb_rol" id="cmb_rol" required>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="cmb_estado">Estado</label>
                        <select id="cmb_estado" class="form-control">
                            <option value="">Seleccionar Estado</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- botones -->
                <div class="d-flex justify-content-center flex-wrap mt-3">
                    <button type="button" class="btn btn-primary mr-2 mb-2" id="btn_guardar">
                        <i class="fas fa-save mr-1"></i>Guardar
                    </button>
                    <button type="button" class="btn btn-warning mr-2 mb-2" id="btn_cancelar">
                        <i class="fas fa-times-circle mr-1"></i>Cancelar
                    </button>
                </div>

            </div>
        </div>


        <!-- Tabla abajo -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <div class="form-section-title mb-2 mb-md-0">Listado de usuarios</div>
                </div>
                <div class="table-responsive" style="overflow-x: hidden;overflow-y:scroll;height: 80vh;padding:1em">
                    <table class="table table-bordered" width="100%" cellspacing="0" id="grid_usuarios">
                        <thead style="background: #4c7e97;color: #FFF;">
                            <tr>
                                <th style="width:2%">#ID</th>
                                <th style="width:15%">nombre</th>
                                <th style="width:15%">Apellidos</th>
                                <th style="width:7%">Rut</th>
                                <th style="width:7%">Fono</th>
                                <th style="width:15%">correo</th>
                                <th style="width:15%">Rol</th>
                                <th style="width:0%">Estado_id</th>
                                <th style="width:15%">Estado</th>
                                <th style="width:0%">rol_id</th>
                                <th style="width:40%">Editar</th>
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

    <script src="../public/assets/js/usuarios.js?v=<?php echo $version; ?>"></script>


</body>

</html>