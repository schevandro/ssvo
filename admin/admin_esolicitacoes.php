<?php
include_once "includes/inc_header.php";

//Selecionar os servidores
$buscaServidor = read('servidores', "WHERE ativo = 1 ORDER BY nome ASC");

//Selecionar os veículos
$buscaVeiculo = read('vt_veiculos', "WHERE deletado = 0 ORDER BY veiculo ASC");

//Outros dados
$hoje = date('Y-m-d');
$status_solicitacao = array('Aguardando...', 'Autorizada', 'Nao Autorizada', 'Cancelada', 'Encerrada');

//PAGINAÇÃO
$pag = $_GET["pag"];
if ($pag >= '1'):
    $pag = $pag;
else:
    $pag = '1';
endif;
$maximo = '30'; //resultados por páginas
$inicio = ($pag * $maximo) - $maximo;

/* Filtro por data */
if (isset($_POST['sendFiltroData'])):
    $pegaData = $_POST['input_searchData'];
    if ($pegaData != ''):
        $dataFiltro = explode('/', $pegaData);
        $diaFiltro = $dataFiltro[0];
        $mesFiltro = $dataFiltro[1];
        $anoFiltro = $dataFiltro[2];
        $dataFiltro = $anoFiltro . '-' . $mesFiltro . '-' . $diaFiltro;
        $readSolicitacoes = read('vt_solicitacoes', "WHERE (data_uso = '$dataFiltro' AND situacao != '') ORDER BY horario_uso ASC LIMIT $inicio,$maximo");
        $contaSolicitacoes = read('vt_solicitacoes', "WHERE (data_uso = '$dataFiltro' AND situacao != '')");
        $wherePrint = "WHERE (data_uso = '$dataFiltro' AND situacao != '') ORDER BY criadoEm DESC";
        $mandaFiltro = 'Solicitações do dia ' . $pegaData;
        $numPag = count($contaSolicitacoes);
        $listando = $pegaData;
    else:
        //$dataFiltro = date('d/m/Y');
        $readSolicitacoes = read('vt_solicitacoes', "WHERE (situacao != '') ORDER BY data_uso ASC LIMIT $inicio,$maximo");
        $contaSolicitacoes = read('vt_solicitacoes', "WHERE (data_uso = '$dataFiltro' AND situacao != '')");
        $mandaFiltro = 'Solicitações de hoje - ' . $dataFiltro;
        $wherePrint = "WHERE (data_uso >= '$dataFiltro' AND situacao != '') ORDER BY criadoEm DESC";
        $numPag = count($contaSolicitacoes);
        $listando = $pegaData;
    endif;
else:
    //Lista somente as que estão aguardando, se selecionadas no link <Aguardando>
    if ($_GET['solit'] == 'aberto'):
        $readSolicitacoes = read('vt_solicitacoes', "WHERE situacao = '$status_solicitacao[0]' ORDER BY criadoEm DESC LIMIT $inicio,$maximo");
        $contaSolicitacoes = read('vt_solicitacoes', "WHERE situacao = '$status_solicitacao[0]'");
        $mandaFiltro = 'Aguardando autorização';
        $wherePrint = "WHERE situacao = '$status_solicitacao[0]' ORDER BY criadoEm DESC";
        $numPag = count($contaSolicitacoes);
        $listando = 'Aguardando autorização';
    else:
        $readSolicitacoes = read('vt_solicitacoes', "ORDER BY data_uso DESC LIMIT $inicio,$maximo");
        $contaSolicitacoes = read('vt_solicitacoes');
        $mandaFiltro = 'Todas';
        $wherePrint = "ORDER BY criadoEm DESC";
        $numPag = count($contaSolicitacoes);
        $listando = 'Todas';
    endif;
endif;

/* Filtro por servidor */
if (isset($_POST['sendFiltroServidor'])):
    $pegaServidor = $_POST['input_searchServidor'];
    if ($pegaServidor != ''):
        $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$pegaServidor' AND situacao != '') ORDER BY data_uso DESC, horario_uso DESC LIMIT $inicio,$maximo");
        $contaSolicitacoes = read('vt_solicitacoes', "WHERE (siape = '$pegaServidor' AND situacao != '')");
        $wherePrint = "WHERE (siape = '$pegaServidor' AND situacao != '') ORDER BY criadoEm DESC";
        $mandaFiltro = 'Solicitações do servidor: ' . $pegaServidor;
        $numPag = count($contaSolicitacoes);
        //Mostrar nome do servidor no filtro
        foreach ($readSolicitacoes as $nomeServidor)
            ;
        $listando = $nomeServidor['servidor'];
    else:
        $readSolicitacoes = read('vt_solicitacoes', "WHERE (siape = 'sem_servidor')");
        $wherePrint = "WHERE (siape = 'sem_servidor')";
        $numPag = '';
        $listando = 'Selecione um servidor';
    endif;
endif;

/* Filtro por veículo */
if (isset($_POST['sendFiltroVeiculo'])):
    $pegaVeiculo = $_POST['input_searchVeiculo'];
    if ($pegaVeiculo != ''):
        $readSolicitacoes = read('vt_solicitacoes', "WHERE (veiculo = '$pegaVeiculo' AND situacao != '') ORDER BY data_uso DESC, horario_uso DESC LIMIT $inicio,$maximo");
        $contaSolicitacoes = read('vt_solicitacoes', "WHERE (veiculo = '$pegaVeiculo' AND situacao != '')");
        $wherePrint = "WHERE (veiculo = '$pegaVeiculo' AND situacao != '') ORDER BY criadoEm DESC";
        $mandaFiltro = 'Solicitações do veículo <strong>' . $pegaVeiculo . '</strong>';
        $numPag = count($contaSolicitacoes);
        $listando = $pegaVeiculo;
    else:
        $readSolicitacoes = read('vt_solicitacoes', "WHERE (veiculo = 'sem_veiculo')");
        $wherePrint = "WHERE (veiculo = 'sem_veiculo')";
        $numPag = '';
        $listando = 'Selecione um veículo';
    endif;
endif;
?>


<?php
    if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):
?>  
        <div class="bloco_search">

            <div class="btns_buscas">
                <h2><i class="fa fa-filter"></i> Filtros:</h2>
                <a href="#"><h1 class="btn_buscaData">Data</h1></a>
                <a href="#"><h1 class="btn_buscaServidor">Servidor</h1></a>
                <a href="#"><h1 class="btn_buscaVeiculo">Veículo</h1></a>
                <a href="#" title="Fechar Filtros"><h3 class="btn_fechar">X</h3></a>
            </div>

            <div class="search_data">
                <form name="sdata" action="" method="post">
                    <label>
                        <span>Selecione uma data:</span>
                        <input type="text" name="input_searchData" id="input_searchData" maxlength="0" value="" onclick="if (this.value == 'Data') this.value = ''" />
                    </label>
                    <input type="submit" value="Ok" name="sendFiltroData" class="btn" />
                </form>          
            </div>

            <div class="search_servidor">
                <form name="sservidor" action="" method="post">
                    <label>
                        <span>Selecione um servidor:</span>
                        <select name="input_searchServidor" id="servidor">
                            <option value="" selected>Servidores</option>
                            <?php foreach ($buscaServidor as $busca) {
                            $busca_selecionada = $busca['nome'];
                            $siape_cond = $busca['siape']; ?>
                            <option value="<?php echo $siape_cond; ?>"><?php echo $busca_selecionada; ?></option>;
                            <?php
                            }
                            ?>
                        </select>
                    </label>
                    <input type="submit" value="Ok" name="sendFiltroServidor" class="btn" />
                </form> 
            </div>

            <div class="search_veiculo">
                <form name="sveiculo" action="" method="post">
                    <label>
                        <span>Selecione um veículo:</span>
                        <select name="input_searchVeiculo" id="veiculo">
                            <option value="" disabled="disabled" selected>Veículos</option>
                            <?php foreach ($buscaVeiculo as $veic) {
                            $veic_selecionada = $veic['veiculo'];
                            $veic_placa = $veic['placa']; ?>
                            <option value="<?php echo $veic_selecionada . '-' . $veic_placa; ?>"><?php echo $veic_selecionada; ?></option>;
                            <?php
                            }
                            ?>
                        </select>
                    </label>
                    <input type="submit" value="Ok" name="sendFiltroVeiculo" class="btn" />
                </form> 
            </div>
        </div> 

        <div class="submenu">
            <?php
            //Verifica se exitem solicitações aguardando aprovação
            $readAguardando = read('vt_solicitacoes', "WHERE situacao = 'Aguardando...' ORDER BY criadoEm DESC");
            $numAguardando  = count($readAguardando);

            if ($numAguardando >= 1):
                echo '<a href="admin_esolicitacoes.php?solit=aberto"><h3><i class="fa fa-clock-o"></i> &nbsp;&nbsp;&nbsp;' . $numAguardando . ' Aguardando Aprovação</h3></a>';
            else:
                echo '<h3 class="semAguardando">0 Aguardando Aprovação</h3>';
            endif;
            ?>
            <a href="admin_esolicitacoes.php"><h5>Listar Todas</h5></a>
            <a href="#"><h4 class="listando"><strong><i class="fa fa-bars"></i> Listando:</strong>  <?php echo $listando . '&nbsp;&nbsp;<i class="fa fa-caret-right"></i>&nbsp;&nbsp;' . $numPag; ?></h4></a>
        </div>

        <?php
        if ($readSolicitacoes <= 0) {
            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Não existe nenhuma solicitação cadastrada no sistema com essa descrição!</h4>";
        } else {
        ?> 
            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
                <tr class="tr_header">
                    <td align="center">ID / Ano</td>
                    <td align="center">Doc</td>
                    <td align="left" style="padding-left:10px">Nome</td>
                    <td align="center">Destino</td>
                    <td align="center">Saída</td>
                    <td align="center">Retorno</td>
                    <td align="center">Situação</td>
                    <td align="center">Veículo</td>
                    <td align="center" colspan="2">Ações</td>
                </tr>

                <?php
                foreach ($readSolicitacoes as $readSol) {
                   $colorPage++;
                    if ($colorPage % 2 == 0):
                        $cor = 'style="background:#f3f3f3;"';
                    else:
                        $cor = 'style="background:#fff;"';
                    endif;
                ?>
                <tr <?php echo $cor; ?> class="lista_itens">
                    <td align="center" style="font-size: 0.875em; font-weight: 600;"><?php echo $readSol['id'] . ' / ' . date('y', strtotime($readSol['criadoEm'])); ?></td>
                    <td align="center">
                        <?php
                            if($readSol['comprovante'] != ''):
                                echo "<a href='../admin/documentos/comprovantes_guia/{$readSol['comprovante']}' target='_blank' title='Visualizar o comprovante' style='color: #09F; font-size: 1.4em;'><i class='fa fa-file-pdf-o'></i></a>";  
                            else:
                                echo "<i class='fa fa-file-o' style='color: #666; font-size: 1.4em;' title='Não há comprovante para esta viagem'></i>";  
                            endif;
                        ?>
                    </td>
                    <td align="left" style="padding-left:10px;">
                        <?php
                        $abrServidor = explode(' ', $readSol['servidor']);
                        $escreveAbr = $abrServidor[0] . ' ' . $abrServidor[1] . ' ' . $abrServidor[2];
                        if ($readSol['passageiros'] != "") {
                            $separa_passageiros = explode(",", $readSol['passageiros']);
                            $conta_passageiros = count($separa_passageiros);
                            $comCarona = "<span class='com_carona' title='{$conta_passageiros} Carona(s)'>+{$conta_passageiros}</span>";
                            echo $escreveAbr . '&nbsp;&nbsp;&nbsp; ' . $comCarona;
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
                    <?php
                        if ($readSol['data_uso'] < $hoje) {
                            echo '<td align="center" style="color:#999;">' . date('d/m', strtotime($readSol['data_uso'])) . ' às ' . date('H:i', strtotime($readSol['horario_uso'])) . '</td>';
                        } elseif ($readSol['data_uso'] >= $hoje) {
                            echo '<td align="center" style="color:#32A041;">' . date('d/m', strtotime($readSol['data_uso'])) . ' às ' . date('H:i', strtotime($readSol['horario_uso'])) . '</td>';
                        }
                        
                        if ($readSol['prev_retorno_data'] < $hoje) {
                            echo '<td align="center" style="color:#999;">' . date('d/m', strtotime($readSol['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($readSol['prev_retorno_hora'])) . '</td>';
                        } elseif ($readSol['prev_retorno_data'] >= $hoje) {
                            echo '<td align="center" style="color:#32A041;">' . date('d/m', strtotime($readSol['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($readSol['prev_retorno_hora'])) . '</td>';
                        }
                    ?>

                    <?php
                    if ($readSol['situacao'] == $status_solicitacao[0]) {
                        echo '<td align="center"><i class="fa fa-exclamation-triangle fa-2x" style="color: #FFBA75;" title="Aguardando liberação" alt="Aguardando liberação"></i></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[1]) {
                        echo '<td align="center"><i class="fa fa-check-square-o fa-2x" style="color: green;" title="Solicitação autorizada" alt="Solicitação liberada"></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[2]) {
                        echo '<td align="center"><i class="fa fa-close fa-2x" style="color: #FF5959;" title="Solicitação não autorizada" alt="Solicitação não autorizada"></i></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[3]) {
                        echo '<td align="center"><img src="../_assets/img/ico_cancelada.png" width="75px" title="Solicitação cancelada" alt="Solicitação cancelada"></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[4]) {
                        echo '<td align="center"><img src="../_assets/img/ico_encerrada.png" width="75px" title="Solicitação encerrada" alt="Solicitação encerrada"></td>';
                    }
                    ?>

                    <td align="center">
                        <?php
                        if ($readSol['veiculo'] == 'n/d' && ($readSol['situacao'] != 'Cancelada' && $readSol['situacao'] != 'Nao Autorizada')) {
                            echo '<i class="fa fa-exclamation-triangle fa-2x" style="color: #FFBA75;" title="Aguardando liberação" alt="Aguardando liberação"></i>';
                        } elseif ($readSol['situacao'] == 'Cancelada' || $readSol['situacao'] == 'Nao Autorizada') {
                            echo '-';
                        } else {
                            echo $readSol['veiculo'];
                        }
                        ?>
                    </td>
                    
                    <?php
                    if ($readSol['situacao'] == $status_solicitacao[0]) {
                        echo '<td align="center" colspan="2" width="140px"><a href="admin_asolicitacoes.php?idsolicit=' . $readSol['id'] . '"';
                        echo 'title="Editar/Alterar a situação" style="text-decoration:none; color:#033; font-size: 1.7em;"><i class="fa fa-pencil-square-o"></i></a></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[1] && $readSol['data_uso'] >= $hoje) {
                        echo '<td align="right" width="70px"><a href="admin_asolicitacoes.php?idsolicit=' . $readSol['id'] . '"';
                        echo 'title="Editar/Alterar a situação" style="text-decoration:none; color:#033; font-size: 1.7em; margin-right: 10px;"><i class="fa fa-pencil-square-o"></i></a></td>';
                        echo '<td align="left" width="70px"><a href="print2.php?id=' . $readSol['id'] . '" target="_blank" title="Imprimir a guia" style="text-decoration:none; color:#32A041; font-size: 1.7em; margin-left: 10px;">';
                        echo '<i class="fa fa-print"></i></a></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[1] && $readSol['data_uso'] < $hoje) {
                        echo '<td align="center" colspan="2" width="140px"><a href="admin_visusolicitacoes.php?idsolicit=' . $readSol['id'] . '"';
                        echo 'title="Visualizar dados da solicitação" style="text-decoration:none; color:#033; font-size: 1.7em; margin-right: 10px;"><i class="fa fa-eye"></i></a></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[2] && $readSol['data_uso'] >= $hoje) {
                        echo '<td align="center" colspan="2" width="140px"><a href="admin_asolicitacoes.php?idsolicit=' . $readSol['id'] . '"';
                        echo 'title="Editar/Alterar a situação" style="text-decoration:none; color:#033; font-size: 1.7em;"><i class="fa fa-pencil-square-o"></i></a></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[2] && $readSol['data_uso'] < $hoje) {
                        echo '<td align="center" colspan="2" width="140px"><a href="admin_visusolicitacoes.php?idsolicit=' . $readSol['id'] . '"';
                        echo 'title="Visualizar dados da solicitação" style="text-decoration:none; color:#033; font-size: 1.7em; margin-right: 10px;"><i class="fa fa-eye"></i></a></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[3]) {
                        echo '<td align="center" colspan="2" width="140px"><a href="admin_visusolicitacoes.php?idsolicit=' . $readSol['id'] . '"';
                        echo 'title="Visualizar dados da solicitação" style="text-decoration:none; color:#033; font-size: 1.7em; margin-right: 10px;"><i class="fa fa-eye"></i></a></td>';
                    } elseif ($readSol['situacao'] == $status_solicitacao[4]) {
                        echo '<td align="center" colspan="2" width="140px"><a href="admin_visusolicitacoes.php?idsolicit=' . $readSol['id'] . '"';
                        echo 'title="Visualizar dados da solicitação" style="text-decoration:none; color:#033; font-size: 1.7em; margin-right: 10px;"><i class="fa fa-eye"></i></a></td>';
                    }
                    ?>                       
                </tr>
            <?php
                }
            ?>
            </table>

    <div id="paginator">
<?php
        //PAGINAÇÃO
        $total = $numPag;

        $paginas = ceil($total / $maximo);
        $links = '10'; //QUANTIDADE DE LINKS NO PAGINATOR

        echo '<a href=admin_esolicitacoes.php?solit=' . $_GET['solit'] . '&pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;';

        for ($i = $pag - $links; $i <= $pag - 1; $i++):
            if ($i <= 0):
            else:
                echo'<a href=admin_esolicitacoes.php?solit=' . $_GET['solit'] . '&pag=' . $i . '>' . $i . '</a>&nbsp;&nbsp;&nbsp;';
            endif;
        endfor;
        echo "<h1>$pag</h1>";

        for ($i = $pag + 1; $i <= $pag + $links; $i++):
            if ($i > $paginas):
            else:
                echo '<a href=admin_esolicitacoes.php?solit=' . $_GET['solit'] . '&pag=' . $i . '>' . $i . '</a>&nbsp;&nbsp;&nbsp;';
            endif;
        endfor;
            echo '<a href=admin_esolicitacoes.php?solit=' . $_GET['solit'] . '&pag=' . $paginas . '>Última</a>&nbsp;&nbsp;&nbsp;';
    }//fecha else se não há nenhuma solicitação com a descrição informada
?>
    </div>
<?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
include_once "includes/inc_footer.php";
?>