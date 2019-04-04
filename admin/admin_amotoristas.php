<?php
include_once "includes/inc_header.php";

//Selecionar o servidor
$id_condutor = $_GET['upcondutor'];

if (empty($id_condutor)):
    header('Location: admin_emotoristas.php');
endif;

if (!empty($id_condutor)):
    $buscaSiape = read('servidores', "WHERE id = '$id_condutor'");
    $countBuscaSiape = count($buscaSiape);
    if ($countBuscaSiape <= 0):
        header('Location: admin_emotoristas.php?msg=true');
    else:
        foreach ($buscaSiape as $condutor);
        if ($condutor['os_motorista'] == 'n'):
            header('Location: admin_emotoristas.php?cond=true');
        else:
            if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):
?>
                <h1 class="titulo-secao"><i class="fa fa-pencil-square-o"></i> Editar dados do condutor</h1> 

<?php
                echo "<a href='admin_emotoristas.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Condutores'><i class='fa fa-list'></i> Listar Condutores</a>";
                
                if (isset($_POST['up_condutor'])):
                    //pega data da cnh
                    $venc_cnh = $_POST['venc_cnh'];
                    $separa_venc_cnh = explode('/', $venc_cnh);
                    $venc_cnh_dia = $separa_venc_cnh[0];
                    $venc_cnh_mes = $separa_venc_cnh[1];
                    $venc_cnh_ano = $separa_venc_cnh[2];
                    $venc_cnh_ok = $venc_cnh_ano . '-' . $venc_cnh_mes . '-' . $venc_cnh_dia;
                    //pega data da OS
                    $venc_os = $_POST['data_os'];
                    $separa_venc_os = explode('/', $venc_os);
                    $venc_os_dia = $separa_venc_os[0];
                    $venc_os_mes = $separa_venc_os[1];
                    $venc_os_ano = $separa_venc_os[2];
                    $venc_os_ok = $venc_os_ano . '-' . $venc_os_mes . '-' . $venc_os_dia;

                    $fup['cnh'] = strip_tags(trim($_POST['cnh']));
                    $fup['cnh_categoria'] = strip_tags(trim($_POST['categ_cnh']));
                    $fup['cnh_vencimento'] = $venc_cnh_ok;
                    $fup['os_motorista'] = strip_tags(trim($_POST['numero_os']));
                    $fup['os_motorista_data'] = $venc_os_ok;
                    $fup['cod_abastecimento'] = strip_tags(trim($_POST['cod_abastecimento']));
                    $fup['pass_abastecimento'] = strip_tags(trim($_POST['pass_abastecimento']));
                    $id_condutor = $_POST['condutor'];
                    $atArquivo_os = $_FILES['arquivo_os']['tmp_name'];
                    $atArquivo_cnh = $_FILES['arquivo_cnh']['tmp_name'];


                    if ($fup['cnh'] == '' or $fup['cnh_categoria'] == '' or $fup['cnh_vencimento'] == '' or $fup['os_motorista'] == '' or $fup['os_motorista_data'] == ''):
                        echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Preencha todos os campos e selecione o arquivo PDF da OS!</i></h4>';
                    else:
                        $verifica_cnh = read('servidores', "WHERE cnh = '$fup[cnh]'");
                        $num_cnh = count($verifica_cnh);
                        if ($num_cnh > 1):
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Este número de CNH já esta cadastrado</h4>";
                        else:
                            $verifica_os = read('servidores', "WHERE os_motorista = '$fup[os_motorista]'");
                            $num_os = count($verifica_os);
                            if ($num_os > 50):
                                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Este número de Ordem de Serviço já foi usado!</h4>";
                            else:
                                $hoje = date('Y-m-d');
                                if ($fup['cnh_vencimento'] < $hoje):
                                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Data inválida para o vencimento da CNH!</h4>";
                                else:
                                    //Arquivo da OS
                                    if ($atArquivo_os != ''):
                                        //Validações do envio do arquivo
                                        $up_os['pasta'] = 'os/';
                                        $up_os['tamanho'] = 1024 * 1024 * 2; //2MB
                                        $up_os['renomeia'] = true;
                                        $veri_ext = array('pdf', 'rtf'); //Extensão do arquivo
                                        //Array com os tipos de erro de uploads de arquivos no php
                                        $up_os['erros'][0] = 'Não houve erro';
                                        $up_os['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                                        $up_os['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                                        $up_os['erros'][3] = 'O upload do arquivo foi feito parcialmente';
                                        $up_os['erros'][4] = 'Não foi feito o upload do arquivo';

                                        //Verifica se houve erro com o upload do arquivo
                                        if ($_FILES['arquivo_os']['error'] != 0):
                                            die('Não foi possível fazer o upload do arquivo (OS). Erro:<br />' . $up_os['erros'][$_FILES['arquivo_os']['error']]);
                                            exit;
                                        endif;

                                        //Verifica a extensão do arquivo
                                        $extensao_os = strtolower(end(explode('.', $_FILES['arquivo_os']['name'])));
                                        if (!in_array($extensao_os, $veri_ext)):
                                            echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Somente arquivos PDF ou RTF (OS)</i></h4>';
                                        elseif ($up_os['tamanho'] <= $_FILES['arquivo_os']['size']):
                                            echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Tamanho máximo de arquivo: 2MB (OS)</h4>';
                                        else:
                                            // Primeiro verifica se deve trocar o nome do arquivo
                                            if ($up_os['renomeia'] == true):
                                                $nome_final_os = 'os_' . $condutor_siape . '.' . $extensao_os;
                                                $fup['os_digitalizada'] = $nome_final_os;
                                            else:
                                                $nome_final_os = $_FILES['arquivo_os']['name'];
                                                $fup['os_digitalizada'] = $nome_final_os;
                                            endif;
                                            if ($dados['os_digializada'] == $f['os_digitalizada']):
                                                $nome_final_os = 'os_' . $condutor['siape'] . '_' . time() . '.' . $extensao_os;
                                                $fup['os_digitalizada'] = $nome_final_os;
                                            endif;
                                        endif;
                                    else:
                                        $fup['os_digitalizada'] = $condutor['os_digitalizada'];
                                    endif;
                                    
                                    //Arquivo da CNH
                                    if ($atArquivo_cnh != ''):
                                        //Validações do envio do arquivo
                                        $up_cnh['pasta'] = 'cnh/';
                                        $up_cnh['tamanho'] = 1024 * 1024 * 2; //2MB
                                        $up_cnh['renomeia'] = true;
                                        $veri_ext = array('pdf', 'rtf'); //Extensão do arquivo
                                        //Array com os tipos de erro de uploads de arquivos no php
                                        $up_cnh['erros'][0] = 'Não houve erro';
                                        $up_cnh['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                                        $up_cnh['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                                        $up_cnh['erros'][3] = 'O upload do arquivo foi feito parcialmente';
                                        $up_cnh['erros'][4] = 'Não foi feito o upload do arquivo';

                                        //Verifica se houve erro com o upload do arquivo
                                        if ($_FILES['arquivo_cnh']['error'] != 0):
                                            die('Não foi possível fazer o upload do arquivo (CNH). Erro:<br />' . $up_os['erros'][$_FILES['arquivo_cnh']['error']]);
                                            exit;
                                        endif;

                                        //Verifica a extensão do arquivo
                                        $extensao_cnh = strtolower(end(explode('.', $_FILES['arquivo_cnh']['name'])));
                                        if (!in_array($extensao_cnh, $veri_ext)):
                                            echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Somente arquivos PDF ou RTF (CNH)</i></h4>';
                                        elseif ($up_cnh['tamanho'] <= $_FILES['arquivo_cnh']['size']):
                                            echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Tamanho máximo de arquivo: 2MB (CNH)</h4>';
                                        else:
                                            // Primeiro verifica se deve trocar o nome do arquivo
                                            if ($up_cnh['renomeia'] == true):
                                                $nome_final_cnh = 'os_' . $condutor_siape . '.' . $extensao_cnh;
                                                $fup['cnh_digitalizada'] = $nome_final_cnh;
                                            else:
                                                $nome_final_cnh = $_FILES['arquivo_cnh']['name'];
                                                $fup['cnh_digitalizada'] = $nome_final_cnh;
                                            endif;
                                            if ($dados['cnh_digializada'] == $f['cnh_digitalizada']):
                                                $nome_final_cnh = 'cnh_' . $condutor['siape'] . '_' . time() . '.' . $extensao_cnh;
                                                $fup['cnh_digitalizada'] = $nome_final_cnh;
                                            endif;
                                        endif;
                                    else:
                                        $fup['cnh_digitalizada'] = $condutor['cnh_digitalizada'];
                                    endif;
                                    
                                    //Atualiza dados na base de dados
                                    $upCondutor = update('servidores', $fup, "id = '$id_condutor'");
                                    
                                    //Verifica uploads dos arquivos
                                    if($upCondutor):
                                        echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Dados do condutor atualizados com sucesso!</h6>";
                                        // Depois verifica se é possível mover os arquivos para as pastas escolhidas
                                        //OS
                                        if ($atArquivo_os != ''):
                                            if($_FILES['arquivo_os']['error'] == 0):
                                                move_uploaded_file($_FILES['arquivo_os']['tmp_name'], $up_os['pasta'] . $nome_final_os);
                                                unlink('os/' . $condutor['os_digitalizada']);
                                            else:
                                                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao fazer upload do arquivo (OS)!</h4>";                                            
                                            endif;
                                        endif;
                                        //CNH
                                        if ($atArquivo_cnh != ''):
                                            if($_FILES['arquivo_cnh']['error'] == 0):
                                                move_uploaded_file($_FILES['arquivo_cnh']['tmp_name'], $up_cnh['pasta'] . $nome_final_cnh);
                                                unlink('cnh/' . $condutor['cnh_digitalizada']);
                                            else:
                                                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao fazer upload do arquivo (CNH)!</h4>";                                            
                                            endif;
                                        endif;
                                        header('Refresh: 2;url=admin_emotoristas.php');
                                    else:    
                                       echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao atualizar dados do condutor!</h4>";
                                    endif;        
                                endif;                                    
                            endif;
                        endif;
                    endif;
                endif;
?> 

                <form name="cad_condutor" method="post" action="" enctype="multipart/form-data">
                    <fieldset class="user_altera_dados">
                        <h2 class="form_sub_titulo_100">Informe os seguintes dados</h2>    
                        <label>
                            <span><strong>Servidor:</strong> <?php echo $condutor['nome']; ?></span><br />
                        </label>
                        <label class="label_medio">
                            <span>Número da CNH:</span>
                            <input type="text" name="cnh" class="select_medio" value="<?php
                                if (isset($f['cnh'])):
                                    echo $f['cnh'];
                                else:
                                    if ($condutor['cnh'] != ''):
                                        echo $condutor['cnh'];
                                    endif;
                                endif;
                            ?>"/>
                        </label>
                        <label class="label_medio">
                            <span>Categoria da CNH:</span>
                            <select name="categ_cnh" id="categ_cnh" class="select_medio">
                                <?php if ($condutor['cnh_categoria'] == '') echo '<option value="" selected></option>';
                                echo '<option value=""></option>'; ?>
                                <?php if ($condutor['cnh_categoria'] == 'AB') echo '<option value="AB" selected="selected">AB</option>';
                                echo '<option value="AB">AB</option>'; ?>
                                <?php if ($condutor['cnh_categoria'] == 'B') echo '<option value="B" selected="selected">B</option>';
                                echo '<option value="B">B</option>'; ?>
                                <?php if ($condutor['cnh_categoria'] == 'AC') echo '<option value="AC" selected="selected">AC</option>';
                                echo '<option value="AC">C</option>'; ?>
                                <?php if ($condutor['cnh_categoria'] == 'C') echo '<option value="C" selected="selected">C</option>';
                                echo '<option value="C">C</option>'; ?>
                                <?php if ($condutor['cnh_categoria'] == 'AD') echo '<option value="AD" selected="selected">AD</option>';
                                echo '<option value="AD">C</option>'; ?>
                                <?php if ($condutor['cnh_categoria'] == 'D') echo '<option value="D" selected="selected">D</option>';
                                echo '<option value="D">D</option>'; ?>
                                <?php if ($condutor['cnh_categoria'] == 'E') echo '<option value="E" selected="selected">E</option>';
                                echo '<option value="E">E</option>'; ?>
                            </select>
                        </label>
                        <label class="label_medio">
                            <span>Vencimento da CNH:</span>
                            <input type="text" name="venc_cnh" id="venc_cnh" class="select_medio" value="<?php
                            if (isset($venc_cnh)):
                                echo $venc_cnh;
                            else:
                                if ($condutor['cnh_vencimento'] != ''):
                                    echo date('d/m/Y', strtotime($condutor['cnh_vencimento']));
                                endif;
                            endif;
                            ?>"/>
                        </label>
                        <label class="label_medio">
                            <span>Número Ordem de Serviço:</span>
                            <input type="text" name="numero_os" class="select_medio" maxlength="5" value="<?php
                            if (isset($f['os_motorista'])) {
                                echo $f['os_motorista'];
                            } else {
                                if ($condutor['os_motorista'] != 'n') {
                                    echo $condutor['os_motorista'];
                                }
                            }
                            ?>"/>
                        </label>
                        <label class="label_medio">
                            <span>Data Ordem de Serviço:</span>
                            <input type="text" name="data_os" id="data_os" class="select_medio" value="<?php
                            if (isset($venc_os)):
                                echo $venc_os;
                            else:
                                if ($condutor['os_motorista_data'] != ''):
                                    echo date('d/m/Y', strtotime($condutor['os_motorista_data']));
                                endif;
                            endif;
                            ?>"/>
                        </label>
                        <label style="margin-top: 10px;">
                            <span>Ordem de Serviço Digitalizada:</span>
                            <input type="file" name="arquivo_os" id="arquivo_os" class="foto"/>
                        </label>
                        <label>
                            <?php if(!empty($condutor['os_digitalizada'])): ?>
                                <span style="margin:-30px 10px 25px 10px;"><a href="os/<?php echo $condutor['os_digitalizada']; ?>" target="_blank" style=" font:13px 'Trebuchet MS', Arial, Helvetica, sans-serif; color:#06F; text-decoration:none;">&raquo; Visualizar o arquivo atual</a></span>
                            <?php else: ?>
                                <span style="margin:-30px 10px 25px 10px; font:13px 'Trebuchet MS', Arial, Helvetica, sans-serif; color:#8c8c8c;">&raquo; Não existe arquivo cadastro para a OS.</span>
                            <?php endif; ?>
                        </label>
                        <label style="margin-top: 10px;">
                            <span>CNH Digitalizada:</span>
                            <input type="file" name="arquivo_cnh" id="arquivo_cnh" class="foto"/>
                        </label>
                        <label>
                            <?php if(!empty($condutor['cnh_digitalizada'])): ?>
                                <span style="margin:-30px 10px 25px 10px;"><a href="cnh/<?php echo $condutor['cnh_digitalizada']; ?>" target="_blank" style=" font:13px 'Trebuchet MS', Arial, Helvetica, sans-serif; color:#06F; text-decoration:none;">&raquo; Visualizar a CNH atual</a></span>
                            <?php else: ?>
                                <span style="margin:-30px 10px 25px 10px; font:13px 'Trebuchet MS', Arial, Helvetica, sans-serif; color:#8c8c8c;">&raquo; Não existe arquivo cadastro para a CNH.</span>
                            <?php endif; ?>
                        </label>
                        <label class="label_medio">
                            <span>Código para abastecimento:</span>
                            <input type="text" name="cod_abastecimento" class="select_medio" maxlength="8" value="<?php
                            if (isset($f['cod_abastecimento'])) {
                                echo $f['cod_abastecimento'];
                            } else {
                                if ($condutor['cod_abastecimento'] != '0') {
                                    echo $condutor['cod_abastecimento'];
                                }
                            }
                            ?>"/>
                        </label>
                        <label class="label_medio">
                            <span>Senha para abastecimento:</span>
                            <input type="text" name="pass_abastecimento" class="select_medio" maxlength="4" value="<?php
                            if (isset($fup['pass_abastecimento'])) {
                                echo $fup['pass_abastecimento'];
                            } elseif ($condutor['pass_abastecimento'] == '0000') {
                                echo '0000';
                            } else {
                                if ($condutor['pass_abastecimento'] != '0') {
                                    echo $condutor['pass_abastecimento'];
                                }
                            }
                            ?>"/>
                        </label>
                        <label></label>
                        <input type="submit" name="up_condutor" value="Atualizar" class="btn btn_altera btn_green fl_right" />
                    </fieldset>
                    <input type="hidden" name="condutor" value="<?php echo $condutor['id']; ?>" />  
                </form>

            
            
<?php
            else:
                echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
            endif;        
        endif;
    endif;
endif;
include_once "includes/inc_footer.php"; 
?>