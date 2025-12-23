<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* GLOBAL RESET & VARIABLES */
    :root {
        --font-main: 'Outfit', sans-serif;
        --bg-body: #0f172a; /* Slate 900 */
        --bg-panel: #1e293b; /* Slate 800 */
        --bg-card: #334155; /* Slate 700 */
        --text-main: #ffffff; /* Pure White */
        --text-muted: #cbd5e1; /* Slate 300 - Much brighter than before */
        
        --primary: #6366f1; /* Indigo 500 */
        --primary-glow: rgba(99, 102, 241, 0.5);
        --secondary: #06b6d4; /* Cyan 500 */
        
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        
        --glass: rgba(30, 41, 59, 0.7);
        --glass-border: rgba(255, 255, 255, 0.1);
        
        --radius: 16px;
        --radius-sm: 8px;

        --glass-bg: rgba(255, 255, 255, 0.08);
        --glass-bd: rgba(255, 255, 255, 0.15);
    }

    body {
        font-family: var(--font-main);
        background-color: var(--bg-body);
        color: var(--text-main);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
        background-image: 
            radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.08), transparent 25%),
            radial-gradient(circle at 85% 30%, rgba(6, 182, 212, 0.08), transparent 25%);
        background-attachment: fixed;
    }

    /* SCROLLBAR */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: var(--bg-body); }
    ::-webkit-scrollbar-thumb { background: var(--bg-card); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

    /* TYPOGRAPHY */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        letter-spacing: -0.02em;
        color: #fff;
    }
    .text-muted { color: var(--text-muted) !important; }
    .text-white { color: #fff !important; }

    /* CARDS & GLASS */
    .card, .stat-card, .search-card, .bg-dark {
        background: var(--bg-panel) !important;
        border: 1px solid var(--glass-border) !important;
        border-radius: var(--radius) !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        backdrop-filter: blur(12px);
    }
    .card-glass { 
        background: var(--glass-bg); 
        backdrop-filter: blur(16px); 
        border: 1px solid var(--glass-bd); 
        border-radius: 24px; 
        transition: all 0.3s ease; 
    }
    .card-glass:hover { 
        border-color: rgba(255, 255, 255, 0.25); 
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.1); 
    }
    .hover-lift:hover { transform: translateY(-6px); box-shadow: 0 20px 44px rgba(0,0,0,0.45) !important; }
    
    .card-body { padding: 1.5rem; }
    
    /* MODALS - Fix for Dark Theme */
    .modal-content.card-glass {
        background: rgba(15, 23, 42, 0.95) !important;
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-bd) !important;
        color: #fff !important;
    }
    .modal-header, .modal-footer { border-color: rgba(255, 255, 255, 0.1) !important; }
    .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
    
    /* TABLES */
    .table {
        color: var(--text-main) !important;
        border-color: var(--glass-border) !important;
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        background: rgba(15, 23, 42, 0.5) !important;
        border-bottom: 0 !important;
        padding-top: 1rem; padding-bottom: 1rem;
    }
    .table td {
        vertical-align: middle;
        background: transparent !important;
        border-bottom: 1px solid var(--glass-border);
        padding-top: 1rem; padding-bottom: 1rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }

    /* BUTTONS */
    .btn {
        border-radius: var(--radius-sm);
        padding: 0.6rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-primary {
        background: linear-gradient(135deg, var(--primary), #4f46e5);
        color: white;
        box-shadow: 0 4px 12px var(--primary-glow);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px var(--primary-glow);
        background: linear-gradient(135deg, #4f46e5, var(--primary));
    }
    .btn-outline-primary {
        border: 1px solid var(--primary);
        color: var(--primary);
        background: transparent;
    }
    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
    }
    .btn-success { background: var(--success); }
    .btn-danger { background: var(--danger); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); }
    .btn-warning { background: var(--warning); color: #fff; }
    .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.875rem; }

    /* FORMS */
    .form-control, .form-select {
        background-color: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid var(--glass-border) !important;
        color: var(--text-main) !important;
        border-radius: var(--radius-sm);
        padding: 0.75rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        background-color: rgba(15, 23, 42, 0.8) !important;
    }
    ::placeholder { color: rgba(148, 163, 184, 0.5) !important; }

    /* BADGES */
    .badge {
        font-weight: 500;
        letter-spacing: 0.02em;
        padding: 0.5em 0.8em;
        border-radius: 6px;
    }
    .badge-active { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .badge-suspended { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
    .badge-blocked { background: rgba(239, 68, 68, 0.2); color: #f87171; }

    /* NAVBAR */
    .navbar {
        background: rgba(15, 23, 42, 0.85) !important;
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--glass-border);
    }
    .navbar-brand { color: #fff !important; letter-spacing: -0.5px; font-size: 1.5rem; }
    .nav-link { color: var(--text-muted) !important; transition: color 0.2s; }
    .nav-link:hover, .nav-link.active { color: #fff !important; }

    /* ANIMATIONS */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .container { animation: fadeIn 0.4s ease-out; }
</style>
