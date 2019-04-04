<main class="main_content container">
    <div class="content-box">
        <section class="menu_nav container" style="margin-left: -18px;">
            <div class="content-box">
                <ul>
                    <li class="titulo"><i class="fa fa-bars"></i> OPÇÕES</li>
                    <li><a href="painel.php"><i class="fa fa-home"></i> Página Inicial</a></li>
                    <li><a href="pre_solicitar_viatura.php"><i class="fa fa-car"></i> Solicitar Viaturas</a></li>
                    <li>
                        <?php
                        $siapeCarona = $_SESSION['autUser']['siape'];
                        $hojeCarona = date('Y-m-d');
                        $readVCaronas = read('vt_solicitacoes', "WHERE siape = '$siapeCarona' AND data_uso >= '$hojeCarona' AND caronas >= 1 AND (situacao = 'Aguardando...' OR situacao = 'Autorizada')");
                        $countVCaronas = count($readVCaronas);
                        if ($countVCaronas >= 1) {
                            ?>	
                            <a href="minhas_solicitacoes.php">
                                <i class="fa fa-folder"></i> Minhas Solicitações &nbsp;&nbsp;&nbsp;
                                <i title="Você tem pedidos de carona" class="fa fa-exclamation-circle" style="color: #F90; font-size: 1.4em;"></i>
                            </a>
                            <?php
                        } else {
                            ?>
                            <a href="minhas_solicitacoes.php"><i class="fa fa-folder"></i> Minhas Solicitações</a>
                            <?php
                        }
                        ?>
                    </li>
                    <li><a href="pedidos_carona.php"><i class="fa fa-tags"></i> Meus Pedidos de Carona</a></li>
                    <li><a href="solicitacoes_all.php"><i class="fa fa-calendar"></i> Solicitações Agendadas</a></li>
                    <?php
                    $siapeAberta = $_SESSION['autUser']['siape'];
                    $hojeAberta = date('Y-m-d');
                    $readAbertas = read('vt_solicitacoes', "WHERE siape = '$siapeAberta' AND situacao = 'Autorizada' AND prev_retorno_data < '$hojeAberta'");
                    $readCount = count($readAbertas);
                    if ($readCount >= 1) {
                        ?>
                        <li class="encerra">
                            <a href="encerrar_solicitacoes.php"><i class="fa fa-refresh"></i> Encerrar Solicitações <?php echo '[&nbsp;' . $readCount . '&nbsp;]'; ?></a>
                        </li>
                        <?php
                    }
                    ?> 
                </ul>  
                <ul>
                    <li class="rodape"></li>
                </ul>
            </div>        
        </section>  

        <section class="conteudo_dados container" style="margin-top: 18px;">          