<?php
ob_start();
session_start();
require('../dts/dbaSis.php');
require('../dts/getSis.php');
require('../dts/setSis.php');
require('../dts/outSis.php');

if (!$_SESSION['autUser']) {
    header('Location: index.php');
} else {
    $userId = $_SESSION['autUser']['id'];
    $readAutUser = read('servidores', "WHERE id = '$userId'");
    if ($readAutUser) {
        foreach ($readAutUser as $autUser)
            ;
        if ($autUser['admin'] < '1' || $autUser['admin'] > '3') {
            header('Location: ../sis_viaturas');
        }
    } else {
        header('Location: ../index.php');
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>IFRS - Câmpus Feliz-RS   |    Painel de Controle</title> 

        <link href="../_assets/css/admin.css" rel="stylesheet" type="text/css" />
        <link href="../_assets/css/admin_menu.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script
        <script src="http://code.jquery.com/jquery-2.2.3.min.js"></script>
        <script src="../_assets/js/admin_calendario.js"></script>
        <script src="../_assets/js/admin_scripts.js"></script>
        <script src="../_assets/js/jsadmin.js"></script>
        
        <link rel="shortcut icon" href="../_assets/img/fav.fw.png"/>

    </head>

    <body class="dashboard_main">
        
        <div class="dashboard_fix">

            <?php
            include_once "includes/inc_menu.php";
            ?>

            <div class="dashboard">
                
                <div class="dashboard_sidebar">
                    <span class="mobile_menu icon-menu icon-notext"><i class="fa fa-bars" style="font-size: 1.5em; cursor: pointer; color: #00B92F;"></i></span>
                    <div class="fl_right">
                        <!--<a href="../old_version/admin" class="btn_alternativo"><i class="fa fa-refresh"></i> Versão anterior</a>-->
                        <a class="link-close" href="logoff.php"><i class="fa fa-sign-out"></i> Sair</a>
                    </div>
                </div>

                <header class="dashboard_header">
                    <div class="main_header_logo">
                        <h1><?= $pg_title; ?></h1>
                        <a href="index.php" title="SSVO">
                            <img class="logo-full" src="../_assets/img/logo-ifrs-campusfeliz.fw.png" alt="IFRS - Campus Feliz" title="IFRS - Campus Feliz" />
                            <img class="mini-logo" src="../_assets/img/logo_big.png" alt="IFRS - Campus Feliz" title="IFRS - Campus Feliz" />
                        </a> 
                    </div>
                </header>

                <div class="dashboard_content">