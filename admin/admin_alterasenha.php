<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['id'] == $_GET['id_servidor']):
?>

<h1 class="titulo-secao"><i class="fa fa-lock"></i> Alterar senha de acesso</h1>

    <?php
    if ($_SESSION['autUser']['id'] == $_GET['id_servidor']):
        echo "<a href='painel.php' class='btn btn_blue fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Voltar ao início'><i class='fa fa-home'></i> Voltar ao início</a>";
    else:
        echo "<a href='admin_eservidores.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Servidores'><i class='fa fa-list'></i> Listar Servidores</a>";
    endif;
    
    //Caso confirmada opção de alterar a senha
    if (isset($_POST['resetar'])):
        $idUpdate = strip_tags(trim($_POST['idServidor']));
        $f['senha'] = strip_tags(trim($_POST['senha']));
        $repetenovasenha = strip_tags(trim($_POST['repetesenha']));
        if ($_SESSION['autUser']['id'] == $idUpdate):
            $f['mudarsenha'] = 0;
        else:
            $f['mudarsenha'] = $_POST['mudarsenha'];
        endif;


        if ($f['senha'] and $repetenovasenha != ''):
            if (strlen($f['senha']) < 8 || strlen($f['senha']) > 12):
                echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90;'></i>&nbsp&nbsp&nbsp A senha deve conter entre 8 e 12 caracteres!</h4>";
            else:
                if ($f['senha'] == $repetenovasenha):
                    if ($f['mudarsenha'] == '' ? $f['mudarsenha'] = 0 : $f['mudarsenha'] = 1);
                    $f['code'] = $f['senha'];
                    $f['senha'] = md5($f['senha']);
                    $upSenha = update('servidores', $f, "id = '$idUpdate'");
                    if ($upSenha):
                        if ($f['mudarsenha'] < 1):
                            echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Senha alterada com sucesso!</h4>";
                            header('Refresh: 2;url=admin_alterasenha.php?id_servidor=' . $idUpdate . '&id_senha=true');
                        else:
                            echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Senha alterada com sucesso! O servidor deve alterar a senha no próximo login.</h4>";
                            header('Refresh: 2;url=admin_alterasenha.php?id_servidor=' . $idUpdate . '&id_senha=true');
                        endif;
                    endif;
                else:
                    echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90;'></i>&nbsp&nbsp&nbsp As senhas informadas não conferem!</h4>";
                endif;
            endif;
        else:
            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90;'></i>&nbsp&nbsp&nbspInforme os dois campos requisitados!</h4>";
        endif;
    endif;
    ?>

    <?php
    if (isset($_GET['id_servidor']) && isset($_GET['id_senha'])):
        if (isset($_GET['id_senha']) && $_GET['id_senha'] == 'true'):
            $idServidor = strip_tags(trim($_GET['id_servidor']));
            $readBuscaServidor = read('servidores', "WHERE id = '$idServidor'");
            $countBuscaServidor = count($readBuscaServidor);
            if ($countBuscaServidor < 1):
                header('Location:admin_eservidores.php');
            else:
                foreach ($readBuscaServidor as $dados);
                ?>				
                <form name="reseta_senha" id="reseta_senha" method="post" action="">
                    <h6 class="form_sub_titulo">Alterar senha de acesso do usuário <strong><?php echo $dados['nome']; ?></strong></h6>
                    <fieldset class="user_altera_dados">
                        <label>
                            <span>Nova Senha:</span>
                            <input name="senha" type="password" value="" maxlength="12" class="senha"/>
                        </label>
                        <label>
                            <span>Repetir a Nova Senha:</span>
                            <input name="repetesenha" type="password" value="" maxlength="12" class="senha"/>
                        </label>
                        <?php
                        if ($_SESSION['autUser']['id'] != $idServidor) {
                            ?>
                            <div class="check_altera_senha">
                                <input class="checksenha" type="checkbox" name="mudarsenha" value="1" />
                                <span>Mudar senha no primeiro acesso</span>
                            </div>
                            <?php
                        }
                        ?>
                        <input type="hidden" name="idServidor" value="<?php echo $idServidor; ?>" />
                        <input type="submit" name="resetar" value="Atualizar" class="btn btn_altera btn_green flt_right" />
                    </fieldset>
                </form>
                <?php
            endif;
        else:
            header('Location:admin_eservidores.php');
        endif;
    else:
        header('Location:admin_eservidores.php');
    endif;
    ?>


<?php
    else:
        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Seu nível de acesso não permite visualizar esta página!</h4>";
    endif;
?>      
<!--Termina conteudo das páginas-->

<?php include_once "includes/inc_footer.php"; ?>