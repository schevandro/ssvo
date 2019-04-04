<?php
if (isset($_GET['id_guia'])) {
    $num_viagem = $_GET['id_guia'];
    $id_carona = $_GET['id_carona'];
    $email_carona = $_GET['email_carona'];
    $agora = date('Y-m-d');
    $agoraHora = date('H:i');

    //Busca solicitação mensionada
    $readSolicitacaoCarona = read('vt_solicitacoes', "WHERE id = '$num_viagem'");
    foreach ($readSolicitacaoCarona as $readSC)
        ;

    //Busca Carona Solicitada
    $readCarona = read('vt_caronas', "WHERE id = '$id_carona'");
    foreach ($readCarona as $readC)
        ;

    //Envia E-mail Solicitando carona e apresenta mensagem e insere na tabela carona

    if (isset($_POST['confirma'])) {
        //Verifica se servidor já não esta entre os caroneiros
        $exp_passageiros = explode(",", $readSC['passageiros']);
        if (in_array($readC['servidor'], $exp_passageiros)) {
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp&nbsp&nbsp Este servidor já se encontra entre os caroneiros!</h4>";
        } else {
            //Muda digito verificador na Guia correspondente e Insere Caroneiro
            $insereDigito = $readSC['caronas'] - 1;
            if ($readSC['passageiros'] == '') {
                $inserePassageiro = $readC['servidor'];
            } else {
                $inserePassageiro = $readSC['passageiros'] . ',' . $readC['servidor'];
            }
            $upDados = array('caronas' => $insereDigito, 'passageiros' => $inserePassageiro);
            $upCarona = update('vt_solicitacoes', $upDados, "id = '$num_viagem'");

            //Muda situação na tabela de caronas
            $mudaSituacao = $carona_situacao + 1;
            $id_car = $readC['id'];
            $upAceita = array('situacao' => $mudaSituacao);
            $upAceitaCarona = update('vt_caronas', $upAceita, "id = $id_car");

            if ($upCarona) {
                if ($upAceitaCarona) {

                    //Script para enviar email para o caroneiro
                    $nomeTrat = explode(' ', $readC['servidor']);
                    $msg = '		
		<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá ' . $nomeTrat[0] . ',</p>
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que seu pedido de carona para ' . $readSC['roteiro'] . ' no dia ' . date('d/m/Y', strtotime($readSC['data_uso'])) . ' foi <strong style="color:#06F; font-size:16px;">ACEITO</strong>.<br /><br />
		Abaixo seguem os dados do titular da viagem:</p>
		<hr />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitande Titular:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['servidor'] . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['email'] . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['motorista'] . '</span><br />	
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['roteiro'] . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($readSC['data_uso'])) . ' às ' . date('H:i', strtotime($readSC['horario_uso'])) . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($readSC['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($readSC['prev_retorno_hora'])) . '</span><br /><br />
		
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['passageiros'] . '<br /><br />
		<span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Carona confirmada em <strong>' . date('d/m/Y') . '</strong> às <strong>' . date('H:i') . '</strong></span><br /><br /><br />
		<hr />
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Entre em contato com o titular da solicitação para combinar outros detalhes da viagem.<br />
		</p>
		<img scr="http://200.17.85.251/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';

                    sendMail('ACEITO - pedido de carona para ' . $readSC['roteiro'] . '', $msg, MAILUSER, SITENAME, $email_carona, $servidor_carona);

                    //mensagem se OK
                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp OK! O caroneiro será avisado por email, que seu pedido de carona foi <strong>aceito</strong></h4>";
                    header('refresh: 2;url=minhas_solicitacoes.php?solit=aberto');
                }
            }

            //Script para enviar emails para os interessados e acrescentar aviso na guia correspondente
        }
    }

    //Confirmar solicitação de carona
    echo '
        <div id="aceita_carona">
            <div class="exibeDadosGuia">
                <h1>Você confirma carona para <strong>' . $readC['servidor'] . ' </strong></h1>
                <h1>No dia <strong>' . date("d/m/y", strtotime($readSC['data_uso'])) . ' às ' . date("H:i", strtotime($readSC['horario_uso'])) . '</strong></h1>
                <h1>Para <strong>' . $readSC['roteiro'] . '</strong></h1>
            </div>

            <div class="exibeBtnGuia">
                <h3 onclick="stopPedido()">Cancelar</h3>
                <form method="post">
                    <input type="submit" id="confirma" name="confirma" value="Confirmar" class="btn_confirmar" onclick="stopReCarona()" />
                </form>
            </div>
        </div><!--fecha div pedir carona-->
    '; 
}
?>



<?php
if (isset($_GET['id_negou'])) {

    $num_viagem = $_GET['id_negou'];
    $id_carona = $_GET['id_carona'];
    $email_carona = $_GET['email_carona'];

    //Busca solicitação mensionada
    $readSolicitacaoCarona = read('vt_solicitacoes', "WHERE id = '$num_viagem'");
    foreach ($readSolicitacaoCarona as $readSC)
        ;

    //Busca Carona Solicitada
    $readCarona = read('vt_caronas', "WHERE id = '$id_carona'");
    foreach ($readCarona as $readC)
        ;

    //Envia E-mail avisando de carona negada e apresenta mensagem e insere na tabela carona

    if (isset($_POST['negar'])) {
        $motivo_negou = $_POST['motivo_negou'];

        if ($motivo_negou == "") {
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp&nbsp&nbsp É necessário informar um motivo para não aceirar um pedido de carona.</h4>";
        } else {
            //Muda digito verificador na Guia correspondente e Insere Caroneiro
            $insereDigito = $readSC['caronas'] - 1;
            $upDados = array('caronas' => $insereDigito);
            $upCarona = update('vt_solicitacoes', $upDados, "id = '$num_viagem'");

            //Muda situação na tabela de caronas
            $mudaSituacao = 2;
            $id_car = $readC['id'];
            $upAceita = array('situacao' => $mudaSituacao, 'motivo' => $motivo_negou);
            $upAceitaCarona = update('vt_caronas', $upAceita, "id = '$id_car'");

            if ($upCarona) {
                if ($upAceitaCarona) {

                    //Script para enviar email para o caroneiro
                    $nomeTrat = explode(' ', $readC['servidor']);
                    $msg = '		
		<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá ' . $nomeTrat[0] . ',</p>
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que seu pedido de carona para ' . $readSC['roteiro'] . ' no dia ' . date('d/m/Y', strtotime($readSC['data_uso'])) . ' foi <strong style="color:#F00; font-size:16px;">NEGADO</strong>.<br /><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#F00;">Motivo:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#000;">' . $motivo_negou . '</span><br /><br /><br /><br />
		Abaixo seguem os dados do titular da viagem:</p>
		<hr />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitande Titular:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['servidor'] . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['email'] . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['motorista'] . '</span><br />	
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['roteiro'] . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($readSC['data_uso'])) . ' às ' . date('H:i', strtotime($readSC['horario_uso'])) . '</span><br />
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($readSC['prev_retorno_data'])) . ' às ' . date('H:i', strtotime($readSC['prev_retorno_hora'])) . '</span><br />
		
		<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $readSC['passageiros'] . '<br /><br /><br />
		<span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Pedido de carona negado em <strong>' . date('d/m/Y') . '</strong> às <strong>' . date('H:i') . '</strong></span><br />
		<hr /><br />
		<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Entre em contato com o titular da solicitação para maiores detalhes.<br /><br /><br />
		</p>
		<img scr="http://200.17.85.251/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';

                    sendMail('NEGADO - pedido de carona para ' . $readSC['roteiro'] . '', $msg, MAILUSER, SITENAME, $email_carona, $servidor_carona);
                    //mensagem se OK
                    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; OK! O caroneiro será avisado por email, que seu pedido de carona <strong>NÃO foi aceito</strong></h4>";
                    header('refresh: 3;url=minhas_solicitacoes.php?solit=aberto');
                }
            }

            //Script para enviar emails para os interessados e acrescentar aviso na guia correspondente
        }
    }

    //Negar solicitação de carona
    echo '
        <div id="nega_carona">
            <div class="exibeDadosGuia">
                <h1>Informe um motivo para não aceitar o pedido de carona de <strong>' . $readC['servidor'] . ' </strong></h1>
                <h1>No dia <strong>' . date("d/m/y", strtotime($readSC['data_uso'])) . ' às ' . date("H:i", strtotime($readSC['horario_uso'])) . '</strong>, com destino a <strong>' . $readSC['roteiro'] . '</strong></h1>
            </div>

            <div class="exibeBtnGuia">
                <h3 onclick="stopNegaPedido()">Cancelar</h3>
                <form method="post">
                    <textarea name="motivo_negou" id="motivo_negou" class="motivo_negou"></textarea>
                    <input type="submit" id="negar" name="negar" value="Confirmar" class="btn_confirmar" />
                </form>
            </div>
        </div><!--fecha div pedir carona-->
    ';
}
?>