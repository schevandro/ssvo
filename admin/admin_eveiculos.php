<?php
include_once "includes/inc_header.php";
$hoje_seguro = date('Y-m-d');
$listaStatus = 'vazio';

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):

    $readVeiculos = read('vt_veiculos', "WHERE deletado = '0' ORDER BY veiculo ASC");
    $countReadVeiculos = count($readVeiculos);
    $readVeiculosAtivos = read('vt_veiculos', "WHERE ativo = 1");
    $countReadVeiculosAtivos = count($countReadVeiculosAtivos);
?>

    <h1 class="titulo-secao"><i class="fa fa-car"></i> Veículos <span><?php echo $countReadVeiculos; ?> registros</span></h1>
    <a href="admin_cadVeiculos.php" class="btn btn_green fl_right" title="Adicionar um veículo"><i class="fa fa-plus-circle"></i> VEÍCULO</a>

<?php    
    if($countReadVeiculos <= 0):
        echo "<h4 class='ms al' style='margin: 35px 0;'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Não existem veículos cadastrados no sistema!</h4>";
    endif;
    
    //Abastecer o veículo
    if (isset($_GET['gasolina'])):
        if ($_GET['gasolina'] == 'true'):
            $veiculo_abastecido = $_GET['id_veiculo'];
            $gas['combustivel'] = 4;
            $up_gasolina = update('vt_veiculos', $gas, "id = '$veiculo_abastecido'");
            if ($up_gasolina):
                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Abastecido com sucesso!</h6>";
                header('Refresh: 1;url=admin_eveiculos.php');
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao abastecer o veículo selecionado!</h4>";
            endif;
        else:
            header('Refresh: 1;url=admin_eveiculos.php');
        endif;
    endif;
?>

<?php
    //Trocar óleo do motor
    if (isset($_GET['oleo'])):
        if ($_GET['oleo'] == 'true'):
            if (isset($_POST['atualiza_oleo'])):
                $veiculo_oleo = $_GET['id_veiculo'];
                $oleo['troca_oleo'] = strip_tags(trim($_POST['km_oleo']));
                $up_oleo = update('vt_veiculos', $oleo, "id = '$veiculo_oleo'");
                if ($up_gasolina):
                    echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Troca de Óleo atualizada com sucesso!</h6>";
                    header('Refresh: 1;url=admin_eveiculos.php');
                else:
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao atualizar troca de óleo do motor!</h4>";
                endif;
            endif;

            echo '<div class="troca_oleo">
                    <form name="up_oleo" method="post"></form>
                  </div>';
        else:
            header('Refresh: 1;url=admin_eveiculos.php');
        endif;
    endif;
    
    //Abre a div de exibição dos veículos
    echo "<div class='lista-veiculos'>";
    
    //Faz busca de veículos e seus respectivos dados
    foreach ($readVeiculos as $veiculos):
        //Verifica revisão
        $dif_revisao = $veiculos['revisao'] - $veiculos['km'];
        if ($dif_revisao >= 1):
            $mostrarevisao = "<span class='time-info-ok'><i class='fa fa-angle-left'></i> faltam {$dif_revisao} km <i class='fa fa-angle-right'></i></span>";
        else:
            $dif_revisao = (-$dif_revisao);
            $mostrarevisao = "<span class='time-info-no'><i class='fa fa-angle-left'></i> vencida a {$dif_revisao} km <i class='fa fa-angle-right'></i></span>";
        endif;
        
        //Verifica troca oleo
        $dif_oleo = $veiculos['troca_oleo'] - $veiculos['km'];
        if ($dif_oleo >= 1):
            $mostraoleo = "<span class='time-info-ok'><i class='fa fa-angle-left'></i> faltam {$dif_oleo} km <i class='fa fa-angle-right'></i></span>";
        else:
            $dif_oleo = (-$dif_oleo);
            $mostraoleo = "<span class='time-info-no'><i class='fa fa-angle-left'></i> vencida a {$dif_oleo} km <i class='fa fa-angle-right'></i></span>";
        endif;

        //Verifica Seguro Obrigatório
        $hoje = date('Y-m-d');
        $time_inicial = strtotime($veiculos['seguro']);
        $time_final = strtotime($hoje);
        $dif_seguro = $time_inicial - $time_final;
        $dias = (int) floor($dif_seguro / (60 * 60 * 24));

        if ($dias >= 1):
            $mostra_data_seguro = date('d/m/Y',strtotime($veiculos['seguro']));
            $mostraseguro = "<span class='time-info-ok'><i class='fa fa-angle-left'></i> faltam {$dias} dias <i class='fa fa-angle-right'></i></span>";
        else:
            $mostra_data_seguro = date('d/m/Y',strtotime($veiculos['seguro']));
            $diasvencido = (-$dias);
            $mostraseguro = "<span class='time-info-no'><i class='fa fa-angle-left'></i> vencido a {$diasvencido} dias <i class='fa fa-angle-right'></i></span>";
        endif;

        //Combustivel
        if ($veiculos['combustivel'] == 4) {
            $combustivel = '<img class="img-combustivel" src="imagens/combustivel_full.png" title="Full" alt="Full" />';
            $escreve_abastecer = "<span class='btn btn_disabled fl_right' title='Este veículo esta com o tanque cheio'><i class='fa fa-flask'></i> Abastecer</span>";
        } elseif ($veiculos['combustivel'] == 3) {
            $combustivel = '<img class="img-combustivel" src="imagens/combustivel_3-4.png" title="3/4" alt="3/4" />';
            $escreve_abastecer = "<a href='admin_eveiculos.php?id_veiculo={$veiculos['id']}&gasolina=true' class='btn btn_orange fl_right' title='Informar abastecimento (encher tanque)'><i class='fa fa-flask'></i> Abastecer</a>	";
        } elseif ($veiculos['combustivel'] == 2) {
            $combustivel = '<img class="img-combustivel" src="imagens/combustivel_1-2.png" title="1/2" alt="1/2" />';
            $escreve_abastecer = "<a href='admin_eveiculos.php?id_veiculo={$veiculos['id']}&gasolina=true' class='btn btn_orange fl_right' title='Informar abastecimento (encher tanque)'><i class='fa fa-flask'></i> Abastecer</a>	";
        } elseif ($veiculos['combustivel'] == 1) {
            $combustivel = '<img class="img-combustivel" src="imagens/combustivel_1-4.png" title="1/4" alt="1/4" />';
            $escreve_abastecer = "<a href='admin_eveiculos.php?id_veiculo={$veiculos['id']}&gasolina=true' class='btn btn_orange fl_right' title='Informar abastecimento (encher tanque)'><i class='fa fa-flask'></i> Abastecer</a>	";
        } elseif ($veiculos['combustivel'] == 0) {
            $combustivel = '<img class="img-combustivel" src="imagens/combustivel_reserva.png" title="Reserva" alt="Reserva" />';
            $escreve_abastecer = "<a href='admin_eveiculos.php?id_veiculo={$veiculos['id']}&gasolina=true' class='btn btn_orange fl_right' title='Informar abastecimento (encher tanque)'><i class='fa fa-flask'></i> Abastecer</a>	";
        }

        //Verifica Situação
        if ($veiculos['ativo'] < 1):
            $classSituacao = 'veiculo-titulo-inativo';
            $iconeSituacao = "<i class='fa fa-exclamation-circle fl_right' title='{$veiculos['situacao']}'></i>";
        else:
            if ($dias < 0):
                $tituloSituacao = 'Seguro Obrigatório Vencido';
                $classSituacao = 'veiculo-titulo-inativo';
                $iconeSituacao = "<i class='fa fa-exclamation-circle fl_right' title='{$tituloSituacao}'></i>";
            else:
                if ($veiculos['ativo'] == 1 && $dif_oleo >= 1 && $dif_revisao >= 1 && $dias == 0):
                    $tituloSituacao = 'Seguro Obrigatório vencendo HOJE';
                    $classSituacao = 'veiculo-titulo-ativo';
                    $iconeSituacao = "<i class='fa fa-check-square-o fl_right' title='{$tituloSituacao}'></i>";
                elseif ($veiculos['ativo'] == 1 && $dif_oleo >= 1 && $dif_revisao >= 1 && $dias > 0):
                    $tituloSituacao = 'Veículo pronto para uso';
                    $classSituacao = 'veiculo-titulo-ativo';
                    $iconeSituacao = "<i class='fa fa-check-square-o fl_right' title='{$tituloSituacao}'></i>";
                elseif ($veiculos['ativo'] == 1 && $dif_oleo < 1 && $dif_revisao >= 1):
                    $tituloSituacao = 'Trocar Óleo do Motor';
                    $classSituacao = 'veiculo-titulo-ativo';
                    $iconeSituacao = "<i class='fa fa-check-square-o fl_right' title='{$tituloSituacao}'></i>";
                elseif ($veiculos['ativo'] == 1 && $dif_revisao < 1 && $dif_oleo >= 1):
                    $tituloSituacao = 'Fazer Revisão';
                    $classSituacao = 'veiculo-titulo-ativo';
                    $iconeSituacao = "<i class='fa fa-check-square-o fl_right' title='{$tituloSituacao}'></i>";
                elseif ($veiculos['ativo'] == 1 && $dif_oleo < 1 && $dif_revisao < 1):
                    $tituloSituacao = 'Fazer Revisão e Trocar Óleo do Motor';
                    $classSituacao = 'veiculo-titulo-ativo';
                    $iconeSituacao = "<i class='fa fa-check-square-o fl_right' title='{$tituloSituacao}'></i>";
                else:
                    $tituloSituacao = $veiculos['situacao'];
                    $classSituacao = 'veiculo-titulo-inativo';
                    $iconeSituacao = "<i class='fa fa-exclamation-circle fl_right' title='{$veiculos['situacao']}></i>";
                endif;
            endif;
        endif;

        //Busca a Última GUIA
        $ultimaGuia = $veiculos['ultima_solicit'];
        $buscaUltimaGuia = read('vt_solicitacoes', "WHERE id = '$ultimaGuia'");
        $countBuscaUltimaGuia = count($buscaUltimaGuia);
        $escreve_guia = NULL;
        if ($countBuscaUltimaGuia >= 1):
            foreach ($buscaUltimaGuia as $uguia);
            $ano_guia = date('y', strtotime($uguia['criadoEm']));
            $data_guia = date('d/m/y', strtotime($uguia['data_uso']));
            $servidor_guia = $uguia['servidor'];
            $destino_guia = $uguia['roteiro'];
            $escreve_guia = "<a href='admin_visusolicitacoes.php?idsolicit={$uguia['id']}' title='Ver detalhes da guia' class='link-ultima-guia'><strong>Última Guia de viagem</strong> <i class='fa fa-caret-right'></i> {$uguia['id']}/{$ano_guia} <i class='fa fa-caret-right'></i> {$servidor_guia} <i class='fa fa-caret-right'></i> {$data_guia} <i class='fa fa-caret-right'></i> {$destino_guia}</a>";
        endif;
        
        //Verifica se exite arquivo do renavam
        if ($veiculos['doc_veiculo'] != '' && $veiculos['doc_veiculo'] != NULL):
            $link_renavam = "<a href='doc-veiculos/{$veiculos['doc_veiculo']}' target='_blank' class='btn btn_green fl_right' style='margin-left: 5px;' title='Visualizar documento do veículo'><i class='fa fa-file-text'></i> Documento</a>";
        else:
            $link_renavam = "<span class='btn btn_disabled fl_right' style='margin-left: 5px;' title='Não há documento gravado'><i class='fa fa-file-o'></i> Documento</span>";
        endif;
        
        echo "   
            <div class='veiculo-single'>

                <div class='{$classSituacao}'>{$iconeSituacao}</div>

                <div class='veiculos-detalhes-topo'>
                    <div class='veiculos-detalhes-topo-img'>
                        <img src='imagens/veiculos/{$veiculos['foto']}' title='{$veiculos['veiculo']}-{$veiculos['placa']}' alt='{$veiculos['nome']}-{$veiculos['placa']}'/>
                    </div>

                    <div class='veiculos-detalhes-topo-dados'>
                        <h1>{$veiculos['veiculo']}</h1>
                        <h2>{$veiculos['placa']}</h2>
                        <h3>{$veiculos['km']} km</h3>
                        {$combustivel}
                        {$escreve_abastecer}
                    </div>                                
                </div>

                <div class='veiculos-detalhes-middle'>
                    <h1><strong>Próxima troca de óleo</strong> <i class='fa fa-caret-right'></i> {$veiculos['troca_oleo']} {$mostraoleo}</h1>
                    <h2><strong>Próxima revisão</strong> <i class='fa fa-caret-right'></i> {$veiculos['revisao']} {$mostrarevisao}</h2>
                    <h3><strong>Vencimento do seguro</strong> <i class='fa fa-caret-right'></i> {$mostra_data_seguro} {$mostraseguro}</h3>
                    {$escreve_guia}
                </div>

                <div class='veiculos-detalhes-bottom'>
                    <span title='Deletar este veículo' class='btn btn_red fl_right btn_deleta_veiculo' style='margin-bottom: 30px; margin-left: 10px;' value='{$veiculos['id']}'><i class='fa fa-trash-o'></i> Excluir</span>
                    <a href='admin_aveiculos.php?id_veiculo={$veiculos['id']}' title='Editar dados deste veículo' class='btn btn_blue fl_right' style='margin-bottom: 30px; margin-left: 10px;'><i class='fa fa-edit'></i> Editar</a>
                    {$link_renavam}
                    <a href='admin_esolicitacoes_viatura.php?idviat={$veiculos['id']}&filtro=prox' class='btn btn_orange fl_left' style='margin-left: 5px;' title='Verificar as próximas viagens deste veículo'><i class='fa fa-car'></i> Ver próximas viagens</a>
                </div>  

            </div><div id='clear'></div>";
?>
            <!-- DIV para confirmar exclusão do veículo-->
            <div id="modalEncerra<?php echo $veiculos['id']; ?>" class="modal_encerra_guia" style="display: none;">
                <div class="modal_encerra_guia_content" style="display: block;"> 
                    <div class="modal_encerra_guia_header">Confirma a exclusão do veículo <?php echo $veiculos['veiculo'].' <i class="fa fa-caret-right"></i> '.$veiculos['placa'].'?'; ?></div>
                    <div class="msg_retorno"></div>

                    <div class="formEncerradaBlock">
                        <h4 class="ms green"><i class="fa fa-refresh" style="font-size: 1.4em color:#F06;"></i>&nbsp;&nbsp;&nbsp; Este veículo não existe na base de dados!</h4>    
                    </div>

                    <form name="deletaVeiculo" method="post">

                        <h2 class="info_ativa_servidor"><strong>Veículo:</strong> <?php echo $veiculos['veiculo']; ?></h2><br />
                        <h2 class="info_ativa_servidor"><strong>Placa:</strong> <?php echo $veiculos['placa']; ?></h2><br />
                        <h2 class="info_ativa_servidor"><strong>Ano Modelo:</strong> <?php echo $veiculos['ano_modelo']; ?></h2><br />
                        <h2 class="info_ativa_servidor"><strong>KM Atual:</strong> <?php echo  $veiculos['km']; ?></h2><br />
                        <?php
                            //Faz verificações para poder deletar
                            $hoje = date('Y-m-d');
                            $nomeVeiculo = $veiculos['veiculo'].'-'.$veiculos['placa'];
                            $buscaSolicitacoesAbertas = read('vt_solicitacoes',"WHERE veiculo = '$nomeVeiculo' AND  situacao = 'Autorizada' AND prev_retorno_data < '$hoje'");
                            $countSolicitacoesAbertas = count($buscaSolicitacoesAbertas);
                            $abertas = $countSolicitacoesAbertas;

                            $buscaSolicitacoesAgendadas = read('vt_solicitacoes',"WHERE veiculo = '$nomeVeiculo' AND  (situacao = 'Autorizada' OR situacao = 'Aguardando...') AND data_uso >= '$hoje'");
                            $countSolicitacoesAgendadas = count($buscaSolicitacoesAgendadas);
                            $agendadas = $countSolicitacoesAgendadas;

                            if($abertas >= 1 || $agendadas >= 1):
                                $disab_btn =  "<button type='button' class='btn btn_disabled fl_right'>Sim, exluir!</button>";
                                if($abertas >= 1):
                                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; <strong>Não é possível deletar o veículo:</strong> possui viagens a serem encerradas [ {$abertas} ]</h4>";
                                endif;
                                if($agendadas >= 1):
                                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; <strong>Não é possível deletar o veículo:</strong> possui viagens agendadas [ {$agendadas} ]</h4>";
                                endif;
                            else:
                                $disab_btn =  "<button type='button' class='btn btn_green fl_right j_button'>Sim, exluir!</button>"; 
                            endif;
                        ?>
                        <div class="modal_encerra_guia_actions">
                            <input type="hidden" id="veiculo_id" name="veiculo_id" value="<?php echo $veiculos['id']; ?>" />
                            <?php echo $disab_btn; ?>
                            <h4 class="btn btn_red btn_fecha_modal fl_left" value="<?php echo  $veiculos['id']; ?>">x</h4>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </form>
                </div>
            </div> 
            <!-- Encerra DIV para confirmar exclusão do veículo-->
<?php    
        endforeach;
    echo "</div>";
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;

include_once "includes/inc_footer.php";
?>

<script src="ajax/js/deleta_veiculo.js"></script>            
<script type="text/javascript">
    $(".btn_fecha_modal").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra"+str).style.display = "none";
    });

    $(".btn_deleta_veiculo").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra"+str).style.display = "block";
    });
</script> 