<?php
include_once "includes/inc_header.php";

if($_SESSION['autUser']['admin'] == 1):

    //PAGINAÇÃO
    $pag = $_GET['pag'];
    if($pag >= '1'):
        $pag = $pag;
    else:
        $pag = '1';
    endif;
    $maximo = '10'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;
	
    //Busca servidores administradores
    $buscaAdmin 	= read('servidores',"WHERE admin < '4' ORDER BY nome ASC LIMIT $inicio,$maximo");
    $num		= count($buscaAdmin);
?>
    
    <h1 class="titulo-secao"><i class="fa fa-cog"></i> Administradores do Sistema <span><?php echo $num; ?> registros</span></h1>
    <span class="btn btn_blue fl_right btn_cadAdmin" style="margin-bottom: 30px; margin-left: 10px;" title="Adicionar um administrador"><i class="fa fa-plus-circle"></i> ADMINISTRADOR</span>
    
<?php
    
    //Deleta Administrador
    if($_GET['del'] == 'true'):
        $del_id = $_GET['id_admin'];
        $delNivel = array('admin' => '9');
        $delAdmin = update('servidores', $delNivel, "id = '$del_id'");	

        if($delAdmin):
            echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp;&nbsp;&nbsp; Administrador excluído com sucesso!</h6>";
            header("Refresh: 1; url=admin_eadministradores.php"); 
        else:
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao excluir administrador!</h4>";
        endif;
    endif;
	
    //Cabeçalho da Tabela 
    echo "<table width='100%' border='0' cellpadding='5' cellspacing='0' class='tb_geral'>
            <tr class='tr_header'>
                <td align='center' width='10%'>Servidor</td>
                <td align='left' width='30%' style='padding-left:10px;'></td>
                <td align='center' width='10%'>Nível</td>
                <td align='center' width='15%'>Siape</td>
                <td align='center' width='25%'>E-mail</td>
                <td align='center' width='10%'>Ações</td>
            </tr>";
      
    //Foreach dos servidores administradores
    foreach ($buscaAdmin as $admin) {
	
        $colorPage++;
        if ($colorPage % 2 == 0):
            $cor = 'style="background:#f3f3f3;"';
        else:
            $cor = 'style="background:#fff;"';
        endif;
        
        //Verifica nível de acesso do usuário
        if ($admin['admin'] == 1):
            $listNivel  = "<i class='fa fa-cog' style='font-size: 1.3em;' title='Administrador do Sistema'></i>&nbsp;&nbsp;";
            $nivel      = "Administrador";
        elseif ($admin['admin'] == 2):
            $listNivel  = "<i class='fa fa-car' style='font-size: 1.3em;' title='Operador Viaturas'></i>&nbsp;&nbsp;";
            $nivel	= "Operador Viaturas";
        elseif ($admin['admin'] == 3):
            $listNivel  = "<i class='fa fa-group' style='font-size: 1.3em;' title='Operador CGP'></i>&nbsp;&nbsp;";
            $nivel      = "Operador CGP";
        else:
           $listNivel = NULL;
        endif;

        echo "<tr {$cor} class='lista_itens'>";
        
        echo '  <td align="center"><img src="imagens/servidores/'.$admin['foto'].'" width="30px" style="margin-right:5px;"></td>
                <td align="left">'.$admin['nome'].'</td>
                <td align="center">'.$listNivel.'<br />'.$nivel.'</td>
                <td align="center">'.$admin['siape'].'</td>
                <td align="center">'.$admin['email'].'</td>   				
                <td align="center">
                <span style="color:#CCC; font-size: 1.7em;" title="Editar dados através do menu servidores"><i class="fa fa-pencil-square-o"></i></span>
            ';
        
        if($num <= 1){
            echo "&nbsp;&nbsp;&nbsp;<span style='color:#CCC; font-size: 1.7em;' title='Há somente 1 administrador cadastrado'><i class='fa fa-trash-o'></i></span>";
        }else{
            echo "&nbsp;&nbsp;&nbsp;<a href='admin_eadministradores.php?id_admin={$admin['id']}&del=true' style='text-decoration:none; color:#033; font-size: 1.7em;' title='Excluir administrador' alt='Excluir administrador'><i class='fa fa-trash-o'></i></a>";
        }
        echo '</td></tr>';
    }//fecha o foreach
    echo '</table>';
?>    

    <!-- DIV para confirmar ativação do servidor-->
    <div id="modalEncerra" class="modal_encerra_guia" style="display: none;">
        <div class="modal_encerra_guia_content" style="display: block;"> 
            <div class="modal_encerra_guia_header">Cadastrar Administrador do Sistema</div>
            <div class="msg_retorno"></div>

            <div class="formEncerradaBlock">
                <h4 class="ms green"><i class="fa fa-refresh" style="font-size: 1.4em color:#F06;"></i>&nbsp;&nbsp;&nbsp; Esta solicitação já esta encerrada!</h4>    
            </div>
            
            <?php $buscaServidor = read('servidores',"WHERE ativo = 1 ORDER BY nome ASC"); ?>
            <form name="cadastraAdmin" method="post" action="" enctype="multipart/form-data">
                <fieldset class="user_altera_dados">
                    <label>
                        <span style="margin-left: -10px; margin-bottom: -5px;">Servidor*:</span>
                        <select name="servidor_id" id="servidor_id">
                            <option value="" selected disabled>Selecione</option>
                            <?php foreach ($buscaServidor as $busca) { $busca_selecionada = $busca['nome']; ?>
                            <option value="<?php echo $busca['id']; ?>"><?php echo $busca_selecionada; ?></option>
                            <?php } ?>
                        </select>
                    </label>
                    
                    <label>
                        <span style="margin-left: -10px; margin-bottom: -5px;">Tipo de acesso*:</span>
                        <select name="servidor_admin" id="servidor_admin">
                            <option value="" selected disabled>Selecione</option>
                            <option value="2" >Operador Viaturas</option>
                            <option value="3" >Operador CGP</option>
                            <option value="1" >Super Admin</option>                            
                        </select>
                    </label>
                </fieldset>            

                <div class="modal_encerra_guia_actions">
                    <button type="button" class="btn btn_green fl_right j_button">CADASTRAR</button>
                    <h4 class="modal_encerra_close btn btn_red" value="<?php echo  $exibeId; ?>">x</h4>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </form>
        </div>
    </div> 
    
<?php    
echo '<div id="paginator">';

//PAGINAÇÃO
$total = $num;

$paginas = ceil($total/$maximo);
$links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

echo "<a href=admin_eadministradores.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

for ($i = $pag-$links; $i <= $pag-1; $i++){
if ($i <= 0){
}else{
echo"<a href=admin_eadministradores.php?;pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
}
}echo "<h1>$pag</h1>";

for($i = $pag + 1; $i <= $pag+$links; $i++){
if($i > $paginas){
}else{
echo "<a href=admin_eadministradores.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
}
}
echo "<a href=admin_eadministradores.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";

echo '</div><!--fecha paginator-->';
?>


<?php
else:
    echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
endif;
include_once "includes/inc_footer.php";
?>

<script src="ajax/js/cadastra_administrador.js"></script>    
<script type="text/javascript">
    $(".modal_encerra_close").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra").style.display = "none";
    });

    $(".btn_cadAdmin").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra").style.display = "block";
    });
</script>  