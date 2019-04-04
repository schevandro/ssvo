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
<link href="../_assets/css/prints/print-relatorios.css" rel="stylesheet" type="text/css" />
<style media="print" type="text/css">
	.print{display:none;}
</style>
</head>

<body>
<?php
//Busca Servidor
$hoje					= date('Y-m-d');
$agora					= date('H:i:s');
$readCondutores			= read('servidores',"WHERE os_motorista != 'n' ORDER BY nome ASC");
$countReadCondutores	= count($readCondutores);

if($countReadCondutores <= 0){
?>

<div id="guia_nAutorizada">
<h3>ERRO: Impossível buscar dados solicitados.</h3>
<h4><a href="admin_esolicitacoes.php?solit=aberto">Voltar</a></h4>	
</div><!--fecha div conteudo-->

<?php
}else{
?>

<div id="formulario">
	<img src="imagens/logo.png" title="IFRS - Campus Feliz" align="IFRS - Campus Feliz" width="150px" /><br /><br />
	<h2>Servidores com Ordem de Serviço com autoriazação para conduzir veículos oficiais do Campus Feliz do IFRS</h2><br />
    <?php
		echo '<h2>Lista gerada em '.date('d/m/Y',strtotime($hoje)).' às '.$agora.'</h2><br />';
	?>
    <div class='print'><a href='javascript:self.print()'>Imprimir</a></div>
	<table width="100%">
    	<tr style="background-color:#333; color:#FFF; font:bold 13px Tahoma, Geneva, sans-serif;">
        	<td align="center">&nbsp;</td>
            <td align="center">Nome</td>
            <td align="center">Função</td>
            <td align="center">SIAPE</td>
            <td align="center">CNH</td>
            <td align="center">Vencimento CNH</td>
            <td align="center">OS Condutor</td>
            <td align="center">Cod Abastecimento</td>
        </tr>
        
		<?php
		if($countReadCondutores >= 1){
			foreach($readCondutores as $condu){
			$i=$i+1;
			?>
          	<tr>
            	<td align="center"><?php echo $i; ?></td>
            	<td align="left"><?php echo $condu['nome']; ?></td>
                <td align="center"><?php echo $condu['funcao']; ?></td>
                <td align="center"><?php echo $condu['siape']; ?></td>
                <td align="center"><?php echo $condu['cnh']; ?></td>
                <?php
					if($condu['cnh_vencimento'] < $hoje){
               			echo '<td align="center" style="background-color:#666; color:#FFF;">'.date('d/m/Y',strtotime($condu['cnh_vencimento'])).'</td>';
					}else{
						echo '<td align="center">'.date('d/m/Y',strtotime($condu['cnh_vencimento'])).'</td>';
					}
				?>
                <td align="center"><?php echo $condu['os_motorista'].'/'.date('Y',strtotime($condu['os_motorista_data'])); ?></td>
                <td align="center"><?php echo $condu['cod_abastecimento']; ?></td>
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