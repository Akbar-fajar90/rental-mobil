<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> - Rental Mobil</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            transition: background-color 0.3s, color 0.3s;
        }
        
        /* ========== DARK MODE STYLES ========== */
        body.dark-mode {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }
        
        body.dark-mode .sidebar {
            background-color: #1e293b !important;
            border-right-color: #334155 !important;
        }
        
        body.dark-mode .sidebar .nav-link {
            color: #94a3b8 !important;
        }
        
        body.dark-mode .sidebar .nav-link:hover {
            background-color: #334155 !important;
            color: #ffffff !important;
        }
        
        body.dark-mode .sidebar .nav-link.active {
            background-color: #2563eb !important;
            color: white !important;
        }
        
        body.dark-mode .top-header {
            background-color: #1e293b !important;
            border-bottom-color: #334155 !important;
        }
        
        body.dark-mode .top-header .header-title {
            color: #ffffff !important;
        }
        
        body.dark-mode .card,
        body.dark-mode .card-custom,
        body.dark-mode .stat-card,
        body.dark-mode .section-card,
        body.dark-mode .table-container,
        body.dark-mode .booking-card {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        
        body.dark-mode .table thead th,
        body.dark-mode .table-custom thead th,
        body.dark-mode .table-history thead th {
            border-bottom-color: #334155 !important;
            color: #94a3b8 !important;
        }
        
        body.dark-mode .table tbody td,
        body.dark-mode .table-custom tbody td,
        body.dark-mode .table-history tbody td {
            border-color: #334155 !important;
            color: #cbd5e1 !important;
        }
        
        body.dark-mode .bg-light,
        body.dark-mode .bg-light.bg-opacity-10,
        body.dark-mode .info-box,
        body.dark-mode .financial-card,
        body.dark-mode .checklist-card {
            background-color: #0f172a !important;
        }
        
        body.dark-mode .text-muted {
            color: #94a3b8 !important;
        }
        
        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode .search-box {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        
        body.dark-mode .form-control:focus,
        body.dark-mode .form-select:focus {
            border-color: #2563eb !important;
            background-color: #0f172a !important;
        }
        
        body.dark-mode .btn-outline-secondary,
        body.dark-mode .btn-outline-primary,
        body.dark-mode .btn-outline-danger {
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }
        
        body.dark-mode .btn-outline-secondary:hover,
        body.dark-mode .btn-outline-primary:hover {
            background-color: #2563eb !important;
            color: white !important;
        }
        
        body.dark-mode .dropdown-menu {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        
        body.dark-mode .dropdown-item {
            color: #e2e8f0 !important;
        }
        
        body.dark-mode .dropdown-item:hover {
            background-color: #334155 !important;
        }
        
        body.dark-mode .modal-content {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        
        body.dark-mode .modal-header,
        body.dark-mode .modal-footer {
            border-color: #334155 !important;
        }
        
        body.dark-mode .alert-info {
            background-color: #1e3a5f !important;
            border-color: #2563eb !important;
            color: #e2e8f0 !important;
        }
        
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: auto;
        }
        
        .page-container {
            padding: 24px 30px;
            flex: 1;
        }
        
        /* Switch toggle untuk dark mode */
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .theme-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }
        
        .theme-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        
        input:checked + .theme-slider {
            background-color: #2563eb;
        }
        
        input:checked + .theme-slider:before {
            transform: translateX(26px);
        }
        
        .logo-img {
            transition: filter 0.3s;
        }
        
        body.dark-mode .logo-img {
            filter: brightness(0) invert(1);
        }
        /* ========== PAGINATION STYLES ========== */
        .pagination {
            gap: 5px;
        }
        
        .page-link {
            border: none;
            color: var(--text-gray, #5f6368);
            border-radius: 8px !important;
            padding: 8px 16px;
            font-weight: 500;
            transition: 0.3s;
        }
        
        .page-link:hover {
            background-color: #f1f3f4;
            color: #1a73e8;
        }
        
        .page-item.active .page-link {
            background-color: #1a73e8 !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(26, 115, 232, 0.2);
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar (navbar.php) -->
        <?= view('layout/navbar'); ?>
        
        <div class="main-content">
            <!-- Header (header.php) -->
            <?= view('layout/header'); ?>
            
            <!-- Konten Utama -->
            <div class="page-container">
                <?= $this->renderSection('content'); ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Dark Mode Functions
        function setDarkMode(enabled) {
            if (enabled) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('dark_mode', 'on');
                // Update checkbox di pengaturan jika ada
                const darkModeCheckbox = document.getElementById('darkModeToggle');
                if (darkModeCheckbox) darkModeCheckbox.checked = true;
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('dark_mode', 'off');
                const darkModeCheckbox = document.getElementById('darkModeToggle');
                if (darkModeCheckbox) darkModeCheckbox.checked = false;
            }
        }
        
        // Load dark mode preference on page load
        const savedDarkMode = localStorage.getItem('dark_mode');
        if (savedDarkMode === 'on') {
            setDarkMode(true);
        }
        
        // Function to sync with settings form
        window.syncDarkModeFromSettings = function(isChecked) {
            setDarkMode(isChecked);
        };
    </script>
</body>
</html>