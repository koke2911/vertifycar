// alert('aqui');

function login() {
    let usuario = $("#txt_usuario").val();
    let pass = $("#txt_password").val();

    if (usuario == "" || pass == "") {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ambos campos son obligatorios'
        });
        e.preventDefault();
        return;
    }

    $.post("../model/model_login.php", { usuario: usuario, pass: pass }, function (data) {
        if (data == 1) {
            window.location.href = "principal.php";
        } else {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data
            });
        }
    });
}

$(document).ready(function () {


    $("#txt_usuario").keyup(function () {
    });

    $("#txt_password").keyup(function () {
    });


    $("#btn_entrar").click(function () {
        login();
    });

    $('#txt_password').on('keypress', function (event) {
        if (event.which === 13) {
            login();
        }
    });




});