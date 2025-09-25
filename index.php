<?php
include("config/variables.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Revisión de Autos Pre-Venta | Tu Marca</title>
  <meta name="description" content="Servicios de revisión pre-compra de vehículos. Elige el plan, agenda una fecha estimada y paga en línea. Recibe tu comprobante al instante." />
  <link rel="icon" href="assets/img/favicon.png" />

  <!-- Bootstrap 4.6 + vendors que ya usas -->
  <link rel="stylesheet" href="public/assets/js/libs/bootstrap-4.6/bootstrap.min.css">
  <link rel="stylesheet" href="public/assets/js/libs/vendor/datepicker/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="public/assets/js/libs/vendor/datatables/dataTables.cellEdit.css">
  <link href="public/assets/js/libs/js/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css">
  <script src="public/assets/js/libs/js/fontawesome.all.js" crossorigin="anonymous"></script>

  <style>
    :root {
      --brand: #0E6FFF;
      --accent: #18A999;
      --ink: #111827;
      --muted: #6B7280;
      --bg: #F3F4F6;
    }

    body {
      color: var(--ink);
    }

    .topbar {
      background: #0E6FFF;
      color: #fff;
      font-size: .875rem;
    }

    .topbar a {
      color: #fff;
    }

    .navbar-brand b {
      color: var(--brand);
    }

    .btn-brand {
      background: var(--brand);
      color: #fff;
      border: none;
    }

    .btn-brand:hover {
      background: #0b59cc;
      color: #fff;
    }

    .btn-accent {
      background: var(--accent);
      color: #fff;
      border: none;
    }

    .btn-accent:hover {
      background: #128d83;
      color: #fff;
    }

    .hero {
      background: linear-gradient(180deg, #ffffff 0%, #f7f9ff 100%);
    }

    .hero-badge {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 999px;
      padding: .35rem .75rem;
      font-size: .875rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
      display: inline-flex;
      align-items: center;
    }

    .section-pad {
      padding: 4rem 0;
    }

    .section-muted {
      background: #f8fafc;
    }

    .divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    }

    .card-service {
      transition: transform .2s ease, box-shadow .2s ease;
      border: 1px solid #eef2f7;
    }

    .card-service:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 24px rgba(0, 0, 0, .06);
    }

    .price-badge {
      position: absolute;
      top: 12px;
      right: 12px;
    }

    .step-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e9f2ff;
      color: var(--brand);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: .75rem;
      flex: 0 0 auto;
    }

    .benefit {
      border: 1px solid #eef2f7;
      border-radius: 1rem;
      padding: 1rem;
      background: #fff;
      height: 100%;
    }

    .gallery img {
      border-radius: .75rem;
    }

    .pay-logos img {
      height: 24px;
      filter: grayscale(1) opacity(.8);
      margin-right: 12px;
    }

    .footer-small {
      color: var(--muted);
    }

    .navbar.sticky-shrink {
      box-shadow: 0 6px 20px rgba(0, 0, 0, .06);
    }

    /* Testimonios */
    .quote {
      font-size: 1rem;
      color: #334155;
    }

    .quote i {
      color: #94a3b8;
    }

    /* WhatsApp flotante */
    .wa-float {
      position: fixed;
      right: 16px;
      bottom: 16px;
      z-index: 9999;
      background: #25D366;
      color: #fff;
      border-radius: 999px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
      display: flex;
      align-items: center;
      padding: .6rem .9rem;
      font-weight: 600;
    }

    .wa-float i {
      font-size: 1.2rem;
      margin-right: .4rem;
    }

    .wa-float:hover {
      color: #fff;
      background: #1ebe5d;
      text-decoration: none;
    }
  </style>

  <!-- Datos estructurados -->
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Tu Marca",
      "description": "Revisión pre-compra de vehículos con pago online y voucher inmediato.",
      "url": "https://www.tusitio.cl",
      "telephone": "+56 9 1234 5678",
      "address": {
        "@type": "PostalAddress",
        "addressCountry": "CL"
      },
      "areaServed": "Chile"
    }
  </script>
</head>

<body>

  <!-- TOP BAR (opcional) -->
  <div class="topbar py-1 d-none d-md-block">
    <div class="container d-flex justify-content-between">
      <div><i class="fas fa-shield-alt mr-1"></i> Sitio seguro · <i class="fas fa-lock mr-1"></i> Pagos cifrados</div>
      <div><i class="far fa-envelope mr-1"></i> contacto@tusitio.cl · <i class="fas fa-phone mr-1"></i> +56 9 1234 5678</div>
    </div>
  </div>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <span class="rounded-circle d-inline-block mr-2" style="width:28px;height:28px;background:var(--brand);"></span>
        <span><b>Tu</b>Marca</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
          <li class="nav-item"><a class="nav-link" href="#como-funciona">Cómo funciona</a></li>
          <li class="nav-item"><a class="nav-link" href="#testimonios">Testimonios</a></li>
          <li class="nav-item"><a class="nav-link" href="#faq">Preguntas</a></li>
          <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
        </ul>
        <a class="btn btn-brand ml-lg-3" href="pages/login.php"><i class="far fa-user mr-1"></i> Entrar</a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero section-pad">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <span class="hero-badge mb-3">
            <i class="fas fa-shield-alt mr-2"></i> Revisión pre-compra confiable
          </span>
          <h1 class="display-4 font-weight-bold">
            Revisa tu auto antes de comprar.<br>
            Rápido, claro y con <span style="color:var(--brand)">pago seguro</span>.
          </h1>
          <p class="lead text-muted mt-3">
            Elige tu plan, agenda una fecha estimada y confirma en línea.
            Recibe tu <b>comprobante</b> al instante.
          </p>
          <div class="mt-3">
            <a href="reservar.php" class="btn btn-brand btn-lg mr-2"><i class="far fa-calendar-check mr-2"></i>Reservar ahora</a>
            <a href="#servicios" class="btn btn-outline-secondary btn-lg">Ver planes</a>
          </div>
          <div class="mt-3 small text-muted"><i class="fas fa-clock mr-1"></i> Atención Lun–Sáb · <i class="fas fa-map-marker-alt mr-1"></i> Cobertura por comunas</div>
        </div>
        <div class="col-lg-6">
          <img class="img-fluid" src="https://placehold.co/640x420/png?text=Revisión+pre-compra" alt="Mecánico revisando vehículo" loading="lazy">
        </div>
      </div>
    </div>
  </header>

  <div class="divider"></div>

  <!-- SERVICIOS -->
  <section id="servicios" class="section-pad">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="font-weight-bold">Planes de revisión</h2>
        <p class="text-muted mb-0">Transparencia total: lo que incluye cada plan y su precio.</p>
      </div>

      <div class="row">
        <!-- Básica -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card card-service h-100 position-relative">
            <span class="badge badge-light price-badge">Ahorra tiempo</span>
            <img src="https://placehold.co/640x400?text=Revisión+Básica" class="card-img-top" alt="Revisión Básica" loading="lazy">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">Revisión Básica</h5>
              <ul class="list-unstyled text-muted small mb-3">
                <li><i class="fas fa-check-circle text-success mr-1"></i> Inspección visual general</li>
                <li><i class="fas fa-check-circle text-success mr-1"></i> Neumáticos y luces</li>
                <li><i class="fas fa-check-circle text-success mr-1"></i> Prueba corta</li>
              </ul>
              <div class="mt-auto d-flex align-items-center justify-content-between">
                <div>
                  <div class="h5 mb-0">$39.990</div><small class="text-muted">Precio final</small>
                </div>
                <a class="btn btn-brand" href="reservar.php?service=basica"><i class="far fa-calendar-check mr-1"></i>Reservar</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Completa -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card card-service h-100 position-relative">
            <span class="badge badge-primary price-badge">Recomendada</span>
            <img src="https://placehold.co/640x400?text=Revisión+Completa" class="card-img-top" alt="Revisión Completa" loading="lazy">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-0">Revisión Completa</h5>
              <ul class="list-unstyled text-muted small mb-3 mt-2">
                <li><i class="fas fa-check-circle text-success mr-1"></i> Mecánica general</li>
                <li><i class="fas fa-check-circle text-success mr-1"></i> Escáner OBD</li>
                <li><i class="fas fa-check-circle text-success mr-1"></i> Informe simple</li>
              </ul>
              <div class="mt-auto d-flex align-items-center justify-content-between">
                <div>
                  <div class="h5 mb-0">$59.990</div><small class="text-muted">Precio final</small>
                </div>
                <a class="btn btn-brand" href="reservar.php?service=completa"><i class="far fa-calendar-check mr-1"></i>Reservar</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Full -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card card-service h-100 position-relative">
            <span class="badge badge-success price-badge">Mayor detalle</span>
            <img src="https://placehold.co/640x400?text=Revisión+Full" class="card-img-top" alt="Revisión Full" loading="lazy">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">Revisión Full</h5>
              <ul class="list-unstyled text-muted small mb-3">
                <li><i class="fas fa-check-circle text-success mr-1"></i> Full + prueba en ruta</li>
                <li><i class="fas fa-check-circle text-success mr-1"></i> Informe detallado</li>
                <li><i class="fas fa-check-circle text-success mr-1"></i> Soporte prioritario</li>
              </ul>
              <div class="mt-auto d-flex align-items-center justify-content-between">
                <div>
                  <div class="h5 mb-0">$89.990</div><small class="text-muted">Precio final</small>
                </div>
                <a class="btn btn-brand" href="reservar.php?service=full"><i class="far fa-calendar-check mr-1"></i>Reservar</a>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /row -->
      <p class="small text-muted mb-0"><i class="fas fa-info-circle mr-1"></i> Todos los valores incluyen impuestos. Puedes reagendar con 24 h de anticipación.</p>
    </div>
  </section>

  <!-- CÓMO FUNCIONA -->
  <section id="como-funciona" class="section-pad section-muted">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="font-weight-bold">Cómo funciona</h2>
        <p class="text-muted mb-0">En cuatro pasos simples.</p>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-3 d-flex mb-3">
          <div class="d-flex">
            <div class="step-icon"><i class="fas fa-list"></i></div>
            <div>
              <h6 class="mb-1">1. Elige tu plan</h6>
              <p class="text-muted small mb-0">Básica, Completa o Full.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 d-flex mb-3">
          <div class="d-flex">
            <div class="step-icon"><i class="fas fa-id-card"></i></div>
            <div>
              <h6 class="mb-1">2. Ingresa tus datos</h6>
              <p class="text-muted small mb-0">Personales y del vehículo.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 d-flex mb-3">
          <div class="d-flex">
            <div class="step-icon"><i class="far fa-calendar-alt"></i></div>
            <div>
              <h6 class="mb-1">3. Selecciona fecha</h6>
              <p class="text-muted small mb-0">Coordinamos la visita.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 d-flex mb-3">
          <div class="d-flex">
            <div class="step-icon"><i class="fas fa-shield-alt"></i></div>
            <div>
              <h6 class="mb-1">4. Paga y confirma</h6>
              <p class="text-muted small mb-0">Validación segura.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-4">
        <a class="btn btn-accent btn-lg" href="reservar.php"><i class="far fa-credit-card mr-2"></i>Reservar ahora</a>
      </div>
    </div>
  </section>

  <!-- BENEFICIOS -->
  <section class="section-pad">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="font-weight-bold">¿Por qué elegirnos?</h2>
        <p class="text-muted mb-0">Servicio claro, rápido y confiable.</p>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="benefit"><i class="fas fa-clipboard-check text-success"></i> Checklist profesional e informe claro.</div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="benefit"><i class="fas fa-map-marker-alt text-primary"></i> Cobertura por regiones/comunas.</div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="benefit"><i class="fas fa-lock text-warning"></i> Pago seguro con pasarelas líderes.</div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="benefit"><i class="fas fa-headset text-info"></i> Soporte cercano por email/WhatsApp.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIOS (NUEVO) -->
  <section id="testimonios" class="section-pad section-muted">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="font-weight-bold">Lo que dicen nuestros clientes</h2>
        <p class="text-muted mb-0">Confianza basada en resultados reales.</p>
      </div>

      <div id="carouselTesti" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselTesti" data-slide-to="0" class="active"></li>
          <li data-target="#carouselTesti" data-slide-to="1"></li>
          <li data-target="#carouselTesti" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="row justify-content-center">
              <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                  <div class="card-body">
                    <p class="quote mb-2"><i class="fas fa-quote-left mr-2"></i>Servicio rapidísimo y el informe me dio seguridad para cerrar la compra.</p>
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle bg-light mr-2" style="width:36px;height:36px;"></div>
                      <div><b>María G.</b> <span class="text-muted small">· Santiago</span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="row justify-content-center">
              <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                  <div class="card-body">
                    <p class="quote mb-2"><i class="fas fa-quote-left mr-2"></i>Detectaron detalles que no había notado. Me ahorraron un mal negocio.</p>
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle bg-light mr-2" style="width:36px;height:36px;"></div>
                      <div><b>Pedro M.</b> <span class="text-muted small">· Maipú</span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="row justify-content-center">
              <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                  <div class="card-body">
                    <p class="quote mb-2"><i class="fas fa-quote-left mr-2"></i>Buen precio por la tranquilidad que entregan. Recomendados.</p>
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle bg-light mr-2" style="width:36px;height:36px;"></div>
                      <div><b>Camila R.</b> <span class="text-muted small">· Ñuñoa</span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <a class="carousel-control-prev" href="#carouselTesti" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Anterior</span>
        </a>
        <a class="carousel-control-next" href="#carouselTesti" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Siguiente</span>
        </a>
      </div>
    </div>
  </section>

  <!-- GALERÍA -->
  <section class="section-pad">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="font-weight-bold">Galería</h2>
        <p class="text-muted mb-0">Algunos trabajos y revisiones realizadas.</p>
      </div>
      <div class="row gallery">
        <div class="col-6 col-md-4 col-lg-3 mb-3"><a href="https://placehold.co/1000x700" target="_blank"><img class="img-fluid" src="https://placehold.co/500x350" alt="Trabajo 1" loading="lazy"></a></div>
        <div class="col-6 col-md-4 col-lg-3 mb-3"><a href="https://placehold.co/1000x700" target="_blank"><img class="img-fluid" src="https://placehold.co/500x350" alt="Trabajo 2" loading="lazy"></a></div>
        <div class="col-6 col-md-4 col-lg-3 mb-3"><a href="https://placehold.co/1000x700" target="_blank"><img class="img-fluid" src="https://placehold.co/500x350" alt="Trabajo 3" loading="lazy"></a></div>
        <div class="col-6 col-md-4 col-lg-3 mb-3"><a href="https://placehold.co/1000x700" target="_blank"><img class="img-fluid" src="https://placehold.co/500x350" alt="Trabajo 4" loading="lazy"></a></div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="section-pad section-muted">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="font-weight-bold">Preguntas frecuentes</h2>
      </div>
      <div id="faqAcc" role="tablist" aria-multiselectable="true">
        <div class="card mb-2">
          <div class="card-header" role="tab" id="q1">
            <h5 class="mb-0">
              <a data-toggle="collapse" href="#a1" aria-expanded="true" aria-controls="a1">¿Qué incluye cada revisión?</a>
            </h5>
          </div>
          <div id="a1" class="collapse show" role="tabpanel" aria-labelledby="q1" data-parent="#faqAcc">
            <div class="card-body">Cada plan incluye una lista de chequeos estándar. La Revisión Full agrega prueba en ruta e informe detallado.</div>
          </div>
        </div>
        <div class="card mb-2">
          <div class="card-header" role="tab" id="q2">
            <h5 class="mb-0">
              <a class="collapsed" data-toggle="collapse" href="#a2" aria-expanded="false" aria-controls="a2">¿Cómo reagendo mi cita?</a>
            </h5>
          </div>
          <div id="a2" class="collapse" role="tabpanel" aria-labelledby="q2" data-parent="#faqAcc">
            <div class="card-body">Puedes solicitar el cambio respondiendo el correo de confirmación o por WhatsApp (24 h antes).</div>
          </div>
        </div>
        <div class="card mb-2">
          <div class="card-header" role="tab" id="q3">
            <h5 class="mb-0">
              <a class="collapsed" data-toggle="collapse" href="#a3" aria-expanded="false" aria-controls="a3">¿Qué pasa si el pago falla?</a>
            </h5>
          </div>
          <div id="a3" class="collapse" role="tabpanel" aria-labelledby="q3" data-parent="#faqAcc">
            <div class="card-body">Si el pago no se confirma, la reserva queda en pendiente. Validamos servidor a servidor antes de agendar.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTACTO (NUEVO) -->
  <section id="contacto" class="section-pad">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="font-weight-bold">¿Tienes dudas antes de reservar?</h2>
        <p class="text-muted mb-0">Escríbenos y te respondemos en el día.</p>
      </div>
      <div class="row">
        <div class="col-lg-7 mb-4 mb-lg-0">
          <form>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nombre</label>
                <input type="text" class="form-control" placeholder="Tu nombre">
              </div>
              <div class="form-group col-md-6">
                <label>Correo</label>
                <input type="email" class="form-control" placeholder="tu@correo.cl">
              </div>
            </div>
            <div class="form-group">
              <label>Mensaje</label>
              <textarea class="form-control" rows="4" placeholder="Cuéntanos tu caso"></textarea>
            </div>
            <button class="btn btn-brand"><i class="far fa-paper-plane mr-1"></i>Enviar</button>
            <small class="text-muted d-block mt-2"><i class="fas fa-lock mr-1"></i> Nunca compartimos tus datos.</small>
          </form>
        </div>
        <div class="col-lg-5">
          <div class="border rounded p-3 h-100">
            <h6 class="mb-2">Atención</h6>
            <p class="mb-2"><i class="far fa-clock mr-1"></i> Lun–Sáb, 09:00–19:00</p>
            <h6 class="mb-2">Contacto</h6>
            <p class="mb-2"><i class="far fa-envelope mr-1"></i> contacto@tusitio.cl<br><i class="fas fa-phone mr-1"></i> +56 9 1234 5678</p>
            <h6 class="mb-2">Cobertura</h6>
            <p class="mb-0"><i class="fas fa-map-marker-alt mr-1"></i> RM y comunas aledañas</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA FINAL -->
  <section class="section-pad text-center section-muted">
    <div class="container">
      <h3 class="font-weight-bold mb-2">¿Listo para agendar tu revisión?</h3>
      <p class="text-muted mb-4">Reserva en minutos y recibe tu comprobante al instante.</p>
      <a href="reservar.php" class="btn btn-brand btn-lg"><i class="far fa-calendar-check mr-2"></i>Reservar ahora</a>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="pt-5 pb-4 bg-white border-top">
    <div class="container">
      <div class="row">
        <div class="col-md-6 mb-4 mb-md-0">
          <h5><b>Tu</b>Marca</h5>
          <p class="footer-small mb-2">Revisión pre-compra de vehículos con pago online y voucher inmediato.</p>
          <p class="footer-small mb-0"><i class="fas fa-map-marker-alt mr-1"></i> Región/Comuna, Chile</p>
          <p class="footer-small mb-0"><i class="far fa-envelope mr-1"></i> contacto@tusitio.cl</p>
          <p class="footer-small mb-0"><i class="fas fa-phone mr-1"></i> +56 9 1234 5678</p>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
          <h6 class="mb-3">Secciones</h6>
          <ul class="list-unstyled footer-small">
            <li><a class="text-reset text-decoration-none" href="#servicios">Servicios</a></li>
            <li><a class="text-reset text-decoration-none" href="#testimonios">Testimonios</a></li>
            <li><a class="text-reset text-decoration-none" href="#contacto">Contacto</a></li>
            <li><a class="text-reset text-decoration-none" href="terminos.html">Términos y Condiciones</a></li>
            <li><a class="text-reset text-decoration-none" href="privacidad.html">Política de Privacidad</a></li>
          </ul>
        </div>
        <div class="col-md-3">
          <h6 class="mb-3">Medios de pago</h6>
          <div class="pay-logos mb-3">
            <img src="https://placehold.co/120x24?text=Transbank" alt="Transbank">
            <img src="https://placehold.co/120x24?text=Flow" alt="Flow">
            <img src="https://placehold.co/120x24?text=MercadoPago" alt="MercadoPago">
          </div>
          <p class="footer-small">Sitio protegido con <b>HTTPS/SSL</b>.</p>
        </div>
      </div>
      <div class="border-top mt-4 pt-3 d-flex justify-content-between align-items-center">
        <small class="footer-small">© <span id="y"></span> TuMarca. Todos los derechos reservados.</small>
        <small class="footer-small"><a class="text-reset" href="mailto:contacto@tusitio.cl">Soporte</a></small>
      </div>
    </div>
  </footer>

  <!-- WhatsApp flotante -->
  <a href="https://wa.me/56912345678?text=Hola,%20quiero%20cotizar%20una%20revisión%20pre-compra" class="wa-float" target="_blank" rel="noopener">
    <i class="fab fa-whatsapp"></i> WhatsApp
  </a>


  <!-- JS (orden correcto BS4: jQuery -> Popper incluido en bundle -> plugins) -->
  <script src="public/assets/js/libs/bootstrap-4.6/jquery-3.6.0.min.js"></script>
  <script src="public/assets/js/libs/bootstrap-4.6/bootstrap.bundle.min.js"></script>
  <script src="public/assets/js/libs/js/jquery-validation/dist/jquery.validate.js"></script>
  <script src="public/assets/js/libs/js/sweetalert2/sweetalert2.all.min.js"></script>
  <script src="public/assets/js/libs/js/bootstrap-select/js/bootstrap-select.min.js"></script>

  <script src="public/assets/js/index.js?v=<?php echo $version; ?>"></script>

</body>

</html>