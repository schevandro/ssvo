<?php
include_once "includes/inc_header.php";

//Selecionar o servidor
$sql_buscaserv = read('servidores',"WHERE ativo = 1 ORDER BY nome ASC");

if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2 || $_SESSION['autUser']['admin'] == 3):
?>
    <h1 class="titulo-secao"><i class="fa fa-cube"></i> Editar dados do setor</h1> 

<?php	
    echo "<a href='admin_esetores.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Setores'><i class='fa fa-list'></i> Listar Setores</a>";
    
    //Selecionar o setor
    $setorId = $_GET['id_setor'];
    $sql_busca = read('setores',"WHERE id = {$setorId} ORDER BY setor ASC");		  

    foreach ($sql_busca as $res):	    
        $exibeId	= $res['id'];
        $exibeSetor	= $res['setor'];
        $exibeAbrev	= $res['abreviatura'];
        $exibeChefe	= $res['chefe'];
    endforeach;

    if (isset($_POST['atualizar'])):

        //Recebe dados do formulário
        $up['setor']        =   strip_tags(trim($_POST['setor']));
        $up['abreviatura']  =   strip_tags(trim($_POST['abreviatura']));
        $up['chefe']        =   strip_tags(trim($_POST['chefe']));

        //Verifica se todos os campos foram preenchidos
        if ($up['setor'] and $up['abreviatura'] and $up['chefe'] != ""):

            //Atualiza oos dados do servidor na tabela servidores
            $upSetor = update('setores',$up,"id = {$setorId}");

            //Se deu certo
            if($upSetor):
                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Dados do setor atualizados com sucesso!</h6>";
                header('Refresh: 2,url=admin_asetores.php?id_setor='.$setorId);	
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao atualiar os dados do setor!</h4>";

                echo '<div class="ms no">Erro ao atualiar os dados do setor!</div>';
            endif;
        else:
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Por favor, preencha todos os campos obrigatórios!</h4>";
        endif;
    endif;
?>


    <form name="cad_setor" id="cad_servidor" method="post" action="" enctype="multipart/form-data">

        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Informe os seguintes dados</h2>
            <label>
                <span>Nome do Setor:</span><br />
                <input name="setor" type="text" id="setor" value="<?php echo $exibeSetor; ?>" maxlength="200"/>
            </label>
            <label>
                <span>Abreviatura:</span><br />
                <input name="abreviatura" type="text" class="siape" id="abreviatura" value="<?php echo $exibeAbrev; ?>" maxlength="9"/>
            </label>
            <label>
                <span>Chefe Imediato do Setor:</span><br />
                <select name="chefe" size="1" class="selects" id="chefe" >
                    <option value="<?php echo $exibeChefe; ?>" selected><?php echo $exibeChefe; ?></option>
                    <?php foreach ($sql_buscaserv as $select_busca) { $busca_selecionada = $select_busca['nome']; ?>
                    <option value="<?php echo $busca_selecionada; ?>"><?php echo $busca_selecionada; ?></option>
                    <?php } ?>               	
                </select> 
            </label> 
            <input type="submit" name="atualizar" value="Atualizar" id="atualizar" class="btn btn_altera btn_green fl_right" />  
        </fieldset>        
    </form>
    
<?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Seu nível de acesso não permite visualizar esta página!</h4>";
endif;

include_once "includes/inc_footer.php";
?>