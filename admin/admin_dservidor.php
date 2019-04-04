<?php
include_once('includes/inc_header.php');

$delid = $_GET['id_servidor'];
$readServidor = read('servidores',"WHERE id = '$delid'");
$countReadServidor = count($readServidor);
if($countReadServidor < 1):
    header('Location: admin_eservidores.php');
else:
    foreach ($readServidor as $servidor);

    if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2 || $_SESSION['autUser']['admin'] == 3):
?>
    <h1 class="titulo-secao"><i class="fa fa-user-times"></i> Confirmar desativação do usuário</h1>
    
<?php 
    echo "<a href='admin_eservidores.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Servidores'><i class='fa fa-list'></i> Listar Servidores</a>";

    if (isset($_POST['btn_del'])) {
	$del_id = $_POST['id_del'];
	$up['ativo'] = 0;
	$upServidor = update('servidores',$up,"id = '$del_id'");
	
	if($upServidor){
            echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Servidor desativado com sucesso!</h4>";
            header("Refresh: 2; url=admin_eservidores.php"); 
	}else{
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao desativar o servidor!</h4>";
	}
    }
?>

    <form name="delServidores" method="post" >
        <fieldset class="user_altera_dados">
            
            <?php
                //Faz verificações para poder desativar
                $hoje = date('Y-m-d');
                $admin = 9;
                $siapeServidor = $servidor['siape'];
                $buscaSolicitacoesAbertas = read('vt_solicitacoes',"WHERE siape = '$siapeServidor' AND  situacao = 'Autorizada' AND prev_retorno_data < '$hoje'");
                $countSoicitacoesAbertas = count($buscaSolicitacoesAbertas);
                $abertas = $countSoicitacoesAbertas;

                $buscaSolicitacoesAgendadas = read('vt_solicitacoes',"WHERE siape = '$siapeServidor' AND  (situacao = 'Autorizada' OR situacao = 'Aguardando...') AND data_uso >= '$hoje'");
                $countSolicitacoesAgendadas = count($buscaSolicitacoesAgendadas);
                $agendadas = $countSolicitacoesAgendadas;

                $buscastatus = read('servidores',"WHERE siape = '$siapeServidor' AND admin != '$admin'");
                $countBuscaStatus = count($buscastatus);
                $nivelservidor = $countBuscaStatus;

                if($abertas >= 1 || $agendadas >= 1 || $nivelservidor >= 1){
                    $disab_btn =  '<input type="submit" disabled="disabled" name="" value="Confirmar Desativação" id="btn_del_disab" class="btn btn_disabled fl_right" />';
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Não é possível desativar o servidor(a) <strong>{$servidor['nome']}</strong></h4>";
                    if($abertas >= 1){
                        echo '<h2 class="texto_desativa">&raquo; Este servidor possui viagens a serem encerradas [ '.$abertas.' ]<br /></h2>';  
                    }
                    if($agendadas >= 1){
                        echo '<h2 class="texto_desativa">&raquo; Este servidor possui viagens agendadas [ '.$agendadas.' ] <br /></h2>';  
                    }
                    if($nivelservidor >= 1){
                        echo '<h2 class="texto_desativa">&raquo; Este servidor possui nivel de acesso privilegiado. 
                              Remova em "Configurações > Usuários Admin", antes de desativá-lo. <br /></h2>';  
                    }
                }else{
                    $disab_btn = '<input type="submit" name="btn_del" value="Confirmar Desativação" id="btn_del" class="btn btn_altera btn_red fl_right" />'; 
            ?>
                    <h2 class="form_titulo_remove_user">
                        <img style="float:left; margin:0 40px 0 0;" src="imagens/servidores/<?php echo $servidor['foto']; ?>" width="100px" /><br />
                        Você esta desativando o servidor <strong><?php echo $servidor['nome']; ?></strong>.<br /><br />
                        Ele não poderá efetuar login e nem aparecerá mais nas listas de exibição do sistema.
                    </h2>
            <?php
                }	  
            ?>

            <input type="hidden" name="id_del" id="id_del" value="<?php echo $servidor['id']; ?>" />
            <?php echo $disab_btn; ?>
        </fieldset>
    </form>

<?php
    else:
        echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
    endif;
endif;

include_once "includes/inc_footer.php";
?>