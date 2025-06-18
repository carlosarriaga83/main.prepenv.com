
<?php
	
	

  
?>

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
            
            
            <li class="sidebar-menu-group-title">Client</li>
            
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:plane" class="menu-icon"></iconify-icon>
                    <span>Vuelos</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="view">
                        <a href="_VUELOS_list.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Vuelos</a>
                    </li>



                </ul>
            </li>
            
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:teacher" class="menu-icon"></iconify-icon>
                    <span>Cursos</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="view">
                        <a href="_CURSOS_licencias.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Licencias</a>
                    </li>
                    <li class="view">
                        <a href="_CURSOS_resultados.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Resultados</a>
                    </li>


                </ul>
            </li>

					
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="fluent-mdl2:product-variant" class="menu-icon"></iconify-icon>
                    <span>Productos</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="view">
                        <a href="_PRODUCTOS_Ver.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ver productos</a>
                    </li>
                    <li class="add">
                        <a href="_PRODUCTOS_edit.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Nuevo producto</a>
                    </li>

                </ul>
            </li>
										
								
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="ix:customer" class="menu-icon"></iconify-icon>
                    <span>Clientes</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="view">
                        <a href="_EMPRESAS_Ver.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ver empresas (viejo)</a>
                    </li>
                    <li class="view">
                        <a href="_CLIENTS_Ver.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ver clientes</a>
                    </li>
                    <li class="view">
                        <a href="_CLIENTS_edit.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Nuevo cliente</a>
                    </li>


                </ul>
            </li>
											
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="fa6-solid:file-invoice-dollar" class="menu-icon"></iconify-icon>
                    <span>Facturas</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="view">
                        <a href="_FACTURAS_Ver.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ver facturas</a>
                    </li>
                    <li class="view">
                        <a href="_FACTURAS2_DB_new.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Nueva factura</a>
                    </li>


                </ul>
            </li>
			

            
            <li class="sidebar-menu-group-title">Admin</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <i class="ri-user-settings-line text-xl me-6 d-flex w-auto"></i>
                    <span>Usuarios</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="view">
                        <a href="users-list.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List</a>
                    </li>

                    <li class="admin_add">
                        <a href="view-profile.php?id="><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add User</a>
                    </li>

                    <li class="admin_view">
                        <a href="role-access.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Roles & Access</a>
                    </li>

                </ul>
                
                <li class="dropdown">
                    <a href="javascript:void(0)" class="">
                        <iconify-icon icon="mingcute:hand-card-line" class="menu-icon"></iconify-icon>
                        <span>Billing</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="">
                            <a href="memberships.php"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Memberships</a>
                        </li>
                </li>



                </ul>
                
                
            </li>
			
			<li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                    <span>Settings</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="">
                        <a href="_COMPANY_edit.php" class=""><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Company</a>
                    </li>
                    <li class="d-none" >
                        <a href="notification.php"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Notification</a>
                    </li>
                    <li class="d-none">
                        <a href="notification-alert.php"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Notification Alert</a>
                    </li>
                    <li class="d-none">
                        <a href="theme.php"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Theme</a>
                    </li>
                    <li class="d-none">
                        <a href="currencies.php"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Currencies</a>
                    </li>
                    <li class="d-none">
                        <a href="language.php"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Languages</a>
                    </li>
                    <li class="">
                        <a href="payment-gateway.php"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Payment Gateway</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>

