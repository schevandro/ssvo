<?php include_once "includes/inc_header.php"; ?> 
<?php include_once "includes/inc_menu.php"; ?>

<!--Conteudo das páginas -->
<h1 class="titulo-secao-medio"><i class="fa fa-hand-stop-o"></i> Meus pedidos de carona</h1>                   

<?php
//PAGINAÇÃO
$pag = "$_GET[pag]";
if($pag >= '1'){
 $pag = $pag;
}else{
 $pag = '1';
}
$maximo = '10'; //RESULTADOS POR PÁGINA
$inicio = ($pag * $maximo) - $maximo;

	$hoje = date('Y-m-d');
	$siape_login	= $_SESSION['autUser']['siape'];
	$status_solicitacao = array ('0','1','2','3');
	
	if(empty($_GET['car'])){		
		$readCaronas = read('vt_caronas',"WHERE siape = '$siape_login' ORDER BY solicitadaEm DESC LIMIT $inicio,$maximo");
		$todas = count($readCaronas);
		$bgTodas = 'style="background-color:#71ca73;"';
	}
	elseif(isset($_GET['car']) && $_GET['car'] == 'aceito'){
		$readCaronas = read('vt_caronas',"WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[1]') ORDER BY solicitadaEm DESC LIMIT $inicio,$maximo");
		$autorizadas = count($readCaronas);	
		$bgAceitas = 'style="background-color:#71ca73;"';
	}
	elseif(isset($_GET['car']) && $_GET['car'] == 'negado'){
		$readCaronas = read('vt_caronas',"WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[2]') ORDER BY solicitadaEm DESC LIMIT $inicio,$maximo");
		$negadas = count($readCaronas);	
		$bgNegadas = 'style="background-color:#71ca73;"';
	}
	elseif(isset($_GET['car']) && $_GET['car'] == 'aberto'){
		$readCaronas = read('vt_caronas',"WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[0]') ORDER BY solicitadaEm DESC LIMIT $inicio,$maximo");
		$abertas = count($readCaronas);	
		$bgAbertas = 'style="background-color:#71ca73;"';
	}
	elseif(isset($_GET['car']) && $_GET['car'] == 'cancelado'){
		$readCaronas = read('vt_caronas',"WHERE (siape = '$siape_login' AND situacao = '$status_solicitacao[3]') ORDER BY solicitadaEm DESC LIMIT $inicio,$maximo");
		$canceladas = count($readCaronas);	
		$bgCanceladas = 'style="background-color:#71ca73;"';
	}
?>
    <div class="submenu">
        <a href="pedidos_carona.php?car=aberto"><h5 <?php echo $bgAbertas; ?>>Abertos <?php if(!empty($abertas)) echo '<strong>['.$abertas.']</strong>'; ?></h5></a>
        <a href="pedidos_carona.php?car=aceito"><h5 <?php echo $bgAceitas; ?>>Aceitos <?php if(!empty($autorizadas)) echo '<strong>['.$autorizadas.']</strong>'; ?></h5></a>
        <a href="pedidos_carona.php?car=negado"><h5 <?php echo $bgNegadas; ?>>Negados <?php if(!empty($negadas)) echo '<strong>['.$negadas.']</strong>'; ?></h5></a>
        <a href="pedidos_carona.php?car=cancelado"><h5 <?php echo $bgCanceladas; ?>>Cancelados <?php if(!empty($canceladas)) echo '<strong>['.$canceladas.']</strong>'; ?></h5></a>
        <a href="pedidos_carona.php"><h5 <?php echo $bgTodas; ?>>Todos <?php if(!empty($todas)) echo '<strong>['.$todas.']</strong>'; ?></h5></a>
    </div><!--fecha div class fotos-->
    
	<?php include_once ('codes/cancela_carona.php'); ?>
    
<?php
if($readCaronas <= 0){
    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle'></i>&nbsp&nbsp&nbsp Não há pedidos de carona com esta descrição!</h4>";
}else{
?>    
    
    <div class="lista_fotos">
        <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        	<tr class="tr_header">
                <td align="center">ID</td>
                <td align="center">Solicitada Em</td>
                <td align="center">Nº da Viagem</td>
                <td align="center">Titular da Viagem</td>
                <td align="center">Data / Hora</td>
                <td align="center">Destino</td>
                <td align="center">Situação</td>
                <td align="center">Ações</td>
        	</tr>
<?php
	
	foreach($readCaronas as $readC){
		$colorPage++;
		if ($colorPage % 2 == 0) 
		{
			$cor = 'style="background:#f3f3f3;"';
		}else {
			$cor = 'style="background:#fff;"';
		}  
		
		//Buscar a guia de viagem
		$carGuiaCarona = $readC['guia_carona'];
		$readGuia = read('vt_solicitacoes',"WHERE id = '$carGuiaCarona'");
		foreach($readGuia as $readG);
		
		
?>
            <tr <?php echo $cor; ?> class="lista_itens">
                <td align="center" style="font-size: 0.8em; font-weight: 600; color: #32A041;"><?php echo $readC['id']; ?></td>
                <td align="center"><?php echo date('d/m/y', strtotime($readC['solicitadaEm'])); ?></td>
                <td align="center"><?php echo $readC['guia_carona']; ?></td>
                <td align="center"><?php echo $readG['servidor']; ?></td>
                <td align="center"><?php echo date('d/m/y', strtotime($readG['data_uso'])). ' às ' .date('H:i', strtotime($readG['horario_uso'])).'hs'; ?></td>
                <td align="center">
                    <?php
                        if($readG['roteiro_3'] != ''){
                            echo $readG['roteiro'].'<br /><span style="font-size:10px; color:#000; width:100%"> ('.$readG['roteiro_2'].', '.$readG['roteiro_3'].')</span>';
                        }elseif($readG['roteiro_2'] != '' && $readG['roteiro_3'] == ''){
                            echo $readG['roteiro'].'<br /><span style="font-size:10px; color:#000; width:100%"> ('.$readG['roteiro_2'].')</span>';
                        }else{
                            echo $readG['roteiro'];
                        }
                    ?>
                </td>
                <td align="center">
					<?php
                    	if($readC['situacao'] == 0){
                            echo '<i class="fa fa-exclamation-triangle fa-2x" style="color: #FFBA75;" title="Aguardando" alt="Aguardando"></i>';
                        }elseif($readC['situacao'] == 1){
                            echo '<i class="fa fa-check-square-o fa-2x" style="color: green;" title="Autorizado"></i>';		
                    	}elseif($readC['situacao'] == 3){
                            echo '<img src="../_assets/img/ico_cancelada.png" width="75px" title="Cancelado por '.$_SESSION['autUser']['nome'].'" alt="Pedido de Carona Cancelado">';		
                    	}else{
                            echo '<i class="fa fa-close fa-2x" style="color: #FF5959;" title="Pedido de carona negado"></i>';
                        }
                    ?>
          		</td>  
            
            	<td align="center">
                    <a href="detalha_solicitacao_car.php?id_solicitacao=<?php echo $readG['id']; ?>" style="text-decoration:none; color:#900;" title="Visualizar detalhes da viagem"><i class="fa fa-search" style="color: #555; margin: 0 5px; font-size: 1.4em;"></i></a>
		<?php
                    if($readC['situacao'] == 0 or $readC['situacao'] == 1){
                ?>
                    <a href="pedidos_carona.php?id_cancela=<?php echo $readC['id']; ?>&amp;siape=<?php echo $readC['siape']; ?>&amp;guia_viagem=<?php echo $readC['guia_carona']; ?>" style="text-decoration:none; color:#900; margin-left:5px;" title="Cancelar pedido de carona"><i class="fa fa-trash" style="color: #555; font-size: 1.4em;"></i></a>
                <?php
                    }
                ?>
            </td>
            </tr>  
            
            <?php	
                }
            ?>
            </table>
            
            <?php
                }
            ?>
		
		<?php if($readCaronas >= 1){?><div id="paginator">
		<?php
		//PAGINAÇÃO
		$total = $num;
		
		$paginas = ceil($total/$maximo);
		$links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR
		
		echo "<a href=minhas_solicitacoes.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";
		
		for ($i = $pag-$links; $i <= $pag-1; $i++){
		if ($i <= 0){
		}else{
		echo"<a href=minhas_solicitacoes.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
		}
		}echo "<h1>$pag</h1>";
		
		for($i = $pag + 1; $i <= $pag+$links; $i++){
		if($i > $paginas){
		}else{
		echo "<a href=minhas_solicitacoes.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
		}
		}
		echo "<a href=minhas_solicitacoes.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";
		?>
        
		</div> <?php }?>
	</div> <!--fecha div class lista_fotos-->  
</div> <!--fecha div class paginas--> 
		 
<!--Encerra conteúdo das páginas-->
<?php include_once "includes/inc_footer.php"; ?>