<?php

/*****************************************	
SETA URL DA HOME
*****************************************/

	function setHome(){
		echo BASE;
	}

/*****************************************	
FUNÇÃO: INCLUI ARQUIVOS
*****************************************/	

	function setArq($nomeArquivo){
		if(file_exists($nomeArquivo.'.php')){
			include($nomeArquivo.'.php');
		}else{
			echo 'Erro ao incluir <strong>'.$nomeArquivo.'.php</strong>, arquivo ou caminho inexistente!';
		}
	}

/*****************************************	
TRANSFORMA STRING EM URL
*****************************************/

	function setUri($string){
		$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
		$b = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';	
		$string = utf8_decode($string);
		$string = strtr($string, utf8_decode($a), $b);
		$string = strip_tags(trim($string));
		$string = str_replace(" ","-",$string);
		$string = str_replace(array("-----","----","---","--"),"-",$string);
		return strtolower(utf8_encode($string));
	}

/*****************************************	
SOMA VISITAS
*****************************************/
	function setViews($topicoId){
		$topicoId = mysql_real_escape_string($topicoId);
		$readArtigo = read('up_posts',"WHERE id = '$topicoId'");
		
		foreach($readArtigo as $artigo);
			$views = $artigo['visitas'];
			$views = $views+1;
			$dataViews = array(
				'visitas' => $views
			);
			
			update('up_posts',$dataViews,"id = '$topicoId'");
	}
?>