<?php
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";
?>

<?php
//PAGINAÇÃO
$pag = $_GET["pag"];
if ($pag >= '1') {
    $pag = $pag;
} else {
    $pag = '1';
}

$maximo = '30'; //RESULTADOS POR PÁGINA
$inicio = ($pag * $maximo) - $maximo;

$hoje = date('Y-m-d');
$hojehora = date('H:i');
$cancelada = 'Cancelada';
$status = array('Autorizada', 'Aguardando...', 'Nao Autorizada', 'Cancelada', 'Encerrada');

if (isset($_POST['sendFiltro'])) {
    $pegaData = $_POST['search_data'];
    if ($pegaData != '') {
        $dataFiltro = explode('/', $pegaData);
        $diaFiltro = $dataFiltro[0];
        $mesFiltro = $dataFiltro[1];
        $anoFiltro = $dataFiltro[2];
        $dataFiltro = $anoFiltro . '-' . $mesFiltro . '-' . $diaFiltro;
        $where = "WHERE (data_uso = '$dataFiltro' AND (situacao = '$status[0]' OR situacao = '$status[1]')) ORDER BY horario_uso ASC LIMIT $inicio,$maximo";
        $whereTotal = "WHERE (data_uso = '$dataFiltro' AND (situacao = '$status[0]' OR situacao = '$status[1]'))";
        $readTotal = read('vt_solicitacoes', $whereTotal);
        $num = count($readTotal);
        $msg_lista = date('d/m/Y', strtotime($dataFiltro));
    } else {
        $dataFiltro = $hoje;
        $where = "WHERE (data_uso >= '$dataFiltro' AND (situacao = '$status[0]' OR situacao = '$status[1]')) ORDER BY data_uso ASC LIMIT $inicio,$maximo";
        $whereTotal = "WHERE (data_uso >= '$dataFiltro' AND (situacao = '$status[0]' OR situacao = '$status[1]'))";
        $readTotal = read('vt_solicitacoes', $whereTotal);
        $num = count($readTotal);
        $msg_lista = 'Todas';
    }
} else {
    $where = "WHERE (data_uso >= '$hoje' AND (situacao = '$status[0]' OR situacao = '$status[1]')) ORDER BY data_uso ASC LIMIT $inicio,$maximo";
    $whereTotal = "WHERE (data_uso >= '$hoje' AND (situacao = '$status[0]' OR situacao = '$status[1]'))";
    $readTotal = read('vt_solicitacoes', $whereTotal);
    $num = count($readTotal);
    $msg_lista = 'Todas';
}
?>

<!--Conteudo das páginas -->

    <h1 class="titulo-secao-medio"><i class="fa fa-calendar-check-o"></i> Solicitações Agendadas</h1>   
    <div class="solicitacoes_search">
        <form name="filtro" action="" method="post" class="form_search">
            <label class="input_search_by_data">
                <input type="text" name="search_data" id="search_data" maxlength="0" size="30" value="" 
                    onclick="if (this.value == 'Data')
                        this.value = ''" placeholder="Selecione a data"
                    />
                <input type="submit" value="ir" name="sendFiltro" class="btn btn_green" />
            </label>
        </form>
        
        <div class="msg_search_lista">
            <?php
                $readBusca = read('vt_solicitacoes', $where);
                echo "<h1><i class='fa fa-list' style='color: #000;'></i> {$msg_lista}&nbsp;&nbsp;[&nbsp;{$num}&nbsp;]</h1>";
            ?>
           
        </div>
    </div>
    

    <?php
    if ($num <= 0) {
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle'></i>&nbsp&nbsp&nbsp Nenhuma solicitação encontrada!</h4>";
    } else {
        ?>

        <?php include_once ('codes/solicita_carona.php'); ?><!--inclui o script solicita_carona-->

        <?php
        if (isset($_GET['solic']) && $_GET['solic'] == 'true') {
            echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação de Carona Enviada com Sucesso! Aguarde a resposta do titular da GUIA.</h4>";
            header('Refresh: 2;url=solicitacoes_all.php');
        }
        ?>

            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
                <tr class="tr_header">
                    <td align="center">ID</td>
                    <td align="left" style="padding-left:5px">Servidor</td>
                    <td align="center">Saída / Retorno</td>
                    <td align="center">Destino</td>
                    <td align="center">Situação</td>
                    <td align="center">Veículo</td>
                    <td align="center">Carona</td>
                </tr>
            <?php
            }
            if ($num >= 1) {
                foreach ($readBusca as $busca) {
                    $saidaData      = date('d-m', strtotime($busca['data_uso']));
                    $retornoData    = date('d-m', strtotime($busca['prev_retorno_data']));
                    $saidahora      = date('H:i', strtotime($busca['horario_uso']));
                    $retornoHora    = date('H:i', strtotime($busca['prev_retorno_hora']));

                    $colorPage++;
                    if ($colorPage % 2 == 0) {
                        $cor = 'style="background:#f3f3f3;"';
                    } else {
                        $cor = 'style="background:#fff;"';
                    }
                    ?>

                    <tr <?php echo $cor; ?> class="lista_itens">
                        <td align="center" style="font-size: 0.8em; font-weight: 600; color: #32A041;"><?php echo $busca['id']; ?></td>
                        <td align="left" style="padding-left:5px;">
                            <?php
                            //Veririca se há passageiros
                            if ($busca['passageiros'] == "") {
                                $num_passageiros_cont = 0;
                            } else if ($proxPassageiros != "" and ( strpos($proxPassageiros, ',') == true)) {
                                $num_passageiros = explode(",", $proxPassageiros);
                                $num_passageiros_cont = count($num_passageiros);
                            } else {
                                $num_passageiros_cont = 1;
                            }

                            $abrServidor = explode(' ', $busca['servidor']);
                            $escreveAbr = $abrServidor[0] . ' ' . $abrServidor[1] . ' ' . $abrServidor[2] . ' ' . $abrServidor[3];
                            if ($num_passageiros_cont > 0) {
                                echo $escreveAbr . ' (+' . $num_passageiros_cont . ')';
                            } else {
                                echo $escreveAbr;
                            }
                            ?>
                        </td>    
                        
                        <?php
                        if ($busca['prev_retorno_data'] == $hoje): //Verifica se a data é igual a hoje                            
                            if ($hojehora > $busca['prev_retorno_hora']): //Verifica se a hora de retorno já passou                                
                                echo "<td align='center'>"
                                . "<i class='fa fa-arrow-right' style='color: #090;'></i> {$saidaData} às {$saidahora}<br />"
                                . "<i class='fa fa-arrow-left' style='color: #F00;'></i> {$retornoData} às {$retornoHora}"
                                . "</td>";
                                
                            else: //Se a hora de retorno ainda não passou, faz isso
                                echo "<td align='center' class='data_uso_all'>"
                                . "<i class='fa fa-arrow-right' style='color: #090;'></i> {$saidaData} às {$saidahora}<br />"
                                . "<i class='fa fa-arrow-left' style='color: #F00;'></i> {$retornoData} às {$retornoHora}"
                                . "</td>";
                            endif;
                        else: //Se a data é maior que hoje, faz isso
                            echo "<td align='center' class='data_uso_all'>"
                                . "<i class='fa fa-arrow-right' style='color: #090;'></i> {$saidaData} às {$saidahora}<br />"
                                . "<i class='fa fa-arrow-left' style='color: #F00;'></i> {$retornoData} às {$retornoHora}"
                                . "</td>";
                        endif;
                        ?>
 
                        <td align="center">
                        <?php
                        if ($busca['roteiro_3'] != '') {
                            echo $busca['roteiro'] . '<br /><span style="font-size:10px; color:#000; width:100%"> (' . $busca['roteiro_2'] . ', ' . $busca['roteiro_3'] . ')</span>';
                        } elseif ($busca['roteiro_2'] != '' && $busca['roteiro_3'] == '') {
                            echo $busca['roteiro'] . '<br /><span style="font-size:10px; color:#000; width:100%"> (' . $busca['roteiro_2'] . ')</span>';
                        } else {
                            echo $busca['roteiro'];
                        }
                        ?>
                        </td>
                        
                        <td align="center">
                            <?php
                            if ($busca['situacao'] == "Aguardando...") {
                                echo '<i class="fa fa-exclamation-triangle fa-2x" style="color: #FFBA75;" title="Aguardando" alt="Aguardando"></i>';
                            } elseif ($busca['situacao'] == "Autorizada") {
                                echo '<i class="fa fa-check-square-o fa-2x" style="color: green;" title="Autorizada"></i>';
                            } elseif ($busca['situacao'] == "Encerrada") {
                                echo '<img src="../_assets/img/ico_encerrada.png" width="75px" title="Encerrada" alt="Encerrada"></div>&nbsp;&nbsp;<a href="print2.php?id=' . $proxId . '" target="_blank"></a>';
                            } elseif ($busca['situacao'] == "Cancelada") {
                                echo '<img src="../_assets/img/ico_cancelada.png" width="75px" title="Cancelada" alt="Cancelada"></div>&nbsp;&nbsp;<a href="print2.php?id=' . $proxId . '" target="_blank"></a>';
                            } else {
                                echo '<img src="../_assets/img/nAutorizada.png" title="Não Autorizada" alt="Não Autorizada">';
                            }
                            ?>
                        </td> 
                        <td align="center" width="130px" style="font:10px">
                            <?php
                            if ($busca['veiculo'] == 'n/d') {
                                echo '-';
                            } else {
                                echo $busca['veiculo'];
                            }
                            ?>  
                        </td>
                        <td align="center">
                            <?php
                            //Exclui placa do nome do veiculo e mostra máximo de passageiros
                            $mostra_veiculo = explode("-", $busca['veiculo']);
                            $veiculo_nome = $mostra_veiculo[0];
                            $veiculo_placa = $mostra_veiculo[1];

                            //Busca Capacidade do veiculo
                            $buscaCapacidade = read('vt_veiculos', "WHERE placa = '$veiculo_placa'");
                            $countBuscaCapacidade = count($buscaCapacidade);
                            if ($countBuscaCapacidade >= 1) {
                                foreach ($buscaCapacidade as $capVeiculo)
                                    ;
                                $maxPassageiros = ($capVeiculo['capacidade'] - 1);
                            } else {
                                $maxPassageiros = 4;
                            }

                            /* if($veiculo_lista == "Livina"){
                              $maxPassageiros = 6;
                              }else{
                              $maxPassageiros = 4;
                              } */

                            if ($num_passageiros_cont < $maxPassageiros and $busca['situacao'] != 'Nao Autorizada' and $busca['situacao'] != 'Cancelada') {

                                if ($busca['data_uso'] > $hoje || ($busca['data_uso'] == $hoje && $busca['horario_uso'] > $hojehora)) {
                                    echo '
                                        <a href="solicitacoes_all.php?id_solic=' . $busca['id'] . '" class="shown.bs.modal">
                                            <img src="../_assets/img/btn_carona.png" alt="Solicitar Carona" title="Solicitar Carona" />
                                        </a>
        			';
                                } else {
                                    echo '
					<img src="../_assets/img/ico_carona_nao.png" alt="Não é possível solicitar carona" title="Não é possível solicitar carona" width="25px" />
					';
                                }
                            } else {
                                echo '
					<img src="../_assets/img/ico_carona_nao.png" alt="Não é possível solicitar carona" title="Não é possível solicitar carona" width="25px" />
					';
                            }
                            ?>
                        </td>
                    </tr>

                            <?php
                        }
                    }
                    ?>

        </table>
            <?php if ($num > 0) { ?>
            <div id="paginator">
    <?php
    //PAGINAÇÃO
    $total = $num;

    $paginas = ceil($total / $maximo);
    $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

    echo "<a href=solicitacoes_all.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

    for ($i = $pag - $links; $i <= $pag - 1; $i++) {
        if ($i <= 0) {
            
        } else {
            echo"<a href=solicitacoes_all.php?pag={$i}>{$i}</a>&nbsp;&nbsp;&nbsp;";
        }
    }echo "<h1>$pag</h1>";

    for ($i = $pag + 1; $i <= $pag + $links; $i++) {
        if ($i > $paginas) {
            
        } else {
            echo "<a href=solicitacoes_all.php?pag={$i}>{$i}</a>&nbsp;&nbsp;&nbsp;";
        }
    }
    echo "<a href=solicitacoes_all.php?pag={$paginas}>Última</a>&nbsp;&nbsp;&nbsp;";
    ?>
            <?php } ?>


    </div> <!--fecha div class paginas--> 

    <!--Encerra conteúdo das páginas-->
<?php include_once "includes/inc_footer.php"; ?>