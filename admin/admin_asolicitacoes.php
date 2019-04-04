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
            $buscaSituacao = read('vt_solicitacoes', "WHERE id = '$idsolicit' AND (situacao = '$situacao[0]' OR situacao = '$situacao[1]' OR situacao = '$situacao[2]')");
            $countBuscaSituacao = count($buscaSituacao);
            if ($countBuscaSituacao <= 0):
                header('Location: admin_esolicitacoes.php');
            else:
                foreach ($buscaSolicitacao as $patr);

                //Se a solicitação for Nao Autorizada e a data de uso for menor que hoje
                if ($patr['situacao'] == 'Nao Autorizada' and $patr['data_uso'] < $hoje):
                    header('Location: admin_esolicitacoes.php');
                endif;

                //Lista passageiros no email
                if ($patr['passageiros'] == ''):
                    $pass = 'Não há passageiros!';
                else:
                    $pass = $patr['passageiros'];
                endif;
?>

<?php
                if (isset($_POST['confirmar'])):
                    $up['situacao'] = strip_tags(trim($_POST['sit']));
                    $up['veiculo'] = strip_tags(trim($_POST['radio_veiculos']));
                    $up['motivo'] = strip_tags(trim($_POST['motivo']));
                    $up['siape_aceitador'] = $_SESSION['autUser']['siape'];

                    if ($up['situacao'] == 'Autorizada'):
                        if ($up['veiculo'] != ''):
                            $up_atualiza = update('vt_solicitacoes', $up, "id = '$idsolicit'");
                            if ($up_atualiza):
                                //Envia E-mail para o Admin
                                $msg = '<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#666;">Olá ' . $patr['servidor'] . ',</p>
				<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que sua solicitação de viatura oficial de número <strong>' . $patr['id'] . '/' . date('Y') . '</strong> foi
				<span style="color:#093" font-size:18px><strong>&nbsp;&nbsp;&raquo; AUTORIZADA<strong></span></p><br /><br />
				Abaixo seguem os dados da referida solicitação:</p>
				<hr />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Veículo Liberado:</span> <span style="font:15px Tahoma, Geneva, sans-serif; color:#333;">' . $up['veiculo'] . '</span><br /><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitante:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['servidor'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['email'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Siape:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['siape'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['motorista'] . '</span><br /><br />
				
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['roteiro'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($patr['data_uso'])) . ' às ' . date('H:i', strtotime($patr['horario_uso'])) . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['finalidade'] . ' / ' . $patr['desc_finalidade'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($patr['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($patr['prev_retorno_hora'])) . '</span><br /><br />
				
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $pass . '<br /><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#06F;">Solicitada em <strong>' . date('d/m/Y', strtotime($patr['criadoEm'])) . '</strong> às <strong>' . date('H:i', strtotime($patr['criadoEm'])) . '</strong></span><br /><br />
				<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Você já pode imprimir a guia referente a esta solicitação através do seu painel no <a href="http://200.17.85.249">Sistema de Viaturas do Campus Feliz</a>.</p><br />';
                                sendMail('Solicitação de Viatura AUTORIZADA', $msg, MAILUSER, SITENAME, $patr['email'], $patr['servidor']);
                                
                                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação Autorizada!</h6>";
                                header('Refresh: 2;url=admin_asolicitacoes.php?idsolicit=' . $idsolicit);
                            else:
                                $ano_os = date('Y');
                                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao atualizar situação da solicitação nº <strong>{$idsolicit}/{$ano_os}<strong>!</h4>";
                            endif;
                        else:
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Selecione o veículo que será utilizado!</h4>";
                        endif;
                    endif;
                endif;
                
                if (isset($_POST['cancelar'])):
                    $up['situacao'] = strip_tags(trim($_POST['sitC']));
                    $up['veiculo'] = 'n/d';

                    if ($up['situacao'] == 'Cancelada'):
                        $up_atualiza = update('vt_solicitacoes', $up, "id = '$idsolicit'");
                        if ($up_atualiza):
                            //Envia E-mail para o Admin
                            $msg = '<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#666;">Olá ' . $patr['servidor'] . ',</p>
                            <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que sua solicitação de viatura oficial de número <strong>' . $patr['id'] . '/' . date('Y') . '</strong> foi
                            <span style="color:#F00" font-size:18px><strong>&nbsp;&nbsp;&raquo; CANCELADA<strong></span></p>
                            Abaixo seguem os dados da referida solicitação:</p><br /><br />
                            <hr />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitante:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['servidor'] . '</span><br />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['email'] . '</span><br />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Siape:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['siape'] . '</span><br />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['motorista'] . '</span><br /><br />

                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['roteiro'] . '</span><br />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($patr['data_uso'])) . ' às ' . date('H:i', strtotime($patr['horario_uso'])) . '</span><br />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['finalidade'] . ' / ' . $patr['desc_finalidade'] . '</span><br />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($patr['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($patr['prev_retorno_hora'])) . '</span><br /><br />

                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $pass . '<br /><br />
                            <span style="font:14px Tahoma, Geneva, sans-serif; color:#06F;">Solicitada em <strong>' . date('d/m/Y', strtotime($patr['criadoEm'])) . '</strong> às <strong>' . date('H:i', strtotime($patr['criadoEm'])) . '</strong></span><br /><br />
                            <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Você pode efetuar novas solicitações através do seu painel no <a href="http://200.17.85.249">Sistema de Viaturas do Campus Feliz</a>.</p><br />';

                            if ($up['situacao'] == 'Cancelada'):
                                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação Cancelada!</h6>";
                                header('Refresh: 2;url=admin_visusolicitacoes.php?idsolicit=' . $idsolicit);
                            else:
                                sendMail('Solicitação de Viatura CANCELADA', $msg, MAILUSER, SITENAME, $patr['email'], $patr['servidor']);
                                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação Cancelada!</h6>";
                                header('Refresh: 2;url=admin_visusolicitacoes.php?idsolicit=' . $idsolicit);
                            endif;
                        
                        else:
                            $ano_os = date('Y');
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao cancelar a solicitação nº <strong>{$idsolicit}/{$ano_os}<strong>!</h4>";
                        endif;
                    endif;
                endif;
                
                if (isset($_POST['nAutorizar'])):
                    $up['situacao'] = strip_tags(trim($_POST['sitN']));
                    $up['veiculo'] = 'n/d';
                    $up['motivo'] = strip_tags(trim($_POST['motivo']));

                    if ($up['situacao'] == 'Nao Autorizada'):
                        if ($up['motivo'] != ''):
                            $up_atualiza = update('vt_solicitacoes', $up, "id = '$idsolicit'");
                            if ($up_atualiza):
                                //Envia E-mail para o Admin
                                $msg = '<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#666;">Olá ' . $patr['servidor'] . ',</p>
				<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que sua solicitação de viatura oficial de número <strong>' . $patr['id'] . '/' . date('Y') . '</strong> foi
				<span style="color:#F00" font-size:18px><strong>&nbsp;&nbsp;&raquo; NEGADA<strong></span></p>
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#F00;">Motivo:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $up['motivo'] . '</span><br />
				Abaixo seguem os dados da referida solicitação:</p><br /><br />
				<hr />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitante:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['servidor'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['email'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Siape:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['siape'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['motorista'] . '</span><br /><br />
				
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['roteiro'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($patr['data_uso'])) . ' às ' . date('H:i', strtotime($patr['horario_uso'])) . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $patr['finalidade'] . ' / ' . $patr['desc_finalidade'] . '</span><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($patr['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($patr['prev_retorno_hora'])) . '</span><br /><br />
				
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $pass . '<br /><br />
				<span style="font:14px Tahoma, Geneva, sans-serif; color:#06F;">Solicitada em <strong>' . date('d/m/Y', strtotime($patr['criadoEm'])) . '</strong> às <strong>' . date('H:i', strtotime($patr['criadoEm'])) . '</strong></span><br /><br />
				<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Você pode efetuar novas solicitações através do seu painel no <a href="http://200.17.85.249">Sistema de Viaturas do Campus Feliz</a>.</p><br />';

                                if ($up['situacao'] == 'Cancelada'):
                                    echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação Cancelada!</h6>";
                                    header('Refresh: 2;url=admin_visusolicitacoes.php?idsolicit=' . $idsolicit);
                                else:
                                    sendMail('Solicitação de Viatura NEGADA', $msg, MAILUSER, SITENAME, $patr['email'], $patr['servidor']);
                                    echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação Não Autorizada!</h6>";
                                    header('Refresh: 2;url=admin_asolicitacoes.php?idsolicit=' . $idsolicit);
                                endif;
                            else:
                                $ano_os = date('Y');
                                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao atualizar situação da solicitação nº <strong>{$idsolicit}/{$ano_os}<strong>!</h4>";
                            endif;
                        else:
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Você precisa informar um motivo para não autorizar esta solicitação!</h4>";
                        endif;
                    endif;
                endif;
?>
    
            <form name="detalha_solicitacao" method="post" action="" enctype="multipart/form-data">
                <div class="detalhesGuia">
                    <h1><i class="fa fa-tag"></i> Solicitação <strong>Nº <?php echo $patr['id']; ?> </strong></h1>

                    <?php echo '<h2><i class="fa fa-clock-o"></i> solicitada em ' . date('d/m/Y', strtotime($patr['criadoEm'])) . ' às ' . date('H:i', strtotime($patr['criadoEm'])) . '</h2>'; ?>
                    
                    <!-- mostra a situação da GUIA-->
                    <?php
                    $dia_hoje = date('Y-m-d');

                    if ($patr['situacao'] == "Autorizada") {
                        echo "<div class='ms green' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-check-square-o fa-2x' style='color: #32A041;'></i>&nbsp;&nbsp;&nbsp; Autorizada &nbsp;&nbsp;&nbsp; <i class='fa fa-caret-right'></i> &nbsp;&nbsp;&nbsp; {$patr['veiculo']}</div>";
                        
                        if ($patr['situacao'] == 'Autorizada' && $patr['data_uso'] < $hoje) {
                            echo "<div class='ms al' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-share-square-o fa-2x' style='color: #F60;'></i>&nbsp;&nbsp;&nbsp; Esta solicitação precisa ser encerrada. Contate o solicitante através do email <b>{$patr['email']}</b> <div class='btn btn_red fl_right btn_cancelarGuia' style='padding: 15px;'>Cancelar esta guia</div></div><br />";
                        } else {
                            echo "<div class='btn btn_green fl_left btn_autorizarGuia' style='margin-right: 10px;'>Alterar Veículo</div>
                                  <div class='btn btn_red fl_left btn_naoAutorizarGuia' style='margin-right: 10px;'>Desautorizar</div>
                                  <div class='btn btn_orange fl_left btn_cancelarGuia'>Cancelar a Guia</div>
                                 ";
                        }                                                
                    } elseif ($patr['situacao'] == "Aguardando...") {
                        //Verifica se a solicitação não esta com a data de uso vencida
                        if ($patr['situacao'] == 'Aguardando...' && $patr['data_uso'] < $hoje) {
                            echo "<div class='ms al' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F93;'></i>&nbsp;&nbsp;&nbsp; Esta solicitação não pode mais ser autorizada por estar com a data de uso vencida! <div class='btn btn_red fl_right btn_cancelarGuia' style='padding: 15px;'>Cancelar esta guia</div></div><br />";
                        } else {
                            echo "<div class='ms al' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F93;'></i>&nbsp;&nbsp;&nbsp; Aguardando liberação</div><br />";
                            echo "<div class='btn btn_green fl_left btn_autorizarGuia' style='margin-right: 10px;'>AUTORIZAR</div>
                                  <div class='btn btn_red fl_left btn_naoAutorizarGuia' style='margin-right: 10px;'>NÃO AUTORIZAR</div>
                                  <div class='btn btn_orange fl_left btn_cancelarGuia'>CANCELAR</div>
                                 ";
                        }
                    } elseif ($part['situacao'] = 'Nao Autorizada') {
                        echo "<div class='ms no' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-times fa-2x' style='color: red;'></i>&nbsp;&nbsp;&nbsp; Não Autorizada";
                        echo "<h4><i class='fa fa-angle-double-right' style='margin-left: -30px; margin-top: 3px;'></i> {$patr['motivo']}</h4>";
                        echo "</div>";
                        
                        if ($patr['situacao'] == 'Nao Autorizada' && $patr['data_uso'] >= $hoje) {
                            echo "<div class='btn btn_green fl_left btn_autorizarGuia' style='margin-right: 10px;'>Autorizar</div>
                                  <div class='btn btn_orange fl_left btn_cancelarGuia'>Cancelar a Guia</div>
                                 ";
                        }   
                    }
                    ?>
                    <!-- encerra espaço que mostra a situação da GUIA-->
                    
                <!-- inicia Div's de autorização, negação ou cancelamento de guias -->
                <div class="autoriza_guias">

                    <div class="autorizarGuia">	
                        <span class="tituloAutorizaGuia">Selecione o veículo<span class="fechar_listaVeiculos">x</span></span>
                        
                        <div class="listaVeiculos">
                            <?php
                            $buscaVeiculo = read('vt_veiculos', "WHERE deletado = '0'");
                            foreach ($buscaVeiculo as $select_veiculo) {
                                $veiculo_selecionado = $select_veiculo['veiculo'] . '-' . $select_veiculo['placa'];
                                $veiculo_label = $select_veiculo['veiculo'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $select_veiculo['placa'];
                                $time_inicial = strtotime($select_veiculo['seguro']);
                                $time_final = strtotime($hoje);
                                $dif_seguro = $time_inicial - $time_final;
                                $dias_seguro = (int) floor($dif_seguro / (60 * 60 * 24));
                                ?>

                                <span class="radio_veiculo">
                                    <input name="radio_veiculos" type="radio" id="<?php echo $select_veiculo['id']; ?>" value="<?php echo $veiculo_selecionado; ?>"
                                        <?php
                                        //Script para desativar as viaturas que não podem ser liberadas por estarem sendo utilizadas no horário solicitado da referida solicitação que esta sendo liberada
                                        if ($select_veiculo['ativo'] != 1):
                                            echo 'disabled';
                                            $label_ativo = '<br /><h1 class="label_ativo"><i class="fa fa-exclamation-circle"></i> Veículo Desativado</h1>';
                                         else:
                                             $label_ativo = NULL;
                                         endif;

                                         if ($dias_seguro < 0):
                                             $label_seguro = '<br /><h1 class="label_seguro"><i class="fa fa-exclamation-circle"></i> Seguro Vencido</h1>';
                                         else:
                                             $label_seguro = NULL;
                                         endif;

                                        $data_usoVerif = $patr['datetime_saida'];
                                        $data_retornoVerif = $patr['datetime_retorno'];


                                        //Faz consulta se veiculo esta ocupado ou nao naquele horario
                                        $ocupado = read('vt_solicitacoes', "WHERE (situacao = 'Autorizada' AND veiculo='{$veiculo_selecionado}') "
                                                . "AND (('$data_usoVerif' >= datetime_saida AND '$data_retornoVerif' <= datetime_retorno)"
                                                . "OR ('$data_usoVerif' <= datetime_retorno AND '$data_retornoVerif' >= datetime_retorno)"
                                                . "OR ('$data_usoVerif' <= datetime_saida AND '$data_retornoVerif' >= datetime_retorno)"
                                                . "OR ('$data_usoVerif' <= datetime_saida AND '$data_retornoVerif' >= datetime_saida))");

                                        $ocupado = count($ocupado);

                                        $label_ocupado = NULL;

                                        if ($ocupado >= 1) {

                                            echo 'disabled';
                                            $label_ocupado = '<br /><h1 class="label_ocupado">Ocupado</h1>';
                                        }
                                        ?>     
                                    /><!--fecha o input radio veiculos-->
                                    <label for="<?php echo $select_veiculo['id']; ?>">
                                        <?php echo $veiculo_label; ?>
                                        <?php echo $label_ocupado; ?>
                                        <?php echo $label_ativo; ?>
                                        <?php echo $label_seguro; ?>
                                    </label>
                                </span>
                            <?php } ?>                
                        </div>
                        <input type="hidden" name="sit" value="Autorizada" />
                        <input type="submit" name="confirmar" value="CONFIRMAR" id="confirmar" class="btn btn_green fl_right" />
                    </div>

                    <div class="naoAutorizarGuia">
                        <span class="tituloNaoAutorizaGuia">Informe um motivo para não autorizar<span class="fechar_motivo">x</span></span>
                        <label>
                            <textarea id="motivo" name="motivo" rows="4" cols="50" class="textarea_autoriza" /></textarea>
                        </label>
                        <input type="hidden" name="sitN" value="Nao Autorizada" />
                        <input type="submit" name="nAutorizar" value="CONFIRMAR" id="confirmar" class="btn btn_red fl_right" /> 
                    </div> 

                    <div class="cancelaGuia">
                        <span class="tituloCancelaGuia">Confirma o cancelamento da guia nº <?php echo $patr['id']; ?>?<span class="fechar_cancela">x</span></span>
                        <input type="hidden" name="sitC" value="Cancelada" />
                        <div class="espaco_cancela">
                            <input type="submit" name="cancelar" value="CONFIRMAR" id="confirmar" class="btn btn_orange fl_center" /> 
                        </div>
                    </div>
                </div>
                <!-- encerra Div's de autorização, negação ou cancelamento de guias -->
                    
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