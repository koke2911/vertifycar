// Rutas
const URL_FAQ_LISTAR = "../model/grid/datagrid_faq.php";
const URL_FAQ_GUARDAR = "../model/model_faq.php";
const URL_FAQ_ESTADO = "../model/acciones/faq_cambiar_estado.php";
const URL_FAQ_ELIMINAR = "../model/acciones/faq_eliminar.php";
const URL_FAQ_CATS = "../model/data/cmb_faq_categorias.php";

let pagActual = 1, totalPaginas = 1, pageSize = 12;

$(function () {
    cargarCategoriasFAQ();
    cargarFAQ();

    $("#btn_filtrar").on("click", () => { pagActual = 1; cargarFAQ(); });
    $("#btn_limpiar").on("click", limpiarFiltros);
    $("#btn_nuevo").on("click", abrirModalNuevo);
    $("#btn_guardar_faq").on("click", guardarFAQ);
    $("#filtro_q").on("keypress", e => { if (e.which === 13) { pagActual = 1; cargarFAQ(); } });

    $("#filtro_estado,#filtro_categoria").on("change", () => { pagActual = 1; cargarFAQ(); });
});

function cargarCategoriasFAQ() {
    $.getJSON(URL_FAQ_CATS).done(data => {
        let opts = `<option value="">Todas</option>`;
        (data || []).forEach(c => opts += `<option value="${c.id}">${c.glosa}</option>`);
        $("#filtro_categoria").html(opts);
        $("#faq_id_categoria").html(`<option value="">(Sin categoría)</option>` + opts.replace('Todas', ''));
    });
}

function toggleLoader(b) { $("#grid_loader").toggle(!!b); }
function toggleEmpty(b) { $("#grid_vacio").toggle(!!b); }
function escapeHTML(s = '') { return String(s).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])); }

function cargarFAQ() {
    toggleLoader(true); $("#faq_grid").empty(); $("#paginacion").empty(); toggleEmpty(false);

    const params = $.param({
        page: pagActual,
        page_size: pageSize,
        q: $("#filtro_q").val().trim(),
        estado: $("#filtro_estado").val(),
        id_categoria: $("#filtro_categoria").val()
    });

    $.getJSON(`${URL_FAQ_LISTAR}?${params}`).done(res => {
        const items = res?.data || [];
        totalPaginas = res?.total_pages || 1;
        pagActual = res?.page || 1;

        if (items.length === 0) toggleEmpty(true);
        else renderCards(items);
        renderPaginacion();
    }).fail(() => Swal.fire("Error", "No fue posible cargar las FAQs", "error"))
        .always(() => toggleLoader(false));
}

function renderCards(items) {
    const $g = $("#faq_grid");
    items.forEach(f => {
        const estadoChip = String(f.estado) === '1'
            ? `<span class="badge badge-success">Activo</span>`
            : `<span class="badge badge-secondary">Inactivo</span>`;
        const cat = f.categoria ? `<span class="badge badge-info">${escapeHTML(f.categoria)}</span>` : '';
        const tags = (f.tags || '').split(';').filter(Boolean).slice(0, 8)
            .map(t => `<span class="badge badge-light mr-1 mb-1">${escapeHTML(t)}</span>`).join('');

        const col = document.createElement('div');
        col.className = "col-md-6 col-lg-4";
        col.style = "margin-bottom:1em;";
        col.innerHTML = `
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div class="font-weight-bold">${escapeHTML(f.pregunta || '')}</div>
            <div class="text-muted" style="cursor:pointer" title="Editar" onclick="abrirModalEditar(${f.id})">
              <i class="fas fa-edit"></i>
            </div>
          </div>
          <div class="text-muted small mt-1">${cat} ${estadoChip} ${f.orden ? `· Orden: ${f.orden}` : ''}</div>
          <div class="mt-2">${escapeHTML(f.respuesta || '')}</div>
          <div class="mt-2">${tags}</div>
          <div class="mt-2">
            ${String(f.estado) === '1'
                ? `<button class="btn btn-sm btn-outline-secondary" onclick="cambiarEstado(${f.id},0)"><i class="fas fa-ban mr-1"></i> Desactivar</button>`
                : `<button class="btn btn-sm btn-success" onclick="cambiarEstado(${f.id},1)"><i class="fas fa-check mr-1"></i> Activar</button>`}
            <button class="btn btn-sm btn-primary" onclick="abrirModalEditar(${f.id})"><i class="fas fa-pencil-alt mr-1"></i></button>
            <button class="btn btn-sm btn-danger" onclick="eliminarFAQ(${f.id})"><i class="fas fa-trash mr-1"></i></button>
          </div>
        </div>
      </div>`;
        $g.append(col);
    });
}

function renderPaginacion() {
    const $p = $("#paginacion"); if (totalPaginas <= 1) return;
    const mk = (n, label, active, disabled) => `<li class="page-item ${active ? 'active' : ''} ${disabled ? 'disabled' : ''}">
    <a class="page-link" href="javascript:void(0)" onclick="goPag(${n})">${label}</a></li>`;
    $p.append(mk(Math.max(1, pagActual - 1), '&laquo;', false, pagActual === 1));
    for (let i = 1; i <= totalPaginas; i++) {
        if (i === 1 || i === totalPaginas || Math.abs(i - pagActual) <= 2) $p.append(mk(i, i, i === pagActual, false));
        else if ((i === 2 && pagActual > 4) || (i === totalPaginas - 1 && pagActual < totalPaginas - 3)) $p.append(`<li class="page-item disabled"><span class="page-link">…</span></li>`);
    }
    $p.append(mk(Math.min(totalPaginas, pagActual + 1), '&raquo;', false, pagActual === totalPaginas));
}
window.goPag = function (n) { if (n < 1 || n > totalPaginas) return; pagActual = n; cargarFAQ(); }

// Modal
function abrirModalNuevo() {
    $("#modal_title").text("Nueva FAQ");
    $("#faq_id").val(""); $("#faq_pregunta").val(""); $("#faq_respuesta").val("");
    $("#faq_tags").val(""); $("#faq_orden").val("0"); $("#faq_estado").val("1");
    $("#faq_id_categoria").val($("#filtro_categoria").val() || "");
    $("#modal_faq").modal("show");
}

window.abrirModalEditar = function (id) {
    $.getJSON(`${URL_FAQ_LISTAR}?id=${id}`).done(r => {
        const f = r?.data?.[0]; if (!f) { Swal.fire("Error", "No se encontró la FAQ", "error"); return; }
        $("#modal_title").text("Editar FAQ");
        $("#faq_id").val(f.id); $("#faq_pregunta").val(f.pregunta || "");
        $("#faq_respuesta").val(f.respuesta || ""); $("#faq_tags").val(f.tags || "");
        $("#faq_orden").val(f.orden || "0"); $("#faq_estado").val(String(f.estado ?? 1));
        $("#faq_id_categoria").val(f.id_categoria || "");
        $("#modal_faq").modal("show");
    }).fail(() => Swal.fire("Error", "No fue posible cargar la FAQ", "error"));
}

function guardarFAQ() {
    const fd = {
        id: $("#faq_id").val().trim(),
        id_categoria: $("#faq_id_categoria").val(),
        pregunta: $("#faq_pregunta").val().trim(),
        respuesta: $("#faq_respuesta").val().trim(),
        tags: $("#faq_tags").val().trim(),
        orden: $("#faq_orden").val().trim(),
        estado: $("#faq_estado").val()
    };
    if (!fd.pregunta) { Swal.fire("Valida", "La pregunta es obligatoria", "warning"); return; }
    if (!fd.respuesta) { Swal.fire("Valida", "La respuesta es obligatoria", "warning"); return; }

    $.post(URL_FAQ_GUARDAR, fd, function (r) {
        if (r?.codigo === 0) { $("#modal_faq").modal("hide"); Swal.fire("OK", "FAQ guardada", "success"); cargarFAQ(); }
        else { Swal.fire("Error", r?.error || r?.mensaje || "No fue posible guardar", "error"); }
    }, "json").fail(() => Swal.fire("Error", "No fue posible guardar", "error"));
}

// Acciones
window.cambiarEstado = function (id, nuevo) {
    $.post(URL_FAQ_ESTADO, { id, estado: nuevo }, function (r) {
        if (r?.codigo === 0) { cargarFAQ(); } else { Swal.fire("Error", r?.error || "No fue posible cambiar el estado", "error"); }
    }, "json").fail(() => Swal.fire("Error", "No fue posible cambiar el estado", "error"));
};

window.eliminarFAQ = function (id) {
    Swal.fire({
        title: "¿Eliminar FAQ?", text: "Se marcará como eliminada", icon: "warning", showCancelButton: true,
        confirmButtonText: "Sí, eliminar", cancelButtonText: "Cancelar"
    }).then(res => {
        if (!res.isConfirmed) return;
        $.post(URL_FAQ_ELIMINAR, { id }, function (r) {
            if (r?.codigo === 0) { Swal.fire("Eliminada", "La FAQ fue eliminada", "success"); cargarFAQ(); }
            else { Swal.fire("Error", r?.error || "No fue posible eliminar", "error"); }
        }, "json").fail(() => Swal.fire("Error", "No fue posible eliminar", "error"));
    });
};

function limpiarFiltros() {
    $("#filtro_q").val(""); $("#filtro_estado").val(""); $("#filtro_categoria").val("");
    pagActual = 1; cargarFAQ();
}
