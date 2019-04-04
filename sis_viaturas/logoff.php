<?php
	require('../dts/dbaSis.php');
	require('../dts/outSis.php');
	
	ob_start();
	session_start();
	
	$upAcesso['logof']	= date('Y-m-d H:i:s');
	$chaveLogoff		= $_SESSION['autUser']['chave_acesso'];
	update('acessos',$upAcesso,"chave = '$chaveLogoff'");

	unset($_SESSION['autUser']);
	header('Location: index.php');
	ob_end_flush();
?>