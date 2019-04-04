<?php
  ob_start(); session_start(); 
  require('../dts/dbaSis.php');
  require('../dts/getSis.php');
  require('../dts/setSis.php');
  require('../dts/outSis.php');
?>

<?php
if(!$_SESSION['autUser']){
		header('Location: index.php');
}else{
	$userId = $_SESSION['autUser']['id'];	
	$readAutUser = read('servidores',"WHERE id = '$userId'");
	if($readAutUser){
		foreach($readAutUser as $autUser);
		if($autUser['admin'] < '1' || $autUser['admin'] > '2'){
			unset($_SESSION['autUser']);
			ob_end_flush();
			header('Location: ../index.php');
		}
	}else{
		header('Location: ../index.php');
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Guia de Controle de Saída de Veículos Oficiais</title>
<link href="styles/style-print-solicitacoes.css" rel="stylesheet" type="text/css" />
<style media="print" type="text/css">
	.print{display:none;}
</style>
</head>

<body>
<?php
$hoje					= date('Y-m-d');
$agora					= date('H:i:s');

//recebe dados
$wherePrint  = $_POST['dados_print'];
$mandaFiltro = $_POST['manda_filtro'];
//Busca a solicitacao
$readSolicitacoes = read('vt_solicitacoes',$wherePrint);
//conta as solicitações
$countReadSolicitacoes	= count($readSolicitacoes);

//Se não existir nenhuma da msg de erro
if($countReadSolicitacoes <= 0){
	
?>

<div id="guia_nAutorizada">
<h3>ERRO: Impossível buscar dados solicitados.</h3>
<h4><a href="admin_esolicitacoes.php?solit=aberto">Voltar</a></h4>	
</div><!--fecha div conteudo-->

<?php
//Senão apresenta relatório
}else{
?>

<div id="formulario">
	<img src="imagens/logo.png" title="IFRS - Campus Feliz" align="IFRS - Campus Feliz" width="150px" /><br /><br />
	<h2>Solicitações para conduzir veículos oficiais do Campus Feliz do IFRS</h2><br />
    <h2>FILTRO: <?php echo $mandaFiltro; ?></h2><br />
    <?php
		echo '<h2>Lista gerada em '.date('d/m/Y',strtotime($hoje)).' às '.$agora.'</h2><br />';
	?>
    <div class='print'><a href='javascript:self.print()'>Imprimir</a></div>
	<table>
    	<tr style="background-color:#333; color:#FFF; font:bold 13px Tahoma, Geneva, sans-serif;">
        	<td align="center">&nbsp;</td>
            <td align="center">ID</td>
            <td align="center">Solicitada em</td>
            <td align="center">Servidor</td>
            <td align="center">Motorista</td>
            <td align="center">Finalidade</td>
            <td align="center">Roteiro</td>
            <td align="center">Passageiros</td>
            <td align="center">Uso em</td>
            <td align="center">Veículo</td>
            <td align="center">KM Saída</td>
            <td align="center">KM Chegada</td>
            <td align="center">KM Percorridos</td>
        </tr>
        
		<?php
		if($countReadSolicitacoes >= 1){
			foreach($readSolicitacoes as $solit){
			$i=$i+1;
			?>
          	<tr>
            	<td align="center"><?php echo $i; ?></td>
                <td align="center"><?php echo $solit['id']; ?></td>
                <td align="center"><?php echo date('d/m/Y',strtotime($solit['criadoEm'])); ?></td>
                <td align="center"><?php echo $solit['servidor']; ?></td>
                <td align="center"><?php echo $solit['motorista']; ?></td>
                <td align="center"><?php echo $solit['finalidade']; ?></td>
                <td align="center"><?php echo $solit['roteiro']; ?></td>
                <td align="center" width="200px"><?php if($solit['passageiros'] == ''){echo '-';}else{echo $solit['passageiros'];} ?></td>
                <td align="center"><?php echo date('d/m/Y',strtotime($solit['data_uso'])).' às '.date('H:i:s',strtotime($solit['horario_uso'])); ?></td>
                <td align="center"><?php echo $solit['veiculo']; ?></td>
                <?php
				if($solit['situacao'] == 'Nao Autorizada' || $solit['situacao'] == 'Cancelada'){
				?>
                	<td align="center" colspan="3" Style="background-color:#999; color:#FFF; font:bold 11px Tahoma, Geneva, sans-serif;"><?php echo $solit['situacao']; ?></td>
                <?php
                }else{
				?>
               		<td align="center"><?php echo $solit['km_saida']; ?></td>
                	<td align="center"><?php echo $solit['km_chegada']; ?></td>
                	<td align="center"><?php echo ($solit['km_chegada']-$solit['km_saida']); ?></td>
                <?php
                }
                ?>
                
            </tr>  
            <?php
			}	
		}
		?>
    </table>

</div>
</body>
</html>
<?php
	ob_end_flush();
  }
?>