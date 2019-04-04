<?php
if (isset($_GET['id_cancela'])) {
	
$del_id		 	 = $_GET['id_cancela'];
$sit_cancelada	 = 3;
$guia_viagem	 = $_GET['guia_viagem'];
$siape_carona	 = $_SESSION['autUser']['siape'];
$servidor_carona = $_SESSION['autUser']['nome'];

//Encontra a solicitação correspondente
$readBuscaGuia = read('vt_solicitacoes',"WHERE id = '$guia_viagem'");
foreach($readBuscaGuia as $readBG);

	//Conta os passageiros
	$separa_Passageiros	=	explode(",", $readBG['passageiros']);
	$qtd_passageiros	=	count($separa_Passageiros);
	
	//Caronas
        if (in_array($_SESSION['autUser']['nome'], $separa_Passageiros)) { 
            foreach($separa_Passageiros as $c => $v) {
                if($v == $_SESSION['autUser']['nome']) {
                        unset($separa_Passageiros[$c]);
                }
            }
            $guia_Caronas_Exclui	= $guia_Caronas;
        }else{
            $guia_Caronas_Exclui	= $guia_Caronas - 1;
        }
		
		if(empty($separa_Passageiros) || $qtd_passageiros == 0){
			$atualiza_passageiros = '';	
		}else{
    		$atualiza_passageiros = implode(",", $separa_Passageiros);  
		}
		
		if($readBG['caronas'] == 0){
			$atualizaItem = 0;	
		}else{
			$atualizaItem = $readBG['caronas'] - 1;
		}

//Muda digito verificador da tabela caronas
$up_DadosCarona = array('situacao' => $sit_cancelada);
$up_tbCaronas = update('vt_caronas',$up_DadosCarona,"id = '$del_id' AND siape = '$siape_carona'");
	
	//Muda digito verificador na tabela solicitações e apaga nome do caroneiro
	$up_DadosSolit = array('caronas' => $atualizaItem,'passageiros' => $atualiza_passageiros);
	$up_tbSolit	   = update('vt_solicitacoes',$up_DadosSolit,"id = '$guia_viagem'");
	
	if($up_tbCaronas){
		if($up_tbSolit){
			
			//Envia email para o titular da Guia avisando do cancelamento
		$msg = '		
		<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá '.$readBG['servidor'].',</p>
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que o servidor '.$servidor_carona.',<strong style="color:#F00"> CANCELOU</strong> o pedido de carona feito para '.$readBG['roteiro'].'. <br /><br />
		Abaixo seguem os dados da sua solicitação:</p>
		<hr />		
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$readBG['roteiro'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($readBG['data_uso'])).' às '.date('H:i',strtotime($readBG['horario_uso'])).'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$readBG['finalidade'].' / '.$readBG['desc_finalidade'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($readBG['prev_retorno_data'])).' às '.date('H:i',strtotime($readBG['prev_retorno_hora'])).'</span><br /><br />
		
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas já confirmadas por você:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.($readBG['passageiros'] != '' ? $caronas = $readBG['passageiros'] : $caronas = 'Nenhum caroneiro').'<br /><br />
		<span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Carona cancelada em <strong>'.date('d/m/Y').'</strong> às <strong>'.date('H:i').'</strong></span><br />
		<hr />
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Não é necessário nenhuma atitude, o sistema automaticamente excluirá o nome do servidor da lista de caroneiros.<br />
		</p>
		<img scr="http://200.17.85.251/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';
		
		sendMail('Cancelamento de Carona para '.$readBG['roteiro'].'',$msg,MAILUSER,SITENAME,$readBG['email'],$servidor_carona);			
                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Pedido de Carona Cancelado!</h4>";
                    header("Refresh: 2;url=pedidos_carona.php"); 
		}
	}
}