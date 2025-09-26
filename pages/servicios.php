<?php
session_start();
include("../config/variables.php");
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mantenedor de Servicios</title>

    <!-- Bootstrap 4.6 + FontAwesome (usas tus rutas locales) -->
    <link rel="stylesheet" href="../public/assets/js/libs/bootstrap-4.6/bootstrap.min.css">
    <script src="../public/assets/js/libs/js/fontawesome.all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f9fafb;
            --border: #e5e7eb;
            --muted: #6b7280;
            --chip: #eef2ff;
            --chipText: #1d4ed8;
        }

        body {
            background: var(--bg);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .page-title {
            font-weight: 600;
        }

        .card {
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
        }

        .toolbar .form-control,
        .toolbar .custom-select {
            height: calc(1.5em + .75rem + 2px);
        }

        /* GRID de tarjetas */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            grid-gap: 1rem;
        }

        .service-card {
            border: 1px solid var(--border);
            border-radius: .75rem;
            background: #fff;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .srv-title {
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .srv-desc {
            color: var(--muted);
            font-size: .9rem;
            min-height: 44px;
        }

        .srv-price {
            font-weight: 700;
        }

        .srv-meta {
            font-size: .8rem;
            color: #374151;
        }

        .img-thumb {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid var(--border);
        }

        /* Chips */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .2rem .55rem;
            font-weight: 600;
            font-size: .75rem;
            background: var(--chip);
            color: var(--chipText);
            margin: .15rem .25rem .15rem 0;
        }

        .chip-estado-on {
            background: #ecfdf5;
            color: #047857;
        }

        .chip-estado-off {
            background: #f3f4f6;
            color: #6b7280;
        }

        .chip-flag {
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }

        .card-actions .btn {
            margin-right: .35rem;
            margin-bottom: .35rem;
        }

        .empty-state {
            border: 1px dashed var(--border);
            border-radius: .75rem;
            padding: 2rem;
            text-align: center;
            color: var(--muted);
        }
    </style>
</head>

<body>

    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
            <h1 class="page-title h4 mb-2"><i class="fas fa-tools mr-2"></i>Mantenedor de Servicios</h1>
            <div>
                <button class="btn btn-primary" id="btn_nuevo"><i class="fas fa-plus mr-1"></i> Nuevo servicio</button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body toolbar">
                <div class="form-row">
                    <div class="form-group col-sm-4 col-md-3">
                        <label class="mb-1">Buscar</label>
                        <input type="text" class="form-control" id="filtro_q" placeholder="Nombre, ítems…">
                    </div>
                    <div class="form-group col-sm-4 col-md-3">
                        <label class="mb-1">Estado</label>
                        <select class="custom-select" id="filtro_estado">
                            <option value="">Todos</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4 col-md-3">
                        <label class="mb-1">Categoría</label>
                        <select class="custom-select" id="filtro_categoria">
                            <option value="">Todas</option>
                            <!-- se llena por JS -->
                        </select>
                    </div>
                    <div class="form-group mb-2 mr-2">
                        <label class="mb-1">Agenda</label>
                        <select id="filtro_agenda" class="custom-select custom-select-sm" style="min-width:140px;">
                            <option value="">Todos</option>
                            <option value="1">Con agenda</option>
                            <option value="0">Sin agenda</option>
                        </select>
                    </div>

                    <div class="form-group mb-2 mr-2">
                        <label class="mb-1">Pago</label>
                        <select id="filtro_pago" class="custom-select custom-select-sm" style="min-width:140px;">
                            <option value="">Todos</option>
                            <option value="1">Con pago</option>
                            <option value="0">Sin pago</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-12 col-md-3 d-flex align-items-end">
                        <button class="btn btn-outline-secondary mr-2" id="btn_limpiar"><i class="fas fa-eraser mr-1"></i> Limpiar</button>
                        <button class="btn btn-info" id="btn_filtrar"><i class="fas fa-search mr-1"></i> Aplicar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de cards -->
        <div class="card">
            <div class="card-body">
                <div id="grid_loader" class="text-center my-4" style="display:none;">
                    <div class="spinner-border text-secondary" role="status"><span class="sr-only">Cargando…</span></div>
                    <div class="mt-2 text-muted">Cargando servicios…</div>
                </div>

                <div id="grid_vacio" class="empty-state my-4" style="display:none;">
                    <i class="far fa-folder-open fa-2x mb-2"></i>
                    <div>No se encontraron servicios con los filtros aplicados.</div>
                </div>

                <div id="service_grid" class="service-grid"></div>

                <div class="d-flex justify-content-center mt-3">
                    <nav aria-label="Paginación">
                        <ul class="pagination pagination-sm mb-0" id="paginacion"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal crear/editar -->
    <div class="modal fade" id="modal_servicio" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document"> <!-- más ancho -->
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="modal_title">Nuevo servicio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="srv_id">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="mb-1">Nombre *</label>
                            <input type="text" class="form-control" id="srv_nombre" maxlength="200">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="mb-1">Categoría</label>
                            <select class="custom-select" id="srv_id_categoria"></select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="mb-1">Descripción *</label>
                            <textarea class="form-control" id="srv_descipcion" rows="2" maxlength="500"></textarea>
                            <small class="text-muted">Máx. 2–3 líneas.</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="mb-1">Ítems (separar por “;”)</label>
                            <textarea class="form-control" id="srv_items" rows="2" maxlength="1000"
                                placeholder="OBD; Revisión frenos; Informe"></textarea>
                            <div id="items_preview" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="mb-1">Precio (CLP)</label>
                            <input type="number" class="form-control" id="srv_valor" min="0" step="1000" placeholder="39990">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="mb-1">Agenda</label>
                            <select class="custom-select" id="srv_agenda">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="mb-1">Pago</label>
                            <select class="custom-select" id="srv_pago">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="mb-1">Estado</label>
                            <select class="custom-select" id="srv_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="mb-1">Imagen</label>
                            <input type="file" class="form-control" id="srv_imagen_file" accept="image/*">
                            <input type="hidden" id="srv_imagen" value="">
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_srv">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- JS -->
    <script src="../public/assets/js/libs/bootstrap-4.6/jquery-3.6.0.min.js"></script>
    <script src="../public/assets/js/libs/bootstrap-4.6/bootstrap.bundle.min.js"></script>
    <script src="../public/assets/js/libs/js/sweetalert2/sweetalert2.all.min.js"></script>


    <script src="../public/assets/js/servicios.js?v=<?php echo $version; ?>"></script>
</body>

</html>

<!-- Lógica front (solo diseño + llamadas a páginas) -->
</body>

</html>