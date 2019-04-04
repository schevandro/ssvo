<?php
if (isset($_GET['id_solic'])) {
	
$num_guia 	 = $_GET['id_solic'];
$siape_carona 	 = $_SESSION['autUser']['siape'];
$email_carona 	 = $_SESSION['autUser']['email'];
$servidor_carona = $_SESSION['autUser']['nome'];
$data_solic		 = date('Y-m-d H:i:s');

//Busca solicitação mensionada
$readSolicitacao = read('vt_solicitacoes',"WHERE id = '$num_guia'");
$countSolicitacao = count($readSolicitacao);
if($countSolicitacao <= 0){
	header('Location: solicitacoes_all.php');
}else{
	
foreach($readSolicitacao as $dadosSolicitacao);

//Verifica se servidor já pediu carona para esta viagem
$readVerifica = read('vt_caronas',"WHERE siape = '$siape_carona' AND guia_carona = '$num_guia' AND situacao != 3");
$resultadoContaCarona = count($readVerifica);

if($resultadoContaCarona >= 3){
    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x'></i>&nbsp&nbsp&nbsp Você ja fez <strong>3</strong> pedidos de carona para esta solicitação de viagem. Por favor, aguarde a resposta do titular da viagem!</h4>";
}elseif($resultadoContaCarona > 0){
    foreach($readVerifica as $carona);
    //verifica situacao do pedido
    if($carona['situacao'] == 0){
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x'></i>&nbsp&nbsp&nbsp Seu pedido ainda não foi respondido pelo titular da Guia. Aguarde a resposta antes pedir carona novamente para esta viagem.</h4>";
    }elseif($carona['situacao'] == 1){
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x'></i>&nbsp&nbsp&nbsp Seu pedido de carona para esta viagem já foi ACEITO. Verifique seu e-mail.</h4>";
    }elseif($carona['situacao'] == 3){
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x'></i>&nbsp&nbsp&nbsp Você já Cancelou um pedido de carona para esta viagem. Fazer um novo pedido?</h4>";
        
        echo '
            <div id="pedir_carona">
                <div class="exibeDadosGuia">
                    <h1>Você confirma o pedido de carona para <strong>'.$dadosSolicitacao['servidor'].' </strong></h1>
                    <h1>No dia <strong>'.date("d/m/y", strtotime($dadosSolicitacao['data_uso'])).' às '.date("H:i", strtotime($dadosSolicitacao['horario_uso'])).'</strong></h1>
                    <h1>Para <strong>'.$dadosSolicitacao['roteiro'].'</strong></h1>
                </div>
                
                <div class="exibeBtnGuia">
                    <h3 onclick="stopCarona()">Cancelar</h3>
                    <form method="post">
                        <input type="submit" id="confirma" name="confirma" value="Confirmar" class="btn_confirmar" onclick="stopReCarona()" />
                    </form>
                </div>
            </div><!--fecha div pedir carona-->
        '; 
    }elseif($carona['situacao'] == 2){
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x'></i>&nbsp&nbsp&nbsp Seu pedido de carona para esta viagem foi NEGADO. Tem certeza que quer fazer outro pedido para esta viagem?</h4>";
        
        echo '
            <div id="pedir_carona">
                <div class="exibeDadosGuia">
                    <h1>Você confirma o pedido de carona para <strong>'.$dadosSolicitacao['servidor'].' </strong></h1>
                    <h1>No dia <strong>'.date("d/m/y", strtotime($dadosSolicitacao['data_uso'])).' às '.date("H:i", strtotime($dadosSolicitacao['horario_uso'])).'</strong></h1>
                    <h1>Para <strong>'.$dadosSolicitacao['roteiro'].'</strong></h1>
                </div>
                
                <div class="exibeBtnGuia">
                    <h3 onclick="stopCarona()">Cancelar</h3>
                    <form method="post">
                        <input type="submit" id="confirma" name="confirma" value="Confirmar" class="btn_confirmar" onclick="stopReCarona()" />
                    </form>
                </div>
            </div><!--fecha div pedir carona-->
        ';
    }
}else{
    //Verifica se solicitante da carona não é o próprio solicitante da viagem
    $readSolicitante = read('vt_solicitacoes',"WHERE (siape = '$siape_carona' AND id = '$num_guia')");
    $resultado_readSolicitante = count($readSolicitante);
    //Verifica se solicitante da carona não é o próprio motorista da viagem
    $readMotorista = read('vt_solicitacoes',"WHERE (siape_motorista = '$siape_carona' AND id = '$num_guia')");
    $resultado_readMotorista = count($readMotorista);
    //Verifica se o servidor já se encontra entre os caroneiros
    $exp_passageiros = explode(",", $dadosSolicitacao['passageiros']);
    if(in_array($servidor_carona, $exp_passageiros) ? $caroneirook = 1 : $caroneirook = 0);

    if($resultado_readSolicitante == 1 || $resultado_readMotorista == 1){
            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x'></i>&nbsp&nbsp&nbsp Você é o solicitante ou o motorista desta viagem <strong>(Guia nº {$dadosSolicitacao['id']})</strong>. Não é necessário solicitar carona!</h4>";
    }elseif($caroneirook == 1){
            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x'></i>&nbsp&nbsp&nbsp Você já se encontra entre os caroneiros desta viagem <strong>(Guia nº {$dadosSolicitacao['id']})</strong>.</h4>";
    }else{
            echo '
                <div id="pedir_carona">
                <div class="exibeDadosGuia">
                    <h1>Você confirma o pedido de carona para <strong>'.$dadosSolicitacao['servidor'].' </strong></h1>
                    <h1>No dia <strong>'.date("d/m/y", strtotime($dadosSolicitacao['data_uso'])).' às '.date("H:i", strtotime($dadosSolicitacao['horario_uso'])).'</strong></h1>
                    <h1>Para <strong>'.$dadosSolicitacao['roteiro'].'</strong></h1>
                </div>
                
                <div class="exibeBtnGuia">
                    <h3 onclick="stopCarona()">Cancelar</h3>
                    <form method="post">
                        <input type="submit" id="confirma" name="confirma" value="Confirmar" class="btn_confirmar" onclick="stopReCarona()" />
                    </form>
                </div>
            </div><!--fecha div pedir carona-->
            ';
    }
}

    if(isset($_POST['confirma'])){
            //Muda digito verificador na Guia correspondente
            $up['caronas'] = ($dadosSolicitacao['caronas'] + 1);
            $upCarona = update('vt_solicitacoes', $up, "id = '$num_guia'");
            
            $fcar['solicitadaEm']   = $data_solic;
            $fcar['servidor']       = $servidor_carona;
            $fcar['siape']          = $siape_carona;
            $fcar['email']          = $email_carona;
            $fcar['guia_carona']    = $num_guia;
            $fcar['data_viagem']    = $dadosSolicitacao['data_uso'];
            
            $sql_carona = create('vt_caronas', $fcar);
            
            if($sql_carona):
                //mensagem se OK
                header('Location: solicitacoes_all.php?solic=true');

                //Script para enviar email para o titular da guia
                $msg = '		
                <p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá '.$dadosSolicitacao['servidor'].',</p>
                <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que houve uma nova solicitação de CARONA para sua solicitação de viatura oficial para o dia '.date('d/m/Y',strtotime($dadosSolicitacao['data_uso'])).' através do <strong>sistema de viaturas oficiais</strong> do IFRS - Campus Feliz.<br /><br />
                Abaixo seguem os dados da sua solicitação e do solicitante da carona:</p>
                <hr />
                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitante de Carona:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$_SESSION['autUser']['nome'].'</span><br />
                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$email_carona.'</span><br />
                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Siape:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$siape_carona.'</span><br /><br />		
                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$dadosSolicitacao['roteiro'].'</span><br />
                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data da Viagem:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($dadosSolicitacao['data_uso'])).' às '.date('H:i',strtotime($dadosSolicitacao['horario_uso'])).'</span><br />
                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$dadosSolicitacao['finalidade'].' / '.$dadosSolicitacao['desc_finalidade'].'</span><br />
                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.date('d/m/Y',strtotime($dadosSolicitacao['prev_retorno_data'])).' às '.date('H:i',strtotime($dadosSolicitacao['prev_retorno_hora'])).'</span><br /><br />

                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas já confirmadas por você:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">'.$dadosSolicitacao['passageiros'].'<br /><br />
                <span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Carona solicitada em <strong>'.date('d/m/Y',strtotime($data_solic)).'</strong> às <strong>'.date('H:i',strtotime($data_solic)).'</strong></span><br /><br /><br />
                <hr />
                <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Para Aceitar ou Negar esta solicitação de carona, acesse o <strong>Sistema de Viaturas Oficiais do Campus Feliz</strong>.<br />
                </p>
                <img scr="http://200.17.85.251/imagens/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';

                sendMail('Pedido de carona para '.$dadosSolicitacao['roteiro'].'',$msg,MAILUSER,SITENAME,$dadosSolicitacao['email'],$servidor_carona);
            else:
                echo 'Impossível fazer esta solicitação de carona no momento. Tente mais tarde!';
            endif;      
    }
}
}
?>