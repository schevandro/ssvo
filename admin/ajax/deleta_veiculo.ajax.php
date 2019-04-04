<?php
ob_start();
session_start();
require('../../dts/dbaSis.php');
require('../../dts/getSis.php');
require('../../dts/setSis.php');
require('../../dts/outSis.php');


switch ($_POST['acao']) {

    case 'deletar':
        sleep(1);
        
        $veiculo_id = $_POST['veiculo_id'];

        if ($veiculo_id == "") {
            echo '1';
        } else {
            //Deleta veículo da base de dados
            $up['deletado'] = 1;
            $upVeiculo = update('vt_veiculos',$up,"id = '$veiculo_id'");
            if ($upVeiculo) {
                echo '3';
                header('Refresh:3; url=admin_eveiculos.php');
            } else {
                echo '1';
            }
        }
        break;
    
    default:
        echo 'Erro ao executar a ação [Ativação de servidor]!';
}
?>