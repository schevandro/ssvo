<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):
?>
    <h1 class="titulo-secao"><i class="fa fa-eye"></i> Informações da solicitação</h1>

<?php
    echo "<a href='admin_esolicitacoes.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Solicitações'><i class='fa fa-list'></i> Listar Solicitações</a>";
    $idsolicit = $_GET['idsolicit'];
    $hoje = date('Y-m-d');
    $buscaSolicitacao = read('vt_solicitacoes', "WHERE id = '$idsolicit'");
    $countBuscaSolicitacao = count($buscaSolicitacao);

    if ($idsolicit == ''):
        header('Location: admin_esolicitacoes.php');
    else:
        if ($countBuscaSolicitacao <= 0):
            header('Location: admin_esolicitacoes.php');
        else:
            $situacao = array('Aguardando...', 'Autorizada', 'Nao Autorizada', 'Cancelada', 'Encerrada');
            $buscaSituacao = read('vt_solicitacoes', "WHERE id = '$idsolicit' AND (situacao = '$situacao[2]' OR situacao = '$situacao[3]' OR situacao = '$situacao[4]')");
            $countBuscaSituacao = count($buscaSituacao);
            if ($countBuscaSituacao <= 0):
                header('Location: admin_asolicitacoes.php?idsolicit=' . $idsolicit);
            else:
                foreach ($buscaSolicitacao as $patr);
                //Lista passageiros no email
                if ($solicitacao['passageiros'] == ''):
                    $pass = 'Não há passageiros!';
                else:
                    $pass = $solicitacao['passageiros'];
                endif;
?>

        <form name="detalha_solicitacao" method="post" action="" enctype="multipart/form-data">
            <div class="detalhesGuia">
                <h1><i class="fa fa-tag"></i> Solicitação <strong>Nº <?php echo $patr['id']; ?> </strong></h1>
                <?php
                $dia_hoje_can = date('Y-m-d');
                if ($patr['siape'] == $_SESSION['autUser']['siape'] and ( ($patr['situacao'] == "Autorizada" and $patr['data_uso'] >= $dia_hoje_can) or $patr['situacao'] == "Aguardando...")) {
                    echo '<a href="detalha_solicitacao.php?id_cancela=' . $patr['id'] . '&amp;siape=' . $patr['siape'] . '">';
                    echo '<div class="btn btn_red flt_right" style="padding: 15px; margin-top: -35px;">Cancelar Guia</div></a>';
                }
                ?>

                <?php echo '<h2><i class="fa fa-clock-o"></i> solicitada em ' . date('d/m/Y', strtotime($patr['criadoEm'])) . ' às ' . date('H:i', strtotime($patr['criadoEm'])) . '</h2>'; ?>

                <!-- mostra a situação da GUIA-->
                <?php
                $dia_hoje = date('Y-m-d');
                if ($patr['situacao'] == "Autorizada" and $patr['data_uso'] <= $dia_hoje) {
                    if ($patr['veiculo'] != "n/d") {
                        echo "<div class='ms al' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-share-square-o fa-2x' style='color: #F60;'></i>&nbsp;&nbsp;&nbsp; Esta solicitação precisa ser encerrada, <a href='encerrar_solicitacoes.php' style='color: #06F; text-decoration: none;'><b>Clique aqui</b></a> para encerrá-la.</div><br />";
                    }
                } elseif ($patr['situacao'] == "Aguardando...") {
                    ?>

                    <div class="ms al" style="margin: 15px 0 10px 0; paddind-left: 0;"><i class="fa fa-exclamation-triangle fa-2x" style="color: #F93;"></i>&nbsp;&nbsp;&nbsp; Aguardando liberação</div><br />

                    <?php
                } elseif ($patr['situacao'] == "Encerrada") {
                    ?>

                    <div class="ms blue" style="margin: 15px 0 0 0; paddind-left: 0;"><i class="fa fa-share-square-o fa-2x" style="color: #FFF;"></i>&nbsp;&nbsp;&nbsp; Encerrada</div><br />                
                    <div class="detalhesEncerrada">
                        <h4><i class="fa fa-arrow-right"></i> <strong>Data Chegada:</strong> <?php echo date('d/m/Y', strtotime($patr['data_chegada'])) . ' às ' . date('H:i', strtotime($patr['hora_chegada'])); ?></h4>
                        <h4><i class="fa fa-arrow-right"></i> <strong>KM de Saída:</strong> <?php echo $patr['km_saida']; ?></h4>
                        <h4><i class="fa fa-arrow-right"></i> <strong>KM de Chegada:</strong> <?php echo $patr['km_chegada']; ?></h4>
                        <h4><i class="fa fa-arrow-right"></i> <strong>KM Percorridos:</strong> <?php echo $patr['km_percorridos']; ?></h4>                               
                        <?php
                            if($patr['observacao'] != null){
                           ?>
                                <h4><i class="fa fa-exclamation-triangle"></i> <strong>Observações:</strong> <?php echo $patr['observacao']; ?></h4>
                           <?php
                            }
                        ?>
                    </div>

                    <?php
                } elseif ($patr['situacao'] == "Autorizada" and $patr['data_uso'] >= $dia_hoje) {
                    ?>

                    <div class="ms green" style="margin: 15px 0 10px 0; paddind-left: 0;"><i class="fa fa-check-square-o fa-2x" style="color: #32A041;"></i>&nbsp;&nbsp;&nbsp; Autorizada</div><br />

                    <?php
                } elseif ($patr['situacao'] == "Cancelada") {
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
                        <h4><i class="fa fa-angle-double-right" style="margin-left: -30px; margin-top: 3px;"></i> <?php echo $patr['motivo']; ?></h4>
                    </div>
                    <?php
                }
                ?>
                <!-- encerra espaço que mostra a situação da GUIA-->

                <?php
                if ($patr['servidor'] == $patr['motorista']) {
                    ?>
                    <span>Solicitante:</span><br />
                    <h3><?php echo $patr['servidor']; ?></h3><br />
                    <h4><i class="fa fa-chevron-right"></i> <?php echo 'CNH: ' . $patr['cnh_motorista']; ?></h4>
                    <h4><i class="fa fa-chevron-right"></i> <?php echo 'Vencimento da CNH: ' . date('d/m/Y', strtotime($patr['cnh_vencimento'])); ?></h4>
                    <h4><i class="fa fa-chevron-right"></i> <?php echo 'OS de Condutor: ' . $patr['os_motorista'] . ' de ' . date('d/m/Y', strtotime($patr['os_motorista_data'])); ?></h4>
                    <?php
                            if($patr['comprovante'] != ''):
                        ?>
                                <h4><i class="fa fa-chevron-right"></i> <?php echo "<a href='../admin/documentos/comprovantes_guia/{$patr['comprovante']}' title='Ver o comprovante' target='_blank' style='text-decoration: none; cursor: Pointer; color: #09F;'><i class='fa fa-file-pdf-o'></i> Visualizar o comprovante</a>" ?></h4>
                        <?php
                            endif;
                        ?>
                    <?php
                } else {
                    ?>
                    <span>Solicitante:</span><br />
                    <h3><?php echo $patr['servidor']; ?></h3>
                    <?php
                        if($patr['comprovante'] != ''):
                    ?>
                            <h4><i class="fa fa-chevron-right"></i> <?php echo "<a href='../admin/documentos/comprovantes_guia/{$patr['comprovante']}' title='Ver o comprovante' target='_blank' style='text-decoration: none; cursor: Pointer; color: #09F;'><i class='fa fa-file-pdf-o'></i> Visualizar o comprovante</a>" ?></h4>
                    <?php
                        endif;
                    ?>

                    <span>Motorista:</span>
                    <h3><?php echo $patr['motorista']; ?></h3><br />
                    <h4><i class="fa fa-chevron-right"></i> <?php echo 'CNH: ' . $patr['cnh_motorista']; ?></h4>
                    <h4><i class="fa fa-chevron-right"></i> <?php echo 'Vencimento da CNH: ' . date('d/m/Y', strtotime($patr['cnh_vencimento'])); ?></h4>
                    <h4><i class="fa fa-chevron-right"></i> <?php echo 'OS de Condutor: ' . $patr['os_motorista'] . ' de ' . date('d/m/Y', strtotime($patr['os_motorista_data'])); ?></h4>
                    <?php
                }
                ?>

                <span>Data/Hora da utilização:</span><br />
                <h3><?php echo date('d/m/Y', strtotime($patr['data_uso'])) . ' às ' . date('H:i', strtotime($patr['horario_uso'])); ?></h3>

                <span>Roteiro:</span><br />
                <h3>
                    <?php
                    if ($patr['roteiro_3'] != '') {
                        echo $patr['roteiro'] . '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;' . $patr['roteiro_2'] . '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;' . $patr['roteiro_3'];
                    } elseif ($patr['roteiro_2'] != '' && $patr['roteiro_3'] == '') {
                        echo $patr['roteiro'] . '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;' . $patr['roteiro_2'];
                    } else {
                        echo $patr['roteiro'];
                    }
                    ?>
                </h3>

                <span>Previsão de retorno:</span><br />
                <h3><?php echo date('d/m/Y', strtotime($patr['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($patr['prev_retorno_hora'])); ?></h3>

                <span>Finalidade:</span><br />
                <h3><?php echo $patr['finalidade']; ?></h3>
                <h4><?php echo $patr['desc_finalidade']; ?></h4>

                <span>Passageiros:</span><br />
                <h3>
                    <?php
                    if ($patr['passageiros'] == "") {
                        echo 'Sem passageiros';
                    } else {
                        $arr_passageiros = explode(',', $patr['passageiros']);
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

<?php
            endif;
        endif;
    endif;
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
include_once "includes/inc_footer.php";
?>