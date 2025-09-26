


function llenarRol(){
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "../model/data/cmb_roles_usuario.php",
        }).done(function (data) {
            $("#cmb_rol").html('');

            roles = "<option value=\"\">Seleccione un Rol</option>";

            for (var i = 0; i < data.length; i++) {
                roles += "<option value=\"" + data[i].ID + "\">" + data[i].GLOSA + "</option>";
            }

            $("#cmb_rol").append(roles);
        });
}

function guardaUsuario(){

    var txt_nombre = $("#txt_nombre").val();
    var txt_apellidos = $("#txt_apellidos").val();
    var txt_fono = $("#txt_fono").val();
    var txt_rut = $("#txt_rut").val();
    var cmb_rol = $("#cmb_rol").val();
    var txt_id = $("#txt_id").val();
    var estado = $("#cmb_estado").val();
    var correo = $("#txt_correo").val();


    $.ajax({
        url: "../model/model_usuarios.php",
        type: "POST",
        dataType: 'json',
        data: {
            txt_nombre: txt_nombre,
            txt_apellidos: txt_apellidos,
            txt_fono: txt_fono,
            txt_rut: txt_rut,
            cmb_rol: cmb_rol,
            txt_id: txt_id,
            estado: estado,
            correo: correo           
        },
        success: function (data) {
            if (data.codigo == 0) {
                Swal.fire({
                    title: 'Usuario Guardado',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
                $("#grid_usuarios").dataTable().fnReloadAjax("../model/grid/datagrid_usuarios.php");
                $("#txt_nombre").val("");
                $("#txt_apellidos").val("");
                $("#txt_fono").val("");
                $("#txt_rut").val("");
                $("#cmb_rol").val("");
                $("#txt_correo").val("");
                $("#cmb_estado").val("");
                $("#txt_id").val("");

                $("#btn_guardar").text("Guardar");

            } else {
                Swal.fire({
                    title: 'Ha ocurrido un error',
                    text: data.error,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        }
    });
}

function editarUsuario(rowData) {
    // console.log(filas.id);
    // var $tr = $(btn).closest('tr');
    // // Si está en vista responsive (child), subir al row padre
    // if ($tr.hasClass('child')) $tr = $tr.prev();

    // var rowData = grid_usuarios.row($tr).data();
    // if (!rowData) {
    //     Swal.fire('Error', 'No se pudo obtener la fila seleccionada', 'error');
    //     return;
    // }

    // // Setear campos
    $("#txt_id").val(rowData.id || "");
    $("#txt_nombre").val(rowData.nombre || "");
    $("#txt_apellidos").val(rowData.apellidos || "");
    $("#txt_fono").val(rowData.fono || "");
    $("#txt_rut").val(rowData.rut || "");
    $("#txt_correo").val(rowData.correo || "");
    $("#cmb_estado").val(rowData.estado).trigger('change');
    $("#cmb_rol").val(rowData.rol_id).trigger('change');

    // // Rol (usamos rol_id que tienes oculto en la tabla)
    // if (typeof rowData.rol_id !== "undefined") {
    //     $("#cmb_rol").val(rowData.rol_id).trigger('change');
    // }

    // // Estado (solo si tu dataset lo trae; si no, omite esta línea)
    // if (typeof rowData.estado !== "undefined") {
    //     $("#cmb_estado").val(rowData.estado).trigger('change');
    // }

    // // Opcional: cambia el texto del botón guardar a "Actualizar"
    $("#btn_guardar").text("Actualizar");

    // // Opcional: enfocar primer campo y hacer scroll al formulario
    // $("#txt_nombre").focus();
    // if ($("#form_usuario").length) {
    //     $('html, body').animate({ scrollTop: $("#form_usuario").offset().top - 20 }, 300);
    // }
}

$(document).ready(function () {
    llenarRol();

    var grid_usuarios = $('#grid_usuarios').DataTable({
        "responsive": true,
        "paging": true,
        "destroy": true,
        "ajax": "../model/grid/datagrid_usuarios.php",
        "columns": [
            { "data": "id" },
            { "data": "nombre" },
            { "data": "apellidos" },           
            { "data": "rut" },
            { "data": "fono" },
            { "data": "correo" },
            {
                data: "rol",
                render: function (data, type, row) {
                    let clase = "";
                    if (data === "Administrador") clase = "badge-admin";
                    else if (data === "Mecanico") clase = "badge-mecanico";

                    return `<span class="badge-role ${clase}">${data}</span>`;
                }
            },
            { "data": "estado",visible:false },
            {
                data: "estado_glosa", // o "estado" si usas 1/0
                render: function (data, type, row) {
                    let clase = "";
                    if (data === "Activo" || data === "1") clase = "badge-activo";
                    else if (data === "Inactivo" || data === "0") clase = "badge-inactivo";

                    return `<span class="badge-estado ${clase}">${data}</span>`;
                }
            },
            { "data": "rol_id",visible:false },
            {
                data: "id",
                render: function (data, type, row, meta) {
                    return `
                            <button type="button" class="btn btn-sm btn-info"
                                onclick='editarUsuario(${JSON.stringify(row)})'
                                title="Editar">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </button>
                             <button type="button" class="btn btn-sm btn-primary"
                                onclick='resetearClave(${data})'
                                title="Restablecer Contraseña">
                                <i class="fa fa-key" aria-hidden="true"></i>
                            </button>
                            
                            `;
                }
            }
        ],

        "select": {
            "style": "single"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-excel',
                title: 'solicitudes Finalizadas',
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger',
                title: 'solicitudes Finalizadas',
                orientation: 'portrait',
                pageSize: 'LETTER'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Imprimir',
                titleAttr: 'Imprimir',
                className: 'btn btn-info',
                title: "Informe de Arranques"
            },
        ],

        "language": {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "select": {
                "rows": "<br/>%d Tipos de atención Seleccionados"
            },
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Sig.",
                "previous": "Ant."
            }
        }
    });


    $("#btn_guardar").on("click", function () {
        var txt_nombre = $("#txt_nombre").val().trim();
        var txt_apellidos = $("#txt_apellidos").val().trim();
        var txt_fono = $("#txt_fono").val().trim();
        var txt_rut = $("#txt_rut").val().trim();
        var cmb_rol = $("#cmb_rol").val();
        var estado = $("#cmb_estado").val();
        var correo = $("#txt_correo").val().trim();

        // Validar que no estén vacíos
        if (txt_nombre === "" || txt_apellidos === "" || txt_fono === "" || txt_rut === "" || cmb_rol === "" || estado === "" || correo === "") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe llenar todos los datos del formulario'
            });
            return;
        }

        // Validar teléfono (solo números)
        if (!/^[0-9]+$/.test(txt_fono)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El teléfono solo debe contener números'
            });
            return;
        }

        // Validar correo
        var regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexCorreo.test(correo)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe ingresar un correo electrónico válido'
            });
            return;
        }

        // Validar RUT chileno
        if (!validaRut(txt_rut)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe ingresar un RUT válido (ejemplo: 17525457-9, sin puntos)'
            });
            return;
        }

        // Si todo está bien, guardar
        guardaUsuario();
    });

    function validaRut(rutCompleto) {
        // Formato básico: solo números-guion-dígito (k o número)
        if (!/^[0-9]+-[0-9kK]{1}$/.test(rutCompleto))
            return false;

        var tmp = rutCompleto.split('-');
        var digv = tmp[1].toLowerCase();
        var rut = parseInt(tmp[0], 10);

        return (dv(rut) === digv);
    }

    function dv(T) {
        var M = 0, S = 1;
        for (; T; T = Math.floor(T / 10))
            S = (S + T % 10 * (9 - M++ % 6)) % 11;
        return S ? (S - 1) + '' : 'k';
    }

    $("#btn_cancelar").on("click",function(){
        $("#txt_nombre").val("");
        $("#txt_id").val("");
        $("#txt_apellidos").val("");
        $("#txt_fono").val("");
        $("#txt_rut").val("");
        $("#cmb_rol").val("");
        $("#txt_correo").val("");
        $("#cmb_estado").val("");

        $("#btn_guardar").text("Guardar");
    })
});