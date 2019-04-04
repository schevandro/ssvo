<?php
include_once "includes/inc_header.php";
$id_veiculo = $_GET['idVeic'];

if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):

    //PAGINAÇÃO
    $pag = $_GET['pag'];
    if($pag >= '1'):
        $pag = $pag;
    else:
        $pag = '1';
    endif;
    $maximo = '10'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;

    //Busca Veículos
    $readVeiculo	=	read('vt_veiculos',"WHERE id = '$id_veiculo' AND deletado = 0");
    $contaVeiculos	=	count($readVeiculo);

    if($contaVeiculos >= 1):
        foreach ($readVeiculo as $veiculo);
    endif;

    //Busca Manutenções
    $readManutencao	=	read('vt_manutencao',"WHERE veiculo = '$id_veiculo' ORDER BY criada_em DESC");
    $contaManutencoes	=	count($readManutencao);			  
	
?>  
    <h1 class="titulo-secao"><i class="fa fa-cogs"></i> Manutenções do veículo <?php echo $veiculo['veiculo'].'-'.$veiculo['placa']; ?> <span><?php echo $contaManutencoes; ?> manutenções registradas</span></h1>
    <a href="admin_cadManutencao.php?idVeic=<?php echo $id_veiculo; ?>" class="btn btn_blue fl_right" style="margin-bottom: 30px;" title="Informar uma manutenção"><i class="fa fa-plus-circle"></i> MANUTENÇÃO</a>

<?php	
    echo "<a href='admin_emanutencao.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-right: 10px;' title='Ir até a página de manutenção'><i class='fa fa-list'></i> Voltar para manutenções</a>";
    
    if($contaManutencoes <= 0):
        echo '<div class="ms al" style="margin-top:55px;">Não há manutenções cadastradas para este veículo</div>';
    else:
?>
        <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
            <tr class="tr_header">
                <td align="center" width="50px">ID</td>
                <td align="center" width="150px">Data</td>
                <td align="center" width="150px">KM</td>
                                    <td align="left" width="350px">Servidor</td>
                <td align="center" width="90px">Óleo</td>
                <td align="center" width="90px">Geometria<br />Balanceamento</td>
                <td align="center" width="90px">Corrêa</td>
                <td align="center" width="90px">Rev. Geral</td>
                <td align="center" width="120px">Ações</td>
            </tr>  
<?php
            foreach ($readManutencao as $manut) {
                $exibeId 		      = $manut['id'];
                $exibeServidor	  = $manut['servidor'];
                $exibeCriadaem	  = $manut['criada_em'];
                $exibeData		  = $manut['data'];
                $exibeVeiculo		  = $veiculo['veiculo'].'-'.$veiculo['placa'];
                $exibeKM			  = $manut['km_revisao'];
                $exibeOleo		  = $manut['oleo'];
                $exibeCorrea   	  = $manut['correa'];
                $exibeGeometria  	  = $manut['geometria'];
                $exibeRevisao		  = $manut['revisao'];
					  
                //Buscar o servidor
                $readServidor = read('servidores',"WHERE id = '$exibeServidor'");
                foreach ($readServidor as $nome_servidor);

                $colorPage++;
                if ($colorPage % 2 == 0):
                    $cor = 'style="background:#f3f3f3;"';
                else:
                    $cor = 'style="background:#fff;"';
                endif;
?>
	
            <tr <?php echo $cor; ?> class="lista_itens">
                <td align="center"><?php echo $exibeId; ?></td>
                <td align="center"><?php echo date('d-m-Y',strtotime($exibeData)); ?></td>
                <td align="center"><?php echo $exibeKM; ?></td>
                <td align="left"><?php echo $nome_servidor['nome']; ?></td>
                <td align="center">
                    <?php
                    if($exibeOleo == 1):
                        echo "<i class='fa fa-check-circle fa-2x' style='color: #32A041;' title='Realizada'></i>";	
                    else:
                        echo "<i class='fa fa-close fa-2x' style='color: #F45563;' title='Não realizada'></i>";
                    endif;
                    ?>
                </td>
                <td align="center">
                    <?php
                    if($exibeGeometria == 1):
                        echo "<i class='fa fa-check-circle fa-2x' style='color: #32A041;' title='Realizada'></i>";	
                    else:
                        echo "<i class='fa fa-close fa-2x' style='color: #F45563;' title='Não realizada'></i>";
                    endif;
                    ?>
                </td>
                <td align="center">
                    <?php
                    if($exibeCorrea == 1):
                        echo "<i class='fa fa-check-circle fa-2x' style='color: #32A041;' title='Realizada'></i>";	
                    else:
                        echo "<i class='fa fa-close fa-2x' style='color: #F45563;' title='Não realizada'></i>";
                    endif;
                    ?>
                </td>
                <td align="center">
                    <?php
                    if($exibeRevisao == 1):
                        echo "<i class='fa fa-check-circle fa-2x' style='color: #32A041;' title='Realizada'></i>";	
                    else:
                        echo "<i class='fa fa-close fa-2x' style='color: #F45563;' title='Não realizada'></i>";
                    endif;
                    ?>
                </td>     				
                <td align="center" style="font:15px 'Trebuchet MS', Arial, Helvetica, sans-serif;">
                    <a href="admin_aManutencao.php?idManut=<?php echo $exibeId; ?>&idVeic=<?php echo $id_veiculo; ?>" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Editar dados / Ver Detalhes"><i class="fa fa-pencil-square-o"></i></a>
                </td>
            </tr>	
	<?php
            }
	?>
	</table>
<?php
    endif;
?>

    <div id="paginator">
        <?php
        if($contaManutencoes >= 1){

        //PAGINAÇÃO
        $total = $num;

        $paginas = ceil($total/$maximo);
        $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

        echo "<a href=admin_emotoristas.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

        for ($i = $pag-$links; $i <= $pag-1; $i++){
        if ($i <= 0){
        }else{
        echo"<a href=admin_emotoristas.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
        }
        }echo "<h1>$pag</h1>";

        for($i = $pag + 1; $i <= $pag+$links; $i++){
        if($i > $paginas){
        }else{
        echo "<a href=admin_emotoristas.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
        }
        }
        echo "<a href=admin_emotoristas.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";
        }
        ?>
    </div>
          
<?php
else:
    echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
endif;
include_once "includes/inc_footer.php";
?>