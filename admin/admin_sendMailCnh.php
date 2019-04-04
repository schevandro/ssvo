<?php
require_once('../connections/_conexao.php');
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";
?>

<!--Conteudo das páginas -->

<?php $listaStatus = 'vazio'; ?>

    <div class="paginas">
	<?php
        if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2){
    ?>
    
      <div class="menu_top">
      <img src="imagens/ico_editar.png" align="Retornar para a lista de condutores" title="Retornar para a lista de condutores" height="25px"/>
      <h1><a href="admin_emotoristas.php">Retornar para a lista de condutores</a></h1>
      </div><!--fecha div class menutop-->                  

<?php
	$usuId = $_GET['usuId'];
	$readServidor = read('servidores',"WHERE id = '$usuId'");
    $numServidor = count($readServidor);
	
	if($numServidor <= 0){
		header('Location: admin_emotoristas.php');
	}
	
	foreach ($readServidor as $dados){
		$en_email	 =	$dados['email'];
		$en_nome	 =	$dados['nome'];
		$en_siape	 =	$dados['siape'];
		$en_cnh		 =	$dados['cnh'];
		$en_categCnh =	$dados['cnh_categoria'];
		$en_vencCnh	 =	$dados['cnh_vencimento'];
	}
		
?>
     
      <div class="lista_fotos" style="margin-top:55px;">
     		<table width="100%" border="0" cellpadding="5" cellspacing="0">
                
                 <tr style="background-color:#06C; color:#FFF; font:13px Verdana, Geneva, sans-serif; font-weight:300; height:50px;">
					  <td align="center">
                      	Enviar E-mail informando o servidor(a) <strong><?php echo $en_nome; ?></strong> que sua CNH está vencida?
                      </td>
  			     </tr>
                                  
				<?php
					//Script para envio do email
					if(isset($_POST['sendEmail'])){
						//Envia E-mail para o Admin
						$msg = '<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#666;">Olá '.$en_nome.',<br /><br />
						<p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que sua CNH está <span style="color:#F00" font-size:18px><strong>VENCIDA</strong></span> desde o dia <strong>'.date('d/m/Y',strtotime($en_vencCnh)).'</strong><br /><br />
						Você deve encaminhar uma cópia de sua nova CNH ao Gabinete do Campus Feliz, para poder continuar utilizando os veículos oficiais do Campus.<br /><br />
						Abaixo seguem os dados da sua CNH que está cadastrada no sistema:</p>
						<hr /><br />
						<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Número da CNH:</span> <span style="font:15px Tahoma, Geneva, sans-serif; color:#333;"> '.$en_cnh.'</span><br />
						<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Categoria da CNH:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;"> '.$en_categCnh.'</span><br />
						<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Vencimento da CNH:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;"> '.date('d/m/Y',strtotime($en_vencCnh)).'</span><br /><br />
						<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Seu Siape:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;"> '.$en_siape.'</span><br />
						<span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Seu E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;"> '.$en_email.'</span><br /><br />						
						<span style="font:14px Tahoma, Geneva, sans-serif; color:#06F;">E-mail enviado em <strong>'.date('d/m/Y').' às '.date('H:i:s').'</strong></span><br /><br />';
						sendMail('Atualização de CNH',$msg,MAILUSER,SITENAME,$en_email,$en_nome);
						
						if(sendMail){
							echo ' <tr height="70px">'; 
								echo '<td align="center">';
									echo '<div class="ms ok" style="margin:55px 0 55px 400px">E-mail enviado com sucesso!</div>';
								echo '</td>';
							echo '</tr>';
						}else{
							echo ' <tr height="70px">'; 
								echo '<td align="center">';
									echo '<div class="ms no" style="margin:55px 0 55px 400px">Erro ao enviar o E-mail!</div>';
								echo '</td>';
							echo '</tr>';
						}
						
					}else{	 
					
						echo ' <tr height="70px">'; 
							echo '<td align="center">';
								echo '<form method="post" name="sendEmailCnh" style="margin:10px 0 10px 400px;">';
									echo '<input type="submit" class="btn" value="Enviar E-mail" name="sendEmail" id="sendEmail" onclick="verifBtn()" />';
									echo '<div id="form_load" style="visibility:hidden; margin-top:-1px; margin-left:55px"><img src="../imagens/ico_load.gif" width="50px"  /></div>';
								echo '</form>';
							echo '</td>';
						echo '</tr>';
					}
                ?>

			</table>
            
            <script type="text/javascript">
				 function verifBtn(){
					 document.getElementById("form_load").style.visibility ="visible";
					 document.getElementById("sendEmail").style.visibility ="hidden"; 
				 }
			 </script>


 <?php
}else{
	echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
}
?>      
</div> <!--fecha div class lista_fotos-->

</div> <!--fecha div class paginas--> 
    
<!--Encerra conteúdo das páginas-->
  
<?php include_once "includes/inc_footer.php"; ?>