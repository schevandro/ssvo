<?php
include_once "includes/inc_header.php";

//PAGINAÇÃO
$pag = $_GET['pag'];
if($pag >= '1'):
    $pag = $pag;
else:
    $pag = '1';
endif;
$maximo = '25'; //RESULTADOS POR PÁGINA
$inicio = ($pag * $maximo) - $maximo;

$hoje = date('Y-m-d');
$status_solicitacao = array ('Aguardando...','Autorizada','Nao Autorizada','Cancelada','Encerrada');
$idviat = $_GET['idviat'];
$filtro = $_GET['filtro'];
$buscaVeiculo = read('vt_veiculos',"WHERE id = '$idviat'");
$countBuscaVeiculo = count($buscaVeiculo);

if($countBuscaVeiculo >= 1):
    foreach($buscaVeiculo as $veiculo);
    $veiculoGuia = $veiculo['veiculo'].'-'.$veiculo['placa'];

    //Seleciona se mostra somente as proximas ou as anteriores também
    if($_GET['filtro'] == '' || $_GET['filtro'] == 'prox'):
        $dataProx = read('vt_solicitacoes',"WHERE (veiculo = '$veiculoGuia' AND (situacao = 'Autorizada' OR situacao = 'Aguardando...') AND data_uso >= '$hoje') ORDER BY data_uso ASC, horario_uso ASC LIMIT $inicio,$maximo");
        $contaNumPag = read('vt_solicitacoes',"WHERE (veiculo = '$veiculoGuia' AND (situacao = 'Autorizada' OR situacao = 'Aguardando...') AND data_uso >= '$hoje')");
        $numPag	  = count($contaNumPag);
    else:
        $dataProx = read('vt_solicitacoes',"WHERE veiculo = '$veiculoGuia' ORDER BY data_uso DESC, horario_uso ASC LIMIT $inicio,$maximo");
        $contaNumPag = read('vt_solicitacoes',"WHERE veiculo = '$veiculoGuia'");
        $numPag   = count($contaNumPag);
    endif;

    if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):
        if(!empty($idviat)):
?>
        <h1 class="titulo-secao"><i class="fa fa-car"></i> Solicitações do veículo <?php echo $veiculoGuia; ?></h1> 
                
<?php
            echo "<a href='admin_eveiculos.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Veiculos'><i class='fa fa-list'></i> Listar Veiculos</a>";
            
            if($_GET['filtro'] == 'prox' || $_GET['filtro'] == ''):
                echo "<span class='btn btn_default fl_left' title='Listando Próximas Solicitações' style='margin-right: 15px;'><i class='fa fa-refresh'></i> <strong>Listando:</strong> Próximas Solicitações [ {$numPag} ]</span>";
                echo "<a href='admin_esolicitacoes_viatura.php?idviat={$idviat}&filtro=todas' class='btn btn_orange fl_left' title='Mostrar todas as solicitações'><i class='fa fa-list'></i> Mostrar Todas</a>";
            else:
                echo "<span class='btn btn_default fl_left' title='Listando Próximas Solicitações' style='margin-right: 15px;'><i class='fa fa-refresh'></i> <strong>Listando:</strong> Todas as Solicitações [ {$numPag} ]</span>";
                echo "<a href='admin_esolicitacoes_viatura.php?idviat={$idviat}&filtro=prox' class='btn btn_orange fl_left' title='Mostrar somente as próximas'><i class='fa fa-list'></i> Mostrar somente as próximas</a>";
            endif;

            if($numPag <= 0):
                echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp; Não há solicitações com esta descrição!</h4>';
            else:
?> 
                <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
                    <tr class="tr_header">
                        <td align="center">ID / Ano</td>
                        <td align="left" style="padding-left:10px">Nome</td>
                        <td align="center">Destino</td>
                        <td align="center">Saída</td>
                        <td align="center">Retorno</td>
                        <td align="center">Situação</td>
                    </tr>
<?php
                    foreach($dataProx as $readSol):
                        $colorPage++;
                        if ($colorPage % 2 == 0):
                            $cor = 'style="background:#f3f3f3;"';
                        else:
                            $cor = 'style="background:#fff;"';
                        endif; 
?>
                        <tr <?php echo $cor; ?> class="lista_itens">
                            <td align="center" style="font-size: 0.875em; font-weight: 600;"><?php echo $readSol['id'] . ' / ' . date('y', strtotime($readSol['criadoEm'])); ?></td>
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
                                if($readSol['roteiro_3'] != ''):
                                    echo $readSol['roteiro'].'<br /><span style="font-size:10px; color:#000; width:100%"> ('.$readSol['roteiro_2'].', '.$readSol['roteiro_3'].')</span>';
                                elseif($readSol['roteiro_2'] != '' && $readSol['roteiro_3'] == ''):
                                    echo $readSol['roteiro'].'<br /><span style="font-size:10px; color:#000; width:100%"> ('.$readSol['roteiro_2'].')</span>';
                                else:
                                    echo $readSol['roteiro'];
                                endif;
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
                            </tr>
                    <?php
                        endforeach;
                    ?>
                </table>

<?php
                echo "<div id='paginator'>";
                    //PAGINAÇÃO
                    $total = $numPag;

                    $paginas = ceil($total/$maximo);
                    $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

                    echo '<a href=admin_esolicitacoes_viatura.php?idviat='.$_GET['idviat'].'&filtro='.$_GET['filtro'].'&pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;';

                    for ($i = $pag-$links; $i <= $pag-1; $i++):
                        if ($i <= 0):
                        else:
                            echo'<a href=admin_esolicitacoes_viatura.php?idviat='.$_GET['idviat'].'&filtro='.$_GET['filtro'].'&pag='.$i.'>'.$i.'</a>&nbsp;&nbsp;&nbsp;';
                        endif;
                    endfor;
                    echo "<h1>$pag</h1>";

                    for($i = $pag + 1; $i <= $pag+$links; $i++):
                        if($i > $paginas):
                        else:
                            echo '<a href=admin_esolicitacoes_viatura.php?idviat='.$_GET['idviat'].'&filtro='.$_GET['filtro'].'&pag='.$i.'>'.$i.'</a>&nbsp;&nbsp;&nbsp;';
                        endif;
                    endfor;
                echo '<a href=admin_esolicitacoes_viatura.php?idviat='.$_GET['idviat'].'&filtro='.$_GET['filtro'].'&pag='.$paginas.'>Última</a>&nbsp;&nbsp;&nbsp;';
            endif;
                echo "</div>";
	else:
            header('Location: admin_eveiculos.php');
	endif;
    else:
        echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
    endif;
else:
    header('Location: admin_eveiculos.php');
endif;
include_once "includes/inc_footer.php";
?>