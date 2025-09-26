<?php
session_start();
include("../config/variables.php");
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mantenedor de Multimedia</title>

    <!-- CSS base (mismo stack que usas) -->
    <link rel="stylesheet" href="../public/assets/js/libs/bootstrap-4.6/bootstrap.min.css">
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

        .chip {
            border-radius: 999px;
            padding: .15rem .5rem;
            font-size: .8rem;
            background: #eef2ff;
            color: #1d4ed8;
            display: inline-flex;
            align-items: center;
            margin: .15rem .25rem .15rem 0;
        }

        .chip-estado-on {
            background: #ecfdf5;
            color: #065f46;
        }

        .chip-estado-off {
            background: #fef2f2;
            color: #991b1b;
        }

        .chip-flag {
            background: #eff6ff;
            color: #1e40af;
        }

        .img-thumb {
            width: 100%;
            max-width: 320px;
            height: 180px;
            object-fit: cover;
            border-radius: .4rem;
            border: 1px solid #e5e7eb;
        }

        .srv-title {
            font-weight: 600;
            font-size: 1rem;
            color: #111827;
        }

        .srv-desc {
            font-size: .9rem;
            color: #6b7280;
        }

        .dt-buttons .btn {
            margin-right: .25rem;
        }
    </style>
</head>

<body>

    <div class="container section-pad">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="h5 mb-2"><i class="fas fa-photo-video mr-2"></i>Mantenedor de Multimedia</h1>
            <div>
                <button class="btn btn-primary btn-sm" id="btn_mm_nuevo">
                    <i class="fas fa-plus mr-1"></i> Nuevo
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label class="small mb-1">Buscar</label>
                        <input type="search" id="mm_filtro_q" class="form-control form-control-sm" placeholder="Título, descripción o tag">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="small mb-1">Categoría</label>
                        <select id="mm_filtro_categoria" class="custom-select custom-select-sm">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="small mb-1">Estado</label>
                        <select id="mm_filtro_estado" class="custom-select custom-select-sm">
                            <option value="">Todos</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="small mb-1">Tipo</label>
                        <select id="mm_filtro_tipo" class="custom-select custom-select-sm">
                            <option value="">Todos</option>
                            <option value="imagen">Imagen</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group col-md-1">
                        <button class="btn btn-sm btn-primary btn-block" id="btn_mm_filtrar">Filtrar</button>
                    </div>
                    <div class="form-group col-md-1">
                        <button class="btn btn-sm btn-light btn-block" id="btn_mm_limpiar">Limpiar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado grilla -->
        <div id="mm_grid_loader" style="display:none">Cargando…</div>
        <div id="mm_grid_vacio" class="text-muted" style="display:none">Sin resultados</div>

        <!-- Grid -->
        <div class="row" id="mm_grid"></div>

        <!-- Paginación -->
        <nav aria-label="Paginación" class="mt-3">
            <ul class="pagination pagination-sm" id="mm_paginacion"></ul>
        </nav>
    </div>

    <!-- Modal crear/editar -->
    <div class="modal fade" id="modal_mm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="mm_modal_title">Nuevo elemento multimedia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="mm_id">
                    <input type="hidden" id="mm_archivo"> <!-- nombre anterior del archivo (si existía) -->

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="mb-1">Título *</label>
                            <input type="text" class="form-control" id="mm_titulo" maxlength="200">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="mb-1">Tipo *</label>
                            <select id="mm_tipo" class="custom-select">
                                <option value="imagen">Imagen</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="mb-1">Categoría</label>
                            <select id="mm_id_categoria" class="custom-select"></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="mb-1">Descripción</label>
                        <textarea id="mm_descripcion" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label class="mb-1">Tags (separar por “;”)</label>
                            <input type="text" id="mm_tags" class="form-control" maxlength="500" placeholder="evento;autos;2025">
                            <div id="mm_tags_preview" class="mt-2"></div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="mb-1">Estado</label>
                            <select id="mm_estado" class="custom-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="mb-1">Fuente *</label>
                            <select id="mm_fuente" class="custom-select">
                                <option value="file">Archivo</option>
                                <option value="url">URL externa</option>
                            </select>
                        </div>

                        <!-- Fuente: archivo -->
                        <div class="form-group col-md-9" id="grp_mm_file">
                            <label class="mb-1">Archivo</label>
                            <input type="file" id="mm_file" class="form-control"
                                accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg">
                            <small class="text-muted">
                                Imágenes: JPG/PNG/WEBP (≤ 5MB) · Videos: MP4/WEBM/OGG (≤ 200MB)
                            </small>
                        </div>

                        <!-- Fuente: URL -->
                        <div class="form-group col-md-9" id="grp_mm_url" style="display:none;">
                            <label class="mb-1">URL</label>
                            <input type="url" id="mm_url" class="form-control" placeholder="https://…">
                            <small class="text-muted">Ej: enlace a YouTube o CDN externo.</small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_mm_guardar">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS base -->
    <script src="../public/assets/js/libs/bootstrap-4.6/jquery-3.6.0.min.js"></script>
    <script src="../public/assets/js/libs/bootstrap-4.6/bootstrap.bundle.min.js"></script>
    <script src="../public/assets/js/libs/js/sweetalert2/sweetalert2.all.min.js"></script>

    <!-- Tu JS de multimedia -->
    <script src="../public/assets/js/multimedia.js?v=<?php echo $version; ?>"></script>
</body>

</html>