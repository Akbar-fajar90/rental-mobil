<style>
    .top-header {
        height: 70px;
        padding: 0 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eee;
        background: white;
    }
    .header-title {
        font-weight: 800;
        font-size: 1.25rem;
        color: #000;
        margin: 0;
    }
    .header-tools {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .header-icons {
        display: flex;
        gap: 15px;
        color: #6c757d;
        font-size: 1.2rem;
        align-items: center;
    }
    .header-icons a {
        color: #6c757d;
        transition: 0.3s;
    }
    .header-icons a:hover {
        color: #0d6efd;
    }
    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-left: 20px;
        border-left: 1px solid #dee2e6;
        cursor: pointer;
    }
    .user-info {
        text-align: right;
    }
    .user-name {
        font-weight: 700;
        font-size: 0.9rem;
        margin: 0;
        display: block;
        color: #212529;
    }
    .user-role {
        font-size: 0.75rem;
        color: #adb5bd;
        margin: 0;
        display: block;
    }
    .avatar-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    /* Dark mode button */
    .dark-mode-btn {
        background: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: #6c757d;
        transition: 0.3s;
        padding: 0;
    }
    .dark-mode-btn:hover {
        color: #0d6efd;
    }
    
    /* Dropdown user */
    .dropdown-menu-custom {
        position: absolute;
        top: 50px;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        width: 220px;
        display: none;
        z-index: 1000;
    }
    .dropdown-menu-custom.show {
        display: block;
    }
    .dropdown-item-custom {
        padding: 10px 16px;
        display: block;
        text-decoration: none;
        color: #212529;
        transition: 0.3s;
    }
    .dropdown-item-custom:hover {
        background: #f8f9fa;
    }
</style>

<header class="top-header">
    <h1 class="header-title"><?= $page_title ?? 'Dashboard' ?></h1>

    <div class="header-tools">
        <div class="header-icons">
            <!-- Dark Mode Toggle -->
            <button class="dark-mode-btn" id="darkModeHeaderBtn">
                <i class="bi bi-moon-stars" id="darkModeIcon"></i>
            </button>
            <a href="#"><i class="bi bi-bell"></i></a>
            <a href="#"><i class="bi bi-gear"></i></a>
        </div>

        <div class="user-profile" onclick="toggleUserDropdown()">
            <div class="user-info">
                <span class="user-name"><?= session()->get('adminNama') ?? 'Administrator'; ?></span>
                <span class="user-role"><?= session()->get('adminRole') ?? 'Admin'; ?></span>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('adminNama') ?? 'Admin'); ?>&background=0d6efd&color=fff" 
                 alt="Profile" 
                 class="avatar-img">
        </div>
        
        <div id="userDropdown" class="dropdown-menu-custom">
            <a href="#" class="dropdown-item-custom"><i class="bi bi-person me-2"></i> Profil Saya</a>
            <a href="#" class="dropdown-item-custom"><i class="bi bi-shield-lock me-2"></i> Ubah Password</a>
            <hr class="my-1">
            <a href="<?= base_url('admin/logout'); ?>" class="dropdown-item-custom text-danger">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>
    </div>
</header>

<script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }
    
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const userProfile = document.querySelector('.user-profile');
        
        if (dropdown && !dropdown.contains(event.target) && !userProfile.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
    
    // Dark Mode Button di Header
    const darkModeHeaderBtn = document.getElementById('darkModeHeaderBtn');
    const darkModeIcon = document.getElementById('darkModeIcon');
    
    function updateDarkModeIcon() {
        const isDark = document.body.classList.contains('dark-mode');
        if (isDark) {
            darkModeIcon.classList.remove('bi-moon-stars');
            darkModeIcon.classList.add('bi-sun');
        } else {
            darkModeIcon.classList.remove('bi-sun');
            darkModeIcon.classList.add('bi-moon-stars');
        }
    }
    
    if (darkModeHeaderBtn) {
        darkModeHeaderBtn.addEventListener('click', function() {
            const isDark = document.body.classList.contains('dark-mode');
            if (typeof setDarkMode === 'function') {
                setDarkMode(!isDark);
            } else {
                if (!isDark) {
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('dark_mode', 'on');
                } else {
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('dark_mode', 'off');
                }
            }
            updateDarkModeIcon();
        });
    }
    
    updateDarkModeIcon();
</script>