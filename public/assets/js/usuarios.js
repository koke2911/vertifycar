// ===== Utils =====
let GRID = null;
const $ = window.jQuery;

function setLoading(btn, on) {
    const $b = $(btn);
    if (on) { $b.prop('disabled', true).data('old', $b.html()).html('<i class="fa fa-spinner fa-spin"></i> Guardando'); }
    else { $b.prop('disabled', false).html($b.data('old') || '<i class="fas fa-save mr-1"></i>Guardar'); }
}
function sanitizeRut(raw = '') { return String(raw).replace(/\./g, '').replace(/\s+/g, '').toLowerCase(); }
function validaRut(rutCompleto) {
    rutCompleto = sanitizeRut(rutCompleto);
    if (!/^[0-9]+-[0-9kK]{1}$/.test(rutCompleto)) return false;
    const [num, dv] = rutCompleto.split('-');
    let T = parseInt(num, 10), M = 0, S = 1;
    for (; T; T = Math.floor(T / 10)) S = (S + T % 10 * (9 - M++ % 6)) % 11;
    const dvCalc = S ? String(S - 1) : 'k';
    return dv === dvCalc;
}
function validaEmail(mail) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(mail).trim()); }
function validaFono(f) { return /^[0-9]{7,12}$/.test(String(f).trim()); }

// ===== Cargar roles =====
function llenarRol() {
    $.getJSON("../model/data/cmb_roles_usuario.php").done(data => {
        let html = `<option value="">Seleccione un Rol</option>`;
        (data || []).forEach(r => html += `<option value="${r.ID}">${r.GLOSA}</option>`);
        $("#cmb_rol").html(html);
    });
}

// ===== Init =====
$(document).ready(function () {
    llenarRol();

    GRID = $('#grid_usuarios').DataTable({
        responsive: true,
        destroy: true,
        ajax: "../model/grid/datagrid_usuarios.php",
        columns: [
            { data: "id" },
            { data: "nombre" },
            { data: "apellidos" },
            { data: "rut" },
            { data: "fono" },
            { data: "correo" },
            { data: "rol" },
            { data: "rol_id", visible: false },
            { data: "estado_glosa", render: (g, t, row) => row.estado == 1 ? `<span class="chip ok">Activo</span>` : `<span class="chip off">Inactivo</span>` },
            { data: "estado", visible: false },
            {
                data: "id",
                orderable: false,
                render: function (id, type, row) {
                    return `
            <button class="btn btn-sm btn-info" title="Editar" onclick="editarUsuario(${id})"><i class="fa fa-pencil"></i></button>
           `;
                }
            }
        ],
        language: {
            decimal: "", emptyTable: "No hay información",
            info: "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            infoEmpty: "Mostrando 0 a 0 de 0 Entradas",
            infoFiltered: "(Filtrado de _MAX_ total entradas)",
            lengthMenu: "Mostrar _MENU_ Entradas",
            loadingRecords: "Cargando...", processing: "Procesando...",
            search: "Buscar:", zeroRecords: "Sin resultados encontrados",
            paginate: { first: "Primero", last: "Último", next: "Sig.", previous: "Ant." }
        }
    });

    // Validaciones on-blur
    $("#txt_correo").on("blur", function () { const v = $(this).val().trim(); if (v && !validaEmail(v)) Swal.fire("Atención", "Correo inválido", "warning"); });
    $("#txt_fono").on("blur", function () { const v = $(this).val().trim(); if (v && !validaFono(v)) Swal.fire("Atención", "El fono debe ser solo dígitos (7–12)", "warning"); });
    $("#txt_rut").on("blur", function () { const v = $(this).val().trim(); if (v && !validaRut(v)) Swal.fire("Atención", "RUT inválido. Formato: 11111111-1 (sin puntos)", "warning"); });

    // Guardar
    $("#btn_guardar").on("click", function () {
        const btn = this;
        const payload = {
            txt_id: $("#txt_id").val().trim(),
            txt_nombre: $("#txt_nombre").val().trim(),
            txt_apellidos: $("#txt_apellidos").val().trim(),
            txt_fono: $("#txt_fono").val().trim(),
            txt_rut: sanitizeRut($("#txt_rut").val().trim()),
            cmb_rol: $("#cmb_rol").val(),
            estado: $("#cmb_estado").val(),
            correo: $("#txt_correo").val().trim()
        };

        if (!payload.txt_nombre || !payload.txt_apellidos || !payload.txt_fono || !payload.txt_rut || !payload.cmb_rol || payload.estado === "" || !payload.correo) {
            Swal.fire("Error", "Debe llenar todos los datos del formulario", "error"); return;
        }
        if (!validaFono(payload.txt_fono)) { Swal.fire("Error", "El teléfono solo debe contener dígitos (7–12)", "error"); return; }
        if (!validaEmail(payload.correo)) { Swal.fire("Error", "Debe ingresar un correo válido", "error"); return; }
        if (!validaRut(payload.txt_rut)) { Swal.fire("Error", "Debe ingresar un RUT válido (11111111-1, sin puntos)", "error"); return; }

        setLoading(btn, true);
        $.ajax({
            url: "../model/model_usuarios.php",
            type: "POST",
            dataType: "json",
            data: payload
        }).done(function (r) {
            if (r?.codigo === 0) {
                Swal.fire("OK", "Usuario guardado", "success");
                $("#grid_usuarios").dataTable().fnReloadAjax("../model/grid/datagrid_usuarios.php");
                limpiarForm();
            } else {
                Swal.fire("Error", r?.error || r?.mensaje || "No fue posible guardar", "error");
            }
        }).fail(function (xhr) {
            Swal.fire("Error", xhr?.responseJSON?.error || "No fue posible guardar", "error");
        }).always(() => setLoading(btn, false));
    });

    $("#btn_cancelar").on("click", limpiarForm);
});

function limpiarForm() {
    $("#txt_id").val("");
    $("#txt_nombre").val("");
    $("#txt_apellidos").val("");
    $("#txt_fono").val("");
    $("#txt_rut").val("");
    $("#cmb_rol").val("");
    $("#txt_correo").val("");
    $("#cmb_estado").val("");
    $("#btn_guardar").html('<i class="fas fa-save mr-1"></i>Guardar');
}

window.editarUsuario = function (id) {
    const row = GRID.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!row) { Swal.fire("Error", "No se encontró el usuario", "error"); return; }

    $("#txt_id").val(row.id);
    $("#txt_nombre").val(row.nombre || "");
    $("#txt_apellidos").val(row.apellidos || "");
    $("#txt_fono").val(row.fono || row.contacto || "");
    $("#txt_rut").val(row.rut || "");
    $("#cmb_rol").val(row.rol_id || "");
    $("#txt_correo").val(row.correo || row.email || "");
    $("#cmb_estado").val(String(row.estado ?? 1));
    $("#btn_guardar").html('<i class="fas fa-save mr-1"></i>Actualizar');
    $("html, body").animate({ scrollTop: 0 }, 200);
};

window.bloquearUsuario = function (id) {
    Swal.fire({ title: "¿Bloquear usuario?", icon: "warning", showCancelButton: true, confirmButtonText: "Sí, bloquear", cancelButtonText: "Cancelar" })
        .then(res => {
            if (!res.isConfirmed) return;
            $.post("../model/acciones/usuarios_cambiar_estado.php", { id, estado: 0 }, function (r) {
                if (r?.codigo === 0) { $('#grid_usuarios').dataTable().fnReloadAjax("../model/grid/datagrid_usuarios.php"); }
                else { Swal.fire("Error", r?.error || "No fue posible cambiar el estado", "error"); }
            }, "json").fail(() => Swal.fire("Error", "No fue posible cambiar el estado", "error"));
        });
};

window.activarUsuario = function (id) {
    $.post("../model/acciones/usuarios_cambiar_estado.php", { id, estado: 1 }, function (r) {
        if (r?.codigo === 0) { $('#grid_usuarios').dataTable().fnReloadAjax("../model/grid/datagrid_usuarios.php"); }
        else { Swal.fire("Error", r?.error || "No fue posible cambiar el estado", "error"); }
    }, "json").fail(() => Swal.fire("Error", "No fue posible cambiar el estado", "error"));
};

window.resetearUsuario = function (id) {
    Swal.fire({
        title: "¿Resetear clave?", text: "La clave quedará como hash del RUT", icon: "question", showCancelButton: true,
        confirmButtonText: "Sí, resetear", cancelButtonText: "Cancelar"
    }).then(res => {
        if (!res.isConfirmed) return;
        $.post("../model/acciones/usuarios_resetear_clave.php", { id }, function (r) {
            if (r?.codigo === 0) Swal.fire("OK", "Clave reseteada", "success");
            else Swal.fire("Error", r?.error || "No fue posible resetear", "error");
        }, "json").fail(() => Swal.fire("Error", "No fue posible resetear", "error"));
    });
};
