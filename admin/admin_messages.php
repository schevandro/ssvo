<?php
include_once "includes/inc_header.php";

$readEstatServidores = read('servidores');
$readEstatSolicitacoes = read('vt_solicitacoes');
$hoje = date('Y-m-d');


$verifCnh = read('servidores', "WHERE os_motorista != 'n' AND cnh_vencimento < '$hoje'");
$countVerifCnh = count($verifCnh);
$situacao = 'Aguardando...';
$readSolicAbertas = read('vt_solicitacoes', "WHERE situacao = '$situacao'");
$numSolicAbertas = count($readSolicAbertas);

//Verifica avisos pendentes
if ($_SESSION['autUser']['admin'] != 1 && $_SESSION['autUser']['admin'] != 2) {
    $erroAdmin = 1;
    $msgAdmin = 'Há um problema com seu nível de acesso. Contate o Admin do sistema!';
} else {
    $erroAdmin = NULL;
}

if ($countVerifCnh >= 1) {
    $erroCnh = 2;
    if (($countVerifCnh == 1) ? $condutor = 'condutor' : $condutor = 'condutores')
    $msgCnh = '<a href="admin_emotoristas.php" title="Ver"><h4 style="margin-bottom:10px;"><i class="fa fa-caret-right"></i> <strong>' . $countVerifCnh . '</strong> '.$condutor.' com a CNH vencida</h4></a>';
}else {
    $msgCnh = NULL;
}

if ($numSolicAbertas >= 1) {
    $erroSolicitacao = 3;
    if (($numSolicAbertas == 1) ? $solicitacoes = 'solicitação' : $solicitacoes = 'solicitações')
    $msgSolicitacao = '
    <a href="admin_esolicitacoes.php?solit=aberto"  title="Ver"><h4 style="margin-bottom:10px;">
        <i class="fa fa-caret-right"></i> <strong>' . $numSolicAbertas . '</strong> ' . $solicitacoes . ' de viatura aguardando aprovação
    </h4></a>';
}else {
    $erroSolicitacao = NULL;
}

//Mostra o painel de avisos se necessário
if ($erroAdmin >= 1 || $erroCnh >= 1 || $erroSolicitacao >= 1):
    echo '<div class="painel_avisos" ><h1><i class="fa fa-exclamation-triangle" style="font-size: 1.4em;"></i> Existem '.$numMsg.' verificações pendentes</h1>';
    echo $msgAdmin;
    echo $msgCnh;
    echo $msgSolicitacao;
    echo '</div>';
else:
    echo '<div class="painel_avisos_ok" ><h1><i class="fa fa-check-square" style="font-size: 1.4em;"></i> Não há novas mensagens</h1>';
endif;

?>