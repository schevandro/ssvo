<?php
  ob_start(); session_start(); 
  require('../dts/dbaSis.php');
  require('../dts/getSis.php');
  require('../dts/setSis.php');
  require('../dts/outSis.php');
?>
<?php
if(function_exists(getUser)){
	if(!getUser($_SESSION['autUser']['id'])){
		echo '<span class="ms al">Desculpe, você não tem permissão para acessar esta página!</span>';
	}else{
?>
<?php
if(empty($_GET['id']) || $_GET['id'] <= 0){
	  header('Location: index.php');
  }else{ 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Guia de Controle de Saída de Veículos Oficiais</title>
<link href="../_assets/css/via-print-viaturas.css" rel="stylesheet" type="text/css" />
<style media="print" type="text/css">
	.print{display:none;}
</style>
</head>

<body>
<?php
//Busca Servidor
$solicitacao_verificada = $_GET['id'];
$siape_titular          = $_SESSION['autUser']['siape'];
$solicitacao_situacao   = 'Autorizada';

$readSelecionada		= read('vt_solicitacoes',"WHERE id = '$solicitacao_verificada' AND siape = '$siape_titular' AND situacao = '$solicitacao_situacao'");
$countReadSelecionada	= count($readSelecionada);

	if($countReadSelecionada >= 1){
	foreach($readSelecionada as $guia);
		//Dados do Aceitador
		$siape_aceitador 	 = $guia['siape_aceitador'];
		$readAceitador  	 =	read('servidores',"WHERE siape = '$siape_aceitador'");
		$countReadAceitador  = count($readAceitador);
		if($countReadAceitador >= 1){
			foreach($readAceitador as $aceitador);
		}
		//Dados Usuário
		$readUsuario = read('servidores',"WHERE siape = '$siape_titular'");
		$countReadUsuario = count($readUsuario);
		//Dados Motorista
		$siape_motorista	=	$guia['siape_motorista'];
		$readMotorista		=	read('servidores',"WHERE siape = '$siape_motorista'");
		$countReadMotorista	= 	count($readMotorista);
		if($countReadUsuario >= 1){
			foreach($readMotorista as $dadosMotorista);
			foreach($readUsuario as $dados);
			//Chefe Imediato
			$chefeSetor = $dados['setor'];
			$readChefe = read('setores',"WHERE abreviatura = '$chefeSetor'");
			$countReadChefe = count($readChefe);
			if($countReadChefe >= 1){
				foreach($readChefe as $chefe);	
			}
		}				
	}
?>

<?php
if($countReadSelecionada <= 0){
?>

<div id="guia_nAutorizada">
<h3>ERRO: Você NÃO tem autorização para acessar esta página!</h3>
<h4><a href="minhas_solicitacoes.php">Voltar</a></h4>	
</div><!--fecha div conteudo-->

<?php
}else{
?>

<div id="formulario">

<img src="../imagens/cabecalho_print.png" title="Serviço Público Federal" alt="Serviço público Federal" width="65px" />

<h2>SERVIÇO PÚBLICO FEDERAL</h2>
<h2>MINISTÉRIO DA EDUCAÇÃO</h2>
<h2>Secretaria de Educação Profissional e tecnológica</h2>
<h2>Instituto Federal de Educação, Ciência e Tecnologia do Rio Grande do Sul</h2>
<h2>Campus Feliz-RS</h2>

<h3>FORMULÁRIO DE CONTROLE DE VEÍCULOS OFICIAIS</h3>

<div class="num_guia">
  <span class="guia">GUIA Nº <b><?php echo $guia['id'].'/'.date('Y', strtotime($guia['criadoEm'])); ?></b></span>
  <span class="ano"><strong>Solicitada em</strong> <?php echo date('d/m/Y à\s H:i', strtotime($guia['criadoEm'])); ?></span>
</div><!--fecha div class num_guia-->

<div class='print'><a href='javascript:self.print()'>Imprimir</a></div>

<table width="1024px" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="campos" align="left">Solicitante</td>
    <td class="dados"  colspan="3" align="left" style="padding-left:10px"><b><?php echo $guia['servidor']; ?></b></td>
    <td class="campos_r" align="left">Siape</td>
    <td class="dados_r"  colspan="1" align="left" style="padding-left:10px"><b><?php echo $guia['siape']; ?></b></td>
  </tr>
  <tr>
    <td class="campos" align="left">Motorista</td>
    <td class="dados"  colspan="3" align="left" style="padding-left:10px"><b><?php echo $guia['motorista']; ?></b></td>
    <td class="campos_r" align="left">Siape Motorista</td>
    <td class="dados_r"  colspan="1" align="left" style="padding-left:10px"><b><?php echo $guia['siape_motorista']; ?></b></td>
  </tr>
  <tr>
    <td class="campos" align="left">CNH</td>
    <td class="dados"  colspan="3" align="left" style="padding-left:10px"><b><?php echo $guia['cnh_motorista']; ?></b></td>
    <td class="campos_r" align="left">Categoria CNH</td>
    <td class="dados_r"  colspan="1" align="left" style="padding-left:10px"><b><?php echo $guia['cnh_categoria']; ?></b></td>
  </tr>
  <tr>
    <td class="campos" align="left">Vencimento CNH</td>
    <td class="dados"  colspan="3" align="left" style="padding-left:10px"><b><?php echo date('d/m/Y', strtotime($guia['cnh_vencimento'])); ?></b></td>
    <td class="campos_r" align="left">OS Condutor</td>
    <td class="dados_r"  colspan="1" align="left" style="padding-left:10px"><b><?php echo $guia['os_motorista'].'/'.date('Y',strtotime($guia['os_motorista_data'])); ?></b></td>
  </tr>
  <tr>
    <td class="campos" align="left">Passageiros</td>
    <td colspan="5" align="left" style="padding-left:10px">
        <?php
            if($guia['passageiros'] == ""){
                echo 'Sem passageiros';
            }else{
                echo '<br />';
                $arr_passageiros   =   explode(',', $guia['passageiros']);
                $total_passageiros =   count($arr_passageiros);
                $i  =   1;
                while ($i <= $total_passageiros){
                    echo $i.'- '.$arr_passageiros[$i-1].'<br />'; 
                    $i++;    
                }
                echo '<br />';
            }
        ?> 
    </td>
  </tr>  
  <tr>
    <td class="campos" align="left">Finalidade</td>
    <td colspan="5" align="left" style="padding-left:10px"><b><?php echo $guia['finalidade']; ?></b></td>
  </tr>
  <tr>
    <td class="campos" align="left" >Descrição da Atividade</td>
    <td colspan="5" class="long_text" align="left"><b><?php echo $guia['desc_finalidade']; ?></b></td>
  </tr>
  <tr>
    <td  class="campos" align="left">Roteiro</td>
    <td colspan="5" align="left" style="padding-left:10px">
		<?php
            if($guia['roteiro_3'] != ''){
                echo $guia['roteiro'].'&nbsp;&nbsp;&raquo;&nbsp;&nbsp;'.$guia['roteiro_2'].'&nbsp;&nbsp;&raquo;&nbsp;&nbsp;'.$guia['roteiro_3'];
            }elseif($guia['roteiro_2'] != '' && $guia['roteiro_3'] == ''){
                echo $guia['roteiro'].'&nbsp;&nbsp;&raquo;&nbsp;&nbsp;'.$guia['roteiro_2'];
            }else{
                echo $guia['roteiro'];
            }
        ?>
    </td>
  </tr>
  <tr>
    <td class="campos" align="left">Data e Hora de Saída</td>
    <td colspan="3" align="left" style="padding-left:10px">
        <?php echo date('d/m/Y', strtotime($guia['data_uso'])).' às '.date('H:i', strtotime($guia['horario_uso'])); ?>
    </td>
    <td class="campos" align="left">Prev. de Retorno</td>
    <td colspan="1" align="left" style="padding-left:10px">
        <?php echo date('d/m/y', strtotime($guia['prev_retorno_data'])).' às '.date('H:i', strtotime($guia['prev_retorno_hora'])); ?>
    </td>
  </tr>
</table>

<div class="bloco1">
<h5>
  <b>Preenchimento a cargo do departamento</b><br />
  O Departamento autoriza com a <b>Guia Nº <?php echo $guia['id']; ?></b>, a saída do veículo <b><?php echo $guia['veiculo']; ?></b>, ficando sob a responsabilidade do
  condutor <b><?php echo $guia['motorista']; ?></b>, para que com saída e retorno no(s) dia(s) e hora acima descritos se desloque do IFRS - Campus Feliz.
</h5>
</div><!--fecha div class bloco1-->

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="cabecalho_1">
    <td align="center" colspan="2" >Saída</td>
    <td align="center" colspan="2">Chegada</td>
    <td align="center">Percorridos</td>
    <td align="center">Motorista</td>
  </tr>
  <tr class="cabecalho_2">
    <td align="center">Horas</td>
    <td align="center">KM</td>
    <td align="center">Horas</td>
    <td align="center">KM</td>
    <td align="center">KM</td>
    <td align="center" rowspan="2" class="motorista">&nbsp;</td>      
  </tr>
 <tr class="dados">
    <td align="center"><?php echo $guia['horario_uso']; ?></td>
    <td align="center"><?php echo $gui['km_saida']; ?></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>  
  </tr>
  <tr>
    <td class="combustivel" align="center">Combustível</td>
    <td align="center">(&nbsp;&nbsp;&nbsp;) Reserva</td>
    <td align="center">(&nbsp;&nbsp;&nbsp;) 1/4</td>
    <td align="center">(&nbsp;&nbsp;&nbsp;) 1/2</td>
    <td align="center">(&nbsp;&nbsp;&nbsp;) 3/4</td>
    <td align="center">(&nbsp;&nbsp;&nbsp;) 4/4</td>      
  </tr>
  <tr class="observacoes">
    <td align="center">Observações</td>     
    <td colspan="5"></td> 
  </tr>
</table>
<div class="bloco3">
<h6>Informações para abastecimento do veículo (Somente nos postos credenciados com a NUTRICASH)</h6>
</div><!--fecha div class bloco1-->

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="cabecalho_2">
    <td align="center" colspan="3">Código do Condutor</td>
    <td align="center" colspan="3">Senha para abastecimento</td>  
  </tr>
 <tr class="dados">
    <td align="center" colspan="3" width="50%" style="font-weight:bold;">
		<?php
			if($dadosMotorista['cod_abastecimento'] != 0){
        		echo $dadosMotorista['cod_abastecimento'];
			}else{
				echo 'Você não possui código para abastecimento do veículo';	
			}
		?>
    </td>
    <td align="center" colspan="3" width="50%" style="font-weight:bold;">
		<?php
		if($dadosMotorista['cod_abastecimento'] != 0){
			echo '**** <br /> 4 primeiros digitos do CPF';
		}else{
			echo 'Você não possui senha para abastecimento do veículo';
		}
		?>
    </td>  
  </tr>
</table>

<div id="clear"></div>

<div class="bloco2">
<h4 class="chefia">__________________________<br /><?php echo $chefe['chefe'].' ('.$chefe['abreviatura'].')'; ?><br /><span class="descreve_dap">Chefia Imediata<br /></span></h4>
<h4 class="solicitante">______________________<br />Solicitante</h4>
<h4 class="responsavel">
	<div class="img_assinatura"><img src="/admin/imagens/assinaturas/<?php echo $aceitador['assinatura']; ?>" /></div>
    <span class="listaAssinatura">__________________________</span><br />
    <span class="descreve_resp">Responsável pela liberação</span><br />
	<?php echo $aceitador['nome']; ?>
</h4>
</div><!--fecha div class bloco2-->

</div><!--fecha div formulario-->
<?php
}
?>

</body>
</html>
<?php
	ob_end_flush();
  }
?>
<?php
	}
  }else{
	header('Refresh: 10;url= index.php');	
}
?>