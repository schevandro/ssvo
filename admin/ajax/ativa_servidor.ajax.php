<?php
ob_start();
session_start();
require('../../dts/dbaSis.php');
require('../../dts/getSis.php');
require('../../dts/setSis.php');
require('../../dts/outSis.php');


switch ($_POST['acao']) {

    case 'reativar':
        sleep(1);
        
        $servidor_id = $_POST['servidor_id'];

        if ($servidor_id == "") {
            echo '1';
        } else {
            //Atualiza situação (ativo) do servidor para 1
            $up['ativo'] = 1;
            $up_servidor = update('servidores', $up, "id = '$servidor_id'");
            if ($up_servidor) {
                echo '3';
                header('Refresh:3; url=admin_eservidores.php');
            } else {
                echo '1';
            }
        }
        break;
    
    case 'desativar':
        sleep(1);
        
        $servidor_id = $_POST['servidor_id'];

        if ($servidor_id == "") {
            echo '1';
        } else {
            //Atualiza situação (desativa) do servidor para 0
            $up['ativo'] = 0;
            $up_servidor = update('servidores', $up, "id = '$servidor_id'");
            if ($up_servidor) {
                echo '3';
                header('Refresh:3; url=admin_eservidores.php');
            } else {
                echo '1';
            }
        }
        break;

    default:
        echo 'Erro ao executar a ação [Ativação de servidor]!';
}
?>