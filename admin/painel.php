<?php
include_once "includes/inc_header.php";

$readEstatServidores = read('servidores');
$readEstatSolicitacoes = read('vt_solicitacoes');
$hoje = date('Y-m-d');
?>


<div class="painel_viaturas">

    <h1 class="titulo-secao"><i class="fa fa-clock-o"></i> Viaturas</h1>
    <a class="titulo-secao-link" href="admin_esolicitacoes.php"><span>VER SOLICITAÇÕES</span></a>

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
            $ocupadoDois = read('vt_solicitacoes', "WHERE veiculo = '$veiculoPlaca' AND situacao = 'Autorizada' AND ((data_uso = '$hojeViatura' AND prev_retorno_data = '$hojeViatura' AND horario_uso < '$hojeAgora' AND prev_retorno_hora > '$hojeAgora'))");
            $ocupadoTres = read('vt_solicitacoes', "WHERE veiculo = '$veiculoPlaca' AND situacao = 'Autorizada' AND ((data_uso < '$hojeViatura' AND prev_retorno_data = '$hojeViatura' AND prev_retorno_hora > '$hojeAgora'))");

            $countOcupadoUm = count($ocupadoUm);
            $countOcupadoDois = count($ocupadoDois);
            $countOcupadoTres = count($ocupadoTres);

            if ($countOcupadoUm or $ocupadoDois or $ocupadoTres >= 1) {
                $veiculoSituacao = 'EM USO';
                $veiculoClass = 'h2ocupado';
                $veiculoLabel = '<i class="fa fa-map-marker"></i> ONDE';
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
                $prox = '
                    <h4 class="h4dados">
                        <strong>
                            <i class="fa fa-tag" style="color: #06F;"></i>
                            <a href="admin_asolicitacoes.php?idsolicit=' . $proxVeic['id'] . '" title="Ver detalhes">Solicitação nº ' . $proxVeic['id'] . '</a>
                        </strong><br />
                        <i class="fa fa-user" style="color: #000;"></i> ' . $proxVeic['servidor'] . '<br />
                        <i class="fa fa-map-marker" style="color: #06F;"></i> ' . $proxVeic['roteiro'] . '<br />
                        <i class="fa fa-arrow-right" style="color: #090;"></i> ' . date('d/m/y', strtotime($proxVeic['data_uso'])) . ' às ' . date('H:i', strtotime($proxVeic['horario_uso'])) . '<br />
                        <i class="fa fa-arrow-left" style="color: #F00;"></i> ' . date('d/m/y', strtotime($proxVeic['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($proxVeic['prev_retorno_hora'])) . '<br />
                    </h4>
                ';
            } else {
                $veiculoSituacao = 'LIVRE';
                $veiculoClass = 'h2livre';
                $veiculoLabel = '<i class="fa fa-refresh"></i> PRÓXIMA';
                $proximasVeiculo = read('vt_solicitacoes', "WHERE veiculo = '$veiculoPlaca' AND situacao = 'Autorizada' AND (data_uso > '$hojeViatura' OR (data_uso = '$hojeViatura' AND horario_uso >= '$hojeAgora')) ORDER BY data_uso ASC, horario_uso ASC  LIMIT 1");
                $countProximasVeiculos = count($proximasVeiculo);
                if ($countProximasVeiculos >= 1) {
                    foreach ($proximasVeiculo as $proxVeic)
                        ;
                    $prox = '
                        <h4 class="h4dados">
                            <strong>
                                <i class="fa fa-tag" style="color: #06F;"></i>
                                <a href="admin_asolicitacoes.php?idsolicit=' . $proxVeic['id'] . '" title="Ver detalhes">Solicitação nº ' . $proxVeic['id'] . '</a>
                            </strong></br> 
                            <i class="fa fa-user" style="color: #000;"></i> '. $proxVeic['servidor'] . '<br />
                            <i class="fa fa-map-marker" style="color: #06F;"></i> ' . $proxVeic['roteiro'] . '<br />
                            <i class="fa fa-arrow-right" style="color: #090;"></i> ' . date('d/m/y', strtotime($proxVeic['data_uso'])) . ' às ' . date('H:i', strtotime($proxVeic['horario_uso'])) . '<br />
                            <i class="fa fa-arrow-left" style="color: #F00;"></i> ' . date('d/m/y', strtotime($proxVeic['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($proxVeic['prev_retorno_hora'])) . '<br />
                        </h4>
                    ';
                } else {
                    $prox = '
                        <h4 class="h4dados">
                            <br />
                            <p class="proxLivre">Sem solicitações para este veículo</p><br />
                            <br />
                            <br />
                        </h4>
                    ';
                }
            }

            //Verifica as condições de oleo, revisão, combustível, ipva, seguro e geometria
            //Busca o veiculo
            $placaBuscada = $veic['placa'];
            $buscaVeiculo = read('vt_veiculos', "WHERE placa = '$placaBuscada'");

            foreach ($buscaVeiculo as $dadosViat)
                ;
            //IPVA
            $time_inicial = strtotime($dadosViat['ipva']);
            $time_final = strtotime($hoje);
            $dif_ipva = $time_inicial - $time_final;
            $dias_ipva = (int) floor($dif_ipva / (60 * 60 * 24));
            //Seguro
            $time_inicial_seguro = strtotime($dadosViat['seguro']);
            $time_final_seguro = strtotime($hoje);
            $dif_seguro = $time_inicial_seguro - $time_final_seguro;
            $dias_seguro = (int) floor($dif_seguro / (60 * 60 * 24));

            //Combustivel
            if ($dadosViat['combustivel'] < 1) {
                $vei_gasDados = '<h4><i class="fa fa-exclamation-circle"></i> Está na <strong>reserva</strong> de combustível</h4>';
                $vei_gas = 0;
            } else {
                $vei_gasDados = NULL;
                $vei_gas = 1;
            }

            //Oleo do Motor
            if ($dadosViat['troca_oleo'] < $dadosViat['km']) {
                $vei_oleoDifKm = ($dadosViat['km'] - $dadosViat['troca_oleo']);
                $vei_oleoDados = '<h4><i class="fa fa-exclamation-circle"></i> Troca do óleo do motor vencida a <strong>' . $vei_oleoDifKm . '</strong> KM';
                $vei_oleo = 0;
            } elseif (($dadosViat['troca_oleo'] - $dadosViat['km']) <= 50) {
                $vei_oleoDifKm = -($dadosViat['km'] - $dadosViat['troca_oleo']);
                $vei_oleoDados = '<h4><i class="fa fa-exclamation-circle"></i> Troca do óleo vencendo em <strong>' . $vei_oleoDifKm . '</strong> KM</h4>';
                $vei_oleo = 0;
            } else {
                $vei_oleoDados = NULL;
                $vei_oleo = 1;
            }

            //Revisão Mecanica
            if ($dadosViat['revisao'] < $dadosViat['km']) {
                $vei_revisaoDifKm = ($dadosViat['km'] - $dadosViat['revisao']);
                $vei_revisaoDados = '<h4><i class="fa fa-exclamation-circle"></i> Revisão mecânica vencida a <strong>' . $vei_revisaoDifKm . '</strong> KM</h4>';
                $vei_revisao = 0;
            } elseif (($dadosViat['revisao'] - $dadosViat['km']) <= 50) {
                $vei_revisaoDifKm = -($dadosViat['km'] - $dadosViat['revisao']);
                $vei_revisaoDados = '<h4><i class="fa fa-exclamation-circle"></i> Revisão mecânica vencendo em <strong>' . $vei_revisaoDifKm . '</strong> KM</h4>';
                $vei_revisao = 0;
            } else {
                $vei_revisaoDados = NULL;
                $vei_revisao = 1;
            }

            //Geometria e balanceamento
            if ($dadosViat['geometria'] < $dadosViat['km']) {
                $vei_geometriaDifKm = ($dadosViat['km'] - $dadosViat['geometria']);
                $vei_geometriaDados = '<h4><i class="fa fa-exclamation-circle"></i> Geometria/Balanceamento vencido a <strong>' . $vei_geometriaDifKm . '</strong> KM</h4>';
                $vei_geometria = 0;
            } elseif (($dadosViat['geometria'] - $dadosViat['km']) <= 50) {
                $vei_geometriaDifKm = -($dadosViat['km'] - $dadosViat['geometria']);
                $vei_geometriaDados = '<h4><i class="fa fa-exclamation-circle"></i> Geometria/Balanceamento vencendo em <strong>' . $vei_geometriaDifKm . '</strong> KM</h4>';
                $vei_geometria = 0;
            } else {
                $vei_geometriaDados = NULL;
                $vei_geometria = 1;
            }

            //Seguro
            if ($dias_seguro >= 7 && $dias_seguro < 30) {
                $vei_seguroDados = '<h4><i class="fa fa-exclamation-circle"></i> SEGURO vencendo em menos de <strong>30 dias</h4>';
                $vei_seguro = 0;
            } elseif ($dias_seguro > 1 && $dias_seguro < 7) {
                $vei_seguroDados = '<h4><i class="fa fa-exclamation-circle"></i> SEGURO vencendo em menos de <strong>1 semana</strong></h4>';
                $vei_seguro = 0;
            } elseif ($dias_seguro == 1) {
                $vei_seguroDados = '<h4><i class="fa fa-exclamation-circle"></i> SEGURO vencendo <strong>amanhã!</strong></h4>';
                $vei_seguro = 0;
            } elseif ($dias_seguro == 0) {
                $vei_seguroDados = '<h4><i class="fa fa-exclamation-circle"></i> SEGURO vencendo <strong>HOJE!</strong></h4>';
                $vei_seguro = 0;
            } elseif ($dias_seguro < 0) {
                $vei_seguroDados = '<h4><i class="fa fa-exclamation-circle"></i> SEGURO vencido a <strong>' . -$dias_seguro . '</strong> dias</h4>';
                $vei_seguro = 0;
            } else {
                $vei_seguroDados = NULL;
                $vei_seguro = 1;
            }

            //Verifica a situação do combustivel do veículo para mostrar o gráfico
            if ($veic['combustivel'] == 4) {
                $combustivel = '<img src="imagens/combustivel_full.png" title="Full" alt="Full" />';
            } elseif ($veic['combustivel'] == 3) {
                $combustivel = '<img src="imagens/combustivel_3-4.png" title="3/4" alt="3/4" />';
            } elseif ($veic['combustivel'] == 2) {
                $combustivel = '<img src="imagens/combustivel_1-2.png" title="1/2" alt="1/2" />';
            } elseif ($veic['combustivel'] == 1) {
                $combustivel = '<img src="imagens/combustivel_1-4.png" title="1/4" alt="1/4" />';
            } elseif ($veic['combustivel'] == 0) {
                $combustivel = '<img src="imagens/combustivel_reserva.png" title="Reserva" alt="Reserva" />';
            }

            echo '
                <div class="mostraVeiculo" style="text-align: center;">
                    <div class="box33">
                        <img class="img_veiculo" src="imagens/veiculos/' . $veic['foto'] . '" alt="' . $veic['veiculo'] . '" title="' . $veic['km'] . ' km" width="110px" /><br />
                        <h1 class="h1titulo">
                            <a href="admin_esolicitacoes_viatura.php?idviat=' . $veic['id'] . '&filtro=prox">' . $veic['veiculo'] . '
                            &nbsp<i class="fa fa-caret-right"></i> 
                            ' . $veic['placa'] . 
                        '</a></h1>';
                                            
            /* if ($vei_gas == 0 || $vei_oleo == 0 || $vei_revisao == 0 || $vei_ipva == 0 || $vei_seguro == 0) */
            if ($vei_gas == 0 || $vei_oleo == 0 || $vei_revisao == 0 || $vei_seguro == 0) {
                echo '<div class="atencaoVeiculo" value="' . $veic['placa'] . '"><i class="fa fa-exclamation-triangle fa-3x" style="color: #F90;"></i></div></div>';
            } else {
                echo '</div>';
            }

            echo '
                    <div class="box33" style="text-align: center;">
                        <h2 class="' . $veiculoClass . '">' . $veiculoSituacao . '</h2>
                        <div class="combustivelIndex">' . $combustivel . '</div>
                    </div>
                    
                    <div class="box33" style="text-align: center;">
                        <h3 class="h3proxima">' . $veiculoLabel . '</h3>
                        ' . $prox . '    
                    </div>
                </div>
            ';

            //Div de detalhamento dos avisos		
            echo '
                <div class="avisosVeiculo ' . $veic['placa'] . '">
                    ' . $vei_gasDados . '
                    ' . $vei_oleoDados . '
                    ' . $vei_revisaoDados . '
                    ' . $vei_seguroDados . '
                    ' . $vei_geometriaDados . '
                </div>
            ';
        }
    } else {
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Não existem veículos cadastrados!</h4>";
    }//if countVeiculos
    ?>
</div><!--fecha div painel viaturas-->

<div class="painel_estatisticas">
    <div class="box33">
        <h4>
            <i class="fa fa-users fa-3x" style="color: #737373;"></i><br/><br/>
            <strong><?php echo count($readEstatServidores); ?></strong><br />
            Servidores Cadastrados
        </h4>
    </div>
    
    <div class="box33">
        <h4>
            <i class="fa fa-tags fa-3x" style="color: #737373;"></i><br /><br/>
            <strong><?php echo count($readEstatSolicitacoes); ?></strong><br />
            Solicitações de Viaturas
        </h4>
    </div>
    
    <div class="box33">
        <h4>
            <?php
                $contaAcessoSistema = read('acessos');
                $numAcessoSistema   = count($contaAcessoSistema);
            ?>
            <i class="fa fa-sign-in fa-3x" style="color: #737373;"></i><br /><br/>
            <strong><?php echo $numAcessoSistema; ?></strong><br />
            Acessos ao sistema
        </h4>
    </div>
</div>

<?php include_once "includes/inc_footer.php"; ?>