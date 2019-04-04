<?php
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";

$readEstatServidores = read('servidores');
$readEstatSolicitacoes = read('vt_solicitacoes');
?>

<!--PAINEL - conteúdo -->

<div class="painel_avisos" >
    <?php
    $codigo_abastecimento = $_SESSION['autUser']['cod_abastecimento'];
    if($codigo_abastecimento != 0 && $codigo_abastecimento != ''):
        echo '<h4 class="ms in"><i class="fa fa-credit-card fa-2x" style="color: #337ab7"></i> &nbsp;&nbsp;&nbsp;Seu código de abastecimento: <b>'.$codigo_abastecimento.'</b></i></h4>';
    endif;
    
    $hoje_painel = date('Y-m-d');
    $cnh = $_SESSION['autUser']['cnh'];
    $vecimento_cnh = $_SESSION['autUser']['cnh_vencimento'];
    $time_inicial_painel = strtotime($vecimento_cnh);
    $time_final_painel = strtotime($hoje_painel);
    $diferenca_painel = $time_inicial_painel - $time_final_painel;
    $dias_painel = (int) floor($diferenca_painel / (60 * 60 * 24));

    if ($cnh != "") {
        if ($dias_painel <= 2 ? $escreve = 'fazem' : $escreve = 'faz')
            ;
        if ($dias_painel < 1) {
            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Sua CNH está vencida, {$escreve} <strong>-({$dias_painel})</strong> dias. Envie cópia da CNH com nova data de vencimento para o Gabinete do Campus Feliz.</h4>";
        }
    } else {
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Você não possui \"Ordem de Serviço\" para conduzir veículos oficiais</h4>";
    }

    //Verifica se o servidor tem caronas para aprovar
    $hoje_verif = date('Y-m-d');
    $carona_verif = 1;
    $siape_login = $_SESSION['autUser']['siape'];
    $aguardando = 'Aguardando...';
    $autorizada = 'Autorizada';

    $readCarona = read('vt_solicitacoes', "WHERE (siape = '$siape_login' AND data_uso >= '$hoje_verif' AND caronas >= '$carona_verif' AND (situacao = '$aguardando' OR situacao = '$autorizada'))");
    $reaultadoCarona = count($readCarona);

    if ($reaultadoCarona >= 1) {
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp;Você possui <strong>{$reaultadoCarona}</strong> pedido(s) de carona</h4>";
    }

    //Busca solicitações abertas
    $sit_autorizada = 'Autorizada';
    $sit_hoje = date('Y-m-d');
    $meuSiape = $_SESSION['autUser']['siape'];
    $readSolicAbertas = read('vt_solicitacoes', "WHERE (siape = '$meuSiape' and situacao = '$sit_autorizada' and prev_retorno_data < '$sit_hoje')");
    $solicitacoes_abertas = count($readSolicAbertas);

    if ($solicitacoes_abertas == 1 ? $escreve = array('solicitação', 'precisa', 'fechada') : $escreve = array('solicitações', 'precisam', 'fechadas'))
        ;

    if ($solicitacoes_abertas >= 1) {
        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Você possui <strong>{$solicitacoes_abertas}</strong> {$escreve[0]} que {$escreve[1]} ser {$escreve[2]}</h4>";
    }

    if ($dias_painel > 3 && $reaultadoCarona < 1 && $solicitacoes_abertas < 1):
        echo '<h4 class="ms al"><i class="fa fa-exclamation-triangle fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Antes de fazer uma solicitação de viatura, consulte o menu <i><b>Solicitações Agendadas</b></i></h4>';
    endif;
    ?> 
</div>

<div class="painel_viaturas">
    <h1 class="titulo-secao"><i class="fa fa-clock-o"></i> Viaturas</h1>
    
    <?php
        $hojeViatura = date('Y-m-d');
        $hojeAgora = date('H:i:s');
        $veiculos = read('vt_veiculos', "WHERE deletado = '0' ORDER BY veiculo ASC");
        $countVeiculos = count($veiculos);

        if ($countVeiculos >= 1) {

            foreach ($veiculos as $veic) {
                $veiculoPlaca = $veic['veiculo'] . '-' . $veic['placa'];

                //Verifica LIVRE OU OCUPADO                
                $ocupadoUm = read('vt_solicitacoes', "WHERE veiculo = '$veiculoPlaca' AND situacao = 'Autorizada' AND ((data_uso < '$hojeViatura' AND prev_retorno_data > '$hojeViatura') OR ((data_uso = '$hojeViatura' AND horario_uso <= '$hojeAgora') AND prev_retorno_data > '$hojeViatura'))");
                $countOcupadoUm = count($ocupadoUm);
                $ocupadoDois = read('vt_solicitacoes', "WHERE veiculo = '$veiculoPlaca' AND situacao = 'Autorizada' AND ((data_uso = '$hojeViatura' AND prev_retorno_data = '$hojeViatura' AND horario_uso < '$hojeAgora' AND prev_retorno_hora > '$hojeAgora'))");
                $countOcupadoDois = count($ocupadoDois);
                $ocupadoTres = read('vt_solicitacoes', "WHERE veiculo = '$veiculoPlaca' AND situacao = 'Autorizada' AND ((data_uso < '$hojeViatura' AND prev_retorno_data = '$hojeViatura' AND prev_retorno_hora > '$hojeAgora'))");
                $countOcupadoTres = count($ocupadoTres);
                
                //VERIFICA SE O VEÍCULO ESTA ATIVO OU DESTIVADO PARA O USO
                if($veic['ativo'] == 0):
                    $veiculoSituacao = 'DESATIVADO';
                        $veiculoClass = 'h2desativado';
                        $veiculoLabel = "<i class='fa fa-info-circle'></i>  INFORMAÇÕES";
                                            
                        $prox = "<span class='proxDados'> {$veic['situacao']}</span><br />"
                            . "<span class='proxDados'>&nbsp</span><br />"
                            . "<span class='proxDados'>&nbsp</span><br />"
                            . "<span class='proxDados'>&nbsp</span><br />
                        ";
                else:                    
                    if ($countOcupadoUm or $ocupadoDois or $ocupadoTres >= 1) {
                        $veiculoSituacao = 'OCUPADO';
                        $veiculoClass = 'h2ocupado';
                        $veiculoLabel = "<i class='fa fa-map-marker'></i> ONDE";
                        if ($countOcupadoUm >= 1) {
                            foreach ($ocupadoUm as $proxVeic)
                                ;
                        } elseif ($countOcupadoDois >= 1) {
                            foreach ($ocupadoDois as $proxVeic)
                                ;
                        } elseif ($countOcupadoTres >= 1) {
                            foreach ($ocupadoTres as $proxVeic)
                                ;
                        }
                        $dPartida = date('d/m/y', strtotime($proxVeic['data_uso'])) . ' às ' . date('H:i', strtotime($proxVeic['horario_uso']));
                        $dVolta   = date('d/m/y', strtotime($proxVeic['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($proxVeic['prev_retorno_hora']));  
                        $prox = "<span class='proxDados'><i class='fa fa-user' style='color: #000;'></i> {$proxVeic['servidor']}</span><br />"
                                    . "<span class='proxDados'><i class='fa fa-map-marker' style='color: #06F;'></i> {$proxVeic['roteiro']}</span><br />"
                                    . "<span class='proxDados'><i class='fa fa-arrow-right' style='color: #090;'></i> {$dPartida}</span><br />"
                                    . "<span class='proxDados'><i class='fa fa-arrow-left' style='color: #F00;'></i> {$dVolta}</span><br />";
                    } else {
                        $veiculoSituacao = 'LIVRE';
                        $veiculoClass = 'h2livre';
                        $veiculoLabel = "<i class='fa fa-refresh'></i>  PRÓXIMA";
                        $proximasVeiculo = read('vt_solicitacoes', "WHERE veiculo = '$veiculoPlaca' AND situacao = 'Autorizada' AND (data_uso > '$hojeViatura' OR (data_uso = '$hojeViatura' AND horario_uso >= '$hojeAgora')) ORDER BY data_uso ASC, horario_uso ASC  LIMIT 1");
                        $countProximasVeiculos = count($proximasVeiculo);
                        if ($countProximasVeiculos >= 1) {
                            foreach ($proximasVeiculo as $proxVeic);
                            $dSaida     = date('d/m/y', strtotime($proxVeic['data_uso'])) . ' às ' . date('H:i', strtotime($proxVeic['horario_uso']));
                            $dRetorno   = date('d/m/y', strtotime($proxVeic['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($proxVeic['prev_retorno_hora']));                        
                            $prox = "<span class='proxDados'><i class='fa fa-user' style='color: #000;'></i> {$proxVeic['servidor']}</span><br />"
                                    . "<span class='proxDados'><i class='fa fa-map-marker' style='color: #06F;'></i> {$proxVeic['roteiro']}</span><br />"
                                    . "<span class='proxDados'><i class='fa fa-arrow-right' style='color: #090;'></i> {$dSaida}</span><br />"
                                    . "<span class='proxDados'><i class='fa fa-arrow-left' style='color: #F00;'></i> {$dRetorno}</span><br />";
                        } else {
                            $prox = '
                                    <h4 class="h4dados">
                                        <br />
                                        <strong class="stron1024">Livre</strong><br />
                                        <br />
                                        <br />
                                    </h4>
                                    ';
                        }
                    }
                endif;

                //Busca o veiculo
                $placaBuscada = $veic['placa'];
                $buscaVeiculo = read('vt_veiculos', "WHERE placa = '$placaBuscada'");

                foreach ($buscaVeiculo as $dadosViat);

                //Combustivel
                if ($dadosViat['combustivel'] < 1) {
                    $vei_gasDados = '<h4>&raquo; Está na <strong style="font-size:14px; color:#F00;">reserva</strong> de combustível</h4>';
                    $vei_gas = 0;
                } else {
                    $vei_gasDados = NULL;
                    $vei_gas = 1;
                }

                //Verifica a situação do combustivel do veículo para mostrar o gráfico
                if ($veic['combustivel'] == 4) {
                    $combustivel = '<img src="../admin/imagens/combustivel_full.png" title="Full" alt="Full" />';
                } elseif ($veic['combustivel'] == 3) {
                    $combustivel = '<img src="../admin/imagens/combustivel_3-4.png" title="3/4" alt="3/4" />';
                } elseif ($veic['combustivel'] == 2) {
                    $combustivel = '<img src="../admin/imagens/combustivel_1-2.png" title="1/2" alt="1/2" />';
                } elseif ($veic['combustivel'] == 1) {
                    $combustivel = '<img src="../admin/imagens/combustivel_1-4.png" title="1/4" alt="1/4" />';
                } elseif ($veic['combustivel'] == 0) {
                    $combustivel = '<img src="../admin/imagens/combustivel_reserva.png" title="Reserva" alt="Reserva" />';
                }

                echo "
                    <div class='bloco_33 bloco-veiculos'>
                        <div style='width:130px; float:left;'><img src='../admin/imagens/veiculos/{$veic['foto']}' alt='{$veic['veiculo']}' title='{$veic['km']} km' height='50px' /></div>
                        <h1>{$veic['veiculo']} <i class='fa fa-caret-right'></i> <strong>{$veic['placa']}</strong></h1>
                        <div class='mostra-combustivel'>{$combustivel}</div>			
                        <h2 class='{$veiculoClass}'>{$veiculoSituacao}</h2>
                        <h3 class='bloco-veiculos-subtitulo'>{$veiculoLabel}</h3>
                        {$prox}
                </div>
                ";   
            }
        } else {
            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp;Não há veículos cadastrados.</h4>";
        }//if countVeiculos
        ?>
    </div>

<div class="painel_estatisticas">
    <?php
    $eu = $_SESSION['autUser']['siape'];
    $readSolic = read('vt_solicitacoes');
    $numSolic = count($readSolic);
    $readMinhasSolic = read('vt_solicitacoes', "WHERE siape = '$eu'");
    $numMinhasSolic = count($readMinhasSolic);
    $readCarona = read('vt_caronas', "WHERE siape = '$eu'");
    $numCarona = count($readCarona);
    ?>
    <h1 class="titulo-secao"><i class="fa fa-line-chart"></i> Estatísticas</h1>
    <article class="bloco_33 bloco-info">
        <h1>
            Total de solicitações<br />
            <span><?php echo $numSolic; ?></span>
        </h1>
    </article>
    <article class="bloco_33 bloco-info">
        <h1>
            Minhas solicitações<br />
            <span><?php echo $numMinhasSolic; ?></span>
        </h1>
    </article>
    <article class="bloco_33 bloco-info">
        <h1>
            Meus pedidos de carona<br />
            <span><?php echo $numCarona; ?></span>
        </h1>
    </article>
</div>

<?php
include_once "includes/inc_footer.php";
?>