 <?php
 // Consulta para contar clientes com previsao_entrega vencida
$sql_previsao_vencida = "SELECT COUNT(*) AS total_vencidos FROM propostas 

WHERE id_vendedor = ? and not id_etapa in  (5,6) AND previsao_entrega < NOW()";
$stmt_previsao_vencida = $conn->prepare($sql_previsao_vencida);
$stmt_previsao_vencida->bind_param("i", $idfuncionario);
$stmt_previsao_vencida->execute();
$result_previsao_vencida = $stmt_previsao_vencida->get_result();
$total_vencidos = $result_previsao_vencida->fetch_assoc()['total_vencidos'];
?>
<!-- Navbar -->
 <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-bell"></i>
                        <?php if ($total_vencidos > 0): ?>
                            <span class="badge badge-danger navbar-badge"><?php echo $total_vencidos; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header"><?php echo $total_vencidos; ?> Previsões Vencidas</span>
                        <div class="dropdown-divider"></div>
                        <a href="notificacao_data_previsao.php" class="dropdown-item">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Previsão Pendente
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->