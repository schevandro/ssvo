<?php
ob_start();
session_start();
require('../dts/dbaSis.php');
require('../dts/getSis.php');
require('../dts/setSis.php');
require('../dts/outSis.php');

if(!$_SESSION['autUser']){
    header('Location: ../index.php');
}else{
    $userId = $_SESSION['autUser']['id'];	
    $readAutUser = read('servidores',"WHERE id = '$userId'");
    if($readAutUser){
        foreach($readAutUser as $autUser);
        if($autUser['admin'] < '9'){
            header('Location: painel.php');
        }else{
            header('Location: ../sis_viaturas');
        }
    }else{
        header('Location: ../index.php');
    }
}
?>