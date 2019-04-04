<?php
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";
?>

<!--Conteudo das páginas -->

<div class="menu_top">
    <h1 class="titulo-secao-medio"><i class="fa fa-edit"></i> Atualizar dados cadastrados</h1>
</div><!--fecha div class menutop-->

<?php
$meuId = $_SESSION['autUser']['id'];
$servidorId = $_GET['id'];

if ($servidorId != $meuId) {
    header("Refresh:1; url=adadosServidor.php?id=$meuId");
} else {
    
    $sql_busca = read('servidores',"WHERE id = {$servidorId} ORDER BY nome ASC");

    foreach ($sql_busca as $res);
    ?>

    <?php
    if (isset($_POST['excluir_foto'])) {
        //Informações da foto do servidor
        $fotoservidor = 'sem_foto.png';
        if ($res['foto'] != "") {
            if ($res['foto'] != 'sem_foto.png') {
                $deletaFile = \unlink('../admin/imagens/servidores/' . $res['foto']);
                $_SESSION['autUser']['foto'] = 'sem_foto.png';
            }
            $fotoservidor = 'sem_foto.png';
            $upfoto['foto'] = $fotoservidor;
        } else {
            $fotoservidor = 'sem_foto.png';
            $upfoto['foto'] = $fotoservidor;
        }

        $sql_atualiza = update('servidores',$upfoto,"id = '$servidorId'");
        
        if($sql_atualiza):
            echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Foto Excluída</h4>";
            header('Location: adadosServidor.php?id='.$servidorId);
        else:    
            echo 'Erro ao tentar excluir a foto!';
        endif;
    }


    if (isset($_POST['atualizar'])) {

        $up['fone_celular']     = strip_tags(trim($_POST['fone_celular']));
        $up['end_rua']          = strip_tags(trim($_POST['endereco_rua']));
        $up['end_num']          = strip_tags(trim($_POST['endereco_num']));
        $up['end_complemento']  = strip_tags(trim($_POST['endereco_comp']));
        $up['end_bairro']       = strip_tags(trim($_POST['endereco_bairro']));
        $up['end_cidade']       = strip_tags(trim($_POST['cidade']));
        $up['end_cep']          = strip_tags(trim($_POST['cep']));

        //Informações da foto do servidor
        $sem_foto = 'sem_foto.png';
        $fotoservidor = $_FILES["foto_at"]["tmp_name"];
        if ($fotoservidor != "") {
            if ($res['foto'] != $sem_foto) {
                $deletaFile = \unlink('../admin/imagens/servidores/' . $res['foto']);
                $_SESSION['autUser']['foto'] = 'sem_foto.png';
            }
            $foto_nomeTemporario = $_FILES["foto_at"]["tmp_name"];
            $foto_nomeReal = $_FILES["foto_at"]["name"];
            $foto = $foto_nomeReal;
            $up['foto'] = $foto;
            $_SESSION['autUser']['foto'] = $foto;
        } else {
            $foto = $res['foto'];
            $up['foto'] = $foto;
        }

        $sql_atualiza = update('servidores', $up, "id = '{$servidorId}'");
        
        if($sql_atualiza):
            if ($_FILES["foto_at"]["tmp_name"] != "") {
                copy($foto_nomeTemporario, '../admin/imagens/servidores/' . $foto);
            }
            echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Dados atualizados com sucesso!</h4>";
            header('Location: adadosServidor.php?id='.$servidorId);
        else:
            echo 'Erro ao tentar atualizar seus dados!';
        endif;
    }
    ?>

    <script type="text/javascript">
        function alteraFoto() {
            document.getElementById("altera_foto_servidor").style.display = "block";
            document.getElementById("exclui_foto_servidor").style.display = "none";
        }

        function excluiFoto() {
            document.getElementById("altera_foto_servidor").style.display = "none";
            document.getElementById("exclui_foto_servidor").style.display = "block";
        }
    </script>

        <form name="aservidores" method="post" action="" enctype="multipart/form-data">
            
            <fieldset class="user_altera_foto">
                <div class="user_foto">
                    <img src="../admin/imagens/servidores/<?php echo $_SESSION['autUser']['foto']; ?>"/>
                </div>

                <div class="user_foto_btn">
                    <h4 class="btn_35 btn_green flt_right" onclick="alteraFoto()">Alterar Foto</h4><br /><br /><br />
                    <?php
                    if ($res['foto'] != "sem_foto.png"):
                        echo "<h4 class='btn_35 btn_red flt_right' onclick='excluiFoto()'>Excluir Foto</h4>";
                    endif;
                    ?>           
                    <label class="altera_foto_servidor" id="altera_foto_servidor">
                        <div class="atualiza_img_servidor">
                            <input type="file" name="foto_at" id="foto_at" class="arquivo"/>    
                        </div>
                    </label>
                    <div class="exclui_foto_servidor" id="exclui_foto_servidor">
                        <input type="submit" name="excluir_foto" value="Sim, excluir!"/>
                    </div>
                </div>
            </fieldset>
            
            <div class="user_dados_fixos">                
                <h1>Nome:</h1>
                <h2><?php echo $_SESSION['autUser']['nome']; ?></h2><br />
                
                <h1>Siape:</h1>
                <h2><?php echo $_SESSION['autUser']['siape']; ?></h2><br />
                 
                <h1>E-mail:</h1>
                <h2><?php echo $_SESSION['autUser']['email']; ?></h2></span><br />
            </div>
            
            <fieldset class="user_altera_dados_borda">
                <label>
                    <span class="span_exibeSiape">Telefone:</span><br />
                    <input type="text" name="fone_celular" id="fone_celular" value="<?php echo $res['fone_celular']; ?>" />
                </label>  

                <label>
                    <span>Rua:</span><br />
                    <input type="text" name="endereco_rua" id="endereco_rua" value="<?php echo $res['end_rua']; ?>" />
                </label>  

                <label>
                    <span>Número:</span><br />
                    <input name="endereco_num" type="text" id="endereco_num" value="<?php echo $res['end_num']; ?>" class="horario_saida"/>
                </label>  

                <label>
                    <span>Complemento:</span><br />
                    <input type="text" name="endereco_comp" id="endereco_comp" value="<?php echo $res['end_complemento']; ?>" />
                </label>  

                <label>
                    <span>Bairro:</span><br />
                    <input type="text" name="endereco_bairro" id="endereco_bairro" value="<?php echo $res['end_bairro']; ?>" />
                </label>  

                <label>
                    <span>Cidade:</span><br />
                    <input type="text" name="cidade" id="cidade" value="<?php echo $res['end_cidade']; ?>" />
                </label>  

                <label>
                    <span>CEP:</span><br />
                    <input name="cep" type="text" class="horario_saida" id="cep" value="<?php echo $res['end_cep']; ?>" maxlength="9" />
                </label>  
                
                <input type="submit" name="atualizar" value="Atualizar" id="atualizar" class="btn btn_altera btn_green flt_right" />

           	</fieldset>
            
        </form>

    <!--Termina conteudo das páginas-->
    <?php
}
include_once "includes/inc_footer.php";
?>