<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Boticas D'Toty Farma</title>
    <!-- Custom CSS -->
    <link href="assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="{{ url('dist/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="ruta/a/iconly.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <style>
        /* Transición suave para todos los elementos del enlace */
        .sidebar-link {
            transition: all 0.3s ease;
        }

        /* Íconos y texto dentro del enlace */
        .sidebar-link i,
        .sidebar-link .hide-menu {
            transition: color 0.3s ease, stroke 0.3s ease;
        }

        /* Ítem activo */
        .sidebar-item.active>.sidebar-link {
            background-color: #e6f0ff;
            color: #1a73e8 !important;
            font-weight: 600;
            border-radius: 10px;
        }

        /* Cambiar color texto e ícono activo */
        .sidebar-item.active>.sidebar-link i,
        .sidebar-item.active>.sidebar-link .hide-menu {
            color: #1a73e8 !important;
        }

        /* Forzar cambio de color stroke en path dentro del SVG */
        .sidebar-item.active>.sidebar-link i svg path {
            stroke: #1a73e8 !important;
        }

        /* Hover en ítem */
        .sidebar-item:hover>.sidebar-link {
            background-color: #e6f0ff;
            color: #1a73e8 !important;
            font-weight: 600;
            border-radius: 10px;
        }

        /* Cambiar color texto e ícono al pasar mouse */
        .sidebar-item:hover>.sidebar-link i,
        .sidebar-item:hover>.sidebar-link .hide-menu {
            color: #1a73e8 !important;
        }

        /* Forzar cambio de color stroke en path dentro del SVG al pasar mouse */
        .sidebar-item:hover>.sidebar-link i svg path {
            stroke: #1a73e8 !important;
        }

        /* Color inicial del ícono */
        .sidebar-nav .feather-icon {
            color: #007bff;
            stroke: currentColor;
        }

        /* Color inicial del texto */
        .sidebar-nav .sidebar-link {
            color: #007bff;
        }

        .sidebar-nav .sidebar-link .feather-icon {
            stroke: currentColor;
        }

        .sidebar-item.selected {
            background-color: transparent !important;
            color: inherit !important;
            font-weight: normal !important;
        }

        .sidebar-item.selected>.sidebar-link {
            background-color: transparent !important;
            color: inherit !important;
            font-weight: normal !important;
            border-radius: 0 !important;
        }

        .sidebar-item.selected>.sidebar-link i,
        .sidebar-item.selected>.sidebar-link .hide-menu {
            color: inherit !important;
        }
    </style>

    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>

                    <div class="navbar-brand">
                        <!-- Logo icon -->
                        <a href="index.html">
                            <b class="logo-icon">
                                <!-- Dark Logo icon -->
                                <img src="assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                                <!-- Light Logo icon -->
                                <img src="assets/images/logo-icon.png" alt="homepage" class="light-logo" />
                            </b>
                            <!--End Logo icon -->
                            <!-- Logo text -->
                            <span class="logo-text">
                                <!-- dark Logo text -->
                                <img src="assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                                <!-- Light Logo text -->
                                <img src="assets/images/logo-light-text.png" class="light-logo" alt="homepage" />
                            </span>
                        </a>
                    </div>

                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                            class="ti-more"></i></a>
                </div>

                <div class="navbar-collapse collapse" id="navbarSupportedContent">

                    <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
                        <!-- Notification -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle pl-md-3 position-relative" href="javascript:void(0)"
                                id="bell" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <span><i data-feather="bell" class="svg-icon"></i></span>
                                <span class="badge badge-primary notify-no rounded-circle">5</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                                <ul class="list-style-none">
                                    <li>
                                        <div class="message-center notifications position-relative">
                                            <!-- Message -->
                                            <a href="javascript:void(0)"
                                                class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                                <div class="btn btn-danger rounded-circle btn-circle"><i
                                                        data-feather="airplay" class="text-white"></i></div>
                                                <div class="w-75 d-inline-block v-middle pl-2">
                                                    <h6 class="message-title mb-0 mt-1">Luanch Admin</h6>
                                                    <span class="font-12 text-nowrap d-block text-muted">Just see
                                                        the my new
                                                        admin!</span>
                                                    <span class="font-12 text-nowrap d-block text-muted">9:30 AM</span>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)"
                                                class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                                <span class="btn btn-success text-white rounded-circle btn-circle"><i
                                                        data-feather="calendar" class="text-white"></i></span>
                                                <div class="w-75 d-inline-block v-middle pl-2">
                                                    <h6 class="message-title mb-0 mt-1">Event today</h6>
                                                    <span
                                                        class="font-12 text-nowrap d-block text-muted text-truncate">Just
                                                        a reminder that you have event</span>
                                                    <span class="font-12 text-nowrap d-block text-muted">9:10 AM</span>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)"
                                                class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                                <span class="btn btn-info rounded-circle btn-circle"><i
                                                        data-feather="settings" class="text-white"></i></span>
                                                <div class="w-75 d-inline-block v-middle pl-2">
                                                    <h6 class="message-title mb-0 mt-1">Settings</h6>
                                                    <span
                                                        class="font-12 text-nowrap d-block text-muted text-truncate">You
                                                        can customize this template
                                                        as you want</span>
                                                    <span class="font-12 text-nowrap d-block text-muted">9:08 AM</span>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)"
                                                class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                                <span class="btn btn-primary rounded-circle btn-circle"><i
                                                        data-feather="box" class="text-white"></i></span>
                                                <div class="w-75 d-inline-block v-middle pl-2">
                                                    <h6 class="message-title mb-0 mt-1">Pavan kumar</h6> <span
                                                        class="font-12 text-nowrap d-block text-muted">Just
                                                        see the my admin!</span>
                                                    <span class="font-12 text-nowrap d-block text-muted">9:02 AM</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="nav-link pt-3 text-center text-dark" href="javascript:void(0);">
                                            <strong>Check all notifications</strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="settings" class="svg-icon"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </div>
                        </li>
                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link" href="javascript:void(0)">
                                <div class="customize-input">
                                    <select
                                        class="custom-select form-control bg-white custom-radius custom-shadow border-0">
                                        <option selected>EN</option>
                                        <option value="1">AB</option>
                                        <option value="2">AK</option>
                                        <option value="3">BE</option>
                                    </select>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav float-right">

                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link" href="javascript:void(0)">
                                <form>
                                    <div class="customize-input">
                                        <input class="form-control custom-shadow custom-radius border-0 bg-white"
                                            type="search" placeholder="Search" aria-label="Search">
                                        <i class="form-control-icon" data-feather="search"></i>
                                    </div>
                                </form>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="assets/images/users/profile-pic.jpg" alt="user" class="rounded-circle"
                                    width="40">
                                <span class="ml-2 d-none d-lg-inline-block"><span>Hello,</span> <span
                                        class="text-dark">Jason Doe</span> <i data-feather="chevron-down"
                                        class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="user"
                                        class="svg-icon mr-2 ml-1"></i>
                                    My Profile</a>
                                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="credit-card"
                                        class="svg-icon mr-2 ml-1"></i>
                                    My Balance</a>
                                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="mail"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Inbox</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="settings"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Account Setting</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="power"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Logout</a>
                                <div class="dropdown-divider"></div>
                                <div class="pl-4 p-3"><a href="javascript:void(0)" class="btn btn-sm btn-info">View
                                        Profile</a></div>
                            </div>
                        </li>

                    </ul>
                </div>
            </nav>
        </header>

        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">

                        <li class="nav-small-cap"><span class="hide-menu">Principal</span></li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('dashboard.index') }}" aria-expanded="false">
                                <i class="fa fa-home"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                        <li class="nav-small-cap"><span class="hide-menu">Gestión de usuarios</span></li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('usuarios.index') }}" aria-expanded="false">
                                <i class="fa fa-users"></i>
                                <span class="hide-menu">Usuarios</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="index.html" aria-expanded="false">
                                <i class="fa fa-shield-alt"></i>
                                <span class="hide-menu">Roles y Permisos</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                        <li class="nav-small-cap"><span class="hide-menu">Punto de Venta (POS)</span></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                aria-expanded="false"><i data-feather="shopping-cart" class="feather-icon"></i>
                                <span class="hide-menu">Ventas </span></a>

                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="{{ route('ventas.index') }}"
                                        class="sidebar-link"><span class="hide-menu"> Nueva Venta
                                        </span></a>
                                </li>
                                <li class="sidebar-item"><a href="{{ route('ventas.historial') }}" class="sidebar-link"><span
                                            class="hide-menu"> Historial de ventas
                                        </span></a>
                                </li>
                                <li class="sidebar-item"><a href="{{ route('devoluciones.index') }}"
                                        class="sidebar-link"><span class="hide-menu"> Devoluciones
                                        </span></a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('clientes.index') }}" aria-expanded="false">
                                <i class="fa fa-user"></i>
                                <span class="hide-menu">Clientes</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                        <li class="nav-small-cap"><span class="hide-menu">Compras</span></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                aria-expanded="false"><i data-feather="shopping-bag" class="feather-icon"></i>
                                <span class="hide-menu">Compras </span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="form-inputs.html" class="sidebar-link"><span
                                            class="hide-menu"> Nueva Compra
                                        </span></a>
                                </li>
                                <li class="sidebar-item"><a href="form-input-grid.html" class="sidebar-link"><span
                                            class="hide-menu"> Historial de compras
                                        </span></a>
                                </li>
                                <li class="sidebar-item"><a href="form-checkbox-radio.html"
                                        class="sidebar-link"><span class="hide-menu"> Devoluciones
                                        </span></a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('proveedores.index') }}" aria-expanded="false">
                                <i class="fa fa-truck"></i>
                                <span class="hide-menu">Proveedores</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                        <li class="nav-small-cap"><span class="hide-menu">Inventario</span></li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('productos.index') }}" aria-expanded="false">
                                <i class="fa fa-box"></i>
                                <span class="hide-menu">Productos</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('categorias.index') }}" aria-expanded="false">
                                <i class="fa fa-layer-group"></i>
                                <span class="hide-menu">Categorias</span>
                            </a>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                aria-expanded="false"><i data-feather="shuffle" class="feather-icon"></i>
                                <span class="hide-menu">Movimientos</span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="form-inputs.html" class="sidebar-link"><span
                                            class="hide-menu"> Ingresos
                                        </span></a>
                                </li>
                                <li class="sidebar-item"><a href="form-input-grid.html" class="sidebar-link"><span
                                            class="hide-menu"> Salidas
                                        </span></a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="ticket-list.html" aria-expanded="false">
                                <i data-feather="alert-triangle" class="feather-icon"></i>
                                <span class="hide-menu">Alertas</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                        <li class="nav-small-cap"><span class="hide-menu">Finanzas</span></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                aria-expanded="false"><i data-feather="dollar-sign" class="feather-icon"></i>
                                <span class="hide-menu">Caja</span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="{{ route('caja.apertura.form') }}" class="sidebar-link"><span
                                            class="hide-menu"> Apertura / Cierre de caja
                                        </span></a>
                                </li> 
                                <li class="sidebar-item"><a href="{{ route('caja.listado') }}" class="sidebar-link"><span
                                            class="hide-menu"> Ingresos y egresos
                                        </span></a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="index.html" aria-expanded="false">
                                <i data-feather="bar-chart" class="feather-icon"></i>
                                <span class="hide-menu">Reportes Financieros</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                        <li class="nav-small-cap"><span class="hide-menu">Configuración del sistema</span></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                aria-expanded="false"><i data-feather="settings" class="feather-icon"></i>
                                <span class="hide-menu">Parametros</span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="{{ route('documentos.index') }}"
                                        class="sidebar-link"><span class="hide-menu"> Tipo de documento
                                        </span></a>
                                </li>
                                <li class="sidebar-item"><a href="{{ route('tipopagos.index') }}"
                                        class="sidebar-link"><span class="hide-menu"> Metodo de pago
                                        </span></a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="index.html" aria-expanded="false">
                                <i data-feather="briefcase" class="feather-icon"></i>
                                <span class="hide-menu">Datos de empresa</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="index.html" aria-expanded="false">
                                <i class="fa fa-coins"></i>
                                <span class="hide-menu">Monedas / Impuestos</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="javascript:void(0);"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i data-feather="log-out" class="feather-icon"></i>
                                <span class="hide-menu">Salir</span>
                            </a>
                        </li>

                    </ul>
                </nav>

            </div>

        </aside>

        <div class="page-wrapper">
            @yield('content')
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ url('dist/css/style.css') }}" rel="stylesheet">
    <script src="{{ url('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ url('assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ url('assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- apps -->
    <script src="{{ url('dist/js/app-style-switcher.js') }}"></script>
    <script src="{{ url('dist/js/feather.min.js') }}"></script>
    <script src="{{ url('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ url('dist/js/sidebarmenu.js') }}"></script>

    <!--Custom JavaScript -->
    <script src="{{ url('dist/js/custom.min.js') }}"></script>
    <!--This page JavaScript -->
    <script src="{{ url('assets/extra-libs/c3/d3.min.js') }}"></script>
    <script src="{{ url('assets/extra-libs/c3/c3.min.js') }}"></script>
    <script src="{{ url('assets/libs/chartist/dist/chartist.min.js') }}"></script>
    <script src="{{ url('assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>
    <script src="{{ url('assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ url('assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ url('dist/js/pages/dashboards/dashboard1.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            feather.replace(); // Esto asegura que se ejecuta después de que la página cargue completamente
        });
    </script>


    @yield('scripts')

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>

</html>
