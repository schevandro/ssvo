<?php
ob_start();
session_start();
require('../../dts/dbaSis.php');
require('../../dts/getSis.php');
require('../../dts/setSis.php');
require('../../dts/outSis.php');


switch ($_POST['acao']) {

    case 'cadastrar':
        sleep(1);
        
        $servidor_id    = $_POST['servidor_id'];
        $servidor_nivel = $_POST['servidor_admin'];

        if ($servidor_id == "" || $servidor_nivel == "") {
            echo '1';
        } else {
            //Atualiza nivel do servidor conforme informado (1, 2, 3 ou 9)
            $up['admin'] = $servidor_nivel;

            $up_servidor = update('servidores', $up, "id = '$servidor_id'");
            if ($up_servidor) {
                echo '3';
                header('Refresh:3; url=admin_eadministradores.php');
            } else {
                echo '1';
            }
        }
        break;
    
    default:
        echo 'Erro ao executar a ação [Criação de administrador do sistema]!';
}
?>