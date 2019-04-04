<?php
ob_start();
session_start();

if(!$_SESSION['autUser']):
    header('Location: ../index.php');
else:
    header('Location: painel.php');
endif;
?>