<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="index.php" class="sidebar-logo">
            <img src="assets/images/logo.png" alt="site logo" class="light-logo">
            <img src="assets/images/logo-light.png" alt="site logo" class="dark-logo">
            <img src="assets/images/logo-icon.png" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            
            <li>
                <a href="index.php">
                    <iconify-icon icon="cuida:home-outline" class="menu-icon"></iconify-icon>
                    <span>Home</span>
                </a>
            </li>
            
            
            <li class="sidebar-menu-group-title">Admin</li>
            
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="ic:twotone-event" class="menu-icon"></iconify-icon>
                    <span>Eventos</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="Ver_eventos.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ver eventos</a>
                    </li>
                    <li>
                        <a href="Nuevo_evento.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Nuevo evento</a>
                    </li>

                </ul>
            </li>
					
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mingcute:dish-cover-line" class="menu-icon"></iconify-icon>
                    <span>Platillos</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="Ver_platillo.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ver platillos</a>
                    </li>
                    <li>
                        <a href="Nuevo_platillo.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Nuevo platillo</a>
                    </li>

                </ul>
            </li>
								
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <i class="ri-user-settings-line text-xl me-6 d-flex w-auto"></i>
                    <span>Usuarios</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="users-list.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List</a>
                    </li>
                    <li>
                        <a href="users-grid.php"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Users Grid</a>
                    </li>
                    <li>
                        <a href="add-user.php"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add User</a>
                    </li>
                    <li>
                        <a href="view-profile.php"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> View Profile</a>
                    </li>
                    <li>
                        <a href="role-access.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Role & Access</a>
                    </li>
                    <li>
                        <a href="assign-role.php"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Assign Role</a>
                    </li>
                </ul>
            </li>
			

            <li class="sidebar-menu-group-title">Externo</li>


            <li>
                <a href="gallery.php">
                    <iconify-icon icon="lsicon:user-crowd-outline" class="menu-icon"></iconify-icon>
                    <span>Comensales</span>
                </a>
            </li>
            
        </ul>
    </div>
</aside>