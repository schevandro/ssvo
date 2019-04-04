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
        if ($autUser['admin'] < '1' || ($autUser['admin'] > '3' && $autUser['admin'] < '9')) {
            header('Location: index.php');
        }
    } else {
        header('Location: index.php');
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link rel="stylesheet" type="text/css" href="../_assets/css/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="../_assets/css/boot.css" />
        <link rel="stylesheet" type="text/css" href="../_assets/css/via.css" />
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script
        <script src="http://code.jquery.com/jquery-2.2.3.min.js"></script>
        <script src="../_assets/js/calendario.js"></script>
        <script src="../_assets/js/mobile.js"></script>
        <script src="codes/ajax/combo.js"></script>
        <script src="codes/ajax/encerra_solicitacao.js"></script>
        
        <link rel="shortcut icon" href="../_assets/img/fav.fw.png"/>
        
        <script type="text/javascript">
            function stopCarona() {
                document.getElementById("pedir_carona").style.visibility = "hidden";
                document.getElementById("pedir_carona").style.marginTop = "-105px";
                
            }

            function stopReCarona() {
                document.getElementById("pedir_carona").style.visibility = "hidden";
                document.getElementById("pedir_carona").style.marginTop = "-55px";
            }

            function ocultaCarona() {
                document.getElementById("pedir_carona").style.visibility = "hidden";
                document.getElementById("pedir_carona").style.marginTop = "-105px";
            }

            function stopPedido() {
                document.getElementById("aceita_carona").style.visibility = "hidden";
                document.getElementById("aceita_carona").style.marginTop = "-55px";
                location.href = "minhas_solicitacoes.php";
            }

            function stopNegaPedido() {
                document.getElementById("nega_carona").style.visibility = "hidden";
                document.getElementById("nega_carona").style.marginTop = "-55px";
                location.href = "minhas_solicitacoes.php";
            }

            function stopCaronaAll() {
                document.getElementById("pedir_carona_all").style.display = "none";
                document.getElementById("pedir_carona_all").style.marginTop = "-55px";
                location.href = "solicitacoes_all.php";
            }
        </script>

        <title>IFRS - Câmpus Feliz-RS</title> 
        <script type="text/javascript">
            $(document).ready(function () {
                $("#celular").mask("(99)9999-9999");
            });
        </script> 
    </head>

    <body>

        <?php
        if ($_SESSION['autUser']['admin'] < '9'):
            ?>
                <a class="container link-painel" href="../admin" title="Acessar painel administrativo"><i class="fa fa-cog"></i> Painel de controle</a>
            <?php
        endif;
        ?>

        <header class="main_header container">
            <div class="content-box">
                <div class="main_header_logo">
                    <h1><?= $pg_title; ?></h1>
                    <a href="index.php" title="SSVO">
                        <img class="logo-full" src="../_assets/img/logo-ifrs-campusfeliz.fw.png" alt="IFRS - Campus Feliz" title="IFRS - Campus Feliz" />
                        <img class="mini-logo" src="../_assets/img/logo_big.png" alt="IFRS - Campus Feliz" title="IFRS - Campus Feliz" />
                    </a> 
                </div>

                <div class="main_header_dados">
                    <article class="main_header_dados_img">
                        <img src="../admin/imagens/servidores/<?php echo $_SESSION['autUser']['foto']; ?>" />
                    </article>
                    <article class="main_header_dados_info">
                        <h6>Olá, <?php echo $_SESSION['autUser']['nome']; ?></h6>
                        <h4>
                            <?php echo $_SESSION['autUser']['siape']; ?> <i class="fa fa-caret-right"></i>
                            <?php echo $_SESSION['autUser']['funcao']; ?> <i class="fa fa-caret-right"></i>
                            <?php echo $_SESSION['autUser']['setor']; ?>
                        </h4>
                        
<!--                        <a href="../old_version" class="btn_alternativo"><i class="fa fa-refresh"></i> Versão anterior</a>-->
                        <a href="adadosServidor.php?id=<?php echo $_SESSION['autUser']['id']; ?>" class="btn_dados">Alterar Dados</a>
                        <a href="upPass.php?id_servidor=<?php echo $_SESSION['autUser']['id']; ?>&id_senha=true" class="btn_senha">Alterar Senha</a>
                        <a class="link-close" href="logoff.php"><i class="fa fa-sign-out"></i> Sair</a>
                    </article>
                </div>
                <div class="clear"></div>
            </div>     
            <div class="mobile_action"><i class="fa fa-list" style="color: #FFF;"></i> MENU</div>
        </header>