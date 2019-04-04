
<nav class="dashboard_nav">

    <div class="dashboard_nav_admin">
        <article class="main_header_dados_img">
            <img src="imagens/servidores/<?php echo $_SESSION['autUser']['foto']; ?>" />
        </article>
        <article class="main_header_dados_info">
            <h6><a href="admin_aservidores.php?id_servidor=<?php echo $_SESSION['autUser']['id']; ?>" title="Alterar meus dados"><?php echo $_SESSION['autUser']['nome']; ?></a></h6>
            <h4>
                <?php echo $_SESSION['autUser']['siape']; ?> <i class="fa fa-caret-right"></i>
                <?php echo $_SESSION['autUser']['funcao']; ?> <i class="fa fa-caret-right"></i>
                <?php echo $_SESSION['autUser']['setor']; ?>
            </h4>
        </article>
    </div>

    <ul class="nav_menu">
        <li class="nav_menu_li"><a href="painel.php"><i class="fa fa-home"></i> Painel</a></li>
        <?php
            if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2) { 
                $msgHoje = date('Y-m-d');
                $msgCnh = read('servidores', "WHERE os_motorista != 'n' AND cnh_vencimento < '$msgHoje'");
                $countMsgCnh = count($msgCnh);
                $situacao = 'Aguardando...';
                $msgSolicAbertas = read('vt_solicitacoes', "WHERE situacao = '$situacao'");
                $countMsgSolicAbertas = count($msgSolicAbertas);
              
                if($countMsgCnh >= 1 || $countMsgSolicAbertas >= 1):
                    $numMsg = ($countMsgCnh + $countMsgSolicAbertas);
                    if($numMsg >= 1):
                        $cntInbox = "<span class='numMessages'>{$numMsg}</span>";
                    else:
                        $cntInbox = NULL;
                    endif;
                endif;     
        ?>
            <li class="nav_menu_li"><a href="admin_messages.php"><i class="fa fa-envelope"></i> Mensagens <?php echo $cntInbox; ?></a>
        <?php } ?>
        <li class="nav_menu_li"><a href="#"><i class="fa fa-users"></i> Servidores</a>
            <ul class="nav_menu_sub">
                <?php if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2) { ?>
                    <li class="nav_menu_sub_li"><a href="admin_eservidores.php">&raquo; Gerenciar Servidores</a></li>
                    <li class="nav_menu_sub_li"><a href="admin_esetores.php">&raquo; Setores</a></li>
                <?php } ?>
                <?php if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2) { ?>
                    <li class="nav_menu_sub_li"><a href="admin_emotoristas.php">&raquo; Condutores</a></li>
                <?php } ?>
            </ul>
        </li>
        
        <?php if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 3) { ?>
            <li class="nav_menu_li"><a href="#"><i class="fa fa-building"></i> Livro Ponto</a>
                <ul class="nav_menu_sub">
                    <li class="nav_menu_sub_li"><a href="admin_eferiados.php">&raquo; Feriados</a></li>
                    <li class="nav_menu_sub_li"><a href="admin_geraPonto.php">&raquo; Gerar Ponto</a></li>
                </ul>
            </li>
        <?php } ?>
        
        <?php if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2) { ?>
            <li class="nav_menu_li"><a href="#"><i class="fa fa-car"></i> Viaturas</a>
                <ul class="nav_menu_sub">
                    <li class="nav_menu_sub_li"><a href="admin_esolicitacoes.php">&raquo; Solicitações</a></li>
                    <li class="nav_menu_sub_li"><a href="admin_eveiculos.php">&raquo; Veículos</a></li>
                    <li class="nav_menu_sub_li"><a href="admin_emanutencao.php">&raquo; Manutenção</a></li>
                </ul>
            </li>
        <?php } ?>
        <?php if ($_SESSION['autUser']['admin'] == 1) { ?>
            <li class="nav_menu_li"><a href="#"><i class="fa fa-cogs"></i> Configurações</a>
                <ul class="nav_menu_sub">
                    <li class="nav_menu_sub_li"><a href="admin_eadministradores.php">&raquo; Usuários Admin</a></li>
                    <li class="nav_menu_sub_li"><a href="admin_eacessos.php">&raquo; Acessos ao Sistema</a></li>
                </ul>
            </li>
        <?php } ?>
        <li class="nav_menu_li"><a href="../sis_viaturas"><i class="fa fa-reply"></i> Solicitar Viaturas</a></li>
    </ul>
</nav>