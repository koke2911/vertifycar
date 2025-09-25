// alert('aqui');
// Toggle sidebar en móviles
$('#btnSidebar').on('click', function () {
    $('#sidebar').toggleClass('show');
});

// Manejo de navegación en sidebar
$('.sidebar .nav-link').on('click', function (e) {
    e.preventDefault();
    var url = $(this).data('target');
    if (!url) return;
    $('#appframe').attr('src', url);
    $('.sidebar .nav-link').removeClass('active');
    $(this).addClass('active');

    // En móviles, ocultar sidebar tras elegir
    if (window.matchMedia("(max-width: 991.98px)").matches) {
        $('#sidebar').removeClass('show');
    }
});

// Acciones rápidas
$('[data-open]').on('click', function () {
    var url = $(this).data('open');
    $('#appframe').attr('src', url);
    $('.sidebar .nav-link').removeClass('active');
    $('.sidebar .nav-link[data-target="' + url + '"]').addClass('active');
    if (window.matchMedia("(max-width: 991.98px)").matches) {
        $('#sidebar').removeClass('show');
    }
});

// Logout (ajusta a tu endpoint de salida real)
$('#btnLogout').on('click', function () {
    // Ejemplo simple: redirigir a una ruta PHP que destruya la sesión
    window.location.href = '../model/logout.php';
});