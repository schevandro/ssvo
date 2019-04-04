<?php
include_once "includes/inc_header.php";
$hoje_ipva = date('Y-m-d');
$hoje	   = date('Y-m-d');

if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2){

    //Lista os veículos
    $readVeiculos = read('vt_veiculos',"WHERE deletado = '0' ORDER BY veiculo ASC");
    $countReadVeiculos = count($readVeiculos);
    $readVeiculosAtivos = read('vt_veiculos',"WHERE ativo = 1");
    $countReadVeiculosAtivos = count($countReadVeiculosAtivos);
?>
    
    <h1 class="titulo-secao"><i class="fa fa-cogs"></i> Manutenções dos veículos <span><?php echo $countReadVeiculos; ?> veículos registrados</span></h1>
 
    <div class="painel_index">    
    <div class="painel_viaturas" style="border:0;">
<?php
    echo "<a href='admin_eveiculos.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Veículos'><i class='fa fa-list'></i> Listar Veículos</a>";

    $hojeViatura	= date('Y-m-d');
    $hojeAgora      = date('H:i:s');
    $veiculos	= read('vt_veiculos',"WHERE deletado = '0' ORDER BY veiculo ASC");
    $countVeiculos 	= count($veiculos);
			
			if($countVeiculos >= 1){
				
				foreach ($veiculos as $veic){
					$veiculoPlaca = $veic['veiculo'].'-'.$veic['placa'];

						//Verifica as condições de oleo, revisão, combustível, ipva, seguro, geometria e correa dentada
						//Busca o veiculo
						$placaBuscada		= $veic['placa'];
						$buscaVeiculo		= read('vt_veiculos',"WHERE placa = '$placaBuscada'");
				
						foreach($buscaVeiculo as $dadosViat);

						//Combustivel
            if ($dadosViat['combustivel'] < 1) {
                $vei_gasDados = '<h4><i class="fa fa-exclamation-circle"></i> Está na <strong>reserva</strong> de combustível</h4>';
                $vei_gas = 0;
            } else {
                $vei_gasDados = NULL;
                $vei_gas = 1;
            }

            //Oleo do Motor
            if ($dadosViat['troca_oleo'] < $dadosViat['km']) {
                $vei_oleoDifKm = ($dadosViat['km'] - $dadosViat['troca_oleo']);
                $vei_oleoDados = '<h4><i class="fa fa-exclamation-circle"></i> Troca do óleo do motor vencida a <strong>' . $vei_oleoDifKm . '</strong> KM';
                $vei_oleo = 0;
            } elseif (($dadosViat['troca_oleo'] - $dadosViat['km']) <= 50) {
                $vei_oleoDifKm = -($dadosViat['km'] - $dadosViat['troca_oleo']);
                $vei_oleoDados = '<h4><i class="fa fa-exclamation-circle"></i> Troca do óleo vencendo em <strong>' . $vei_oleoDifKm . '</strong> KM</h4>';
                $vei_oleo = 0;
            } else {
                $vei_oleoDados = NULL;
                $vei_oleo = 1;
            }

            //Revisão Mecanica
            if ($dadosViat['revisao'] < $dadosViat['km']) {
                $vei_revisaoDifKm = ($dadosViat['km'] - $dadosViat['revisao']);
                $vei_revisaoDados = '<h4><i class="fa fa-exclamation-circle"></i> Revisão mecânica vencida a <strong>' . $vei_revisaoDifKm . '</strong> KM</h4>';
                $vei_revisao = 0;
            } elseif (($dadosViat['revisao'] - $dadosViat['km']) <= 50) {
                $vei_revisaoDifKm = -($dadosViat['km'] - $dadosViat['revisao']);
                $vei_revisaoDados = '<h4><i class="fa fa-exclamation-circle"></i> Revisão mecânica vencendo em <strong>' . $vei_revisaoDifKm . '</strong> KM</h4>';
                $vei_revisao = 0;
            } else {
                $vei_revisaoDados = NULL;
                $vei_revisao = 1;
            }

            //Geometria e balanceamento
            if ($dadosViat['geometria'] < $dadosViat['km']) {
                $vei_geometriaDifKm = ($dadosViat['km'] - $dadosViat['geometria']);
                $vei_geometriaDados = '<h4><i class="fa fa-exclamation-circle"></i> Geometria/Balanceamento vencido a <strong>' . $vei_geometriaDifKm . '</strong> KM</h4>';
                $vei_geometria = 0;
            } elseif (($dadosViat['geometria'] - $dadosViat['km']) <= 50) {
                $vei_geometriaDifKm = -($dadosViat['km'] - $dadosViat['geometria']);
                $vei_geometriaDados = '<h4><i class="fa fa-exclamation-circle"></i> Geometria/Balanceamento vencendo em <strong>' . $vei_geometriaDifKm . '</strong> KM</h4>';
                $vei_geometria = 0;
            } else {
                $vei_geometriaDados = NULL;
                $vei_geometria = 1;
            }
						
            //Correa Dentada
            if($dadosViat['correa'] < $dadosViat['km']){
                    $vei_correaDifKm	=	($dadosViat['km'] - $dadosViat['correa']);
                    $vei_correaDados	=	'<h4><i class="fa fa-exclamation-circle"></i> É necessário trocar a Corrêa Dentada do motor. Troca vencida a <strong style="font-size:14px; color:#F00;">'.$vei_correaDifKm.'</strong> KM</h4>'; 
                    $vei_correa			=	0;
            }elseif(($dadosViat['correa'] - $dadosViat['km']) <= 100){
                    $vei_correaDifKm	=	-($dadosViat['km'] - $dadosViat['correa']);
                    $vei_correaDados	=	'<h4><i class="fa fa-exclamation-circle"></i> Troca da Corrêa Dentada esta vencendo em <strong style="font-size:14px; color:#F00;">'.$vei_correaDifKm.'</strong> KM</h4>'; 
                    $vei_correa			=	0;
            }else{
                    $vei_correaDados	=	NULL;
                    $vei_correa			=	1;	
            }	
						
            echo '<div class="mostraVeiculo" style="padding: 30px 15px;">
                        <img src="imagens/veiculos/'.$veic['foto'].'" alt="'.$veic['veiculo'].'" title="'.$veic['km'].' km" height="50px" />
                        <h1 class="h3Manut">'.$veic['veiculo'].'</h1>
                        <h5 class="h4Manut_placa">'.$veic['placa'].'</h5>';

                        if($vei_oleo == 0 || $vei_revisao == 0 || $vei_correa == 0 || $vei_geometria == 0):
                                echo '<div class="atencaoManut" value="' . $veic['placa'] . '">
                                        <i class="fa fa-exclamation-triangle fa-3x" style="color: #F90;" title="Existem alertas para este veículo"></i>
                                      </div>';
                        endif;

            echo '<div class="btn_manut">
                        <a href="admin_listManutencao.php?idVeic='.$veic['id'].'" class="btn btn_orange fl_right" style="margin-top: -15px; margin-right: 10px;">Histórico</a>		
                        <a href="admin_dadosProxManutencao.php?idVeic='.$veic['id'].'" class="btn btn_blue fl_right" style="margin-top: -15px; margin-right: 10px;">Dados Próx. Revisão</a>
                        <a href="admin_cadManutencao.php?idVeic='.$veic['id'].'" class="btn btn_green fl_right" style="margin-top: -15px; margin-right: 10px;">Nova Revisão</a>
                    </div></div>';

            //Div de detalhamento dos avisos		
            echo '<div class="avisosVeiculo '.$veic['placa'].'">
                        '.$vei_oleoDados.'
                        '.$vei_revisaoDados.'
                        '.$vei_correaDados.'
                        '.$vei_geometriaDados.'							
                    </div>';			
        }

    }else{
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Não existem veículos cadastrados no sistema!</h4>";
    }//if countVeiculos

}else{
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
}
include_once "includes/inc_footer.php";
?>