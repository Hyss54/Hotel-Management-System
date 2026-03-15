<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gold Plus Hotel') }} - @yield('title')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #0f172a;
            /* Slate 900 */
            --secondary-color: #3b82f6;
            /* Blue 500 */
            --accent-color: #eab308;
            /* Yellow 500 - Gold */
            --bg-light: #f8fafc;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--primary-color);
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }

        #sidebar ul.components {
            padding: 20px 0;
            margin-bottom: 0;
        }

        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        #sidebar ul li a i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        #sidebar ul li.active>a,
        #sidebar ul li a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border-left-color: var(--accent-color);
        }

        /* Main Content Styles */
        #content {
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            position: relative;
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }

        /* Top Navbar */
        .top-navbar {
            background: #fff;
            padding: 15px 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .main-container {
            padding: 25px;
        }

        /* Basic elements */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 15px 20px;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-warning {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: #000;
        }

        .table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            background: #f8fafc;
        }

        /* Print Styles */
        @media print {

            #sidebar,
            .top-navbar,
            .btn,
            a.btn,
            .d-print-none {
                display: none !important;
            }

            #content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
            }

            .main-container {
                padding: 0 !important;
            }

            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }

            .card-header {
                background-color: transparent !important;
            }

            @page {
                size: portrait;
                margin: 0.5cm;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <x-application-logo class="w-auto h-12 mx-auto object-contain"
                        style="max-height: 50px; width: auto;" />
                </a>
            </div>

            <ul class="list-unstyled components">
                @can('view-dashboard')
                    <li
                        class="{{ request()->routeIs('dashboard') || request()->routeIs('frontdesk.index') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-desktop"></i>
                            @if(auth()->user()->hasRole('front_desk'))
                                Command Center
                            @else
                                Dashboard
                            @endif
                        </a>
                    </li>
                @endcan

                {{-- Remove the separate Front Desk Portal link if it's now the Dashboard --}}
                @if(!auth()->user()->hasRole('front_desk'))
                    @can('view-bookings')
                        <li class="{{ request()->routeIs('frontdesk.index') ? 'active' : '' }}">
                            <a href="{{ route('frontdesk.index') }}"><i class="fas fa-desktop"></i> Front Desk Portal</a>
                        </li>
                    @endcan
                @endif

                @can('view-bookings')
                    <li class="px-3 pt-3 pb-1 text-uppercase text-muted"
                        style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Front Desk</li>
                    <li class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('bookings.index') }}"><i class="fas fa-calendar-check"></i> Bookings &
                            Check-ins</a>
                    </li>
                    <li class="{{ request()->routeIs('incidents.*') ? 'active' : '' }}">
                        <a href="{{ route('incidents.index') }}"><i class="fas fa-exclamation-triangle"></i> Incident
                            Reports</a>
                    </li>
                @endcan

                @can('view-customers')
                    <li class="{{ request()->routeIs('customers.*', 'organizations.*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}"><i class="fas fa-users"></i> Guests / Customers</a>
                    </li>
                @endcan

                @can('view-rooms')
                    <li class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                        <a href="{{ route('rooms.index') }}"><i class="fas fa-bed"></i> Rooms</a>
                    </li>
                @endcan

                @if(auth()->user()->can('view-kitchen-orders') || auth()->user()->can('view-laundry-orders') || auth()->user()->can('view-bar-orders') || auth()->user()->can('update-room-status'))
                    <li class="px-3 pt-3 pb-1 text-uppercase text-muted"
                        style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Services</li>
                @endif

                @can('view-kitchen-orders')
                    <li class="{{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                        <a href="{{ route('kitchen.index') }}"><i class="fas fa-utensils"></i> Kitchen</a>
                    </li>
                @endcan

                @can('view-laundry-orders')
                    <li class="{{ request()->routeIs('laundry.*') ? 'active' : '' }}">
                        <a href="{{ route('laundry.index') }}"><i class="fas fa-tshirt"></i> Laundry</a>
                    </li>
                @endcan

                @can('view-bar-orders')
                    <li>
                        <a href="#barSubmenu" data-bs-toggle="collapse"
                            class="dropdown-toggle {{ request()->is('bar*') ? '' : 'collapsed' }}">
                            <i class="fas fa-glass-martini-alt"></i> Bar
                        </a>
                        <ul class="collapse list-unstyled {{ request()->is('bar*') ? 'show' : '' }}" id="barSubmenu">
                            <li><a href="{{ route('bar.index') }}"
                                    class="{{ request()->routeIs('bar.index') ? 'active' : '' }}">Orders</a></li>
                            @can('manage-bar-items')
                                <li><a href="{{ route('bar-items.index') }}"
                                        class="{{ request()->routeIs('bar-items.*') ? 'active' : '' }}">Inventory</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('update-room-status')
                    <li class="{{ request()->routeIs('housekeeping.*') ? 'active' : '' }}">
                        <a href="{{ route('housekeeping.index') }}"><i class="fas fa-broom"></i> Housekeeping</a>
                    </li>
                @endcan

                @can('view-bills')
                    <li class="px-3 pt-3 pb-1 text-uppercase text-muted"
                        style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Finance</li>
                    <li class="{{ request()->routeIs('bills.*') ? 'active' : '' }}">
                        <a href="{{ route('bills.index') }}"><i class="fas fa-file-invoice-dollar"></i> Bills & Payments</a>
                    </li>
                @endcan

                @can('view-reports')
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('management'))
                        <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <a href="{{ route('reports.daily-sales') }}"><i class="fas fa-chart-bar"></i> Reports</a>
                        </li>
                    @endif
                @endcan

                @if(auth()->user()->can('manage-rooms') || auth()->user()->can('view-audit-logs') || auth()->user()->can('manage-users'))
                    <li class="px-3 pt-3 pb-1 text-uppercase text-muted"
                        style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Admin</li>
                @endif

                @can('manage-rooms')
                    <li class="{{ request()->routeIs('room-types.*') ? 'active' : '' }}">
                        <a href="{{ route('room-types.index') }}"><i class="fas fa-layer-group"></i> Room Types</a>
                    </li>
                @endcan

                @can('view-audit-logs')
                    <li class="{{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                        <a href="{{ route('audit-logs.index') }}"><i class="fas fa-history"></i> Audit Trail</a>
                    </li>
                @endcan

                @can('manage-users')
                    <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}"><i class="fas fa-user-shield"></i> Users & Staff</a>
                    </li>
                @endcan
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Topbar -->
            <div class="top-navbar d-flex justify-content-between">
                <div>
                    <h5 class="mb-0 fw-bold">@yield('page_title', 'Dashboard')</h5>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                        id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff"
                            width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ auth()->user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="main-container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>