<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):
?>
    
    <h1 class="titulo-secao"><i class="fa fa-car"></i> Cadastrar novo veículo</h1>

<?php
    echo "<a href='admin_eveiculos.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Servidores'><i class='fa fa-list'></i> Listar Veículos</a>";
    
    if (isset($_POST['cadastrar'])):

        //Formatar data recebida do Seguro
        $data_seguro = $_POST['data_seguro'];
        $sep_data_seguro = explode('/', $data_seguro);
        $seguro_dia = $sep_data_seguro[0];
        $seguro_mes = $sep_data_seguro[1];
        $seguro_ano = $sep_data_seguro[2];
        $data_seguro_vai = $seguro_ano . '-' . $seguro_mes . '-' . $seguro_dia;

        //Dados do veiculo
        $f['atualizadoEm'] = date('Y-m-d H:i:s');
        $f['veiculo'] = strip_tags(trim($_POST['veiculo']));
        $f['ano_modelo'] = strip_tags(trim($_POST['modelo']));
        $f['placa'] = strip_tags(trim($_POST['placa']));
        $f['capacidade'] = strip_tags(trim($_POST['capacidade']));
        $f['renavam'] = strip_tags(trim($_POST['renavam']));
        $f['ipva'] = NULL;
        $f['seguro'] = $data_seguro_vai;
        $f['km'] = strip_tags(trim($_POST['km']));
        if($f['km'] == ''):
            $f['km'] == 0;
        endif;
        $f['combustivel'] = $_POST['combustivel'];
        $f['troca_oleo'] = strip_tags(trim($_POST['oleo']));
        $f['revisao'] = $_POST['revisao'];
        $f['geometria'] = $_POST['geometria'];
        $f['correa'] = $_POST['km'] + 50000;
        $f['situacao'] = $_POST['situacao'];

        //Cadastra dados na base de dados
        if ($f['veiculo'] and $f['placa'] and $f['capacidade'] and $f['renavam'] and $f['combustivel'] and $f['troca_oleo'] and $f['revisao'] and $f['situacao'] != ""):

            //Foto do veículo
            $f['foto'] = $_FILES["foto"]["tmp_name"];
            if ($f['foto'] == ""):
                $foto_vai = 'sem_veiculo.png';
                $f['foto'] = $foto_vai;
            else:
                $foto_nomeTemporario = $_FILES["foto"]["tmp_name"];
                $foto_nomeReal = $_FILES["foto"]["name"];
                $foto_vai = $f['veiculo'] . '_' . $foto_nomeReal;
                $f['foto'] = $foto_vai;
            endif;

            //Validações do envio do arquivo
            $up['pasta'] = 'doc-veiculos/';
            $up['tamanho'] = 1024 * 1024 * 2; //2MB
            $up['renomeia'] = true;
            $veri_ext = array('pdf', 'rtf'); //Extensão do arquivo
            //Array com os tipos de erro de uploads de arquivos no php
            $up['erros'][0] = 'Não houve erro';
            $up['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
            $up['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
            $up['erros'][3] = 'O upload do arquivo foi feito parcialmente';

            //Verifica a extensão do arquivo
            $extensao = strtolower(end(explode('.', $_FILES['doc_veiculo']['name'])));
            $doc_veiculo = $_FILES["doc_veiculo"]["tmp_name"];
            if($doc_veiculo != ''):
                if (!in_array($extensao, $veri_ext)):
                    echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp; Somente arquivos PDF ou RTF</h4>';
                elseif ($up['tamanho'] <= $_FILES['doc_veiculo']['size']):
                    echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp; Tamanho máximo de arquivo: 2MB</h4>';
                else:
                    // Primeiro verifica se deve trocar o nome do arquivo
                    if ($up['renomeia'] == true):
                        $nome_final = 'doc_' . $f['placa'] . '.' . $extensao;
                        $f['doc_veiculo'] = $nome_final;
                    else:
                        $nome_final = $_FILES['doc_veiculo']['name'];
                        $f['doc_veiculo'] = $nome_final;
                    endif;
                    if ($dados['doc_veiculo'] == $f['doc_veiculo']):
                        $nome_final = 'doc_' . $f['placa'] . '_' . time() . '.' . $extensao;
                        $f['doc_veiculo'] = $nome_final;
                    endif;
                endif;
            else:
                $f['doc_veiculo'] = '';
            endif;   
            
            //Cadastra dados na base de dados
            $cadVeiculo = create('vt_veiculos', $f);

            if ($cadVeiculo):
                //Grava a foto do veiculo na pasta de imagens
                if ($_FILES["foto"]["tmp_name"] != ""):
                    copy($foto_nomeTemporario, 'imagens/veiculos/' . $f['foto']);
                endif;
                
                if ($cadVeiculo):
                    //mensagem se OK
                    echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp;&nbsp;&nbsp; Veículo cadastrado com sucesso!</h6>";
                    
                    //Verifica se é possível mover o arquivo para a pasta escolhida
                    if($doc_veiculo != ''):
                        if (move_uploaded_file($_FILES['doc_veiculo']['tmp_name'], $up['pasta'].$nome_final)):
                            
                        else:
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao fazer o Upload do Arquivo. -> {$up['pasta']}.{$nome_final}</h4>";
                        endif;
                    endif;
                else:
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao cadastrar veículo!</h4>";
                endif;
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao cadastrar. Contate o administrador.</h4>";
            endif;
                    
        else:
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Por favor, preencha todos os campos obrigatórios!</h4>";
        endif;
    endif;
?>

    <form name="cad_servidor" id="cad_servidor" method="post" action="" enctype="multipart/form-data">

        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Informe os seguintes dados</h2>
            <label class="label_medio">
                <span>Veículo:</span>
                <input name="veiculo" type="text" value="" maxlength="100" class="select_80"/>
            </label>
            
            <label class="label_medio">
                <span>Ano Modelo:</span>
                <input name="modelo" type="text" value="" maxlength="4" class="select_80"  />
            </label>
            
            <label class="label_medio">
                <span>Capacidade:</span>
                <input name="capacidade" type="text" class="select_80" value="" maxlength="2"/>
            </label>

            <label class="label_medio">
                <span>Placa:</span>
                <input name="placa" type="text" class="select_80" value="" maxlength="8"/>
            </label>

            <label class="label_medio">
                <span>Renavam:</span>
                <input name="renavam" type="text" class="select_80" value="" maxlength="15"/>
            </label>
            
            <label class="label_medio">
                <span>Vencimento Seguro Obrigatório:</span>
                <input type="text" id="data_ipva" class="select_80" name="data_seguro" />
            </label>
            
            <label>
                <span>Documento do Veículo:</span>
                <div class="cadastra_foto_servidor">
                    <input type="file" name="doc_veiculo" id="doc_veiculo" class="foto"/>
                    <span>Formato PDF</span>
                </div>
            </label> 

            <label class="label_medio">
                <span>KM Atual:</span>
                <input name="km" type="text" id="km" class="select_medio" value="" maxlength="8"/>
            </label>  

            <label class="label_medio">
                <span>Próx. troca de óleo do motor (KM):</span>
                <input name="oleo" type="text" class="select_medio" id="oleo" value="" maxlength="9"/>
            </label>

            <label class="label_medio">
                <span>Próx. Geometria (KM):</span>
                <input name="geometria" type="text" class="select_medio" id="geometria" value="" maxlength="9"/>
            </label>

            <label class="label_medio">
                <span>Próx. revisão mecânica (KM):</span>
                <input name="revisao" type="text" class="select_medio" id="revisao" value="" maxlength="9"/>
            </label>

            <label class="label_medio">
                <span>Combustível:</span>
                <select name="combustivel" id="combustivel" class="select_medio">
                    <option value="" selected disabled>Selecionar</option>         
                    <option value="4">4/4</option>
                    <option value="3">3/4</option>
                    <option value="2">1/2</option>
                    <option value="1">1/4</option>
                    <option value="0">Reserva</option>
                </select>
            </label>  

            <label class="label_medio">
                <span>Situação:</span>
                <select name="situacao" id="situacao" class="select_medio">
                    <option value="" selected disabled>Selecionar</option>         
                    <option value="ok">Ativo - pronto para o uso</option>
                    <option value="inativo">Inativo</option>
                </select>
            </label>   

            <label>
                <span>Foto do veículo:</span>
                <div class="cadastra_foto_servidor">
                    <input type="file" name="foto" id="foto" class="foto"/>
                    <span>Formato JPG ou PNG</span>
                </div>
            </label>
            <input type="submit" name="cadastrar" value="CADASTRAR" id="cadastrar" class="btn btn_green btn_altera fl_right" />
            
        </fieldset>
    </form>

<?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;

include_once "includes/inc_footer.php";
?>