<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../_assets/css/prints/print.css" rel="stylesheet" type="text/css" />
<style media="print" type="text/css">
	.print{display:none;}
</style>

</head>

<body>
<div id="box-print">
<?php
require('../dts/dbaSis.php');
require('../dts/getSis.php');
require('../dts/setSis.php');
require('../dts/outSis.php');

//Busca servidor
$servidor_buscado = $_POST['servidor'];
$sql_servidor = read('servidores',"WHERE nome = '{$servidor_buscado}'");

foreach ($sql_servidor as $res_servidor):
    $servidor_id            = $res_servidor['id'];
    $servidor_nome          = $res_servidor['nome'];
    $servidor_setor         = $res_servidor['setor'];
    $servidor_siape         = $res_servidor['siape'];
    $servidor_funcao        = $res_servidor['funcao'];
    $servidor_cargahoraria  = $res_servidor['carga_horaria'];
endforeach;

//Busca chefe do setor
$chefe_buscado = $servidor_setor;
$sql_chefe = read('setores',"WHERE abreviatura = '{$chefe_buscado}'");
foreach ($sql_chefe as $res_chefe):
    $setor_id            = $res_chefe['id'];
    $setor_nome          = $res_chefe['setor'];
    $setor_abreviatura   = $res_chefe['abreviatura'];
    $setor_chefe         = $res_chefe['chefe'];
endforeach;

//Recebe dados do usuario
$ano = date('Y');
$mes = $_POST['mes'];
$servidor = $servidor_nome;
$funcao = $servidor_funcao;
$cargahoraria = $servidor_cargahoraria;
$siape = $servidor_siape;


if($servidor == -1 or $mes ==""){
    echo "<div class='print-vazio'><h1>Erro!<br />É necessário selecionar o servidor e o mês!<br /><br /><a href='admin_geraPonto.php'>Voltar e selecionar o servidor</a></h1></div>";
}else{

//Conta dias do mês
if($mes == 01 or $mes == 03 or $mes == 05 or $mes == 07 or $mes == 08 or $mes == 10 or $mes == 12){
	$dias_mes = 31;
}else if ($mes == 02){
	$dias_mes = 28;
}else{
	$dias_mes = 30;
}


echo "
		<h1>CÂMPUS DE FELIZ</h1>
		<h2>FOLHA DE PRESENÇA &nbsp;&nbsp; |&nbsp;&nbsp;  MÊS: <strong>".$mes."/".$ano."</strong></h2>
		<h3><strong>NOME:</strong> ".$servidor_nome."</h3>
		<h3><strong>FUNÇÃO:</strong> ".$servidor_funcao."</h3>
		<h3><strong>MATRÍCULA:</strong> ".$servidor_siape."  &nbsp;&nbsp;-&nbsp;&nbsp;  <strong>CARGA HORÁRIA:</strong> ".$servidor_cargahoraria."</h3>
		<div class='print'><a href='javascript:self.print()'>Imprimir</a></div>
	<table cellpadding='0' cellspacing='0'>
		<tr class='titulo'>
			<td colspan='4'>1º Turno</td>
			<td colspan='3'>2º Turno</td>
			<td colspan='3'>3º Turno</td>
		</tr>
		<tr class='subtitulo'>
			<td>Dia</td>
			<td>Hora Entrada</td>
			<td>Hora Saída</td>
			<td>Rúbrica</td>
			<td>Hora Entrada</td>
			<td>Hora Saída</td>
			<td>Rúbrica</td>
			<td>Hora Entrada</td>
			<td>Hora Saída</td>
			<td>Rúbrica</td>
		</tr>
				
	
	";

//Acrescenta 1 dia ao calendario por questões de fórmula
$dia_mes_escreve = ($dias_mes + 1) - $dias_mes;

for($dia_mes_escreve = 1; $dia_mes_escreve <= $dias_mes; $dia_mes_escreve++ ){
	$dia = $dia_mes_escreve."/".$mes."/".$ano;
	
	//Descobre o dia da semana
	$diasemana = date("w", mktime(0,0,0,$mes,$dia_mes_escreve,$ano) );
	
	//Nomeia o dia da semana
	switch($diasemana) {
	case"0": $dia_semana = "domingo"; break;
	case"1": $dia_semana = "segunda"; break;
	case"2": $dia_semana = "terca"; break;
	case"3": $dia_semana = "quarta"; break;
	case"4": $dia_semana = "quinta"; break;
	case"5": $dia_semana = "sexta"; break;
	case"6": $dia_semana = "sabado"; break;
	}//Fecha o SWITCH

	//Ferifica se tem feriado
	$sql_listaferiado = read('feriados',"WHERE ano = {$ano} and mes = {$mes} ORDER BY mes ASC");
	if(!$sql_listaferiado):
            echo 'Impossível encontrar feriados na base de dados.';
        endif;
			
	foreach ($sql_listaferiado as $res_listaferiado):
            $listaferiado_dia 		= $res_listaferiado['dia'];
            $listaferiado_descricao = $res_listaferiado['descricao'];

            if($dia_mes_escreve == $listaferiado_dia){
                $diasemana = 7;
                $nomeferiado = $listaferiado_descricao;
            }
	endforeach;

	
		//Executa os comandos
		if($diasemana == 0){
			echo "<tr class='marca_dia'>
				  	<td class='mostra_dia'>".$dia_mes_escreve."</td>
					<td colspan='9' class='finalsemana'>Domingo</td>
				</tr>";
		}else if($diasemana == 6){
			echo "<tr class='marca_dia'>
				  	<td class='mostra_dia'>".$dia_mes_escreve."</td>
					<td colspan='9' class='finalsemana'>Sábado</td>
				</tr>";
		}else if($diasemana == 7){
			echo "<tr class='marca_dia'>
				  	<td class='mostra_dia'>".$dia_mes_escreve."</td>
					<td colspan='9' class='finalsemana'>".$nomeferiado."</td>
				</tr>";
		}else{
			echo "<tr>
				  	<td class='mostra_dia'>".$dia_mes_escreve."</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		}//Fecha o IF/ELSE
	
}//Fecha o FOR

echo "
	</table>
	<h4>".$servidor_nome."</h4>
	<h5>________________________________</h5>
	<h6>".$setor_chefe." (".$servidor_setor.")</h6><br />
	<h6><span>Chefia Imediata</span></h6>
	
	";

}
?>

</div><!--/box-print-->
</body>
</html>