<?php
include_once "includes/inc_header.php";

if($_SESSION['autUser']['admin'] == 1){	

    //PAGINAÇÃO
    $pag = $_GET['pag'];
    if($pag >= '1'):
        $pag = $pag;
    else:
        $pag = '1';
    endif;
    $maximo = '100'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;
	
    //Busca sercidores administradores
    $contaAcesso 	= read('acessos');
    $num		= count($contaAcesso);
    $buscaAcesso 	= read('acessos',"ORDER BY login DESC LIMIT $inicio,$maximo");
?>
    <h1 class="titulo-secao"><i class="fa fa-dashboard"></i> Acessos ao sistema <span><?php echo $num; ?> acessos</span></h1>
    <a href="painel.php" class="btn btn_green fl_right" style="margin-bottom: 35px;" title="Voltar ao painel inicial"><i class="fa fa-dashboard"></i> VOLTAR AO PAINEL</a>
<?php
//Cabeçalho da Tabela 
     echo '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
                <tr class="tr_header">
                    <td align="left" width="30%" style="padding-left:15px;">Servidor</td>
                    <td align="center" width="25% 100px">Data / Hora (Login)</td>
                    <td align="center" width="25%">Data / Hora (Logoff)</td>
                </tr>';
      
//Foreach dos servidores administradores
            foreach ($buscaAcesso as $access){
                $i=$i+1;
                 //Verifica nível de acesso do usuário
                if ($access['nivel'] == 1):
                    $listNivel = "<i class='fa fa-cog' style='font-size: 1.3em;' title='Super Admin'></i>&nbsp;&nbsp;";
                elseif ($access['nivel'] == 2):
                    $listNivel = "<i class='fa fa-car' style='font-size: 1.3em;' title='Operador Viaturas'></i>&nbsp;&nbsp;";
                elseif ($access['nivel'] == 3):
                    $listNivel = "<i class='fa fa-group' style='font-size: 1.3em;' title='Operador CGP'></i>&nbsp;&nbsp;";
                else:
                   $listNivel = NULL;
                endif;
                
                $colorPage++;
                if ($colorPage % 2 == 0):
                    $cor = 'style="background:#f3f3f3;"';
                else:
                    $cor = 'style="background:#fff;"';
                endif;
                
	//Busca nome do servidor através do siape
	$siapeAcesso   = $access['siape'];
	$buscaServidor = read('servidores',"WHERE siape = '$siapeAcesso'");
	foreach($buscaServidor as $nomeServidor);
	if($access['logof'] != ''){
            $dataLogof = date('d/m/Y à\s H:i:s',strtotime($access['logof']));	
	}else{
            $dataLogof = '-';
	}
            echo '<tr '.$cor.' class="lista_itens">';
            echo '<td align="left" style="padding-left:15px;">'.$nomeServidor['nome'].'&nbsp;&nbsp;&nbsp;'.$listNivel.'</td>
                    <td align="center">'.date('d/m/Y à\s H:i:s',strtotime($access['login'])).'</td> 				
                    <td align="center">'.$dataLogof.'</td>
            </tr>
            ';
	}//fecha o foreach
	echo '</table>';

    
    //PAGINAÇÃO
    echo '<div id="paginator">';
    $total = $num;

    $paginas = ceil($total/$maximo);
    $links = '12'; //QUANTIDADE DE LINKS NO PAGINATOR

    echo "<a href=admin_eacessos.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

    for ($i = $pag-$links; $i <= $pag-1; $i++){
    if ($i <= 0){
    }else{
    echo"<a href=admin_eacessos.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
    }
    }echo "<h1>$pag</h1>";

    for($i = $pag + 1; $i <= $pag+$links; $i++){
    if($i > $paginas){
    }else{
    echo "<a href=admin_eacessos.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
    }
    }
    echo "<a href=admin_eacessos.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";

    echo '</div><!--fecha paginator-->';

}else{
    echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
}
include_once "includes/inc_footer.php";
?>