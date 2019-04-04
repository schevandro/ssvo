<?php

/*****************************************	
FUNÇÃO: GERA RESUMOS
*****************************************/

	function lmWords($string, $words = '100'){
		$string	= strip_tags($string);
		$count	= strlen($string);
		
		if($count <= $words){
			return $string;
		}else{
			$strpos = strrpos(substr($string,0,$words),' ');
			return substr($string,0,$strpos).'...';
		}
	 } 

/*****************************************	
FUNÇÃO: VALIDAÇÃO DE DATA
*****************************************/
	 
	 function valDate($data){

		$data = explode('/',$data);

		$dia = $data[0];
		$mes = $data[1];
		$ano = $data[2];

		$diav = true;
		$mesv = true;
		$anov = true;

		if($ano <= date('Y') && $ano >= 1900){
			
			$arrum = array('1','3','5','7','8','10','12');
			$arrdois = array('4','6','9','11');
			
			if(in_array($mes,$arrum)){
			
				if($dia < 1 || $dia > 31):
					$diav = false;
				endif;
				
			}elseif(in_array($mes,$arrdois)){
			
				if($dia < 1 || $dia > 30):
					$diav = false;
				endif;
				
			}elseif($mes == 2){
			
				if(($ano%4==0 && $ano%100!=0) || ($ano%400==0)){
					$fev = '29';
				}else{
					$fev = '28';
				}
				
				if($dia < 1 || $dia > $fev):
					$diav = false;
				endif;
			
			}else{
				$mesv = false;
			}
			
		}else{
			$anov = false;
		}
}

/*****************************************	
FUNÇÃO: VALIDAÇÃO DE CPF
*****************************************/

	function valCPF($cpf){
		$cpf = preg_replace('/[^0-9]/','',$cpf);
		
		$digitoA = 0;
		$digitoB = 0;
		
		for($i = 0, $x = 10; $i <= 8; $i++, $x--){
			$digitoA += $cpf[$i] * $x;
		}
		
		for($i = 0, $x = 11; $i <= 9; $i++, $x--){
			
			if(str_repeat($i,11) == $cpf){
				return false;
			}
			
			$digitoB += $cpf[$i] * $x;		
		}
		
		$somaA = (($digitoA%11) < 2) ? 0 : 11-($digitoA%11);
		$somaB = (($digitoB%11) < 2) ? 0 : 11-($digitoB%11);
		
		if($somaA != $cpf[9] || $somaB != $cpf[10]){
			return false;
		}else{
			return true;
		}
	}

/*****************************************	
FUNÇÃO: VALIDAÇÃO DE ENDEREÇO DE EMAIL
*****************************************/

	function valMail($email){
		if(preg_match('/^[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/',$email)){
			return true;
		}else{
			return false;
		}
	}	

/*****************************************	
FUNÇÃO: VALIDA O EMAIL
*****************************************/	

	function sendMail($assunto, $mensagem, $remetente, $nomeRemetente, $destino, $nomeDestino, $reply = NULL, $replyNome = NULL){
		
		require_once('mail/class.phpmailer.php'); //Include pasta/classe do PHPMailer
		
		$mail = new PHPMailer(); //INICIA A CLASSE
		$mail->IsSMTP(); //Habilita envio SMPT
		$mail->SMTPAuth = true; //Ativa email autenticado
		$mail->IsHTML(true);
		
		$mail->Host = MAILHOST; //Servidor de envio
		//$mail->SMTPDebug  = 2; // enables SMTP debug information (for testing)
		$mail->SMTPAuth   = MAILAUTH; // enable SMTP authentication
		$mail->SMTPSecure = MAILSEC; // sets the prefix to the servier
		$mail->Port = MAILPORT; //Porta de envio
		$mail->Username = MAILUSER; //email para smtp autenticado
		$mail->Password = MAILPASS; //seleciona a porta de envio
		
		$mail->From = utf8_decode($remetente); //remtente
		$mail->FromName = utf8_decode($nomeRemetente); //remtetene nome
		
		if($reply != NULL){
			$mail->addReplyTo(utf8_decode($reply),utf8_decode($replyNome));
		}
		
		$mail->Subject = utf8_decode($assunto); //assunto
		$mail->Body = utf8_decode($mensagem); //mensagem
		$mail->AddAddress(utf8_decode($destino),utf8_decode($nomeDestino)); //email e nome do destino
		
		if($mail->Send()){
			return true;
		}else{
			return false;
		}
	}	

/*****************************************	
FUNÇÃO: FORAMATA DATA EM TIMESTAMP
*****************************************/		
	
	function formDate(){
		$timestamp = explode(" ",$data);
		$getData = $timestamp[0];
		$getTime = $timestamp[1];
		
			$setData = explode('/',$getData);
			$dia	 = $setData[0];
			$mes	 = $setData[1];
			$ano	 = $setData[2];
			
		if(!$getTime):
			$getTime = date('H:i:s');
		endif;
		
		$resultado = $ano.'-'.$mes.'-'.$dia.' '.$getTime;
		
		return $resultado;
	}	
	
/*****************************************	
FUNÇÃO: GERA ESTATÍSTICAS DO SITE (usuários online, visitas, visitantes, pageviews)
*****************************************/

	function viewManager($times = 600){//definir publico alvo para definir o tempo da sessão (em segundos)
		
		$selMes = date('m');
		$selAno = date('Y');
		
		if(empty($_SESSION['startView']['sessao'])){
			$_SESSION['startView']['sessao']	= session_id();
			$_SESSION['startView']['ip']		= $_SERVER['REMOTE_ADDR'];
			$_SESSION['startView']['url']	    = $_SERVER['PHP_SELF'];
			$_SESSION['startView']['time_end']  = time() + $times;
			
			create('up_views_online',$_SESSION['startView']);
			
			$readViews = read('up_views',"WHERE mes = '$selMes' AND ano = '$selAno'");
			if(!$readViews){
				$createViews = array('mes' => $selMes, 'ano' => $selAno);
				create('up_views',$createViews);
			}else{
				foreach($readViews as $views);
					if(empty($_COOKIE['startView'])){
						$updateViews = array(
							'visitas' => $views['visitas']+1,
							'visitantes' => $views['visitantes']+1
						);
						update('up_views',$updateViews,"mes = '$selMes' AND ano = '$selAno'");
						setcookie('startView',time(),time()+60*60*24,'/');
					}else{
						$updateVisitas = array('visitas' => $views['visitas']+1);
						update('up_views',$updateVisitas,"mes = '$selMes' AND ano = '$selAno'");
					}
			}
			
		}else{
			$readPageViews = read('up_views',"WHERE mes = '$selMes' AND ano = '$selAno'");
			if($readPageViews){
				foreach($readPageViews as $rpgv);
					$updatePageViews = array('pageviews' => $rpgv['pageviews']+1);
					update('up_views',$updatePageViews,"mes = '$selMes' AND ano = '$selAno'");
			}
		
			$id_sessao	= $_SESSION['startView']['sessao'];
			if($_SESSION['startView']['time_end'] <= time()){
				delete('up_views_online',"sessao = '$id_sessao' OR time_end <= time(NOW())");
				unset($_SESSION['startView']);
			}else{
				$_SESSION['startView']['time_end']  = time() + $times;
				$timeEnd = array('time_end' => $_SESSION['startView']['time_end']);
				update('up_views_online',$timeEnd,"sessao = '$id_sessao'");
			}
		}
	}

/*****************************************
FUNÇÃO: PAGINAÇÃO DE RESULTADOS
*****************************************/

	function paginator($tabela, $cond, $maximo, $link, $pag, $width = NULL, $maxlinks = '4'){
		$readPaginator = read("$tabela", "$cond");
		$total = count($readPaginator);
		if($total > $maximo){
			$paginas = ceil($total/$maximo);
			if($width){
				echo '<div class="paginator" style="width:'.$width.'">';
			}else{
				echo '<div class="paginator">';
			}
			echo '<a href="'.$link.'1">Primeira Página</a>&nbsp;&nbsp;&nbsp;';
				for($i = $pag - $maxlinks; $i <= $pag - 1; $i++){
					if($i >= 1){
						echo '<a href="'.$link.$i.'">'.$i.'</a>&nbsp;&nbsp;&nbsp;';
					}
				}
			echo '<span class="atv">'.$pag.'</span>&nbsp;&nbsp;&nbsp;';
				for($i = $pag + 1; $i <= $pag + $maxlinks; $i++){
					if($i <= $paginas){
						echo '<a href="'.$link.$i.'">'.$i.'</a>&nbsp;&nbsp;&nbsp;';
					}
				}
			echo '<a href="'.$link.$paginas.'">Última Página</a>';
			echo '</div><!-- /paginator -->';
		}
	}
?>