<?php
include_once('includes/inc_header.php');

$veiculoId = $_GET['id_veiculo'];
$hoje_ipva = date('Y-m-d');
$readVeiculo = read('vt_veiculos', "WHERE id = '$veiculoId'");
$countReadVeiculo = count($readVeiculo);
if ($countReadVeiculo < 1):
    header('Location: admin_eveiculos.php');
else:
    if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):

        foreach ($readVeiculo as $veiculo);
?>
        <h1 class="titulo-secao"><i class="fa fa-car"></i> Editar dados do veículo</h1>    
<?php
        echo "<a href='admin_eveiculos.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Veículos'><i class='fa fa-list'></i> Listar Veículos</a>";
        
        //Desativa veículo
        if (isset($_POST['btn_desativa'])):
            //Dados do veiculo
            $at['ativo'] = '0';
            $at['situacao'] = strip_tags(trim($_POST['situacaoDescreve']));

            //Atualiza dados na base de dados
            if ($at['ativo'] != '' and $at['situacao'] != ''):
                //Atualiza 
                $upSituacao = update('vt_veiculos', $at, "id = '$veiculoId'");
                //Se deu certo
                if ($upSituacao):
                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Veículo Desativado com sucesso!</h4>";
                    header('Refresh: 1,url=admin_aveiculos.php?id_veiculo=' . $veiculoId);
                else:
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao desativar o veículo!</h4>";                    
                endif;
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Informe todos os dados!</h4>";
            endif;
        endif;

        //Ativa veículo
        if (isset($_POST['btn_ativa'])):
            //Dados do veiculo
            $at['ativo'] = '1';
            $at['situacao'] = 'ok';

            //Atualiza dados na base de dados
            if ($at['ativo'] != '' and $at['situacao'] != ''):
                //Atualiza 
                $upSituacao = update('vt_veiculos', $at, "id = '$veiculoId'");
                //Se deu certo
                if ($upSituacao):
                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Veículo Ativado com sucesso!</h4>";
                    header('Refresh: 1,url=admin_aveiculos.php?id_veiculo=' . $veiculoId);
                else:
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao ativar o veículo!</h4>";
                endif;
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Informe todos os dados!</h4>";
            endif;
        endif;
            
        //Atualizar demais dados do veículo
        if (isset($_POST['atualizar'])):

            //Dados do Seguro
            $data_seguro = strip_tags(trim($_POST['data_seguro']));
            $separa_data_seguro = explode('/', $data_seguro);
            $seguro_dia = $separa_data_seguro[0];
            $seguro_mes = $separa_data_seguro[1];
            $seguro_ano = $separa_data_seguro[2];
            $data_vai_seguro = $seguro_ano . '-' . $seguro_mes . '-' . $seguro_dia;

            $up['atualizadoEm'] = date('Y-m-d H:i:s');
            $up['veiculo'] = strip_tags(trim($_POST['veiculo']));
            $up['ano_modelo'] = strip_tags(trim($_POST['modelo']));
            $up['placa'] = strip_tags(trim($_POST['placa']));
            $up['renavam'] = strip_tags(trim($_POST['renavam']));
            $up['seguro'] = $data_vai_seguro;
            $up['km'] = strip_tags(trim($_POST['km']));
            $up['troca_oleo'] = strip_tags(trim($_POST['oleo']));
            $up['revisao'] = strip_tags(trim($_POST['revisao']));
            $up['geometria'] = strip_tags(trim($_POST['geometria']));
            $up['combustivel'] = strip_tags(trim($_POST['combustivel']));
            $sem_foto = 'sem_veiculo.png';

            //Atualiza dados na base de dados
            if ($up['veiculo'] != '' and $up['placa'] != '' and $up['renavam'] != '' and $up['seguro'] != '' and $up['km'] != '' and $up['troca_oleo'] != '' and $up['revisao'] != '' and $up['combustivel'] != ''):

                //Imagem-Foto do veículo
                $fotoveiculo = $_FILES["foto"]["tmp_name"];
                if ($fotoveiculo != ""):
                    if ($veiculo['foto'] != $sem_foto):
                        $deletaFile = \unlink('imagens/veiculos/' . $veiculo['foto']);
                    endif;
                    $foto_nomeTemporario = $_FILES["foto"]["tmp_name"];
                    $foto_nomeReal = $_FILES["foto"]["name"];
                    $foto = $foto_nomeReal;
                    $up['foto'] = $foto;
                else:
                    $foto = $veiculo['foto'];
                    $up['foto'] = $foto;
                endif;

                //Validações do envio do arquivo
                $upDoc['pasta'] = 'doc-veiculos/';
                $upDoc['tamanho'] = 1024 * 1024 * 2; //2MB
                $upDoc['renomeia'] = true;
                $veri_ext = array('pdf', 'rtf'); //Extensão do arquivo
                //Array com os tipos de erro de uploads de arquivos no php
                $upDoc['erros'][0] = 'Não houve erro';
                $upDoc['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                $upDoc['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                $upDoc['erros'][3] = 'O upload do arquivo foi feito parcialmente';
                $upDoc['erros'][4] = 'Não foi feito o upload do arquivo';

                //Verifica a extensão do arquivo
                $extensao = strtolower(end(explode('.', $_FILES['doc_veiculo']['name'])));
                $doc_veiculo = $_FILES["doc_veiculo"]["tmp_name"];
                if($doc_veiculo != ''):
                    if (!in_array($extensao, $veri_ext)):
                        echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp; Somente arquivos PDF ou RTF</h4>';
                    elseif ($upDoc['tamanho'] <= $_FILES['doc_veiculo']['size']):
                        echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp; Tamanho máximo de arquivo: 2MB</h4>';
                    else:
                        // Primeiro verifica se deve trocar o nome do arquivo
                        if ($upDoc['renomeia'] == true):
                            $nome_final = 'doc_' . $up['placa'] . '_' . time() . '.' . $extensao;
                            $up['doc_veiculo'] = $nome_final;
                        else:
                            $nome_final = $_FILES['doc_veiculo']['name'];
                            $up['doc_veiculo'] = $nome_final;
                        endif;
                        if ($dados['doc_veiculo'] == $veiculo['doc_veiculo']):
                            $nome_final = 'doc_' . $up['placa'] . '_' . time() . '.' . $extensao;
                            $up['doc_veiculo'] = $nome_final;
                        endif;
                    endif;
                else:
                    $up['doc_veiculo'] = $veiculo['doc_veiculo'];
                endif;
                
                //Atualiza os dados do servidor na tabela servidores
                $upVeiculo = update('vt_veiculos', $up, "id = '$veiculoId'");

                if ($upVeiculo):
                    //Grava a foto do servidor na pasta de imagens
                    if ($_FILES["foto"]["tmp_name"] != ""):
                        copy($foto_nomeTemporario, 'imagens/veiculos/' . $up['foto']);
                    endif;
                    
                    // Verifica se foi informado algum documento para o veículo
                    if($doc_veiculo != ''):
                        // Verifica se é possível fazer o upload do arquivo do documento do veículo
                        if (move_uploaded_file($_FILES['doc_veiculo']['tmp_name'], $upDoc['pasta'].$nome_final)):
                            if ($upVeiculo):
                                if($_FILES['doc_veiculo'] != ''):
                                    unlink('doc-veiculos/'.$veiculo['doc_veiculo']);
                                endif;
                            else:
                                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao atualiar os dados do veículo!</h4>";
                            endif;
                        else:
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao fazer o Upload do Arquivo!</h4>";
                        endif;
                    endif;
                    
                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Dados do veículo atualizados com sucesso!</h4>";
                    header('Refresh: 1,url=admin_aveiculos.php?id_veiculo=' . $veiculoId);  
                else:
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao cadastrar. Contate o administrador.</h4>";
                endif;

            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Por favor, preencha todos os campos!</h4>";
            endif;
        endif;
?>

        <div class="veiculo_sit">
            <?php
            if ($veiculo['ativo'] == 1):
                echo '<span class="btn btn_red btn_desativaVeiculo">DESATIVAR ESTE VEÍCULO</span>';
                echo '<form name="motivoDesativa" method="post" class="formMotivoDesativa">
                        <label>
                            <span>Informe o motivo da desativação:</span>
                            <textarea name="situacaoDescreve"></textarea>
                        </label>
                            <h3 class="btn btn_default fl_right cancelaDesativacao">Cancelar</h3>	
                            <input type="submit" name="btn_desativa" class="btn btn_red fl_right btn_desativa" style="margin-right: 10px;" value="OK" />
                    </form>';
            else:
                echo "<div class='ms al' style='margin: 15px 0 10px 0; paddind-left: 0;'><i class='fa fa-exclamation-circle fa-2x' style='color: #F60;'></i>&nbsp;&nbsp;&nbsp; Veículo desativado &nbsp;&nbsp;&nbsp;<i class='fa fa-caret-right'></i>&nbsp;&nbsp;&nbsp; <strong>Situação:</strong> {$veiculo['situacao']} <div class='btn btn_green fl_right btn_ativaVeiculo' style='padding: 15px;'>Ativar este veículo</div></div><br />";
                echo '<form name="ativaVeiculo" method="post" class="formAtivaVeiculo">
                            <h3 class="btn btn_default fl_right cancelaAtivacao">Cancelar</h3>	
                            <input type="submit" name="btn_ativa" class="btn btn_green fl_right btn_ativa" style="margin-right: 10px;" value="Sim, Ativar!" />
                        </form>';
            endif;
            ?>
        </div>

        <form name="up_veiculo" id="cad_servidor" method="post" action="" enctype="multipart/form-data">
            <fieldset class="user_altera_dados">
                <h2 class="form_sub_titulo_100">Atualizar os dados do veículo <?php echo "{$veiculo['veiculo']}-{$veiculo['placa']}"; ?></h2>          
                <label class="label_medio">
                    <span>Veículo:</span>
                    <input name="veiculo" type="text" value="<?php echo $veiculo['veiculo']; ?>" maxlength="100" class="select_80"/>
                </label>
                <label class="label_medio">
                    <span>Ano Modelo:</span>
                    <input name="modelo" type="text" value="<?php echo $veiculo['ano_modelo']; ?>" maxlength="4" class="select_80"/>
                </label>
                <label class="label_medio">
                    <span>Capacidade:</span>
                    <input name="capacidade" type="text" class="select_80" value="<?php echo $veiculo['capacidade']; ?>" maxlength="2"/>
                </label>
                <label class="label_medio">
                    <span>Placa:</span>
                    <input name="placa" type="text" class="select_80" value="<?php echo $veiculo['placa']; ?>" maxlength="8"/>
                </label>
                <label class="label_medio">
                    <span>Renavam:</span>
                    <input name="renavam" type="text" class="select_80" value="<?php echo $veiculo['renavam']; ?>" maxlength="15"/>
                </label>
                <label class="label_medio">
                    <span>Vencimento Seguro Obrigatório:</span>
                    <input type="text" id="data_ipva" class="select_80" name="data_seguro" value="<?php echo date('d/m/Y', strtotime($veiculo['seguro'])); ?>" />
                </label>
                <label>
                    <span>Alterar documento do veículo:</span>
                    <div class="cadastra_foto_servidor">
                        <div style="margin: 5px 0 0 10px; width: 115px; background-color:#FFF; height:100px; border-radius:5px;">
                            <?php
                                if($veiculo['doc_veiculo'] != '' && $veiculo['doc_veiculo'] != NULL):
                            ?>
                                    <a href="<?php echo 'doc-veiculos/'.$veiculo['doc_veiculo']; ?>" target="_blank"><img src="imagens/doc-ok.png" width="80px" style="float:left; margin:15px 0 0 10px"/></a>
                            <?php
                                else:
                            ?>        
                                    <img src="imagens/doc-no.svg" width="80px" style="float:left; margin:15px 0 0 10px"/>
                            <?php
                                endif;
                            ?>
                        </div>
                        <input type="file" name="doc_veiculo" id="doc_veiculo" value="<?php echo $veiculo['doc_veiculo']; ?>" style="border:none; font-size: 0.875em; color:#666; margin: -80px 0 0 130px; float:left;" />
                        <span style="margin: -30px 0 0 140px;">Formato PDF ou RTF</span>
                    </div>
                </label>
                <label class="label_medio">
                    <span>KM Atual:</span>
                    <input name="km" type="text" id="km" class="select_medio" value="<?php echo $veiculo['km']; ?>" maxlength="8"/>
                </label>  
                <label class="label_medio">
                    <span>Próx. troca de óleo (KM):</span>
                    <input name="oleo" type="text" class="select_medio" id="oleo" value="<?php echo $veiculo['troca_oleo']; ?>" maxlength="9"/>
                </label>
                <label class="label_medio">
                    <span>Próx. revisão mecânica (KM):</span>
                    <input name="revisao" type="text" class="select_medio" id="revisao" value="<?php echo $veiculo['revisao']; ?>" maxlength="9"/>
                </label>
                <label class="label_medio">
                    <span>Próx. Geo/Balanceamento (KM):</span>
                    <input name="geometria" type="text" class="select_medio" id="geometria" value="<?php echo $veiculo['geometria']; ?>" maxlength="9"/>
                </label>
                <label class="label_medio">
                    <?php
                    //Verifica Combustivel
                    if ($veiculo['combustivel'] == 4) {
                        $exibeCombustivel = '4/4';
                    } elseif ($veiculo['combustivel'] == 3) {
                        $exibeCombustivel = '3/4';
                    } elseif ($veiculo['combustivel'] == 2) {
                        $exibeCombustivel = '1/2';
                    } elseif ($veiculo['combustivel'] == 1) {
                        $exibeCombustivel = '1/4';
                    } else {
                        $exibeCombustivel = 'Reserva';
                    }
                    ?>
                    <span>Combustível:</span>
                    <select name="combustivel" id="combustivel" class="select_medio">
                        <option value="<?php echo $exibeCombustivel; ?>" selected><?php echo $exibeCombustivel; ?></option>         
                        <option value="4">4/4</option>
                        <option value="3">3/4</option>
                        <option value="2">1/2</option>
                        <option value="1">1/4</option>
                        <option value="0">Reserva</option>
                    </select>
                </label> 
                <label>
                    <span>Mudar Foto do veículo:</span>
                    <div class="cadastra_foto_servidor">
                        <img src="imagens/veiculos/<?php echo $veiculo['foto']; ?>" width="105px" style="float:left; margin:15px 0 0 10px"/>
                        <input type="file" name="foto" id="foto" value="<?php echo $veiculo['foto']; ?>" style="border:none; font-size: 0.875em; color:#666; margin: -45px 0 0 130px;" />
                        <span style="margin: 5px 0 0 140px;">Formato JPG ou PNG</span>
                    </div>
                </label>
                <input type="submit" name="atualizar" value="Atualizar" id="atualizar" class="btn btn_green btn_altera fl_right" />  
            </fieldset>
        </form>

<?php
    else:
        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
    endif;
endif;
include_once "includes/inc_footer.php";
?>