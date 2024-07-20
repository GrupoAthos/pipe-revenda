
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
        <span class="brand-text font-weight-light"><b>Pipe</b> de Vendas</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block"><?php echo strtoupper($_SESSION['nomecompleto'] ?? ''); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="cliente.php" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Cliente</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="cadastrar_cliente.php" class="nav-link">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>Cadastrar Cliente</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="adicionar_proposta.php" class="nav-link">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>Adicionar Proposta</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link sair">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
