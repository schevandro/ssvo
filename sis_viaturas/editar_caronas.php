<?php 
    include_once "includes/inc_header.php";
    include_once "includes/inc_menu.php";
?>

<?php
    $id_solicit = strip_tags(trim($_GET['id_solicitacao']));
    $meusiape	= $_SESSION['autUser']['siape'];
    $situacao	= array('Autorizada','Aguardando...');
    $diaHoje	= date('Y-m-d');
    //verifica se solicitação é do usuário logado
    $readVsolit = read('vt_solicitacoes',"WHERE id = '$id_solicit' AND siape = '$meusiape' AND (situacao = '$situacao[0]' OR situacao = '$situacao[1]') AND data_uso >= '$diaHoje'");
    $countReadVSolit = count($readVsolit);
    if($countReadVSolit < 1){
        header('Location: minhas_solicitacoes.php');
    }else{
        foreach($readVsolit as $solicitacao);
        //Conta quantas caronas já estão confirmadas
        $confirmados = explode(',',$solicitacao['passageiros']);
        $numconfirmados = count($confirmados);
        if(empty($confirmados[0])){
                $numconfirmados = 0;	
        }
        //Busca capacidade do veiculo
        $veiculoSolicitacao = explode('-',$solicitacao['veiculo']);
        $buscaPlaca = $veiculoSolicitacao[1];
        $capacidade = read('vt_veiculos',"WHERE placa = '$buscaPlaca'");
        $countCapacidade = count($capacidade);
        if($countCapacidade < 1){
                $lugares = 4;	
        }else{
            foreach($capacidade as $cap);
            $lugares = $cap['capacidade']-1;	
        }

        //Conta quantos lugares restam
        $restam = $lugares - $numconfirmados;
?>


<!--Conteudo das páginas -->
<h1 class="titulo-secao-medio"><i class="fa fa-users"></i> Editar caroneiros da viagem nº <?= $id_solicit; ?></h1> 
    
<?php
    if($restam < 1){
        echo "<div class='btn_full_caroneiro'><i class='fa fa-user-plus' title='Veículo lotado'></i></div>";
    }else{
        echo "<div class='btn_add_caroneiro'>
                <a href='editar_caronas.php?id_solicitacao={$id_solicit}&addpass=true'><i class='fa fa-user-plus' title='Adicionar um caroneiro'></i></a>
              </div>
        ";
    }
?>

<div class="info_add_caroneiros">
    <h4 class="msg_topo" style="font-size: 1em;">Pedidos de carona para <?php echo $solicitacao['roteiro'].' em '.date('d/m/Y',strtotime($solicitacao['data_uso'])); ?></h4>
    <h5><i class="fa fa-caret-right"></i>&nbsp;&nbsp;&nbsp; Confirmadas: <strong><?php echo $numconfirmados; ?></strong></h5>
    <h5><i class="fa fa-caret-right"></i>&nbsp;&nbsp;&nbsp; Lugares restantes: <strong><?php echo $restam; ?></strong></h5><br />
    <h5><i class="fa fa-exclamation-triangle" style="color:#F00;"></i> <strong style="color: #F00;"><?php if($countCapacidade < 1){echo 'Veículo ainda não confirmado';}else{ echo $solicitacao['veiculo'];} ?></strong></h5>
</div>
	
    <?php
		if(isset($_GET['siapecar'])){
			//Se não é servidor e não possui siape
			if($_GET['siapecar'] == 0){
				$nome_caroneiro = strip_tags(trim($_GET['nomecar']));
				$id_solicitacao = strip_tags(trim($_GET['id_solicitacao']));
				$readBuscaPassageiro = read('vt_solicitacoes',"WHERE id = '$id_solicitacao'");
				foreach($readBuscaPassageiro as $passageiro);
				$exp_passageiros = explode(",", $passageiro['passageiros']);
				if(in_array($nome_caroneiro, $exp_passageiros)){
					$key = array_search($nome_caroneiro,$exp_passageiros);
					unset($exp_passageiros[$key]);
					$monta_passageiros = implode(',',$exp_passageiros);
					$dadosUp = array('passageiros' => $monta_passageiros);
					$upCaronas = update('vt_solicitacoes',$dadosUp,"id = '$id_solicitacao'");
					if($upCaronas){
						echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp OK! Passageiro excluído com sucesso.</h4>";
                                                    header('Refresh: 2,url=editar_caronas.php?id_solicitacao='.$id_solicitacao);
						}						
					}else{
						echo 'Erro ao excluir da tabela!';	
				}
					
			
			
			//Se é servidor e possui siape
			}else{		
			
			//procura o nome do caroneiro
			$siape_caroneiro = strip_tags(trim($_GET['siapecar']));
			$readBuscaNome = read('servidores',"WHERE siape = '$siape_caroneiro'");
			foreach($readBuscaNome as $nomecar);
			$nome_caroneiro = $nomecar['nome'];
			$email_caroneiro = $nomecar['email'];
			
			//Verifica se tem o nome do caroneiro nos passageiros da guia especificada
			$id_solicitacao = strip_tags(trim($_GET['id_solicitacao']));
			$readBuscaPassageiro = read('vt_solicitacoes',"WHERE id = '$id_solicitacao'");
			foreach($readBuscaPassageiro as $passageiro);
			$exp_passageiros = explode(",", $passageiro['passageiros']);
			if(in_array($nome_caroneiro, $exp_passageiros)){
				$key = array_search($nome_caroneiro,$exp_passageiros);
					unset($exp_passageiros[$key]);
					$monta_passageiros = implode(',',$exp_passageiros);
					$dadosUp = array('passageiros' => $monta_passageiros);
					$upCaronas = update('vt_solicitacoes',$dadosUp,"id = '$id_solicitacao'");
					if($upCaronas){
						//Verifica se tem pedido na tabela caronas
						$sitCarona = 1;
						$readTbCaronas = read('vt_caronas',"WHERE guia_carona = '$id_solicitacao' AND siape = '$siape_caroneiro' AND situacao = '$sitCarona'");
						$countReadTbCaronas = count($readTbCaronas);
						if($countReadTbCaronas > 0){
							foreach($readTbCaronas as $tbcaronas);
							$id_carona = $tbcaronas['id'];
							$digito = 2;
							$dadosTbCaronas = array('situacao' => $digito,'motivo' => 'Cancelado pelo titular da solicitacao');
							$upTbCaronas = update('vt_caronas',$dadosTbCaronas,"id = '$id_carona'");
							if($upTbCaronas){
							//Script para enviar email para o caroneiro
		$nomeTrat = explode(' ',$nomecar['nome']);
		$msg = '		
		<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá '.$nomeTrat[0].',</p>
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que seu pedido de carona para '.$passageiro['roteiro'].' no dia '.date('d/m/Y',strtotime($passageiro['data_uso'])).' foi <strong style="color:#F00; font-size:16px;">CANELADO</strong>.<br /><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#F00;">Motivo:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#000;">'.$passageiro['motivo'].'</span><br /><br /><br /><br />
		Abaixo seguem os dados do titular da viagem:</p>
		<hr />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitande Titular:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['servidor'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['email'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['motorista'].'</span><br />	
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['roteiro'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($passageiro['data_uso'])).' às '.date('H:i',strtotime($passageiro['horario_uso'])).'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($passageiro['prev_retorno_data'])).' às '.date('H:i',strtotime($passageiro['prev_retorno_hora'])).'</span><br />
		
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['passageiros'].'<br /><br /><br />
		<span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Pedido de carona cancelado em <strong>'.date('d/m/Y').'</strong> às <strong>'.date('H:i').'</strong></span><br />
		<hr /><br />
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Entre em contato com o titular da solicitação para maiores detalhes.<br /><br /><br />
		</p>
		<img scr="http://viaturas.feliz.ifrs.edu.br/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';
		
		sendMail('CANCELADO - pedido de carona para '.$passageiro['roteiro'].'',$msg,MAILUSER,SITENAME,$email_caroneiro,$nome_caroneiro);
                        echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp OK! Passageiro excluído com sucesso.</h4>";
                        header('Refresh: 2,url=editar_caronas.php?id_solicitacao='.$id_solicitacao);
                        }else{
                                echo 'Erro no digito da tb caronas!';	
                        }
                }else{
							//Script para enviar email para o caroneiro
		$nomeTrat = explode(' ',$nomecar['nome']);
		$msg = '		
		<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá '.$nomeTrat[0].',</p>
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que seu pedido de carona para '.$passageiro['roteiro'].' no dia '.date('d/m/Y',strtotime($passageiro['data_uso'])).' foi <strong style="color:#F00; font-size:16px;">CANELADO</strong>.<br /><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#F00;">Motivo:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#000;">'.$passageiro['motivo'].'</span><br /><br /><br /><br />
		Abaixo seguem os dados do titular da viagem:</p>
		<hr />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitande Titular:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['servidor'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['email'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['motorista'].'</span><br />	
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['roteiro'].'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($passageiro['data_uso'])).' às '.date('H:i',strtotime($passageiro['horario_uso'])).'</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($passageiro['prev_retorno_data'])).' às '.date('H:i',strtotime($passageiro['prev_retorno_hora'])).'</span><br />
		
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$passageiro['passageiros'].'<br /><br /><br />
		<span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Pedido de carona cancelado em <strong>'.date('d/m/Y').'</strong> às <strong>'.date('H:i').'</strong></span><br />
		<hr /><br />
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Entre em contato com o titular da solicitação para maiores detalhes.<br /><br /><br />
		</p>
		<img scr="http://viaturas.feliz.ifrs.edu.br/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';
		
		sendMail('CANCELADO - pedido de carona para '.$passageiro['roteiro'].'',$msg,MAILUSER,SITENAME,$email_caroneiro,$nome_caroneiro);
                                                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp OK! Passageiro excluído com sucesso.</h4>";
                                                    header('Refresh: 2,url=editar_caronas.php?id_solicitacao='.$id_solicitacao);
						}						
					}else{
                                            echo 'Erro ao excluir da tabela!';	
				}
			}
			}

		}		
	?>
    
    <?php
		if(isset($_POST['btn_addpass'])){
			$idsol = $_GET['id_solicitacao'];
			$nomepass = strip_tags(trim($_POST['passageiro']));
			$inputpass = strip_tags(trim($_POST['passageiro_input']));
			if($nomepass != '' || $inputpass != ''){
				if($nomepass != '' && $inputpass != ''){
                                    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle'></i>&nbsp&nbsp&nbsp Você deve escolher apenas um passageiro</h4>";
				}else{
					$readPass = read('vt_solicitacoes',"WHERE id = '$idsol'");
					foreach($readPass as $inPass);
					
					if($nomepass != '' && $inputpass == ''){
						if($inPass['passageiros'] == '' ? $exp_pass = $nomepass : $exp_pass = $inPass['passageiros'].','.$nomepass);
						$dadosUp = array('passageiros' => $exp_pass);
						$upPass = update('vt_solicitacoes',$dadosUp,"id = '$idsol'");
						if($upPass){
                                                    $_GET['addpass'] = '';
                                                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp OK! Passageiro adicionado com sucesso.</h4>";
                                                    header('Refresh: 2,url=editar_caronas.php?id_solicitacao='.$idsol);
						}
						
					}elseif($nomepass == '' && $inputpass != ''){
						if($inPass['passageiros'] == '' ? $exp_pass = $inputpass : $exp_pass = $inPass['passageiros'].','.$inputpass);
						$dadosUp = array('passageiros' => $exp_pass);
						$upPass = update('vt_solicitacoes',$dadosUp,"id = '$idsol'");
						if($upPass){
                                                    $_GET['addpass'] = '';
                                                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp OK! Passageiro adicionado com sucesso.</h4>";
                                                    header('Refresh: 2,url=editar_caronas.php?id_solicitacao='.$idsol);	
						}
					}
				}
			}else{				
                            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle'></i>&nbsp&nbsp&nbsp Selecione na lista de servidores ou informe o nome no campo de texto!</h4>";
                            $_GET['addpass'] = true;
			} 	
		}
	?>
    
    <?php
        if(isset($_GET['addpass']) && $_GET['addpass'] == 'true'){
            $readPassageiro = read('servidores',"ORDER BY nome ASC");
            echo '
                <h5 class="msg_topo_dois">Selecione ou informe o nome do passageiro</h5>
                <div class="add_passageiros_dados">
                <form name="addpass" method="post" >

                <select name="passageiro" id="passageiro">
                <option value="" selected>Selecione</option>';
                foreach ($readPassageiro as $select_passageiro) { $passageiro_selecionado = $select_passageiro['nome'];
                echo '<option value="'.$passageiro_selecionado.'">'.$passageiro_selecionado.'</option>';
                }
                echo '</select>

                    <span>Se o passageiro não for servidor do IFRS Campus Feliz, informe o nome do passageiro no campo abaixo:</span>	
                    <input type="text" name="passageiro_input" id="passageiro_input" value="" />

                    <input type="submit" name="btn_addpass" class="btn btn_green" value="Adicionar" />

                    </form>
                    </div>
                ';
        }           
    ?>

	 <?php
            if($numconfirmados == 0 AND (!isset($_GET['addpass']) && $_GET['addpass'] != 'true')){
                $car_exibeData = date('d/m/Y', strtotime($solicitacao['data_uso']));
                echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle'></i>&nbsp&nbsp&nbsp Não há caroneiros para {$solicitacao['roteiro']} em {$car_exibeData}</h4>";
            }elseif($numconfirmados != 0){	
        ?>  
        
	<div class="lista_fotos">
            <table border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        	<tr class="tr_header">
                <td align="left" style="padding-left:30px">Nome</td>
                <td align="center">Siape</td>
                <td align="center">Situação</td>
                <td align="center">Ações</td>
        	</tr>
            <?php
                for($a = 0; $a < $numconfirmados; $a++){
                    //Busca Siape
                    $readSiapeCarona = read('servidores',"WHERE nome = '$confirmados[$a]'");
                    $countReadSiapeCarona = count($readSiapeCarona);
                    if($countReadSiapeCarona > 0){
                            foreach($readSiapeCarona as $siapecarona);
                    }else{
                            $siapecarona['siape'] = 0;	
                    }
            ?>
            <tr class="lista_itens">
                <td align="left" style="padding-left:30px"><?php $b = $a +1; echo $b.' - '.$confirmados[$a]; ?></td>
                <td align="center" ><?php if($countReadSiapeCarona > 0){echo $siapecarona['siape'];}else{echo 'Não é servidor';} ?></td>
                <td align="center">
                    <?php echo "<i class='fa fa-check-square-o' style='font-size: 1.4em; color: #32A041;' title='Pedido de carona aceito'></i>";?>
                </td>
                <?php
                    if($solicitacao['data_uso'] >= $diaHoje){ 
                        echo "<td align='center' colspan='3'><a href='editar_caronas.php?id_solicitacao={$id_solicit}&siapecar={$siapecarona['siape']}&nomecar={$confirmados[$a]}' title='Excluir o caroneiro'><i class='fa fa-trash' style='color: #555; font-size: 1.4em;'></i></a></td>";
                    }else{
                        echo "<td align='center' colspan='3'><i class='fa fa-trash' style='color: #CCC; font-size: 1.4em;'></i></td>";                        
                    }
                ?>
        	</tr>
            <?php
				}
			?>
		</table>
	</div>

<?php
	}
}
?>
<!--Encerra conteúdo das páginas-->
<?php include_once "includes/inc_footer.php"; ?>