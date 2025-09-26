// ===== Rutas =====
const URL_MM_LISTAR = "../model/grid/datagrid_multimedia.php";          // GET ?page=&page_size=&q=&estado=&id_categoria=&tipo=
const URL_MM_GUARDAR = "../model/model_multimedia.php";                  // POST (FormData)
const URL_MM_ESTADO = "../model/acciones/multimedia_cambiar_estado.php";// POST {id, estado}
const URL_MM_ELIM = "../model/acciones/multimedia_eliminar.php";      // POST {id}
const URL_MM_CATS = "../model/data/cmb_multimedia_categorias.php";    // GET  -> [{id, glosa}]

let mmPagActual = 1, mmTotalPaginas = 1, mmPageSize = 12;

$(function () {
    mmCargarCategorias();
    mmCargar();

    // Filtros
    $("#btn_mm_filtrar").on("click", () => { mmPagActual = 1; mmCargar(); });
    $("#btn_mm_limpiar").on("click", mmLimpiarFiltros);
    $("#mm_filtro_q").on("keypress", (e) => { if (e.which === 13) { mmPagActual = 1; mmCargar(); } });
    $("#mm_filtro_estado, #mm_filtro_categoria, #mm_filtro_tipo").on("change", () => { mmPagActual = 1; mmCargar(); });

    // Modal
    $("#btn_mm_nuevo").on("click", mmAbrirNuevo);
    $("#btn_mm_guardar").on("click", mmGuardar);

    // Interacción fuente (file/url)
    $("#mm_fuente, #mm_tipo").on("change", mmToggleFuenteUI);
    mmToggleFuenteUI(); // estado inicial

    // Tag preview opcional (si lo quieres)
    $("#mm_tags").on("input", function () {
        const tags = (($(this).val() || '').split(';').map(s => s.trim()).filter(Boolean)).slice(0, 12);
        $("#mm_tags_preview").html(tags.map(t => `<span class="chip">${escapeHTML(t)}</span>`).join(''));
    });
});

// ==== Utils
function toggleLoader(b) { $("#mm_grid_loader").toggle(!!b); }
function toggleEmpty(b) { $("#mm_grid_vacio").toggle(!!b); }
function escapeHTML(s = '') { return String(s).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])); }
function escapeAttr(s = '') { return String(s).replace(/"/g, '&quot;'); }

// ==== Carga combos
function mmCargarCategorias() {
    $.getJSON(URL_MM_CATS).done(data => {
        let opts = `<option value="">Todas</option>`;
        (data || []).forEach(c => opts += `<option value="${c.id}">${escapeHTML(c.glosa)}</option>`);
        $("#mm_filtro_categoria").html(opts);
        // Modal
        $("#mm_id_categoria").html(`<option value="">(Sin categoría)</option>` + opts.replace('Todas', ''));
    });
}

// ==== Listado
function mmCargar() {
    toggleLoader(true); $("#mm_grid").empty(); $("#mm_paginacion").empty(); toggleEmpty(false);

    const params = $.param({
        page: mmPagActual,
        page_size: mmPageSize,
        q: ($("#mm_filtro_q").val() || '').trim(),
        estado: $("#mm_filtro_estado").val(),
        id_categoria: $("#mm_filtro_categoria").val(),
        tipo: $("#mm_filtro_tipo").val() // imagen | video | ''
    });

    $.getJSON(`${URL_MM_LISTAR}?${params}`)
        .done(function (res) {
            const items = res?.data || [];
            mmTotalPaginas = res?.total_pages || 1;
            mmPagActual = res?.page || 1;

            if (items.length === 0) { toggleEmpty(true); }
            else { mmRenderCards(items); }
            mmRenderPaginacion();
        })
        .fail(() => Swal.fire("Error", "No fue posible cargar la multimedia", "error"))
        .always(() => toggleLoader(false));
}

function mmRenderCards(items) {
    const $g = $("#mm_grid").empty();
    items.forEach(m => {
        const estadoChip = String(m.estado) === '1'
            ? `<span class="chip chip-estado-on"><i class="fas fa-check"></i> Activo</span>`
            : `<span class="chip chip-estado-off"><i class="fas fa-ban"></i> Inactivo</span>`;
        const cat = m.categoria ? `<span class="chip chip-flag"><i class="fas fa-tag"></i> ${escapeHTML(m.categoria)}</span>` : '';

        const tags = (m.tags || '').split(';').filter(Boolean).slice(0, 8)
            .map(t => `<span class="chip">${escapeHTML(t)}</span>`).join('');

        // Media preview
        let mediaHtml = '';
        if (m.tipo === 'imagen' && m.fuente === 'file' && m.archivo) {
            mediaHtml = `<img class="img-thumb mb-2" src="../public/assets/media/imagenes/${encodeURIComponent(m.archivo)}" alt="${escapeAttr(m.titulo || 'img')}">`;
        } else if (m.tipo === 'video' && m.fuente === 'file' && m.archivo) {
            mediaHtml = `<video class="img-thumb mb-2" src="../public/assets/media/videos/${encodeURIComponent(m.archivo)}" controls preload="metadata"></video>`;
        } else if (m.fuente === 'url' && m.url) {
            // Si quieres, aquí puedes detectar YouTube y embeber un iframe
            mediaHtml = `<a href="${escapeAttr(m.url)}" target="_blank" rel="noopener">Ver recurso externo</a>`;
        }

        const col = document.createElement('div');
        col.className = "col-md-6 col-lg-4";
        col.style.marginBottom = "1em"; // margen inferior exacto
        col.innerHTML = `
      <div class="card h-100">
        <div class="card-body">
          ${mediaHtml}
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="srv-title">${escapeHTML(m.titulo || '')}</div>
              <div class="srv-desc">${escapeHTML(m.descripcion || '')}</div>
            </div>
            <div class="text-muted" style="cursor:pointer" title="Editar" onclick="mmAbrirEditar(${m.id})">
              <i class="fas fa-edit"></i>
            </div>
          </div>
          <div class="mt-2">${cat} ${estadoChip}</div>
          <div class="mt-2">${tags}</div>

          <div class="card-actions mt-3">
            ${String(m.estado) === '1'
                ? `<button class="btn btn-sm btn-outline-secondary" onclick="mmCambiarEstado(${m.id},0)"><i class="fas fa-ban mr-1"></i> Desactivar</button>`
                : `<button class="btn btn-sm btn-success" onclick="mmCambiarEstado(${m.id},1)"><i class="fas a-check mr-1"></i> Activar</button>`}
            <button class="btn btn-sm btn-primary" onclick="mmAbrirEditar(${m.id})"><i class="fas fa-pencil-alt mr-1"></i></button>
            <button class="btn btn-sm btn-danger" onclick="mmEliminar(${m.id})"><i class="fas fa-trash mr-1"></i></button>
          </div>
        </div>
      </div>`;
        $g.append(col);
    });
}

function mmRenderPaginacion() {
    const $p = $("#mm_paginacion"); if (mmTotalPaginas <= 1) return;
    const mk = (n, label, active, disabled) => (
        `<li class="page-item ${active ? 'active' : ''} ${disabled ? 'disabled' : ''}">
      <a class="page-link" href="javascript:void(0)" onclick="mmGoPag(${n})">${label}</a>
    </li>`
    );
    $p.append(mk(Math.max(1, mmPagActual - 1), '&laquo;', false, mmPagActual === 1));
    for (let i = 1; i <= mmTotalPaginas; i++) {
        if (i === 1 || i === mmTotalPaginas || Math.abs(i - mmPagActual) <= 2) $p.append(mk(i, i, i === mmPagActual, false));
        else if ((i === 2 && mmPagActual > 4) || (i === mmTotalPaginas - 1 && mmPagActual < mmTotalPaginas - 3)) $p.append(`<li class="page-item disabled"><span class="page-link">…</span></li>`);
    }
    $p.append(mk(Math.min(mmTotalPaginas, mmPagActual + 1), '&raquo;', false, mmPagActual === mmTotalPaginas));
}
window.mmGoPag = function (n) { if (n < 1 || n > mmTotalPaginas) return; mmPagActual = n; mmCargar(); };

// ==== Filtros
function mmLimpiarFiltros() {
    $("#mm_filtro_q").val("");
    $("#mm_filtro_estado").val("");
    $("#mm_filtro_categoria").val("");
    $("#mm_filtro_tipo").val("");
    mmPagActual = 1;
    mmCargar();
}

// ==== Modal
function mmAbrirNuevo() {
    $("#mm_modal_title").text("Nuevo elemento multimedia");
    $("#mm_id").val("");
    $("#mm_id_categoria").val($("#mm_filtro_categoria").val() || "");
    $("#mm_tipo").val("imagen");
    $("#mm_titulo").val("");
    $("#mm_descripcion").val("");
    $("#mm_tags").val(""); $("#mm_tags_preview").html('');
    $("#mm_fuente").val("file");
    $("#mm_url").val("");
    $("#mm_archivo").val("");
    $("#mm_file").val(null);
    $("#mm_estado").val("1");
    mmToggleFuenteUI();
    $("#modal_mm").modal("show");
}

window.mmAbrirEditar = function (id) {
    $.getJSON(`${URL_MM_LISTAR}?id=${id}`).done(r => {
        const m = r?.data?.[0];
        if (!m) { Swal.fire("Error", "No se encontró el elemento", "error"); return; }
        $("#mm_modal_title").text("Editar elemento multimedia");
        $("#mm_id").val(m.id);
        $("#mm_id_categoria").val(m.id_categoria || "");
        $("#mm_tipo").val(m.tipo || "imagen");
        $("#mm_titulo").val(m.titulo || "");
        $("#mm_descripcion").val(m.descripcion || "");
        $("#mm_tags").val(m.tags || ""); $("#mm_tags").trigger("input");
        $("#mm_estado").val(String(m.estado ?? 1));

        // fuente/archivo/url
        if (m.fuente === 'url') {
            $("#mm_fuente").val("url");
            $("#mm_url").val(m.url || "");
            $("#mm_archivo").val(""); // no aplica
            $("#mm_file").val(null);
        } else {
            $("#mm_fuente").val("file");
            $("#mm_url").val("");
            $("#mm_archivo").val(m.archivo || ""); // nombre anterior
            $("#mm_file").val(null);
        }

        mmToggleFuenteUI();
        $("#modal_mm").modal("show");
    }).fail(() => Swal.fire("Error", "No fue posible cargar el elemento", "error"));
};

function mmToggleFuenteUI() {
    const fuente = $("#mm_fuente").val(); // file | url
    if (fuente === 'file') {
        $("#grp_mm_file").show();
        $("#grp_mm_url").hide();
    } else {
        $("#grp_mm_file").hide();
        $("#grp_mm_url").show();
    }
}

function mmGuardar() {
    const fd = new FormData();
    fd.append('id', $("#mm_id").val().trim());
    fd.append('id_categoria', $("#mm_id_categoria").val());
    fd.append('tipo', $("#mm_tipo").val());           // imagen | video
    fd.append('titulo', $("#mm_titulo").val().trim());
    fd.append('descripcion', $("#mm_descripcion").val().trim());
    fd.append('tags', $("#mm_tags").val().trim());
    fd.append('fuente', $("#mm_fuente").val());       // file | url
    fd.append('url', $("#mm_url").val().trim());
    fd.append('archivo', $("#mm_archivo").val().trim()); // nombre previo si existía
    fd.append('estado', $("#mm_estado").val());

    const fi = document.getElementById('mm_file');
    if ($("#mm_fuente").val() === 'file' && fi && fi.files && fi.files.length > 0) {
        fd.append('media_file', fi.files[0]);
    }

    // Validaciones mínimas
    if (!fd.get('titulo')) { Swal.fire("Valida", "El título es obligatorio", "warning"); return; }
    if (fd.get('fuente') === 'url' && !fd.get('url')) { Swal.fire("Valida", "Debe indicar la URL", "warning"); return; }

    $.ajax({
        url: URL_MM_GUARDAR,
        type: "POST",
        data: fd,
        processData: false,
        contentType: false,
        dataType: "json"
    }).done(function (r) {
        if (r?.codigo === 0) {
            $("#modal_mm").modal("hide");
            Swal.fire("OK", "Multimedia guardada", "success");
            mmCargar();
        } else {
            Swal.fire("Error", r?.error || r?.mensaje || "No fue posible guardar", "error");
        }
    }).fail(function () {
        Swal.fire("Error", "No fue posible guardar", "error");
    });
}

// ==== Acciones
window.mmCambiarEstado = function (id, nuevo) {
    $.post(URL_MM_ESTADO, { id, estado: nuevo }, function (r) {
        if (r?.codigo === 0) { mmCargar(); }
        else { Swal.fire("Error", r?.error || "No fue posible cambiar el estado", "error"); }
    }, "json").fail(() => Swal.fire("Error", "No fue posible cambiar el estado", "error"));
};

window.mmEliminar = function (id) {
    Swal.fire({
        title: "¿Eliminar multimedia?",
        text: "Se marcará como eliminada",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then(res => {
        if (!res.isConfirmed) return;
        $.post(URL_MM_ELIM, { id }, function (r) {
            if (r?.codigo === 0) { Swal.fire("Eliminado", "Elemento eliminado", "success"); mmCargar(); }
            else { Swal.fire("Error", r?.error || "No fue posible eliminar", "error"); }
        }, "json").fail(() => Swal.fire("Error", "No fue posible eliminar", "error"));
    });
};
