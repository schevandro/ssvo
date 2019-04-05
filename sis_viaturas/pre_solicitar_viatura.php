<?php
//Inclui Cabeçalho
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";

//Variáveis
$siapeSolicitante = $_SESSION['autUser']['siape'];
$status = array('Autorizada', 'Aguardando...');
$hojeData = date('Y-m-d'); //Formato do BD
$hojeHorario = date('H:i');
//Selecionar o destino
$readEstadoFirst = read('app_estados', "WHERE estado_id != '21' ORDER BY estado_uf ASC");
$readEstado = read('app_estados', "ORDER BY estado_uf ASC");
//Busca as viaturas
$readViaturas = read('vt_veiculos');
$countViaturas = count($readViaturas);
?>

<!--Conteudo das páginas -->
<h1 class="titulo-secao-medio"><i class="fa fa-flag"></i> Nova solicitação de viatura</h1>                   

<?php
//Verifica se o servidor tem alguma solicitação para fechar
$siapeSolAberta = $_SESSION['autUser']['siape'];
$hojeSolAberta = date('Y-d-m');
$readSolAbertas = read('vt_solicitacoes', "WHERE siape = '$siapeAberta' AND situacao = 'Autorizada' AND prev_retorno_data < '$hojeAberta'");
$readCountAberta = count($readSolAbertas);
if ($readCountAberta >= 1) {
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; NÃO é possível efetuar uma nova solicitação. Você possui solicitações à serem ENCERRADAS.</h4>";
} else {
    ?>

    <?php include_once ('codes/solicita_carona.php'); ?><!--inclui o script solicita_carona-->

    <?php
    //Receber dados do Formulário
    if (isset($_POST['avancar'])) {
        //Dados data saida
        $existeViagem = 1;
        $data_calendario = $_POST['calendario'];
        $data_convertida = explode("/", $data_calendario);
        $saida_dia = $data_convertida[0];
        $saida_mes = $data_convertida[1];
        $saida_ano = $data_convertida[2];
        $data_saida = $saida_ano . '-' . $saida_mes . '-' . $saida_dia;
        $dest_cidade = $_POST['destino'];
        $dest_estado = $_POST['estado'];
        $destino = $dest_cidade . '-' . $dest_estado;
        if ($_POST['destino_dois'] == ''):
            $destino_2 = null;
        else:
            $destino_2 = $_POST['destino_dois'] . '-' . $_POST['estado_dois'];
        endif;
        if ($_POST['destino_tres'] == ''):
            $destino_3 = null;
        else:
            $destino_3 = $_POST['destino_tres'] . '-' . $_POST['estado_tres'];
        endif;

        if ($data_calendario == "" or $dest_cidade == "" or $dest_estado == "") {
            $existeViagem = 0;
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Informe todos os dados para avançar</h4>";
        } else {
            if (($destino_2 != '' && ($destino == $destino_2 || $destino == $destino_3 || $destino_2 == $destino_3)) || ($destino_3 != '' && ($destino == $destino_2 || $destino == $destino_3 || $destino_2 == $destino_3))) {
                $existeViagem = 0;
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Você selecionou um mesmo destino mais de uma vez. Corrija este erro antes de prosseguir!</h4>";
            } else {
                //Verifica se o servidor já não solicitou viatura para o mesmo destino no mesmo dia (sem estar autorizada)
                $readDuplica = read('vt_solicitacoes', "WHERE (siape = '$siapeSolicitante' AND situacao = 'Aguardando...' AND data_uso = '$data_saida' AND roteiro = '$destino')");
                $countDuplica = count($readDuplica);
                if ($countDuplica >= 1) {
                    $existeViagem = 0;
                    $dataInfo = date('d/m/Y', strtotime($data_saida));
                    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Você já solicitou viatura para {$destino} para o dia {$dataInfo}. Aguarde a liberação.</h4>";
                } else {

                    //Verifica se o servidor não tem 8 ou mais solicitações para o mesmo dia
                    $readBuscaChave = read('vt_solicitacoes', "WHERE siape = '$siapeSolicitante' AND data_uso = '$data_saida'");
                    $countReadBuscaChave = count($readBuscaChave);

                    if ($countReadBuscaChave >= 8) {
                        $existeViagem = 0;
                        $dataInfo = date('d/m/Y', strtotime($data_saida));
                        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Você já tem 8 ou MAIS solicitações de Viatura Oficial para o dia {$dataInfo}. Contate o Administrador.</h4>";
                    } else {

                        //Verifica se há solicitações para o local solicitado naquela data
                        $readBuscaSolicitacao = read('vt_solicitacoes', "WHERE (data_uso = '$data_saida' AND roteiro = '$destino'  AND (situacao = '$status[0]' OR situacao = '$status[1]'))");
                        $countBuscaSolicitacao = count($readBuscaSolicitacao);
                        //Se há viaturas indo para o mesmo destino na mesma data da solicitação
                        if ($countBuscaSolicitacao >= 1) {
                            $dataInfo = date('d/m/Y', strtotime($data_saida));
                            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Há viagem para {$destino} em {$dataInfo}. Verifique possibilidade de carona.</h4>";

                            $readBuscaCarona = read('vt_solicitacoes', "WHERE (data_uso = '$data_saida' AND roteiro = '$destino' AND (situacao = '$status[0]' OR situacao = '$status[1]'))");
                            $countBuscaCarona = count($readBuscaCarona);
                            ?>

                            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
                                <tr class="tr_header">
                                    <td colspan="6" style="padding-left:5px;">Carona(s) para <?php echo $destino . ' em ' . date('d/m/Y', strtotime($data_saida)); ?></td>
                                </tr>    
                                <tr height="30px" style="background: #CCC;">				
                                    <td align="left" style="padding-left:5px;">Veiculo</td>
                                    <td align="left" style="padding-left:5px;">Servidor</td>
                                    <td align="center">Hora Saída</td>
                                    <td align="center" width="150px">Prev. Retorno</td>
                                    <td align="center">Destino</td>
                                    <td align="center">Carona</td>
                                </tr>

                                <?php
                                foreach ($readBuscaCarona as $carona) {

                                    //Verifica se há veiculo já designado
                                    if ($carona['veiculo'] == '') {
                                        $carona['veiculo'] = 'Aguardando';
                                    }
                                    //Exclui placa do nome do veiculo e busca veículo na tabela veículos
                                    $mostra_veiculo = explode("-", $carona['veiculo']);
                                    $buscaVeiculo = read('vt_veiculos', "WHERE placa = '$mostra_veiculo[1]'");
                                    $countBuscaVeiculo = count($buscaVeiculo);
                                    if ($countBuscaVeiculo == 1) {
                                        foreach ($buscaVeiculo as $veiculo)
                                            ;
                                    }
                                    //Veririca se há passageiros
                                    $contem == ",";
                                    if ($carona['passageiros'] == "") {
                                        $num_passageiros_cont = 0;
                                    } else if ($carona['passageiros'] != "" and ( strpos($carona['passageiros'], ',') == true)) {
                                        $num_passageiros = explode(",", $carona['passageiros']);
                                        $num_passageiros_cont = count($num_passageiros);
                                    } else {
                                        $num_passageiros_cont = 1;
                                    }
                                    //Conta Passageiros
                                    if($carona['veiculo'] = 'Aguardando'):
                                        $maxPassageiros = 4;
                                    else:
                                        $maxPassageiros = $veiculo['capacidade'] - 1;
                                    endif;
                                    
                                    //Caronas restantes
                                    $caronas_restantes = $maxPassageiros - $num_passageiros_cont;
                                    ?><!--conta passageiros e caronas-->

                                    <tr class="lista_itens_viagens" style="height: 60px;">
                                        <td align="left" style="padding-left:5px;">
                                            <?php
                                            if ($caronas_restantes >= 1) {
                                                echo $veiculo['veiculo'] . '-' . $veiculo['placa'] . '<br /><div style="color:#096; font-size:0.875em; margin-top: 5px;">Resta(m) ' . $caronas_restantes . ' lugar(es)</div>';
                                            } else {
                                                echo $veiculo['veiculo'] . '-' . $veiculo['placa'] . '<br /><div style="color:#F90; font-size:0.875em; margin-top: 5px;">Veiculo Lotado</div>';
                                            }
                                            ?>
                                        </td>    
                                        <td align="left" style="padding-left:5px;">
                                            <?php
                                            if ($num_passageiros_cont > 0) {
                                                echo $carona['servidor'] . ' (+' . $num_passageiros_cont . ')';
                                            } else {
                                                echo $carona['servidor'];
                                            }
                                            ?>
                                        </td>    
                                        <td align="center" style="color:#007FFF;"><?php echo date('H:i', strtotime($carona['horario_uso'])); ?></td>
                                        <td align="center"><?php echo date('d/m/y', strtotime($carona['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($carona['prev_retorno_hora'])); ?></td>
                                        <td align="center"><?php echo $carona['roteiro']; ?></td>
                                        <td align="center">
                                            <?php
                                            if ($carona['data_uso'] > $hojeData || ($carona['data_uso'] == $hojeData && $carona['horario_uso'] > $hojeHorario)) {

                                                if ($num_passageiros_cont < $maxPassageiros) {
                                                    echo '
                                                        <a href="pre_solicitar_viatura.php?id_solic=' . $carona['id'] . '">
                                                        <img src="../_assets/img/btn_carona.png" alt="Solicitar Carona" title="Solicitar Carona" />
                                                        </a>
                                                    ';
                                                } else {
                                                    echo '
                                                        <img src="../_assets/img/ico_carona_nao.png" alt="Veículo Lotado" title="Veículo Lotado" width="25px" />
                                                        ';
                                                }
                                            } else {
                                                echo '
                                                    <img src="../_assets/img/ico_carona_nao.png" alt="Viagem em andamento" title="Viagem em andamento" width="25px" />
                                                    ';
                                            }
                                            ?>
                                        </td>
                                    </tr>  
                                    <?php
                                }
                                ?>
                            </table>
                            <?php
                            //Conta as solicitações do dia
                            $readSolicitDia = read('vt_solicitacoes', "WHERE (data_uso = '$data_saida' AND roteiro != '$destino' AND (situacao = '$status[0]' OR situacao = '$status[1]'))");
                            $countSolicitDia = count($readSolicitDia);

                            if ($countSolicitDia == 0) {
                                ?>
                                    <a class="btn_voltarpg" href="pre_solicitar_viatura.php">Voltar</a>
                                    <a href="solicitar_viatura.php?dia_vai=<?php echo $saida_dia; ?>&mes_vai=<?php echo $saida_mes; ?>&ano_vai=<?php echo $saida_ano; ?>&destino_vai=<?php echo utf8_encode($destino); ?>&destino_2=<?php echo utf8_encode($destino_2); ?>&destino_3=<?php echo utf8_encode($destino_3); ?>"><h1 class="btn_newavanca">Prosseguir</h1></a>
                                <?php
                            } else {
                                ?>    			
                                <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
                                    <tr>&nbsp</tr>
                                    <tr class="tr_yellow">
                                        <td colspan="6" style="padding-left:5px;">Outras viagens agendadas para <?php echo date('d/m/Y', strtotime($data_saida)); ?></td>
                                    </tr>    
                                    <tr height="30px" style="background: #CCC;">
                                        <td align="left" style="padding-left:5px;" width="110px">Veiculo</td>
                                        <td align="left" style="padding-left:5px;">Servidor</td>
                                        <td align="center">Hora Saída</td>
                                        <td align="center" width="150px">Prev. Retorno</td>
                                        <td align="center">Destino</td>
                                        <td align="center">Carona</td>
                                    </tr>

                                    <?php
                                    foreach ($readSolicitDia as $solicitDia) {

                                        //Exclui placa do nome do veiculo e busca veículo na tabela veículos
                                        $mostra_veiculoSolic = explode("-", $solicitDia['veiculo']);
                                        $buscaVeiculoSolic = read('vt_veiculos', "WHERE placa = '$mostra_veiculoSolic[1]'");
                                        $countBuscaVeiculoSolic = count($buscaVeiculoSolic);
                                        if ($countBuscaVeiculoSolic == 1) {
                                            foreach ($buscaVeiculoSolic as $veiculoSolic)
                                                ;
                                        }
                                        ?>
                                        <tr class="lista_itens_viagens" style="height: 60px;">
                                            <td align="left" style="padding-left:5px; font-size:14px;"><?php echo $veiculoSolic['veiculo'] . '-' . $veiculoSolic['placa']; ?></td>
                                            <td align="left" style="padding-left:5px;"><?php echo $solicitDia['servidor']; ?></td>    
                                            <td align="center" style="color:#007FFF;"><?php echo date('H:i', strtotime($solicitDia['horario_uso'])); ?></td>
                                            <td align="center"><?php echo date('d/m/y', strtotime($solicitDia['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($solicitDia['prev_retorno_hora'])); ?></td>
                                            <td align="center"><?php echo $solicitDia['roteiro']; ?></td>
                                            <td align="center"><img src="../_assets/img/ico_carona_nao.png" alt="Para solicitar carona nesta viagem, vá para a página 'Solicitações Agendadas'" title="Para solicitar carona nesta viagem, vá para a página 'Solicitações Agendadas'" width="25px" /></td>
                                        </tr>
                                        <?php
                                    }//fecha foreach	
                                    ?>
                                </table> 
                                    <a class="btn_voltarpg" href="pre_solicitar_viatura.php">Voltar</a>
                                    <a href="solicitar_viatura.php?dia_vai=<?php echo $saida_dia; ?>&mes_vai=<?php echo $saida_mes; ?>&ano_vai=<?php echo $saida_ano; ?>&destino_vai=<?php echo utf8_encode($destino); ?>&destino_2=<?php echo utf8_encode($destino_2); ?>&destino_3=<?php echo utf8_encode($destino_3); ?>"><h1 class="btn_newavanca">Prosseguir</h1></a>
                                <?php
                            }
                            //Se NÃO há viagens para o destino selecionado na data	
                        } else {
                            //Conta as solicitações do dia que são para outro destino
                            $readContaSolicitacoes = read('vt_solicitacoes', "WHERE (data_uso = '$data_saida' AND roteiro != '$destino' AND (situacao = '$status[0]' OR situacao = '$status[1]'))");
                            $countSolicitacoes = count($readContaSolicitacoes);
                            if ($countSolicitacoes == 0) {
                                header('Refresh: 0; url=solicitar_viatura.php?dia_vai=' . $saida_dia . '&mes_vai=' . $saida_mes . '&ano_vai=' . $saida_ano . '&destino_vai=' . $destino . '&destino_2=' . $destino_2 . '&destino_3=' . $destino_3);
                            } else {
                                ?>
                                <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
                                    <tr class="tr_header">
                                        <td colspan="6" style="padding-left:5px;">Viagens agendadas para <?php echo date('d/m/Y', strtotime($data_saida)); ?></td>
                                    </tr>    
                                    <tr height="30px" style="background: #CCC;">
                                        <td align="left" style="padding-left:5px;" width="110px">Veiculo</td>
                                        <td align="left" style="padding-left:5px;">Servidor</td>
                                        <td align="center">Hora Saída</td>
                                        <td align="center" width="150px">Prev. Retorno</td>
                                        <td align="center">Destino</td>
                                        <td align="center">Carona</td>
                                    </tr>
                                    <?php
                                    foreach ($readContaSolicitacoes as $solicitacoes) {
                                        //Exclui placa do nome do veiculo e busca veículo na tabela veículos
                                        $mostra_veiculoSolic = explode("-", $solicitacoes['veiculo']);
                                        $buscaVeiculoSolic = read('vt_veiculos', "WHERE placa = '$mostra_veiculoSolic[1]'");
                                        $countBuscaVeiculoSolic = count($buscaVeiculoSolic);
                                        if ($countBuscaVeiculoSolic == 1) {
                                            foreach ($buscaVeiculoSolic as $veiculoSolic)
                                                ;
                                        }
                                        ?>
                                        <tr height="50px" style="font:12px Tahoma, Geneva, sans-serif;">
                                            <td align="left" style="padding-left:5px;"><?php echo $veiculoSolic['veiculo'] . '-' . $veiculoSolic['placa']; ?></td>
                                            <td align="left" style="padding-left:5px;"><?php echo $solicitacoes['servidor']; ?></td>    
                                            <td align="center" style="color:#007FFF;"><?php echo date('H:i', strtotime($solicitacoes['horario_uso'])); ?></td>
                                            <td align="center"><?php echo date('d/m/y', strtotime($solicitacoes['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($solicitacoes['prev_retorno_hora'])); ?></td>
                                            <td align="center">
                                                <?php
                                                if ($solicitacoes['roteiro_3'] != '') {
                                                    echo $solicitacoes['roteiro'] . '<br /><span style="font-size:10px; color:#000; width:100%"> (' . $solicitacoes['roteiro_2'] . ', ' . $solicitacoes['roteiro_3'] . ')</span>';
                                                } elseif ($solicitacoes['roteiro_2'] != '' && $solicitacoes['roteiro_3'] == '') {
                                                    echo $solicitacoes['roteiro'] . '<br /><span style="font-size:10px; color:#000; width:100%"> (' . $solicitacoes['roteiro_2'] . ')</span>';
                                                } else {
                                                    echo $solicitacoes['roteiro'];
                                                }
                                                ?>
                                            </td>
                                            <td align="center">
                                                <img src="../_assets/img/ico_carona_nao.png" alt="Para solicitar carona nesta viagem, vá para a página 'Solicitações Agendadas'" title="Para solicitar carona nesta viagem, vá para a página 'Solicitações Agendadas'" width="25px" />
                                            </td>
                                        </tr>  
                                        <?php
                                    }//fecha foreach	
                                    ?>
                                </table> 
                                <a class="btn_voltarpg" href="pre_solicitar_viatura.php">Voltar</a>
                                <a href="solicitar_viatura.php?dia_vai=<?php echo $saida_dia; ?>&mes_vai=<?php echo $saida_mes; ?>&ano_vai=<?php echo $saida_ano; ?>&destino_vai=<?php echo utf8_encode($destino); ?>&destino_2=<?php echo utf8_encode($destino_2); ?>&destino_3=<?php echo utf8_encode($destino_3); ?>"><h1 class="btn_newavanca">Prosseguir</h1></a>
                                <?php
                            }
                        }
                    }
                }
            }//fecha em branco
        }//fecha avançar
    }
    ?>
    <?php
        if ($existeViagem != 1):
    ?>
    <form name="solicita_viatura" id="data_consulta_solic" method="post" action="" class="form_seleciona_data">
        <fieldset class="user_solicita_viatura">

            <label class="informa_data_uso" onmousedown="stopCarona()" style="float:left;">
                <span><i class="fa fa-calendar"></i> Data:</span><br />
                <input type="text" id="calendario" class="seleciona_data" name="calendario" readonly="true"
                    value="<?php
                    if(isset($_POST['calendario'])){
                        echo $_POST['calendario'];
                    }
                    ?>"
                />
            </label>            

            <label>
                <span><i class="fa fa-map-marker"></i> Destino <i class="fa fa-caret-right"></i> 1:</span><br />
                <select name="estado" id="estado" class="select_estado j_loadstate">
                    <option value="" disabled="disabled">UF</option>
                    <option value="RS" selected>RS</option>
                    <option value="<?php if($_POST['estado'] != ""){ echo $_POST['estado']; } ?>" <?php if($_POST['estado'] != ""){ echo "selected"; } ?>> <?php if(isset($_POST['estado']) & $_POST['estado'] != ""){ echo $_POST['estado']; } ?> </option>
                    <?php
                    foreach ($readEstadoFirst as $select_estado) {
                        $estado_selecionado = $select_estado['estado_uf'];
                        ?>
                        <option value="<?php echo $estado_selecionado; ?>"><?php echo $select_estado['estado_uf']; ?></option>
                    <?php } ?>
                </select>
                <select class="selects_destinos j_loadcity" name="destino" id="destino">
                    <option disabled <?php if($_POST['destino'] == ""){ echo "selected"; } ?>> Selecione a cidade </option>
                    <option value="<?php if($_POST['destino'] != ""){ echo $dest_cidade; } ?>" <?php if($_POST['destino'] != ""){ echo "selected"; } ?>> <?php if(isset($_POST['destino']) & $_POST['destino'] != ""){ echo $dest_cidade; } ?> </option>
                    <?php
                    $readCityes = read("app_cidades", "WHERE cidade_uf = 'RS'");
                    foreach ($readCityes as $cidades):
                        $nome_cidade = utf8_encode($cidades['cidade_nome']);
                        echo "<option value=\"{$nome_cidade}\"> {$nome_cidade} </option>";
                    endforeach;
                    ?>
                </select>
                <i class="fa fa-plus-circle fa-2x" style="font-size: 1.5em; color: #398431; cursor: pointer;  margin-top: 19px; margin-left: 10px;" title="Adicionar outro destino" id="img_addDest_um" onclick="optionDestDois()"></i>             
            </label>

            <label>
                <div id="div_destino_dois">
                    <span><i class="fa fa-map-marker"></i> Destino <i class="fa fa-caret-right"></i> 2:</span><br />
                    <select name="estado_dois" id="estado_dois" class="select_estado j_loadstate_dois">
                        <option value="" disabled="disabled" selected>UF</option>
                        <?php
                        foreach ($readEstado as $select_estado) {
                            $estado_selecionado = $select_estado['estado_uf'];
                            ?>
                            <option value="<?php echo $estado_selecionado; ?>"><?php echo $select_estado['estado_uf']; ?></option>
                        <?php } ?>
                    </select>
                    <select class="selects_destinos j_loadcity_dois" name="destino_dois" id="destino_dois">
                        <option value="" selected disabled> Selecione antes um estado </option>
                    </select>
                    <i class="fa fa-plus-circle fa-2x" style="font-size: 1.5em; color: #398431; cursor: pointer; margin-top: 19px; margin-left: 10px; margin-right: 10px;" title="Adicionar outro destino" id="img_addDest_dois" onclick="optionDestTres()"></i>             
                    <i class="fa fa-minus-circle fa-2x" style="font-size: 1.5em; color: #F00; cursor: pointer;" title="Remover destino" id="img_remDest_dois" onclick="optionDelDestDois()"></i>             
                </div>
            </label>

            <label>
                <div id="div_destino_tres">
                    <span><i class="fa fa-map-marker"></i> Destino <i class="fa fa-caret-right"></i> 3:</span><br />
                    <select name="estado_tres" id="estado_tres" class="select_estado j_loadstate_tres">
                        <option value="" disabled="disabled" selected>UF</option>
                        <?php
                        foreach ($readEstado as $select_estado) {
                            $estado_selecionado = $select_estado['estado_uf'];
                            ?>
                            <option value="<?php echo $estado_selecionado; ?>"><?php echo $select_estado['estado_uf']; ?></option>
                        <?php } ?>
                    </select>
                    <select class="selects_destinos j_loadcity_tres" name="destino_tres" id="destino_tres">
                        <option value="" selected disabled> Selecione antes um estado </option>
                    </select>
                    <i class="fa fa-minus-circle" style="font-size: 1.5em; color: #F00; cursor: pointer; margin-top: 19px; margin-left: 10px;" title="Remover destino" id="img_remDest_tres" onclick="optionDelDestTres()"></i>             
                </div>
            </label>

            <input type="submit" name="avancar" value="Avançar >>" id="avancar" class="btn btn_green" style="color: #FFF;" />
        </fieldset>     	  
    </form><!--formulário-->
    <?php
        endif;
    ?>

    <script type="text/javascript">

        function optionDestDois() {
            var dest1 = document.getElementById("destino").value;
            if (dest1 != '') {
                document.getElementById("div_destino_dois").style.visibility = "visible";
                document.getElementById("div_destino_dois").style.marginTop = "10px";
                document.getElementById("img_remDest_dois").style.visibility = "visible";
                document.getElementById("img_addDest_dois").style.visibility = "visible";
                document.getElementById("img_addDest_um").style.visibility = "hidden";
            } else {
                alert('Informe o Destino Principal!');
            }
        }
        function optionDestTres() {
            var dest2 = document.getElementById("destino_dois").value;
            if (dest2 != '') {
                document.getElementById("div_destino_tres").style.visibility = "visible";
                document.getElementById("div_destino_tres").style.marginTop = "10px";
                document.getElementById("img_remDest_tres").style.visibility = "visible";
                document.getElementById("img_addDest_dois").style.visibility = "hidden";
                document.getElementById("img_remDest_dois").style.visibility = "hidden";
            } else {
                alert('Informe o Destino 2 antes de adiconar um terceiro destino!');
            }
        }

        function optionDelDestTres() {
            document.getElementById("div_destino_tres").style.visibility = "hidden";
            document.getElementById("div_destino_tres").style.marginTop = "-500px";
            document.getElementById("img_addDest_dois").style.visibility = "visible";
            document.getElementById("img_remDest_dois").style.visibility = "visible";
            document.getElementById("img_remDest_tres").style.visibility = "hidden";
            document.getElementById("destino_tres").value = "";
            document.getElementById("estado_tres").value = "";
        }
        function optionDelDestDois() {
            document.getElementById("destino").disabled = false;
            document.getElementById("div_destino_dois").style.visibility = "hidden";
            document.getElementById("div_destino_dois").style.marginTop = "-500px";
            document.getElementById("img_addDest_um").style.visibility = "visible";
            document.getElementById("img_remDest_dois").style.visibility = "hidden";
            document.getElementById("img_addDest_dois").style.visibility = "hidden";
            document.getElementById("destino_dois").value = "";
            document.getElementById("estado_dois").value = "";
        }

    </script><!--scripts de numero de passageiros-->

    <?php
}
include_once "includes/inc_footer.php";
?>