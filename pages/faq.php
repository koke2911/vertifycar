<?php
session_start();
include("../config/variables.php");
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mantenedor de Preguntas</title>

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
    <!-- faq.php (resumen del cuerpo principal) -->
    <div class="container section-pad">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="h5 mb-2"><i class="fas fa-question-circle mr-2"></i>Mantenedor de Preguntas Frecuentes</h1>
            <div>
                <button class="btn btn-primary btn-sm" id="btn_nuevo"><i class="fas fa-plus mr-1"></i> Nueva FAQ</button>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label class="small mb-1">Buscar</label>
                        <input type="search" id="filtro_q" class="form-control form-control-sm" placeholder="Pregunta, respuesta o tag">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="small mb-1">Categoría</label>
                        <select id="filtro_categoria" class="custom-select custom-select-sm">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="small mb-1">Estado</label>
                        <select id="filtro_estado" class="custom-select custom-select-sm">
                            <option value="">Todos</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <button class="btn btn-sm btn-primary mr-2" id="btn_filtrar">Filtrar</button>
                        <button class="btn btn-sm btn-light" id="btn_limpiar">Limpiar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="grid_loader" style="display:none">Cargando…</div>
        <div id="grid_vacio" class="text-muted" style="display:none">Sin resultados</div>
        <div id="faq_grid" class="row g-3"></div>

        <nav aria-label="Paginación" class="mt-3">
            <ul class="pagination pagination-sm" id="paginacion"></ul>
        </nav>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal_faq" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="modal_title">Nueva FAQ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="faq_id">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label class="mb-1">Pregunta *</label>
                            <input type="text" class="form-control" id="faq_pregunta" maxlength="300">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="mb-1">Categoría</label>
                            <select class="custom-select" id="faq_id_categoria"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Respuesta *</label>
                        <textarea class="form-control" id="faq_respuesta" rows="4" maxlength="2000"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label class="mb-1">Tags (separar por “;”)</label>
                            <input type="text" class="form-control" id="faq_tags" maxlength="500" placeholder="pagos;boletas;cuentas">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="mb-1">Orden</label>
                            <input type="number" class="form-control" id="faq_orden" value="0" min="0" step="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Estado</label>
                        <select class="custom-select" id="faq_estado">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_faq"><i class="fas fa-save mr-1"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- JS -->
    <script src="../public/assets/js/libs/bootstrap-4.6/jquery-3.6.0.min.js"></script>
    <script src="../public/assets/js/libs/bootstrap-4.6/bootstrap.bundle.min.js"></script>
    <script src="../public/assets/js/libs/js/sweetalert2/sweetalert2.all.min.js"></script>


    <script src="../public/assets/js/faq.js?v=<?php echo $version; ?>"></script>
</body>

</html>

<!-- Lógica front (solo diseño + llamadas a páginas) -->
</body>

</html>