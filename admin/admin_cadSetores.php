<?php
include_once "includes/inc_header.php";

//Selecionar o servidor
$sql_busca = read('servidores', "ORDER BY nome ASC");

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2 || $_SESSION['autUser']['admin'] == 3):
?>

    <h1 class="titulo-secao"><i class="fa fa-cubes"></i> Cadastrar novo setor</h1>  

    <?php
    echo "<a href='admin_esetores.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Setores'><i class='fa fa-list'></i> Listar Setores</a>";
    
    if (isset($_POST['cad_setor'])):
        //Receber dados do Formulário
        $f['setor'] = $_POST['setor'];
        $f['abreviatura'] = $_POST['abreviatura'];
        $f['chefe'] = $_POST['chefe'];

        if ($f['setor'] and $f['abreviatura'] != "" and $f['chefe'] != "n"):

            $sql_cadastra = create('setores', $f);

            if ($sql_cadastra):
                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Setor cadastrado com sucesso!</h6>";
                header('Refresh: 2,url=admin_cadSetores.php');
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Erro ao cadastrar o setor!</h4>";
            endif;
        else:
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Informe todos os dados solicitados! </h4>";
        endif;
    endif;
    ?> 

    <form name="cad_setor" method="post" action="" enctype="multipart/form-data">
        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Informe os seguintes dados</h2>

            <label>
                <span>Nome do setor:</span><br />
                <input name="setor" type="text" id="setor" maxlength="200"/>
            </label>
            <label>
                <span>Abreviatura:</span><br />
                <input name="abreviatura" type="text" id="abreviatura" maxlength="6"/>
            </label>
            <label>
                <span>Chefia Imediata deste setor:</span>
                <select name="chefe" id="chefe" class="select_medio">
                    <option value="n" selected>Selecione o servidor</option>
                    <?php foreach ($sql_busca as $select_busca) {
                        $busca_selecionada = $select_busca['nome']; ?>
                        <option value="<?php echo $busca_selecionada; ?>"><?php echo $busca_selecionada; ?></option>
                    <?php } ?>
                </select> 
            </label> 
            <input type="submit" name="cad_setor" value="Cadastrar" id="cad_setor" class="btn btn_altera btn_green fl_right" />
        </fieldset> 
    </form>

    <?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;

include_once "includes/inc_footer.php";
?>