<?php
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";
?>

<!--Conteudo das páginas -->
<h1 class="titulo-secao-medio"><i class="fa fa-file-text-o"></i> Detalhes da solicitação</h1>     

<?php
$solicitacaoId = $_GET['id_solicitacao'];
$readBusca = read('vt_solicitacoes', "WHERE id = '$solicitacaoId'");
$countReadBusca = count($readBusca);

foreach ($readBusca as $busca);
?>

<form name="detalha_solicitacao" method="post" action="" enctype="multipart/form-data">
    <div class="detalhesGuia">
        <h1><i class="fa fa-tag"></i> Solicitação <strong>Nº <?php echo $busca['id']; ?> </strong></h1>
        <?php echo '<h2><i class="fa fa-clock-o"></i> solicitada em ' . date('d/m/Y', strtotime($busca['criadoEm'])) . ' às ' . date('H:i', strtotime($busca['criadoEm'])) .'</h2>'; ?>
                    
        <!-- mostra a situação da GUIA-->
        <?php
            $dia_hoje = date('Y-m-d');
            if ($busca['situacao'] == "Autorizada" and $busca['data_uso'] < $dia_hoje) {
                if ($busca['veiculo'] != "n/d") {
                    echo "<div class='ms al' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-share-square-o fa-2x' style='color: #F60;'></i>&nbsp;&nbsp;&nbsp; Esta solicitação esta aguardando encerramento no sistema</div><br />";
                }
            } elseif ($busca['situacao'] == "Aguardando...") {
        ?>

        <div class="ms al" style="margin: 15px 0 10px 0; paddind-left: 0;"><i class="fa fa-exclamation-triangle fa-2x" style="color: #F93;"></i>&nbsp;&nbsp;&nbsp; Aguardando liberação</div><br />

        <?php
        } elseif ($busca['situacao'] == "Encerrada") {
        ?>

        <div class="ms blue" style="margin: 15px 0 0 0; paddind-left: 0;"><i class="fa fa-share-square-o fa-2x" style="color: #FFF;"></i>&nbsp;&nbsp;&nbsp; Encerrada</div><br />                
        <div class="detalhesEncerrada">
            <h4><i class="fa fa-arrow-right"></i> <strong>Data Chegada:</strong> <?php echo date('d/m/Y', strtotime($busca['data_chegada'])) . ' às ' . date('H:i', strtotime($busca['hora_chegada'])); ?></h4>
            <h4><i class="fa fa-arrow-right"></i> <strong>KM de Saída:</strong> <?php echo $busca['km_saida']; ?></h4>
            <h4><i class="fa fa-arrow-right"></i> <strong>KM de Chegada:</strong> <?php echo $busca['km_chegada']; ?></h4>
            <h4><i class="fa fa-arrow-right"></i> <strong>KM Percorridos:</strong> <?php echo $busca['km_percorridos']; ?></h4>                               
        </div>

        <?php
        } elseif ($busca['situacao'] == "Autorizada" and $busca['data_uso'] >= $dia_hoje) {
        ?>

        <div class="ms green" style="margin: 15px 0 10px 0; paddind-left: 0;"><i class="fa fa-check-square-o fa-2x" style="color: #32A041;"></i>&nbsp;&nbsp;&nbsp; Autorizada</div><br />

        <?php
        } elseif ($busca['situacao'] == "Cancelada") {
        ?>

        <div class="ms no" style="margin: 15px 0 10px 0; paddind-left: 0;"><i class="fa fa-minus-square fa-2x" style="color: red;"></i>&nbsp;&nbsp;&nbsp; Cancelada</div><br />

        <?php
        } else {
        ?>
        <div class="ms no" style="margin: 15px 0 10px 0; paddind-left: 0;"><i class="fa fa-times fa-2x" style="color: red;"></i>&nbsp;&nbsp;&nbsp;
            <?php
            if ($part['situacao'] = 'Nao Autorizada') {
            echo 'Não Autorizada';
            }
            ?>
            <h4><i class="fa fa-angle-double-right" style="margin-left: -30px; margin-top: 3px;"></i> <?php echo $busca['motivo']; ?></h4>
        </div>
        <?php
        }
        ?>
        <!-- encerra espaço que mostra a situação da GUIA-->

        <?php
        if ($busca['servidor'] == $busca['motorista']) {
        ?>
        <span>Solicitante:</span><br />
        <h3><?php echo $busca['servidor']; ?></h3><br />
        <h4><i class="fa fa-chevron-right"></i> <?php echo 'CNH: ' . $busca['cnh_motorista']; ?></h4>
        <h4><i class="fa fa-chevron-right"></i> <?php echo 'Vencimento da CNH: ' . date('d/m/Y', strtotime($busca['cnh_vencimento'])); ?></h4>
        <h4><i class="fa fa-chevron-right"></i> <?php echo 'OS de Condutor: ' . $busca['os_motorista'] . ' de ' . date('d/m/Y', strtotime($busca['os_motorista_data'])); ?></h4>
        <?php
        } else {
        ?>
        <span>Solicitante:</span><br />
        <h3><?php echo $busca['servidor']; ?></h3>

        <span>Motorista:</span>
        <h3><?php echo $busca['motorista']; ?></h3><br />
        <h4><i class="fa fa-chevron-right"></i> <?php echo 'CNH: ' . $busca['cnh_motorista']; ?></h4>
        <h4><i class="fa fa-chevron-right"></i> <?php echo 'Vencimento da CNH: ' . date('d/m/Y', strtotime($busca['cnh_vencimento'])); ?></h4>
        <h4><i class="fa fa-chevron-right"></i> <?php echo 'OS de Condutor: ' . $busca['os_motorista'] . ' de ' . date('d/m/Y', strtotime($busca['os_motorista_data'])); ?></h4>
        <?php
        }
        ?>

        <span>Data/Hora da utilização:</span><br />
        <h3><?php echo date('d/m/Y', strtotime($busca['data_uso'])) . ' às ' . date('H:i', strtotime($busca['horario_uso'])); ?></h3>

        <span>Roteiro:</span><br />
        <h3>
            <?php
            if ($busca['roteiro_3'] != '') {
            echo $busca['roteiro'] . '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;' . $busca['roteiro_2'] . '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;' . $busca['roteiro_3'];
            } elseif ($busca['roteiro_2'] != '' && $busca['roteiro_3'] == '') {
            echo $busca['roteiro'] . '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;' . $busca['roteiro_2'];
            } else {
            echo $busca['roteiro'];
            }
            ?>
        </h3>

        <span>Previsão de retorno:</span><br />
        <h3><?php echo date('d/m/Y', strtotime($busca['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($busca['prev_retorno_hora'])); ?></h3>

        <span>Finalidade:</span><br />
        <h3><?php echo $busca['finalidade']; ?></h3>
        <h4><?php echo $busca['desc_finalidade']; ?></h4>

        <span>Passageiros:</span><br />
        <h3>
            <?php
            if ($busca['passageiros'] == "") {
            echo 'Sem passageiros';
            } else {
            $arr_passageiros = explode(',', $busca['passageiros']);
            $total_passageiros = count($arr_passageiros);
            $i = 1;
            while ($i <= $total_passageiros) {
            echo $i . '- ' . $arr_passageiros[$i - 1] . '<br />';
            $i++;
            }
            }
            ?>
        </h3>
    </div>   
</form>

<!--Termina conteudo das páginas-->

<?php include_once "includes/inc_footer.php"; ?>