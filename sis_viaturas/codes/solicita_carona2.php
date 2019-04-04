<?php

if (isset($_GET['id_solic']) && empty($japediu)) {

    $num_guia = $_GET['id_solic'];
    $siape_carona = $_SESSION['autUser']['siape'];
    $email_carona = $_SESSION['autUser']['email'];
    $servidor_carona = $_SESSION['autUser']['nome'];
    $data_solic = date('Y-m-d H:i:s');

//Busca se já pediu carona
    $readDuplica = read('vt_caronas', "WHERE siape = '$siape_carona' AND guia_carona = '$num_guia'");
    $countDuplica = count($readDuplica);

//Busca solicitação mensionada
    $readSolicitacao = read('vt_solicitacoes', "WHERE id = '$num_guia'");
    foreach ($readSolicitacao as $dadosSolicitacao)
        ;

//Envia E-mail Solicitando carona e apresenta mensagem e insere na tabela carona
    if (isset($_POST['confirma'])) {
        $japediu = 1;
        $naopediu = 1;
        $readVerifica = read('vt_caronas', "WHERE siape = '$siape_carona' AND guia_carona = '$num_guia'");
        $resultadoContaCarona = count($readVerifica);

        if ($resultadoContaCarona > 0) {
            foreach ($readVerifica as $vCar)
                ;
        } else {

            //Verifica se solicitante da carona não é o próprio motorista
            $readMotorista = read('vt_solicitacoes', "WHERE (siape = '$siape_carona' AND id = '$num_guia')");
            $resultado_readMotorista = count($readMotorista);

            if ($resultado_readMotorista > 0) {

                echo '<div class="msg_cadastraerro"><img src="../imagens/ico_erro.jpg" title="ERRO" alt="ERRO" width="30px" /><h6>Você é o motorista desta viagem. Não é necessário solictar carona para a mesma.</h6></div>';
            } else {

                //Muda digito verificador na Guia correspondente
                $up['caronas'] = ($solicitacao_caronas + 1);
                $upCarona = update('vt_solicitacoes', $up, "id = '$num_guia'");

                $fcar['solicitadaEm'] = $data_solic;
                $fcar['servidor'] = $servidor_carona;
                $fcar['siape'] = $siape_carona;
                $fcar['email'] = $email_carona;
                $fcar['guia_carona'] = $num_guia;
                $fcar['data_viagem'] = $dadosSolicitacao['data_uso'];

                $sql_carona = create('vt_caronas', $fcar);

                if ($sql_carona):
                    //mensagem se OK
                    echo '<div class="msg_cadastraok"><img src="../imagens/ico_ok.png" title="OK" alt="OK" width="30px" /><h6>Solicitação de Carona Enviada com Sucesso! Aguarde a resposta do titular da GUIA.</h6></div>';

                    //Script para enviar email para o titular da guia
                    $msg = '		
                    <p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá ' . $dadosSolicitacao['servidor'] . ',</p>
                    <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que houve uma nova solicitação de CARONA para sua solicitação de viatura oficial para o dia ' . date('d/m/Y', strtotime($dadosSolicitacao['data_uso'])) . ' através do <strong>sistema de viaturas oficiais</strong> do IFRS - Campus Feliz.<br /><br />
                    Abaixo seguem os dados da sua solicitação e do solicitante da carona:</p>
                    <hr />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitante de Carona:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $_SESSION['autUser']['nome'] . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $email_carona . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Siape:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $siape_carona . '</span><br /><br />		
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $dadosSolicitacao['roteiro'] . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($dadosSolicitacao['data_uso'])) . ' às ' . date('H:i', strtotime($dadosSolicitacao['horario_uso'])) . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $dadosSolicitacao['finalidade'] . ' / ' . $dadosSolicitacao['desc_finalidade'] . '</span><br />
                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($dadosSolicitacao['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($dadosSolicitacao['prev_retorno_hora'])) . '</span><br /><br />

                    <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas já confirmadas por você:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $dadosSolicitacao['passageiros'] . '<br /><br />
                    <span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Carona solicitada em <strong>' . date('d/m/Y', strtotime($data_solic)) . '</strong> às <strong>' . date('H:i', strtotime($data_solic)) . '</strong></span><br /><br /><br />
                    <hr />
                    <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Para Aceitar ou Negar esta solicitação de carona, acesse o <strong>Sistema de Viaturas Oficiais do Campus Feliz</strong>.<br />
                    </p>
                    <img scr="http://200.17.85.251/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';

                    sendMail('Pedido de carona para ' . $dadosSolicitacao['roteiro'] . '', $msg, MAILUSER, SITENAME, $dadosSolicitacao['email'], $servidor_carona);
                else:
                    echo 'Impossível fazer esta solicitação de carona no momento. Tente mais tarde!';
                endif;
            }
        }
    }


//Confirmar solicitação de carona
    $readDuplica = read('vt_caronas', "WHERE siape = '$siape_carona' AND guia_carona = '$num_guia'");
    $countDuplica = count($readDuplica);

    if ($countDuplica <= 0 && empty($naopediu)) {

        echo '
	<div id="pedir_carona">	
		<h1>Você confirma o pedido de carona para <strong>' . $dadosSolicitacao['servidor'] . ' </strong></h1>
		<h1>No dia <strong>' . date("d/m/y", strtotime($dadosSolicitacao['data_uso'])) . ' às ' . date("H:i", strtotime($dadosSolicitacao['horario_uso'])) . '</strong></h1>
		<h1>Para <strong>' . $dadosSolicitacao['roteiro'] . '</strong></h1>
		<form method="post">
			<input type="submit" id="confirma" name="confirma" value="Confirmar" class="btn_confirmar" />
		</form>
		<h3 onclick="stopCarona_b()">Cancelar</h3>
	</div><!--fecha div pedir carona-->
	';
    } elseif ($countDuplica > 0 && empty($japediu)) {

        foreach ($readDuplica as $vDup)
            ;

        if ($vDup['situacao'] != 3):

            echo '
			<div id="repedir_carona">	
				<h1>Você já solicitou carona para esta viagem.</h1>
				<h1>Fazer novo pedido para <strong>' . $dadosSolicitacao['servidor'] . '</strong>?</h1>
				<h1>Destino: <strong>' . $dadosSolicitacao['roteiro'] . '</strong> dia ' . date("d/m/y", strtotime($dadosSolicitacao['data_uso'])) . ' às ' . date("H:i", strtotime($dadosSolicitacao['horario_uso'])) . '</h1>
				<form method="post">
					<input type="submit" id="confirma" name="confirma" value="Sim, fazer" class="btn_confirmar" />
				</form>
				<h3 onclick="stopReCarona()">Cancelar</h3>
			</div><!--fecha div pedir carona-->
		';

        endif;
    }
}
?>