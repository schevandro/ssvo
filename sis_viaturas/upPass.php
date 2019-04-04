<?php
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";
?>

<!--Conteudo das páginas -->
<div class="paginas">
<?php
	if($_SESSION['autUser']['id'] == $_GET['id_servidor']){
?>

    <div class="menu_top">
        <h1 class="titulo-secao-medio"><i class="fa fa-lock"></i> Alterar senha de acesso</h1>
    </div><!--fecha div class menutop-->

<?php 
if (isset($_POST['resetar'])) {
	$idUpdate		= strip_tags(trim($_POST['idServidor']));
	$f['senha']		= strip_tags(trim($_POST['senha']));
	$repetenovasenha	= strip_tags(trim($_POST['repetesenha']));
	$f['mudarsenha']	= 0;		
	
	if($f['senha'] and $repetenovasenha != ''){
		if(strlen($f['senha']) < 8 || strlen($f['senha']) > 12){
                    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90;'></i>&nbsp&nbsp&nbsp A senha deve conter entre 8 e 12 caracteres!</h4>";			
		}else{
                    if($f['senha'] == $repetenovasenha){
                        if($f['mudarsenha'] == '' ? $f['mudarsenha'] = 0 : $f['mudarsenha'] = 1);
                        $f['code']  = $f['senha'];
                        $f['senha'] = md5($f['senha']);
                        $upSenha = update('servidores',$f,"id = '$idUpdate'");
                        if($upSenha){
                            if($f['mudarsenha'] < 1){
                                echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Senha alterada com sucesso!</h4>";
                                header('Refresh: 2;url=upPass.php?id_servidor='.$idUpdate.'&id_senha=true');	
                            }else{
                                echo "<h4 class='ms ok'><i class='fa fa-check-square-o'>&nbsp&nbsp&nbsp Senha alterada com sucesso! O servidor deve alterar a senha no próximo login.</h4>";	
                                header('Refresh: 2;url=upPass.php?id_servidor='.$idUpdate.'&id_senha=true');							
                            }	
                        }
                    }else{
                        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90;'></i>&nbsp&nbsp&nbsp As senhas informadas não conferem!</h4>";
                    }		
		}
	}else{
		echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90;'></i>&nbsp&nbsp&nbspInforme os dois campos requisitados!</h4>";
	}
}
?>

<?php	
	if(isset($_GET['id_servidor']) && isset($_GET['id_senha'])){
		if(isset($_GET['id_senha']) && $_GET['id_senha'] == 'true'){
                    $idServidor = strip_tags(trim($_GET['id_servidor']));
                    $readBuscaServidor = read('servidores',"WHERE id = '$idServidor'");
                    $countBuscaServidor = count($readBuscaServidor);
                    if($countBuscaServidor < 1){
                            header('Location:painel.php');
                    }else{
                        foreach($readBuscaServidor as $dados);

?>
                        <form name="reseta_senha" id="reseta_senha" method="post" action="">
                            <fieldset class="user_altera_dados">
                                <label>
                                        <span>Nova Senha:</span>
                                        <input name="senha" type="password" value="" maxlength="12" class="senha" required/>
                                </label>
                                <label>
                                        <span>Repetir a Nova Senha:</span>
                                        <input name="repetesenha" type="password" value="" maxlength="12" class="senha" required/>
                                </label>
                                <input type="hidden" name="idServidor" value="<?php echo $idServidor; ?>" />
                                <input type="submit" name="resetar" value="Atualizar" class="btn btn_altera btn_green flt_right" />    
                            </fieldset>
                        </form>
<?php		
                    }
		}else{
			header('Location:painel.php');	
		}
	}else{
		header('Location:painel.php');	
	}

?>
    
 <?php
}else{
	header('Location:painel.php');
}
?>      
</div> <!--fecha div class lista_fotos-->

</div> <!--fecha div class paginas--> 
<!--Termina conteudo das páginas-->
    
<?php include_once "includes/inc_footer.php"; ?>