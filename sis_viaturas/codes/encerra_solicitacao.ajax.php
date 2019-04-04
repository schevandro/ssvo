<?php
ob_start();
session_start();
require('../../dts/dbaSis.php');
require('../../dts/getSis.php');
require('../../dts/setSis.php');
require('../../dts/outSis.php');


switch ($_POST['acao']) {

    case 'encerraGuia':
        sleep(1);
        
        $encerra_id = $_POST['id_encerra'];
        $encerra_hora_chegada = $_POST['hora_chegada'];
        $encerra_km_saida = $_POST['km_saida'];
        $encerra_km_chegada = $_POST['km_chegada'];
        $encerra_km_percorridos = $_POST['km_percorridos'];
        $encerra_combustivel = $_POST['combustivel'];
        $encerra_observacao = $_POST['observacao'];

        //Pega Placa do Veículo
        $encerra_veiculo = $_POST['veiculo_encerra'];
        $encerra_veiculo_separa = explode("-", $encerra_veiculo);
        $encerra_placa = $encerra_veiculo_separa[1];

        //Data Chegada
        $encerra_data_chegada = $_POST['data_chegada'];
        $encerra_data_separa = explode('/', $encerra_data_chegada);
        $encerra_data_dia = $encerra_data_separa[0];
        $encerra_data_mes = $encerra_data_separa[1];
        $encerra_data_ano = $encerra_data_separa[2];
        $encerra_data_chegada_ok = $encerra_data_ano . '-' . $encerra_data_mes . '-' . $encerra_data_dia;

        //Muda situação para Encerrada
        $sit_encerrada = 'Encerrada';
        
        if ($encerra_combustivel == "" or $encerra_km_saida == "" or $encerra_km_chegada == "" or $encerra_hora_chegada == "" or $encerra_data_chegada == "") {
            echo '1';
        } else {


            //Atualiza Combustivel e Km Total na tabela de veículos se a km de chegada for maior que a km atual
            $buscaViatura = read('vt_veiculos', "WHERE placa = '$encerra_placa'");
            foreach ($buscaViatura as $viat)
                ;
            if ($viat['km'] < $encerra_km_chegada) {
                $up_dadosVeiculo = array('combustivel' => $encerra_combustivel, 'km' => $encerra_km_chegada, 'ultima_solicit' => $encerra_id);
                $up_veiculo = update('vt_veiculos', $up_dadosVeiculo, "placa = '$encerra_placa'");
            } else {
                //Atualiza somente a ultima solicitação encerrada com aquele veículo
                $up_dadosVeiculo = array('ultima_solicit' => $encerra_id);
                $up_veiculo = update('vt_veiculos', $up_dadosVeiculo, "placa = '$encerra_placa'");
            }

            //Atualiza Dados na tabela de solicitações
            $up_dadosSolicitacao = array(
                'km_saida' => $encerra_km_saida,
                'km_chegada' => $encerra_km_chegada,
                'km_percorridos' => $encerra_km_percorridos,
                'data_chegada' => $encerra_data_chegada_ok,
                'hora_chegada' => $encerra_hora_chegada,
                'observacao' => $encerra_observacao,
                'situacao' => $sit_encerrada
            );
            $up_solicitacao = update('vt_solicitacoes', $up_dadosSolicitacao, "id = '$encerra_id'");
            if ($up_solicitacao) {
                echo '3';
                header('Refresh:3; url=encerrar_solicitacoes.php');
            } else {
                echo '1';
            }
        }
        break;

    default:
        echo 'Erro ao executar a ação!';
}
?>