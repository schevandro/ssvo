<?php include_once "includes/inc_header.php"; ?> 
<?php include_once "includes/inc_menu.php"; ?>

<!--Conteudo das páginas -->
<h1 class="titulo-secao-medio"><i class="fa fa-street-view"></i> Minhas solicitações de viatura</h1> 

<?php
//PAGINAÇÃO
$pag = "$_GET[pag]";
if ($pag >= '1') {
    $pag = $pag;
} else {
    $pag = '1';
}
$maximo = '10'; //RESULTADOS POR PÁGINA
$inicio = ($pag * $maximo) - $maximo;

$hoje = date('Y-m-d');
$siape_login = $_SESSION['autUser']['siape'];
$status_solicitacao = array('Aguardando...', 'Autorizada', 'Nao Autorizada', 'Cancelada', 'Encerrada');

if (empty($_GET['solit'])) {
    $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND situacao != '') ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
    $todas = count($readSolicitacoes);
    $bgTodas = 'style="background-color:#71ca73;"';
} elseif (isset($_GET['solit']) && $_GET['solit'] == 'autorizado') {
    $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[1]') ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
    $autorizadas = count($readSolicitacoes);
    $bgAutorizadas = 'style="background-color:#71ca73;"';
} elseif (isset($_GET['solit']) && $_GET['solit'] == 'negado') {
    $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[2]') ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
    $negadas = count($readSolicitacoes);
    $bgNegadas = 'style="background-color:#71ca73;"';
} elseif (isset($_GET['solit']) && $_GET['solit'] == 'cancelado') {
    $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[3]') ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
    $canceladas = count($readSolicitacoes);
    $bgCanceladas = 'style="background-color:#71ca73;"';
} elseif (isset($_GET['solit']) && $_GET['solit'] == 'encerrado') {
    $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[4]') ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
    $encerradas = count($readSolicitacoes);
    $bgEncerradas = 'style="background-color:#71ca73;"';
} elseif (isset($_GET['solit']) && $_GET['solit'] == 'aberto') {
    $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[0]') ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
    $abertas = count($readSolicitacoes);
    $bgAbertas = 'style="background-color:#71ca73;"';
}

//Cancelar uma solicitação
if (isset($_GET['id_cancela'])) {
    $del_id = $_GET['id_cancela'];
    $sit_cancelada = 'Cancelada';
    $titular_siape = $_SESSION['autUser']['siape'];
    $up_dadosCancela = array('situacao' => $sit_cancelada);
    $up_cancela = update('vt_solicitacoes', $up_dadosCancela, "(id = '$del_id' AND siape = '$titular_siape')");
    if ($up_cancela) {
        $readSolitCancelada = read('vt_solicitacoes', "WHERE id = '$del_id'");
        $countReadSolitCancelada = count($readSolitCancelada);
        if ($readSolitCancelada >= 1) {
            foreach ($readSolitCancelada as $cancel)
                ;
        }
        //Envia Email informando o Admin do Cancelamento
        $msg = '		
            <p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá Admin,</p>
            <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que o servidor ' . $_SESSION['autUser']['nome'] . ',<strong style="color:#F00"> CANCELOU</strong> a solicitação de viatura ofical nº ' . $del_id . ', solicitada em ' . date('d/m/Y', strtotime($cancel['criadoEm'])) . ', com destino para ' . $cancel['roteiro'] . '.<br /><br />
            Abaixo seguem os dados da solicitação cancelada:</p>
            <hr />		
            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $cancel['roteiro'] . '</span><br />
            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($cancel['data_uso'])) . ' às ' . date('H:i', strtotime($cancel['horario_uso'])) . '</span><br />
            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $cancel['finalidade'] . ' / ' . $cancel['desc_finalidade'] . '</span><br />
            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($cancel['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($cancel['prev_retorno_hora'])) . '</span><br /><br />

            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas que haviam sido confirmadas por ' . $_SESSION['autUser']['nome'] . ':</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . ($cancel['passageiros'] != '' ? $caronas = $cancel['passageiros'] : $caronas = 'Nenhum caroneiro') . '<br /><br />
            <span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">GUIA cancelada em <strong>' . date('d/m/Y') . '</strong> às <strong>' . date('H:i') . '</strong></span><br />
            <hr />
            <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Não é necessário nenhuma atitude, o sistema automaticamente cancelará a GUIA e infomará os caroneiros (se houver) que a GUIA foi cancelada.<br />
            </p>
            <img scr="http://200.17.85.251/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';
        sendMail('Cancelamento de GUIA para ' . $cancel['roteiro'] . '', $msg, MAILUSER, SITENAME, MAILADMIN, $_SESSION['autUser']['nome']);

        //Envia Email para os caroneiros (se houverem)
        if ($cancel['passageiros'] != ''):
            $caroneiros = explode(',', $cancel['passageiros']);
            foreach ($caroneiros as $carnome) {
                $readmail = read('servidores', "WHERE nome = '$carnome'");
                foreach ($readmail as $carmail)
                    ;
                $primeiroNome = explode(' ', $carnome);
                $msg = '
                    <p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá ' . $primeiroNome[0] . ',</p>
                    <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que o servidor ' . $_SESSION['autUser']['nome'] . ',<strong style="color:#F00"> CANCELOU</strong> a solicitação de viatura ofical nº ' . $del_id . ', solicitada em ' . date('d/m/Y', strtotime($cancel['criadoEm'])) . ', com destino para ' . $cancel['roteiro'] . '. A qual você constava como CARONEIRO.<br /><br />
                    Abaixo seguem os dados da solicitação cancelada:</p>
                    <hr />		
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $cancel['roteiro'] . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($cancel['data_uso'])) . ' às ' . date('H:i', strtotime($cancel['horario_uso'])) . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $cancel['finalidade'] . ' / ' . $cancel['desc_finalidade'] . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($cancel['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($cancel['prev_retorno_hora'])) . '</span><br /><br />

                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas que haviam sido confirmadas por ' . $_SESSION['autUser']['nome'] . ':</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . ($cancel['passageiros'] != '' ? $caronas = $cancel['passageiros'] : $caronas = 'Nenhum caroneiro') . '<br /><br />
                    <span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">GUIA cancelada em <strong>' . date('d/m/Y') . '</strong> às <strong>' . date('H:i') . '</strong></span><br />
                    <hr />
                    <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Para maiores informações, entre em contato com o titular da solicitação cancelada.<br />
                    </p>
                    <img scr="http://200.17.85.251/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';
                sendMail('Cancelamento de GUIA para ' . $cancel['roteiro'] . '', $msg, MAILUSER, SITENAME, $carmail['email'], $_SESSION['autUser']['nome']);
            }
        endif;
        echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação Cancelada!</h4>";
        header("Refresh:2; url=minhas_solicitacoes.php");
    }
}
?>

<div class="submenu">
    <a href="minhas_solicitacoes.php?solit=aberto"><h5 <?php echo $bgAbertas; ?>>Aguardando <?php if (!empty($abertas)) echo '<strong>[' . $abertas . ']</strong>'; ?></h5></a>
    <a href="minhas_solicitacoes.php?solit=autorizado"><h5 <?php echo $bgAutorizadas; ?>>Autorizadas <?php if (!empty($autorizadas)) echo '<strong>[' . $autorizadas . ']</strong>'; ?></h5></a>
    <a href="minhas_solicitacoes.php?solit=encerrado"><h5 <?php echo $bgEncerradas; ?>>Encerradas <?php if (!empty($encerradas)) echo '<strong>[' . $encerradas . ']</strong>'; ?></h5></a>
    <a href="minhas_solicitacoes.php?solit=cancelado"><h5 <?php echo $bgCanceladas; ?>>Canceladas <?php if (!empty($canceladas)) echo '<strong>[' . $canceladas . ']</strong>'; ?></h5></a>
    <a href="minhas_solicitacoes.php?solit=negado"><h5 <?php echo $bgNegadas; ?>>Negadas <?php if (!empty($negadas)) echo '<strong>[' . $negadas . ']</strong>'; ?></h5></a>
    <a href="minhas_solicitacoes.php"><h5 <?php echo $bgTodas; ?>>Todas <?php if (!empty($todas)) echo '<strong>[' . $todas . ']</strong>'; ?></h5></a>
</div><!--fecha div class fotos-->

<?php
    include_once("codes/aceita_carona.php");
?>

<?php
if ($readSolicitacoes <= 0) {
    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle'></i>&nbsp&nbsp&nbsp Não há solicitações com esta descrição!</h4>";
} else {
    ?>    

    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        <tr class="tr_header">
            <td align="center">ID</td>
            <td align="center">Em</td>
            <td align="center">Doc</td>
            <td align="left" style="padding-left:15px">Solicitante</td>
            <td align="center">Destino</td>
            <td align="center">Data Uso</td>
            <td align="center" colspan="3">Situação</td>
            <td align="center">Veículo</td>
            <td align="center" colspan="3">Ações</td>
        </tr>
    <?php
    foreach ($readSolicitacoes as $readSol) {
        $colorPage++;
        if ($colorPage % 2 == 0) {
            $cor = 'style="background:#f3f3f3;"';
        } else {
            $cor = 'style="background:#fff;"';
        }
        ?>
            <tr <?php echo $cor; ?> class="lista_itens">
                <td align="center" style="font-size: 0.8em; font-weight: 600; color: #32A041;"><?php echo $readSol['id']; ?></td>
                <td align="center"><?php echo date('d/m/y', strtotime($readSol['criadoEm'])); ?></td>
                <td align="center">
                    <?php
                        if($readSol['comprovante'] != ''):
                            echo "<a href='../admin/documentos/comprovantes_guia/{$readSol['comprovante']}' target='_blank' title='Visualizar o comprovante' style='color: #09F;'><i class='fa fa-file-pdf-o'></i></a>";  
                        else:
                            echo "<i class='fa fa-file-o' style='color: #666;' title='Não há comprovante para esta viagem'></i>";  
                        endif;
                    ?>
                </td>
                <td align="left" style="padding-left:15px">
                <?php
                $abrServidor = explode(' ', $readSol['servidor']);
                $escreveAbr = $abrServidor[0] . ' ' . $abrServidor[1] . ' ' . $abrServidor[2];
                if ($readSol['passageiros'] != "") {
                    $separa_passageiros = explode(",", $readSol['passageiros']);
                    $conta_passageiros = count($separa_passageiros);
                    echo $escreveAbr . ' [+' . $conta_passageiros . ']';
                } else {
                    echo $escreveAbr;
                }
                ?>
                </td>
                
                <td align="center">
                    <?php
                    if ($readSol['roteiro_3'] != '') {
                        echo $readSol['roteiro'] . '<br /><span style="font-size:10px; color:#000; width:100%"> (' . $readSol['roteiro_2'] . ', ' . $readSol['roteiro_3'] . ')</span>';
                    } elseif ($readSol['roteiro_2'] != '' && $readSol['roteiro_3'] == '') {
                        echo $readSol['roteiro'] . '<br /><span style="font-size:10px; color:#000; width:100%"> (' . $readSol['roteiro_2'] . ')</span>';
                    } else {
                        echo $readSol['roteiro'];
                    }
                    ?>
                </td>
                                
                <td align="center"><?php echo date('d/m/y', strtotime($readSol['data_uso'])) . ' às ' . date('H:i', strtotime($readSol['horario_uso'])).'hs'; ?></td>
                    <?php
                    $diaHoje = date('Y-m-d');
                    if ($readSol['situacao'] == "Aguardando...") {
                        echo '<td align="center" colspan="3"><i class="fa fa-exclamation-triangle fa-2x" style="color: #FFBA75;" title="Aguardando" alt="Aguardando"></i></td>';
                    } elseif ($readSol['situacao'] == "Autorizada") {
                        echo '<td align="center" colspan="3"><i class="fa fa-check-square-o fa-2x" style="color: green;" title="Autorizada"></i></td>';
                    } elseif ($readSol['situacao'] == "Encerrada") {
                        echo '<td align="center" colspan="3"><img src="../_assets/img/ico_encerrada.png" width="75px" title="Encerrada" alt="Encerrada"></td>';
                    } elseif ($readSol['situacao'] == "Cancelada") {
                        echo '<td align="center" colspan="3"><img src="../_assets/img/ico_cancelada.png" width="75px" title="Encerrada" alt="Encerrada"></td>';
                    } else {
                        echo '<td align="center" colspan="3"><i class="fa fa-close fa-2x" style="color: #FF5959;" title="Não Autorizada"></i></td>';
                    }
                    ?>  
                
                <td align="center" width="130px" style="font:10px">
                <?php
                if ($readSol['situacao'] == 'Nao Autorizada' || $readSol['situacao'] == 'Cancelada') {
                    echo ' - ';
                } elseif ($readSol['situacao'] == 'Autorizada' or $readSol['situacao'] == 'Encerrada') {
                    echo $readSol['veiculo'];
                } else {
                    echo '<i class="fa fa-exclamation-triangle fa-2x" style="color: #FFBA75;" title="Aguardando" alt="Aguardando"></i>';
                }
                ?>  
                </td> 
                
                
                <?php
                    if ($readSol['situacao'] != 'Cancelada' && $readSol['situacao'] != 'Nao Autorizada' && $readSol['situacao'] != 'Encerrada') {
                        ?>
                    <td align="center" width="30px">
                    <?php
                } else {
                    ?>
                    <td align="center" colspan="3">
                        <?php
                    }
                    ?>
                        <a href="detalha_solicitacao.php?id_solicitacao=<?php echo $readSol['id']; ?>" title="Visualizar detalhes da solicitação"><i class="fa fa-search" style="color: #555; margin: 0 5px; font-size: 1.4em;"></i></a>
                </td>    

                <?php
                if ($readSol['situacao'] == 'Aguardando...') {
                    echo '<td align="center" width="35px"><a href="editar_caronas.php?id_solicitacao=' . $readSol['id'] . '" title="Editar passageiros"><i class="fa fa-user-plus fa-2x" style="color: #555; font-size: 1.4em;"></i></a></td>';
                    echo '<td align="center" width="30px"><a href="minhas_solicitacoes.php?id_cancela=' . $readSol['id'] . '&amp;siape=' . $readSol['siape'] . '" title="Cancelar Solicitação"><i class="fa fa-trash" style="color: #555; font-size: 1.4em;"></i></a></td>';
                } elseif ($readSol['situacao'] == "Autorizada" && $readSol['data_uso'] > $diaHoje) {
                    echo '<td align="center" width="35px"><a href="editar_caronas.php?id_solicitacao=' . $readSol['id'] . '" title="Editar passageiros"><i class="fa fa-user-plus fa-2x" style="color: #555; font-size: 1.4em;"></i></a></td>';
                    echo '<td align="center" width="30px"><a href="minhas_solicitacoes.php?id_cancela=' . $readSol['id'] . '&amp;siape=' . $readSol['siape'] . '" title="Cancelar Solicitação"><i class="fa fa-trash" style="color: #555; font-size: 1.4em;"></i></a></td>';
                } elseif ($readSol['situacao'] == "Autorizada" && $readSol['data_uso'] <= $diaHoje) {
                    echo '<td align="center" width="30px"><a href="encerrar_solicitacoes.php" title="Encerrar solicitação"><i class="fa fa-share-square" style="color: #09F; font-size: 1.4em;"></i></td>';
                }
                ?>
            </tr>            
            
                <?php
                if ($readSol['caronas'] >= 1 AND ($readSol['situacao'] == 'Aguardando...' OR $readSol['situacao'] == 'Autorizada')) {
                    $guiaCarona = $readSol['id'];
                    $situacao_carona = 0;
                    $readCarona = read('vt_caronas', "WHERE (guia_carona = '$guiaCarona' AND situacao = '$situacao_carona')");
                    ?>
                <tr style="background-color: #FFEAD5; font-size: 0.875em;" height="50px">
                    <td colspan="11" style="padding-left:65px; color:#333;"><i class="fa fa-tags"></i> <strong>Pedidos de Carona</strong> <i class="fa fa-caret-right"></i> Guia nº <?= $readSol['id']; ?> <i class="fa fa-level-up"></i></td>
                <?php
                $i = count($readCarona); //Para contar as divs de caronas
                if (!$i <= 0) {
                    foreach ($readCarona as $car) {
                        ?>
                        <tr style="background-color: #FFEAD5; border-bottom:1px solid #000; font-size: 0.875em;" height="50px">
                            <td></td>
                            <td style="padding-left:75px; color:#666;"><i class="fa fa-user"></i> <?php echo $car['servidor']; ?></td>
                            <td style="padding-left:5px; color:#666; text-align: center;">solicitada em <?php echo date('d/m/Y à\s H:i', strtotime($car['solicitadaEm'])); ?></td>
                            <td style="padding-left:5px; text-align: center;">
                                <a href="minhas_solicitacoes.php?id_guia=<?php echo $car['guia_carona']; ?>&id_carona=<?php echo $car['id']; ?>&email_carona=<?php echo $car['email']; ?>" title="Aceitar o pedido de carona"><i class="fa fa-check-circle fa-2x" style="color: #32A041;"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="minhas_solicitacoes.php?id_negou=<?php echo $car['guia_carona']; ?>&id_carona=<?php echo $car['id']; ?>&email_carona=<?php echo $car['email']; ?>" title="NÃO Aceitar o pedido de carona"><i class="fa fa-times-circle fa-2x" style="color:#FF5959;"></i></a>
                            </td>
                            <td colspan="7"></td>                            
                        </tr>          
                    <?php
                }
            }
        }
    }
    ?>
    </table>

        <?php
    }
    ?>

<?php if ($readSolicitacoes >= 1) { ?> <div id="paginator">
    <?php
    //PAGINAÇÃO
    $total = $num;

    $paginas = ceil($total / $maximo);
    $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

    echo "<a href=minhas_solicitacoes.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

    for ($i = $pag - $links; $i <= $pag - 1; $i++) {
        if ($i <= 0) {
            
        } else {
            echo"<a href=minhas_solicitacoes.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
        }
    }echo "<h1>$pag</h1>";

    for ($i = $pag + 1; $i <= $pag + $links; $i++) {
        if ($i > $paginas) {
            
        } else {
            echo "<a href=minhas_solicitacoes.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
        }
    }
    echo "<a href=minhas_solicitacoes.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";
    ?>
    </div> <?php } ?>


<!--Encerra conteúdo das páginas-->
<?php include_once "includes/inc_footer.php"; ?>