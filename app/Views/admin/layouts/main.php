<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeKnob Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1a1a2e;
            --sidebar-hover: #16213e;
            --sidebar-active: #0f3460;
            --green: #27ae60;
            --red: #e74c3c;
            --orange: #f39c12;
            --sidebar-width: 260px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            z-index: 1050;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--green);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .sidebar-brand h5 {
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: -0.3px;
        }

        .sidebar-brand small {
            font-size: 0.7rem;
            opacity: 0.5;
            display: block;
            margin-top: 2px;
        }

        .sidebar-nav {
            padding: 1rem 0;
            list-style: none;
        }

        .sidebar-nav .nav-label {
            padding: 0.5rem 1.25rem 0.4rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.3);
            font-weight: 600;
        }

        .sidebar-nav .nav-item a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1.25rem;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-item a:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav .nav-item a.active {
            background: var(--sidebar-active);
            color: #fff;
            border-left-color: var(--green);
        }

        .sidebar-nav .nav-item a i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Top bar */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 0 1.5rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .topbar .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: #333;
            cursor: pointer;
            padding: 0.25rem;
        }

        .topbar .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar .admin-info .admin-avatar {
            width: 36px;
            height: 36px;
            background: var(--sidebar-bg);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .topbar .admin-info .admin-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #333;
        }

        .topbar .admin-info .admin-role {
            font-size: 0.75rem;
            color: #999;
        }

        /* Content area */
        .content-area {
            padding: 1.5rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }

        .page-header p {
            color: #888;
            font-size: 0.9rem;
            margin: 0.25rem 0 0;
        }

        /* Stat cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e9ecef;
            transition: box-shadow 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #888;
            margin-top: 0.25rem;
        }

        /* Tables */
        .card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: none;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .table > thead > tr > th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            font-weight: 600;
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }

        .table > tbody > tr > td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .table > tbody > tr:hover {
            background: #f8f9fa;
        }

        /* Badges */
        .badge-elder { background: #d4edda; color: #155724; }
        .badge-family { background: #cce5ff; color: #004085; }
        .badge-admin { background: #e2d5f1; color: #4a1a8a; }
        .badge-ok { background: #d4edda; color: #155724; }
        .badge-help { background: #fff3cd; color: #856404; }
        .badge-emergency { background: #f8d7da; color: #721c24; }
        .badge-missed { background: #fff3cd; color: #856404; }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-resolved { background: #e2e3e5; color: #383d41; }
        .badge-inactive { background: #f8d7da; color: #721c24; }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1045;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar .menu-toggle {
                display: block;
            }
        }

        /* Buttons */
        .btn-green {
            background: var(--green);
            color: #fff;
            border: none;
        }

        .btn-green:hover {
            background: #219a52;
            color: #fff;
        }

        /* Utility */
        .text-green { color: var(--green) !important; }
        .text-red { color: var(--red) !important; }
        .text-orange { color: var(--orange) !important; }
        .bg-green-light { background: rgba(39, 174, 96, 0.1); color: var(--green); }
        .bg-red-light { background: rgba(231, 76, 60, 0.1); color: var(--red); }
        .bg-orange-light { background: rgba(243, 156, 18, 0.1); color: var(--orange); }
        .bg-blue-light { background: rgba(52, 152, 219, 0.1); color: #3498db; }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }
    </style>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-heartbeat"></i>
        </div>
        <div>
            <h5>LifeKnob</h5>
            <small>Admin Panel</small>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-label">Main</li>
        <li class="nav-item">
            <a href="/admin/dashboard" class="<?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/users" class="<?= ($activeMenu ?? '') === 'users' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/connections" class="<?= ($activeMenu ?? '') === 'connections' ? 'active' : '' ?>">
                <i class="fas fa-link"></i>
                <span>Connections</span>
            </a>
        </li>

        <li class="nav-label">Monitoring</li>
        <li class="nav-item">
            <a href="/admin/alerts" class="<?= ($activeMenu ?? '') === 'overdue' ? 'active' : '' ?>">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Overdue</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/checkins" class="<?= ($activeMenu ?? '') === 'checkins' ? 'active' : '' ?>">
                <i class="fas fa-clipboard-check"></i>
                <span>Check-ins</span>
            </a>
        </li>

        <li class="nav-label">System</li>
        <li class="nav-item">
            <a href="/admin/languages" class="<?= ($activeMenu ?? '') === 'languages' ? 'active' : '' ?>">
                <i class="fas fa-language"></i>
                <span>Languages</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/legal" class="<?= ($activeMenu ?? '') === 'legal' ? 'active' : '' ?>">
                <i class="fas fa-file-contract"></i>
                <span>Legal Pages</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/settings" class="<?= ($activeMenu ?? '') === 'settings' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item active text-capitalize"><?= $activeMenu ?? 'Dashboard' ?></li>
                </ol>
            </nav>
        </div>

        <div class="admin-info">
            <div class="text-end d-none d-sm-block">
                <div class="admin-name"><?= esc(session()->get('admin_name') ?? 'Administrator') ?></div>
                <div class="admin-role">Administrator</div>
            </div>
            <div class="admin-avatar">
                <?= strtoupper(substr(session()->get('admin_name') ?? 'A', 0, 1)) ?>
            </div>
            <a href="/admin/logout" class="btn btn-sm btn-outline-secondary" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

    <!-- Content -->
    <div class="content-area">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle for mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });

    // Relative time formatting
    function timeAgo(dateString) {
        if (!dateString) return '<span class="text-muted">Never</span>';

        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'Just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
        if (seconds < 604800) return Math.floor(seconds / 86400) + 'd ago';

        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // Apply relative time to all elements with data-time attribute
    document.querySelectorAll('[data-time]').forEach(el => {
        const raw = el.getAttribute('data-time');
        if (raw) {
            el.innerHTML = timeAgo(raw);
            el.title = new Date(raw).toLocaleString();
        }
    });

    // Format absolute dates
    document.querySelectorAll('[data-date]').forEach(el => {
        const raw = el.getAttribute('data-date');
        if (raw) {
            const d = new Date(raw);
            el.textContent = d.toLocaleDateString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });
        }
    });
</script>

<?= $this->renderSection('scripts') ?>

</body>
</html>
