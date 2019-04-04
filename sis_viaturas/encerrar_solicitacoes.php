<?php
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";

$siape_login = $_SESSION['autUser']['siape'];
$hoje = date('Y-m-d');
$autorizada = 'Autorizada';
$readMinhasAbertas = read('vt_solicitacoes', "WHERE siape = '$siape_login' AND situacao = '$autorizada' AND data_uso <= '$hoje'");
$readCountMinhasAbertas = count($readMinhasAbertas);

if ($readCountMinhasAbertas < 1) {
    header("Refresh:0; url=painel.php");
} else {
    ?>

    <!--Conteudo das páginas -->
    <h1 class="titulo-secao-medio"><i class="fa fa-share-square"></i> Solicitações a serem encerradas</h1>                   

    <?php
    //PAGINAÇÃO
    $pag = $_GET["pag"];
    if ($pag >= '1') {
        $pag = $pag;
    } else {
        $pag = '1';
    }
    
    $maximo = '10'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;
    $readBusca = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND situacao = '$autorizada' AND prev_retorno_data <= '$hoje') ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
    $countReadBusca = count($readBusca);
    ?>

    <h5 class="msg_topo"><i class="fa fa-caret-right" style="color: #999;"></i> <strong><?php echo $countReadBusca; ?></strong> aguardando encerramento </h5>

    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        <tr class="tr_header">
            <td align="center">ID</td>
            <td align="left" style="padding-left:5px">Nome</td>
            <td align="center">Solicitada</td>
            <td align="center">Finalidade</td>
            <td align="center">Uso em</td>
            <td align="center">Situação</td>
            <td align="center">Veículo</td>
            <td align="center">Ações</td>
        </tr>

        <?php
        if ($countReadBusca >= 1) {
            
            $datePickerCount = 1;
            
            foreach ($readBusca as $prox){
                                
            $colorPage++;
            if ($colorPage % 2 == 0) {
                $cor = 'style="background:#f5f5f5;"';
            } else {
                $cor = 'style="background:#fff;"';
            }
            ?>

            <tr <?php echo $cor; ?> class="lista_itens">
                <td align="center" style="font-size: 0.8em; font-weight: 600; color: #32A041;"><a href="detalha_solicitacao.php?id_solicitacao=<?php echo $prox['id']; ?>" style="font-size: 1em; font-weight: 600; color: #32A041; text-decoration: none;"><?php echo $prox['id']; ?></a></td>
                <td align="left" style="padding-left:5px">
                    <?php
                    $abrServidor = explode(' ', $prox['servidor']);
                    $escreveAbr = $abrServidor[0] . ' ' . $abrServidor[1] . ' ' . $abrServidor[2];
                    if ($prox['passageiros'] != "") {
                        $separa_passageiros = explode(",", $proxPassageiros);
                        $conta_passageiros = count($separa_passageiros);
                        echo $escreveAbr . ' [+' . $conta_passageiros . ']';
                    } else {
                        echo $escreveAbr;
                    }
                    ?>
                </td>
                <td align="center"><?php echo date('d/m/y', strtotime($prox['criadoEm'])); ?></td>
                <td align="center"><?php echo $prox['finalidade']; ?></td>
                <td align="center"><?php echo date('d/m/y', strtotime($prox['data_uso'])) . ' às ' . date('H:i', strtotime($prox['horario_uso'])); ?></td>

                <td align="center"><?php echo '<i class="fa fa-check-square-o fa-2x" style="color: green;" title="Autorizada"></i>';
                    ?></td>  

                <td align="center" width="130px" style="font:10px">
                    <?php echo '<div class="veiculo_autorizado">' . $prox['veiculo'] . '</div>'; ?>  
                </td>  
                <td align="center" style="font:15px 'Trebuchet MS', Arial, Helvetica, sans-serif;">
                    <i class="fa fa-share-square btn_encerraGuia" style="color: #09F; font-size: 1.4em; cursor: pointer;" title="Encerrar solicitação" id="btn_encerraGuia" value="<?php echo $prox['id']; ?>"></i>
                </td>
            </tr>

            <!-- DIV para informar dados para o fechamento-->
            <div id="modalEncerra<?php echo $prox['id']; ?>" class="modal_encerra_guia" style="display: none;">
                <div class="modal_encerra_guia_content" style="display: block;"> 
                    <div class="modal_encerra_guia_header">Encerrar a Guia Nº <?php echo $prox['id']; ?></div>
                    <div class="msg_retorno"></div>
                    
                    <div class="formEncerradaBlock">
                        <h4 class="ms green"><i class="fa fa-refresh" style="font-size: 1.4em color:#F06;"></i>&nbsp;&nbsp;&nbsp; Esta solicitação já esta encerrada!</h4>    
                    </div>
                    
                    <form name="encerraSolicitacao" method="post">

                        <div class="encerraGuia_infos">
                            <h3><strong>Veículo:</strong> <?php echo $prox['veiculo']; ?></h3>
                            <h3><strong>Destino:</strong> <?php echo $prox['roteiro']; ?></h3>
                            <h3><strong>Data/Hora:</strong> <?php echo date('d/m/y', strtotime($prox['data_uso'])) . ' às ' . date('H:i', strtotime($prox['horario_uso'])); ?></h3>
                        </div>

                        <div class="encerraGuia_left">
                            <span>Data de Chegada:</span>
                            <input type="text" id="data_chegada<?php echo  $datePickerCount; ?>" name="data_chegada" placeholder="Data chegada" />

                            <span>KM de Saída:</span>
                            <input type="text" id="km_saida<?php echo  $prox['id']; ?>" name="km_saida" onkeypress="return SomenteNumero(event)" maxlength="6" />

                            <span>KM Percorridos:</span>
                            <input type="text" id="km_percorridos<?php echo  $prox['id']; ?>" name="km_percorridos" readonly="true"/>
                        </div>

                        <div class="encerraGuia_right">
                            <span>Hora de Chegada:</span>
                            <input name="hora_chegada" type="text" id="hora_chegada" onkeypress="return SomenteNumero(event)" maxlength="5" placeholder="Ex: 11:10" />

                            <span>KM de Chegada:</span>
                            <input type="text" id="km_chegada<?php echo  $prox['id']; ?>" name="km_chegada" onkeypress="return SomenteNumero(event)" onblur="calculaKm(<?php echo  $prox['id']; ?>)" maxlength="6"  />

                            <span>Situação do Combustível:</span>
                            <select name="combustivel" id="combustivel">
                                <option value="" selected="selected" disabled="disabled">Selecione</option>
                                <option value="0">Reserva</option>
                                <option value="1">1/4</option>
                                <option value="2">1/2</option>
                                <option value="3">3/4</option>
                                <option value="4">4/4</option>
                            </select>
                        </div>
                        
                        <div class="encerraGuia_bottom">
                            <span>Observações:</span>
                            <textarea name="observacao" id="observacao" placeholder="Informe aqui se houver obervações"></textarea>
                        </div>

                        <div class="modal_encerra_guia_actions">
                            <input type="hidden" id="id_encerra" name="id_encerra" value="<?php echo $prox['id']; ?>" />
                            <input type="hidden" id="veiculo_encerra" name="veiculo_encerra" value="<?php echo $prox['veiculo']; ?>" />
                            <button type="button" class="btn btn_green fl_right j_button">Encerrar viagem</button>
                            <h4 class="modal_encerra_close btn btn_red" value="<?php echo  $prox['id']; ?>">x</h4>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </form>
                </div>
            </div>

            <?php
            $datePickerCount++;
            }
        }
        ?>

    </table>

    <div id="paginator">
        <?php
        //PAGINAÇÃO
        $total = $countReadBusca;

        $paginas = ceil($total / $maximo);
        $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

        echo "<a href=encerrar_solicitacoes.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

        for ($i = $pag - $links; $i <= $pag - 1; $i++) {
            if ($i <= 0) {
                
            } else {
                echo"<a href=encerrar_solicitacoes.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
            }
        }echo "<h1>$pag</h1>";

        for ($i = $pag + 1; $i <= $pag + $links; $i++) {
            if ($i > $paginas) {
                
            } else {
                echo "<a href=encerrar_solicitacoes.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
            }
        }
        echo "<a href=encerrar_solicitacoes.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";
        ?>
    </div>  

    <!--Encerra conteúdo das páginas-->
    <?php include_once "includes/inc_footer.php"; ?>
    <?php
        }
    ?>

<script type="text/javascript">
    $(".modal_encerra_close").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra"+str).style.display = "none";
    });

    $(".btn_encerraGuia").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra"+str).style.display = "block";
    });
</script>   

<script type="text/javascript">
    function calculaKm(idSol) {
        var km_cheg = document.getElementById("km_chegada"+idSol).value;
        var km_said = document.getElementById("km_saida"+idSol).value;
        if (km_said > km_cheg) {
            alert('ERRO: Quilometragem de chegada menor que quilometragem de saída!');
            var km_perc = '';
        } else {
            var km_perc = km_cheg - km_said;
            document.getElementById("km_percorridos"+idSol).value = km_perc;
        }
    }
</script><!--Script calcula KM Percorridos-->

<script type="text/javascript">
    function SomenteNumero(e) {
        var tecla = (window.event) ? event.keyCode : e.which;
        if ((tecla > 47 && tecla < 59))
            return true;
        else {
            if (tecla == 8 || tecla == 0)
                return true;
            else
                return false;
        }
    }
</script><!--Script para ser inseridos somente numeros nos inputs km-->