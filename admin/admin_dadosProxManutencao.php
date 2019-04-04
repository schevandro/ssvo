<?php
include_once('includes/inc_header.php');

$hoje               = date('Y-m-d');
$veiculoId          = $_GET['idVeic'];
$readVeiculo        = read('vt_veiculos',"WHERE id = '$veiculoId'");
$countReadVeiculo   = count($readVeiculo);
if($countReadVeiculo < 1):
    header('Location: admin_emanutencao.php');
else:
    if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):
        foreach ($readVeiculo as $veiculo);
?>

        <h1 class="titulo-secao"><i class="fa fa-cogs"></i> Dados para a próxima manutenção do <?php echo $veiculo['veiculo'].'-'.$veiculo['placa']; ?></h1>
        <a href="admin_emanutencao.php" class="btn btn_green fl_right" style="margin-bottom: 30px; margin-left: 10px;" title="Listar Manutenções"><i class="fa fa-list"></i> Listar Veículos</a>

        <a href="admin_cadManutencao.php?idVeic=<?php echo $veiculo['id']; ?>" title="Cadastrar uma nova manutenção" class="btn btn_blue"><i class="fa fa-plus-circle"></i> MANUTENÇÃO</a>		
        <a href="admin_aveiculos.php?id_veiculo=<?php echo $veiculo['id']; ?>" class="btn btn_orange"><i class="fa fa-edit"></i> EDITAR ESTES DADOS</a>

       
        <h1 style="color:#093; width: 100%; padding: 15px 0; font-size: 1em; margin: 35px 0 20px 5px; border-bottom: 1px solid #ccc;">Dados para a próxima manutenção do veículo</h1>        
          
        <table style="margin-left:5px;" border="0">
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Veículo</strong></td>
                <td><?php echo $veiculo['veiculo'].'-'.$veiculo['placa']; ?></td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>KM Atual</strong></td>
                <td><?php echo number_format($veiculo['km'], 0, '.', '.').' km'; ?></td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Renavam</strong></td>
                <td><?php echo $veiculo['renavam']; ?></td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Última Manutenção</strong></td>
                <td>
                    <?php
                        $readManutencao 	= read('vt_manutencao',"WHERE veiculo = '$veiculoId' ORDER BY criada_em DESC LIMIT 1");
                        $contaManutencoes	= count($readManutencao);
                        if($contaManutencoes >= 1):
                            foreach($readManutencao as $manutencao);
                            $msg_manutencao = '<a style="font:14px Tahoma, Geneva, sans-serif; color:#09F; text-decoration:none;" href="admin_aManutencao.php?idManut='.$manutencao['id'].'&idVeic='.$veiculoId.'">Ver Detalhes</a>';
                            $dataManutenção = date('d-m-Y',strtotime($manutencao['criada_em'])).'&nbsp;&nbsp;&nbsp;';
                        else:
                            $dataManutenção = NULL;
                            $msg_manutencao = 'Não há registros de manutenção para este veículo';
                        endif;
                        echo $dataManutenção.$msg_manutencao;						
                    ?>
                </td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Próxima Geometria / Balanceamento</strong></td>
                <td>
                    <?php
                        $verifGeometria = ($veiculo['geometria'] - $veiculo['km']);
                        if($verifGeometria < 1):
                            $escreve_info = number_format($veiculo['geometria'], 0, '.', '.');
                            echo "<i class='fa fa-exclamation-triangle' style='color: #F90;'></i> <span style='color:#F90;'>{$escreve_info} km<span>";
                        else:
                            echo number_format($veiculo['geometria'], 0, '.', '.').' km';
                        endif;
                    ?>
                </td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Próxima Troca de Óleo do Motor</strong></td>
                <td>
                    <?php
                        $verifGeometria = ($veiculo['troca_oleo'] - $veiculo['km']);
                        if($verifGeometria < 1):
                            $escreve_oleo = number_format($veiculo['troca_oleo'], 0, '.', '.');
                            echo "<i class='fa fa-exclamation-triangle' style='color: #F90;'></i> <span style='color:#F90;'>{$escreve_oleo} km<span>";
                        else:
                            echo number_format($veiculo['troca_oleo'], 0, '.', '.').' km';
                        endif;
                    ?>
                </td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Próxima Revisão Mecânica</strong></td>
                <td>
                    <?php
                        $verifRevisao = ($veiculo['revisao'] - $veiculo['km']);
                        if($verifRevisao < 1):
                            $escreve_revisao = number_format($veiculo['revisao'], 0, '.', '.');
                            echo "<i class='fa fa-exclamation-triangle' style='color: #F90;'></i> <span style='color:#F90;'>{$escreve_revisao} km<span>";
                        else:
                            echo number_format($veiculo['revisao'], 0, '.', '.').' km';
                        endif;
                    ?>
                </td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Próxima Troca da Corrêa Dentada</strong></td>
                <td>
                    <?php
                        $verifCorrea = ($veiculo['correa'] - $veiculo['km']);
                        if($verifCorrea < 1):
                            $escreve_correa = number_format($veiculo['correa'], 0, '.', '.');
                            echo "<i class='fa fa-exclamation-triangle' style='color: #F90;'></i> <span style='color:#F90;'>{$escreve_correa} km<span>";
                        else:
                            echo number_format($veiculo['correa'], 0, '.', '.').' km';
                        endif;
                    ?>
                </td>
            </tr>
            <tr style="font:16px Tahoma, Geneva, sans-serif;" height="45px">
                <td width="350px"><strong>Vencimento do Seguro Obrigatório</strong></td>
                <td>
                    <?php
                        //Seguro
                        $time_inicial_seguro	= strtotime($veiculo['seguro']);
                        $time_final_seguro  	= strtotime($hoje);
                        $dif_seguro				= $time_inicial_seguro-$time_final_seguro;
                        $dias_seguro   	    	= (int)floor($dif_seguro / (60 * 60 *24));

                        if($dias_seguro < 0):
                            $escreve_data_seguro = date('d-m-Y',strtotime($veiculo['seguro']));
                            echo "<i class='fa fa-exclamation-triangle' style='color: #F90;'></i> <span style='color:#F90;'>{$escreve_data_seguro}<span>";
                        else:
                            echo date('d-m-Y',strtotime($veiculo['seguro']));
                        endif;				
                    ?>
                </td>
            </tr>                          
        </table>
    
<?php
    else:
        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
    endif;
endif;
include_once "includes/inc_footer.php"; 
?>