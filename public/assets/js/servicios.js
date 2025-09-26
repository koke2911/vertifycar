// ===== Rutas (siguiendo tu estructura) =====
const URL_LISTAR = "../model/grid/datagrid_servicios.php";           // GET  ?page=&q=&estado=&id_categoria=&page_size=
const URL_GUARDAR = "../model/model_servicios.php";                    // POST crear/editar
const URL_CAMBIA_EST = "../model/acciones/servicios_cambiar_estado.php";  // POST {id, estado}
const URL_ELIMINAR = "../model/acciones/servicios_eliminar.php";        // POST {id}
const URL_CATEGORIAS = "../model/data/cmb_categorias_servicio.php";       // GET  -> [{id, glosa}]

let pagActual = 1, totalPaginas = 1, pageSize = 12;

$(function () {
    cargarCategorias();
    cargarServicios();

    $("#btn_filtrar").on("click", () => { pagActual = 1; cargarServicios(); });
    $("#btn_limpiar").on("click", limpiarFiltros);
    $("#btn_nuevo").on("click", abrirModalNuevo);
    $("#btn_guardar_srv").on("click", guardarServicio);
    $("#filtro_q").on("keypress", (e) => { if (e.which === 13) { pagActual = 1; cargarServicios(); } });

    // 👉 NUEVO: recargar cuando cambien agenda/pago (y si quieres, el resto también)
    $("#filtro_agenda, #filtro_pago, #filtro_estado, #filtro_categoria").on("change", () => {
        pagActual = 1; cargarServicios();
    });

    // preview de chips
    $("#srv_items").on("input", function () {
        const normalized = normalizeItemsLive($(this).val());
        $("#items_preview").html(buildItemsChips(normalized));
    });
});


// ==== Utilidades UI
function toggleLoader(b) { $("#grid_loader").toggle(!!b); }
function toggleEmpty(b) { $("#grid_vacio").toggle(!!b); }
function escapeHTML(s = '') { return String(s).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])); }
function escapeAttr(s = '') { return String(s).replace(/"/g, '&quot;'); }
function formateaCLP(v) { return Number(v || 0).toLocaleString('es-CL'); }

// ==== Normalización de items (siempre ;) 
function normalizeItems(str = '') {
    return String(str)
        .replace(/[,\n\r]+/g, ';')      // comas y saltos -> ;
        .replace(/\s*;\s*/g, ';')       // limpia espacios alrededor de ;
        .replace(/;{2,}/g, ';')         // evita ;; repetidos
        .replace(/^\s*;\s*|\s*;\s*$/g, '') // quita ; extremos
        .trim();
}
// para preview no quitamos tanto, solo mostramos coherente
function normalizeItemsLive(str = '') { return normalizeItems(str); }

// ==== Chips desde ;
function buildItemsChips(itemsStr) {
    if (!itemsStr) return '';
    const arr = String(itemsStr).split(';').map(s => s.trim()).filter(Boolean).slice(0, 12);
    return arr.map(t => `<span class="chip">${escapeHTML(t)}</span>`).join('');
}

// ==== Filtros
function limpiarFiltros() {
    $("#filtro_q").val("");
    $("#filtro_estado").val("");
    $("#filtro_categoria").val("");
    $("#filtro_agenda").val("");   // 👈 NUEVO
    $("#filtro_pago").val("");     // 👈 NUEVO
    pagActual = 1;
    cargarServicios();
}


function cargarCategorias() {
    $.getJSON(URL_CATEGORIAS)
        .done(function (data) {
            let opts = `<option value="">Todas</option>`;
            (data || []).forEach(c => opts += `<option value="${c.id}">${c.glosa}</option>`);
            $("#filtro_categoria").html(opts);
            // para el modal
            $("#srv_id_categoria").html(`<option value="">(Sin categoría)</option>` + opts.replace('Todas', ''));
        });
}

// ==== Listado
function cargarServicios() {
    toggleLoader(true); $("#service_grid").empty(); $("#paginacion").empty(); toggleEmpty(false);

    const params = $.param({
        page: pagActual,
        page_size: pageSize,
        q: $("#filtro_q").val().trim(),
        estado: $("#filtro_estado").val(),
        id_categoria: $("#filtro_categoria").val(),
        agenda: $("#filtro_agenda").val(),   // 👈 NUEVO
        pago: $("#filtro_pago").val()        // 👈 NUEVO
    });

    $.getJSON(`${URL_LISTAR}?${params}`)
        .done(function (res) {
            const items = res?.data || [];
            totalPaginas = res?.total_pages || 1;
            pagActual = res?.page || 1;

            if (items.length === 0) { toggleEmpty(true); }
            else { renderCards(items); }
            renderPaginacion();
        })
        .fail(() => Swal.fire("Error", "No fue posible cargar los servicios", "error"))
        .always(() => toggleLoader(false));
}


// ==== Render de tarjetas
function renderCards(items) {
    const $grid = $("#service_grid");
    items.forEach(s => {
        // Campos de tu tabla: id, id_categoria, nombre, descipcion, items, valor, agenda, pago, estado, imagen
        const desc = escapeHTML(s.descipcion || s.descripcion || "");
        const chips = buildItemsChips(s.items);
        const estadoChip = (String(s.estado) === "1")
            ? `<span class="chip chip-estado-on"><i class="fas fa-check"></i> Activo</span>`
            : `<span class="chip chip-estado-off"><i class="fas fa-ban"></i> Inactivo</span>`;
        const agendaChip = (String(s.agenda) === "1") ? `<span class="chip chip-flag"><i class="fas fa-calendar-alt"></i> Agenda</span>` : "";
        const pagoChip = (String(s.pago) === "1") ? `<span class="chip chip-flag"><i class="fas fa-credit-card"></i> Pago</span>` : "";
        const img = s.imagen ? `<img class="img-thumb mb-2" src="../public/assets/img/${escapeAttr(s.imagen)}" alt="${escapeAttr(s.nombre || 'img')}">` : "";
        const precio = (s.valor && Number(s.valor) > 0)
            ? `<div class="srv-price mt-1">$ ${formateaCLP(s.valor)} <span class="srv-meta">CLP</span></div>`
            : `<div class="text-muted mt-1">Sin precio</div>`;

        const card = `
      <div class="service-card">
        ${img}
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <div class="srv-title">${escapeHTML(s.nombre || '')}</div>
             <div class="srv-desc">${s.categoria}</div>
             <div class="srv-desc">${desc}</div>
          </div>
          <div class="text-muted" style="cursor:pointer" title="Editar" onclick="abrirModalEditar(${s.id})">
            <i class="fas fa-edit"></i>
          </div>
        </div>

        <div class="mt-2">${chips}</div>
        ${precio}
        <div class="mt-2">
          ${estadoChip} ${agendaChip} ${pagoChip}
        </div>

        <div class="card-actions mt-3">
          ${String(s.estado) === "1"
                ? `<button class="btn btn-sm btn-outline-secondary" onclick="cambiarEstado(${s.id},0)"><i class="fas fa-ban mr-1"></i> Desactivar</button>`
                : `<button class="btn btn-sm btn-success" onclick="cambiarEstado(${s.id},1)"><i class="fas fa-check mr-1"></i> Activar</button>`
            }
          <button class="btn btn-sm btn-primary" onclick="abrirModalEditar(${s.id})"><i class="fas fa-pencil-alt mr-1"></i> </button>
          <button class="btn btn-sm btn-danger" onclick="eliminarServicio(${s.id})"><i class="fas fa-trash mr-1"></i> </button>
        </div>
      </div>`;
        $grid.append(card);
    });
}

// ==== Paginación simple
function renderPaginacion() {
    const $p = $("#paginacion");
    if (totalPaginas <= 1) return;
    const mk = (n, label, active, disabled) => (
        `<li class="page-item ${active ? 'active' : ''} ${disabled ? 'disabled' : ''}">
      <a class="page-link" href="javascript:void(0)" onclick="goPag(${n})">${label}</a>
    </li>`
    );
    $p.append(mk(Math.max(1, pagActual - 1), '&laquo;', false, pagActual === 1));
    for (let i = 1; i <= totalPaginas; i++) {
        if (i === 1 || i === totalPaginas || Math.abs(i - pagActual) <= 2) {
            $p.append(mk(i, i, i === pagActual, false));
        } else if ((i === 2 && pagActual > 4) || (i === totalPaginas - 1 && pagActual < totalPaginas - 3)) {
            $p.append(`<li class="page-item disabled"><span class="page-link">…</span></li>`);
        }
    }
    $p.append(mk(Math.min(totalPaginas, pagActual + 1), '&raquo;', false, pagActual === totalPaginas));
}
window.goPag = function (n) { if (n < 1 || n > totalPaginas) return; pagActual = n; cargarServicios(); };

// ==== Modal crear/editar
function abrirModalNuevo() {
    $("#modal_title").text("Nuevo servicio");
    $("#srv_id").val("");
    $("#srv_nombre").val("");
    $("#srv_descipcion").val("");
    $("#srv_items").val("");
    $("#items_preview").html('');
    $("#srv_valor").val("");
    $("#srv_id_categoria").val($("#filtro_categoria").val() || "");
    $("#srv_agenda").val("1");
    $("#srv_pago").val("1");
    $("#srv_estado").val("1");
    $("#srv_imagen").val("");
    $("#modal_servicio").modal("show");
}

window.abrirModalEditar = function (id) {
    // Puedes crear endpoint detalle; acá reutilizamos el listar por ?id=
    $.getJSON(`${URL_LISTAR}?id=${id}`)
        .done(function (r) {
            const s = r?.data?.[0] || r;
            if (!s) { Swal.fire("Error", "No se encontró el servicio", "error"); return; }
            $("#modal_title").text("Editar servicio");
            $("#srv_id").val(s.id);
            $("#srv_nombre").val(s.nombre || "");
            $("#srv_descipcion").val(s.descipcion || s.descripcion || "");
            $("#srv_items").val(s.items || "");
            $("#items_preview").html(buildItemsChips($("#srv_items").val()));
            $("#srv_valor").val(s.valor || "");
            $("#srv_id_categoria").val(s.id_categoria || "");
            $("#srv_agenda").val(String(s.agenda ?? 1));
            $("#srv_pago").val(String(s.pago ?? 1));
            $("#srv_estado").val(String(s.estado ?? 1));
            $("#srv_imagen").val(s.imagen || "");
            $("#modal_servicio").modal("show");
        })
        .fail(() => Swal.fire("Error", "No fue posible cargar el servicio", "error"));
};

function guardarServicio() {
    const fd = new FormData();
    fd.append('id', $("#srv_id").val().trim());
    fd.append('id_categoria', $("#srv_id_categoria").val());
    fd.append('nombre', $("#srv_nombre").val().trim());
    fd.append('descripcion', $("#srv_descipcion").val().trim()); // tu input se llamaba srv_descipcion
    fd.append('items', $("#srv_items").val().trim());
    fd.append('valor', $("#srv_valor").val().trim());
    fd.append('agenda', $("#srv_agenda").val());
    fd.append('pago', $("#srv_pago").val());
    fd.append('estado', $("#srv_estado").val());
    fd.append('imagen', $("#srv_imagen").val().trim()); // nombre anterior (si existe)

    // Archivo (si el usuario eligió)
    const fileInput = document.getElementById('srv_imagen_file');
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        fd.append('srv_imagen_file', fileInput.files[0]);
    }

    // Validaciones básicas
    if (!fd.get('nombre')) { Swal.fire("Valida", "El nombre es obligatorio", "warning"); return; }
    if (!fd.get('descripcion')) { Swal.fire("Valida", "La descripción es obligatoria", "warning"); return; }

    $.ajax({
        url: "../model/model_servicios.php",
        type: "POST",
        data: fd,
        processData: false,   // IMPORTANTÍSIMO
        contentType: false,   // IMPORTANTÍSIMO
        dataType: "json"
    })
        .done(function (r) {
            if (r?.codigo === 0) {
                $("#modal_servicio").modal("hide");
                Swal.fire("OK", "Servicio guardado", "success");
                cargarServicios();
            } else {
                Swal.fire("Error", r?.error || r?.mensaje || "No fue posible guardar", "error");
            }
        })
        .fail(function (xhr) {
            const msg = xhr?.responseJSON?.error || "No fue posible guardar";
            Swal.fire("Error", msg, "error");
        });
}


// ==== Acciones
window.cambiarEstado = function (id, nuevo) {
    $.post(URL_CAMBIA_EST, { id, estado: nuevo }, function (r) {
        if (r?.codigo === 0) { cargarServicios(); }
        else { Swal.fire("Error", r?.error || "No fue posible cambiar el estado", "error"); }
    }, "json").fail(() => Swal.fire("Error", "No fue posible cambiar el estado", "error"));
};

window.eliminarServicio = function (id) {
    Swal.fire({
        title: "¿Eliminar servicio?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then(res => {
        if (!res.isConfirmed) return;
        $.post(URL_ELIMINAR, { id }, function (r) {
            if (r?.codigo === 0) { Swal.fire("Eliminado", "El servicio fue eliminado", "success"); cargarServicios(); }
            else { Swal.fire("Error", r?.error || "No fue posible eliminar", "error"); }
        }, "json").fail(() => Swal.fire("Error", "No fue posible eliminar", "error"));
    });
};
