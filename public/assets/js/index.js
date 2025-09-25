

$(document).ready(function () {
    // Año footer
    document.getElementById('y').textContent = new Date().getFullYear();

    // Navbar shrink en scroll (jQuery BS4)
    $(window).on('scroll', function () {
        var sc = $(this).scrollTop();
        $('.navbar').toggleClass('sticky-shrink', sc > 8);
    });

    // Smooth scroll anchors
    $('a[href^="#"]').on('click', function (e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 72
            }, 400);
        }
    });

});