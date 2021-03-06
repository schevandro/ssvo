<?php
include_once "includes/inc_header.php";
include_once "includes/inc_menu.php";
?>

<!--calcula data da CNH-->
<?php
$hoje = date('Y-m-d');
$time_inicial = strtotime($_SESSION['autUser']['cnh_vencimento']);
$time_final = strtotime($hoje);
$diferenca = $time_inicial - $time_final;
$dias = (int) floor($diferenca / (60 * 60 * 24));
?>

<!--verifica se solicitante é condutor-->
<?php
$os_ok = 'n';
$siape_os = $_SESSION['autUser']['siape'];
$servidor_nome = $_SESSION['autUser']['nome'];

$readCondutor = read('servidores', "WHERE (ativo = 1 AND os_motorista != '$os_ok' AND cnh_vencimento >= '$hoje') ORDER BY nome ASC");

//Verifica se solicitante tem OS
$readOS = read('servidores', "WHERE (siape = '$siape_os' AND os_motorista != '$os_ok')");
$resultado_condutor = count($readOS);
?>

<?php
//Selecionar o passageiro
$readPassageiro = read('servidores', "WHERE ativo = 1 ORDER BY nome ASC");
?><!--Selecionar os passageiros-->

<?php
//dados de destino
$dia_vai = $_GET['dia_vai'];
$mes_vai = $_GET['mes_vai'];
$ano_vai = $_GET['ano_vai'];
$destino_vai = utf8_decode($_GET['destino_vai']);
$destino_2 = utf8_decode($_GET['destino_2']);
$destino_3 = utf8_decode($_GET['destino_3']);


if ($destino_vai == "" or $dia_vai == "" or $mes_vai == "" or $ano_vai == "") {
    header("Refresh:1; url=pre_solicitar_viatura.php");
} else {
    ?>	

    <?php
    if ($solicitacoes_abertas >= 1) {
        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;NÃO é possível efetuar uma nova solicitação. Você possui solicitações à serem ENCERRADAS.</h4>";
    } else {
        ?>

        <!--Conteudo das páginas -->
        <h1 class="titulo-secao-medio"><i class="fa fa-flag"></i> Nova solicitação de viatura</h1>  

        <?php
        //Verifica a data, para não ter duas solicitações do mesmo servidor para para o mesmo dia e local
        $verifica_data_uso = $ano_vai . '-' . $mes_vai . '-' . $dia_vai;
        $verifica_destino = $destino_vai;
        $read_Solicitacoes = read('vt_solicitacoes', "WHERE (data_uso = '$verifica_data_uso' AND servidor = '$servidor_nome' AND roteiro = '$verifica_destino')");
        $reaultado_conta = count($read_Solicitacoes);

        if ($resultado_conta > 0) {
            $echodata = date('d/m/Y', strtotime($verifica_data_uso));
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Você já tem uma solicitação de Viatura Oficial para {$verifica_destino} em {$echodata}.</h4>";
        } else {

            //Receber dados do Formulário
            if (isset($_POST['solicitar'])) {

                //Dados do solicitante
                $f['servidor']  = $_SESSION['autUser']['nome'];
                $f['email']     = $_SESSION['autUser']['email'];
                $f['siape']     = $_SESSION['autUser']['siape'];
                $f['situacao']  = 'Aguardando...';
                $f['veiculo']   = 'n/d';

                /* //CASO O ADMIN POSSA SELECIONAR O VEÍCULO (linha 531 e adiante)
                  if ($nivel == 1 || $nivel == 2) {
                  $situacao = 'Autorizada';
                  $veiculo = $_POST['veiculo'];
                  } elseif ($nivel > 2 || ($nivel <= 9 && $nivel > 2)) {
                  $situacao = 'Aguardando...';
                  $veiculo = 'n/d';
                  } */

                //Dados do motorista
                $motorista = strip_tags(trim($_POST['motorista']));
                if ($f['servidor'] == $motorista) {
                    $f['motorista']         = $_SESSION['autUser']['nome'];
                    $f['siape_motorista']   = $_SESSION['autUser']['siape'];
                    $f['email_motorista']   = $_SESSION['autUser']['email'];
                    $f['cnh_motorista']     = $_SESSION['autUser']['cnh'];
                    $f['cnh_categoria']     = $_SESSION['autUser']['cnh_categoria'];
                    $f['cnh_vencimento']    = $_SESSION['autUser']['cnh_vencimento'];
                    $f['os_motorista']      = $_SESSION['autUser']['os_motorista'];
                    $f['os_motorista_data'] = $_SESSION['autUser']['os_motorista_data'];
                } else {
                    $separa_dados           = explode(",", $motorista);
                    $f['motorista']         = $separa_dados[0];
                    $f['siape_motorista']   = $separa_dados[6];
                    $f['email_motorista']   = $separa_dados[7];
                    $f['cnh_motorista']     = $separa_dados[1];
                    $f['cnh_categoria']     = $separa_dados[3];
                    $f['cnh_vencimento']    = $separa_dados[2];
                    $f['os_motorista']      = $separa_dados[4];
                    $f['os_motorista_data'] = $separa_dados[5];
                }

                //Para que a viatura será usada
                $f['finalidade']        = strip_tags(trim($_POST['finalidade']));
                $f['desc_finalidade']   = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
                /* $f['desc_finalidade']   = strip_tags(trim($_POST['descricao']));
                ** - Alterado para o filter_input, pois estava dando erro ao cadastrar com apóstrofo na base de dados
                */
                $f['comprovante']       = $_FILES["comprovante_guia"]["tmp_name"];

                //Destino
                $f['roteiro']       = $destino_vai;
                $f['roteiro_2']     = $destino_2;
                $f['roteiro_3']     = $destino_3;
                $f['data_uso']      = $ano_vai . '-' . $mes_vai . '-' . $dia_vai;
                $f['hora_saida']    = $_POST['hora_saida'];
                $f['minuto_saida']  = $_POST['minuto_saida'];
                $f['horario_uso']   = "{$_POST['hora_saida']}:{$_POST['minuto_saida']}";
                //Variável para verificar ocupação das viaturas
                $f['datetime_saida'] = $f['data_uso'] . ' ' . $f['horario_uso'] . ':00';

                //Previsões de retorno
                $calendario                 = strip_tags(trim($_POST['calendario']));
                $prev_data_separa           = explode("/", $calendario);
                $prev_data_dia              = $prev_data_separa[0];
                $prev_data_mes              = $prev_data_separa[1];
                $prev_data_ano              = $prev_data_separa[2];
                $f['prev_retorno_data']     = $prev_data_ano . '-' . $prev_data_mes . '-' . $prev_data_dia;
                $f['hora_retorno']          = $_POST['hora_retorno'];
                $f['minuto_retorno']        = $_POST['minuto_retorno'];
                $f['prev_retorno_hora']     = "{$_POST['hora_retorno']}:{$_POST['minuto_retorno']}";
                //Variável para verificar ocupação das viaturas
                $f['datetime_retorno']      = $f['prev_retorno_data'] . ' ' . $f['prev_retorno_hora'] . ':00';

                //Informações dos passageiros (caronas)

                $passageiros = $_POST['passageiros'];

                if ($passageiros == "Nao") {
                    $f['passageiros'] = "";
                } else {
                    $passageiro_um = strip_tags(trim($_POST['passageiro_um']));
                    $passageiro_dois = strip_tags(trim($_POST['passageiro_dois']));
                    $passageiro_tres = strip_tags(trim($_POST['passageiro_tres']));
                    $passageiro_quatro = strip_tags(trim($_POST['passageiro_quatro']));
                    $passageiro_cinco = strip_tags(trim($_POST['passageiro_cinco']));
                    $passageiro_seis = strip_tags(trim($_POST['passageiro_seis']));

                    if ($passageiro_seis != "") {
                        $f['passageiros'] = $passageiro_um . ',' . $passageiro_dois . ',' . $passageiro_tres . ',' . $passageiro_quatro . ',' . $passageiro_cinco . ',' . $passageiro_seis;
                    } else if ($passageiro_seis == "" and $passageiro_cinco != "") {
                        $f['passageiros'] = $passageiro_um . ',' . $passageiro_dois . ',' . $passageiro_tres . ',' . $passageiro_quatro . ',' . $passageiro_cinco;
                    } else if ($passageiro_cinco == "" and $passageiro_quatro != "") {
                        $f['passageiros'] = $passageiro_um . ',' . $passageiro_dois . ',' . $passageiro_tres . ',' . $passageiro_quatro;
                    } else if ($passageiro_quatro == "" and $passageiro_tres != "") {
                        $f['passageiros'] = $passageiro_um . ',' . $passageiro_dois . ',' . $passageiro_tres;
                    } else if ($passageiro_tres == "" and $passageiro_dois != "") {
                        $f['passageiros'] = $passageiro_um . ',' . $passageiro_dois;
                    } else if ($passageiro_dois == "" and $passageiro_um != "") {
                        $f['passageiros'] = $passageiro_um;
                    } else {
                        $f['passageiros'] = "";
                    }
                }

                //Dados Data de Solicitação e Controle do Banco de Dados
                $f['criadoEm']      = date('Y-m-d H:i:s');
                $f['modificadoEm']  = date('Y-m-d H:i:s');
                $data_chave = date('Ymd');
                $f['chave_solicitacao'] = $data_chave . '-' . $f['email'] . '-' . $_SESSION['autUser']['siape'];

                //Verifica a chave, para não ter duas solicitações do mesmo servidor para para o mesmo dia
                $readBuscaChave = read('vt_solicitacoes', "WHERE siape = '$siape' AND data_uso = '$data_roteiro'");
                $countReadBuscaChave = count($readBuscaChave);

                if ($countReadBuscaChave >= 5) {
                    $echodiavai = $_GET['dia_vai'];
                    $echomesvai = $_GET['mes_vai'];
                    $echoanovai = $_GET['ano_vai'];
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Você já tem 5 ou MAIS solicitações de Viatura Oficial para o dia {$echodiavai}/{$echomesvai}/{$echoanovai}. Contate o Administrador.</h4>";
                } else {

                    //Cadastra dados na base de dados
                    if ($f['servidor'] and $f['siape'] and $f['finalidade'] and $f['desc_finalidade'] and $f['roteiro'] and $f['horario_uso'] and $f['motorista'] and $f['prev_retorno_data'] != "") {
                        
                        if($f['comprovante'] != ""):
                            //Validações do envio do arquivo do comprovante
                            $up['pasta'] = '../admin/documentos/comprovantes_guia/';
                            $up['tamanho'] = 1024 * 1024 * 3; //3MB
                            $up['renomeia'] = true;
                            $veri_ext = array('pdf', 'rtf'); //Extensão do arquivo
                            //Array com os tipos de erro de uploads de arquivos no php
                            $up['erros'][0] = 'Não houve erro';
                            $up['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                            $up['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                            $up['erros'][3] = 'O upload do arquivo foi feito parcialmente';

                            //Verifica a extensão do arquivo
                            $extensao = strtolower(end(explode('.', $_FILES['comprovante_guia']['name'])));
                            $comprovante_guia = $_FILES["comprovante_guia"]["tmp_name"];
                            if($f['comprovante'] != ''):
                                if (!in_array($extensao, $veri_ext)):
                                    echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp; Somente arquivos PDF ou RTF</h4>';
                                elseif ($up['tamanho'] <= $_FILES['comprovante_guia']['size']):
                                    echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp; Tamanho máximo de arquivo: 3MB</h4>';
                                else:
                                    // Primeiro verifica se deve trocar o nome do arquivo
                                    if ($up['renomeia'] == true):
                                        $nome_final = $f['siape'] .'_compr-' . time() . '.' . $extensao;
                                        $f['comprovante'] = $nome_final;
                                    endif;
                                    
                                    if (move_uploaded_file($_FILES['comprovante_guia']['tmp_name'], $up['pasta'].$nome_final)):
                                    
                                    else:
                                        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao fazer o Upload do Arquivo -> {$up['pasta']}{$nome_final}</h4>";
                                    endif;

                                    unset ($f['hora_saida']);
                                    unset ($f['minuto_saida']);
                                    unset ($f['hora_retorno']);
                                    unset ($f['minuto_retorno']);

                                    $sql_cadastra = create('vt_solicitacoes', $f);
                                    
                                    if($sql_cadastra):
                                        //mensagem se OK
                                        echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Solicitação enviada com sucesso!</h6>";

                                        //Envia E-mail para o Admin
                                        $msg = '<p style="font:bold 14px Tahoma, Geneva, sans-serif; color:#093;">Olá Admin,</p>
                                                <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Informamos que houve uma nova solicitação de viatura oficial através do <strong>sistema de viaturas oficiais</strong> do IFRS - Campus Feliz.<br /><br />
                                                Abaixo seguem os dados do solicitante e dos demais passageiros, se houverem.</p>
                                                <hr />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Solicitante:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $_SESSION['autUser']['nome'] . '</span><br />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">E-mail:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $_SESSION['autUser']['email'] . '</span><br />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Siape:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $_SESSION['autUser']['siape'] . '</span><br /><br />

                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Motorista:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $f['motorista'] . '</span><br />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">OS Condutor:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $f['os_motorista'] . ' de ' . date('d/m/Y', strtotime($f['os_motorista_data'])) . '</span><br />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Vencimento CNH:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($f['cnh_vencimento'])) . '</span><br /><br />

                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Destino:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $f['roteiro'] ."&nbsp;&nbsp;". $f['roteiro_2'] . "&nbsp;&nbsp;" . $f['roteiro_3'] . '</span><br />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Data:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($f['data_uso'])) . " às " . $f['horario_uso'] . '</span><br />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Finalidade:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $f['finalidade'] . ' / ' . $f['desc_finalidade'] . '</span><br />
                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Previsão de retorno:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . date('d/m/Y', strtotime($f['prev_retorno_data'])) . " às " . $f['prev_retorno_hora'] . '</span><br /><br />

                                                <span style="font:14px Tahoma, Geneva, sans-serif; color:#093;">Caronas:</span> <span style="font:14px Tahoma, Geneva, sans-serif; color:#333;">' . $F['passageiros'] . '<br /><br />
                                                <span style="font:13px Tahoma, Geneva, sans-serif; color:#930;">Solicitada em <strong>' . date('d/m/Y à\s H:i:s', strtotime($f['criadoEm'])) . '</strong></span><br /><br /><br />
                                                <hr />
                                                <p style="font:14px Tahoma, Geneva, sans-serif; color:#666;">Para Autorizar ou Negar esta solicitação, acesse o <strong>Painel do Administrador</strong>.<br />
                                                </p>
                                                <img scr="http://viaturas.feliz.ifrs.edu.br/_assets/img/ifrsfeliz.png" title="IFRS - Campus Feliz" alt="IFRS - Campus Feliz" />';

                                        sendMail('Nova solicitação de viatura', $msg, MAILUSER, SITENAME, MAILADMIN, $_SESSION['autUser']['nome']);

                                        header('Refresh: 3;url=minhas_solicitacoes.php?solit=aberto');
                                    else:
                                        echo 'Impossível realizar esta solicitação no momento!';
                                    endif;                                    
                                endif;
                            endif;
                        else:
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; É necessário anexar o comprovante!</h4>";
                        endif;
                    } else {
                        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Por favor, preencha todos os campos obrigatórios!</h4>";
                    }
                }
            }
            ?>

            <form name="solicita_viatura" id="cad_servidor" method="post" action="" enctype="multipart/form-data">

                <fieldset class="user_solicita_viatura" style="margin-left: 25px;">
                    <legend class="titulopg_alt"><i class="fa fa-file-text"></i> Preencha os dados solicitados</legend>
                    <?php
                    if ($resultado_condutor == 0) {
                        ?>
                        <!--SOLICITANTE se solicitante não é condutor-->
                        <div class="user_dados_fixos">                
                            <h1>Solicitante:</h1>
                            <h2><?php echo $_SESSION['autUser']['nome']; ?> (<?php echo $_SESSION['autUser']['siape']; ?>)</h2><br />
                            <input type="hidden" id="solicitante" name="solicitante" value="<?php echo $_SESSION['autUser']['nome']; ?>" />
                            <input type="hidden" id="email" name="email" value="<?php echo $_SESSION['autUser']['email']; ?>" />
                            <input type="hidden" id="siape" name="siape" value="<?php echo $_SESSION['autUser']['siape']; ?>" />
                        </div>
                        <div class="sem_os">
                            <h5><i class="fa fa-exclamation-triangle" style="color: #F00;"></i> Você não tem OS para conduzir veículos oficiais. Precisa selcionar um condutor</h5>
                        </div>

                        <?php
                        /* SOLICITANTE se solicitante É condutor */
                    } else {
                        if ($dias < -30) {
                            ?>
                            <div class="user_dados_fixos">                
                                <h1>Solicitante:</h1>
                                <h2><?php echo $_SESSION['autUser']['nome']; ?> (<?php echo $_SESSION['autUser']['siape']; ?>)</h2><br />
                                <input type="hidden" id="solicitante" name="solicitante" value="<?php echo $_SESSION['autUser']['nome']; ?>" />
                                <input type="hidden" id="email" name="email" value="<?php echo $_SESSION['autUser']['email']; ?>" />
                                <input type="hidden" id="siape" name="siape" value="<?php echo $_SESSION['autUser']['siape']; ?>" />
                            </div>
                            <div class="sem_os">
                                <h5><i class="fa fa-exclamation-triangle" style="color: #F00;"></i> Sua CNH está vencida à <?php echo '<strong>' . -$dias . '</strong> dias'; ?>. Você precisa selecionar outro condutor.</h5>
                            </div>

                            <?php
                        } else {
                            ?>
                            <div class="user_dados_fixos">                
                                <h1>Solicitante:</h1>
                                <h2><?php echo $_SESSION['autUser']['nome']; ?> (<?php echo $_SESSION['autUser']['siape']; ?>)</h2><br />
                                <input type="hidden" id="solicitante" name="solicitante" value="<?php echo $_SESSION['autUser']['nome']; ?>" />
                                <input type="hidden" id="email" name="email" value="<?php echo $_SESSION['autUser']['email']; ?>" />
                                <input type="hidden" id="siape" name="siape" value="<?php echo $_SESSION['autUser']['siape']; ?>" />
                            </div>

                            <div class="com_os">
                                <h5>
                                    <strong>CNH:</strong> <?php echo $_SESSION['autUser']['cnh']; ?> <i class="fa fa-caret-right"></i>
                                    <strong>Validade CNH:</strong> <?php echo date('d/m/Y', strtotime($_SESSION['autUser']['cnh_vencimento'])); ?> <i class="fa fa-caret-right"></i>
                                    <strong>Categoria CNH:</strong> <?php echo $_SESSION['autUser']['cnh_categoria']; ?> <i class="fa fa-caret-right"></i>
                                    <strong>OS de Condutor:</strong> <?php echo $_SESSION['autUser']['os_motorista'] . ' de ' . date('d/m/Y', strtotime($_SESSION['autUser']['os_motorista_data'])); ?>
                                </h5>
                            </div>

                            <?php
                        }
                    }
                    ?>     

                    <?php
                    if ($resultado_condutor == 0) {
                        ?>
                        <!--CONDUTOR se solicitante não é condutor-->
                        <label>
                            <span>Condutor*:</span><br />
                            <select name="motorista" id="motorista" onchange="meAddCarona()">
                                <option value="" selected>Selecione</option>
                                <option value="<?php echo $f['motorista']; ?>" <?php if(isset($f['motorista'])){ echo "selected"; } ?>><?php echo $f['motorista']; ?></option>
                                <?php
                                foreach ($readCondutor as $select_condutor) {
                                    $condutor_selecionado = $select_condutor['nome'] . ',' . $select_condutor['cnh'] . ',' . $select_condutor['cnh_vencimento'] . ',' . $select_condutor['cnh_categoria'] . ',' . $select_condutor['os_motorista'] . ',' . $select_condutor['os_motorista_data'] . ',' . $select_condutor['siape'] . ',' . $select_condutor['email'];
                                    $condutor_nome = $select_condutor['nome'];
                                    ?>
                                    <option value="<?php echo $condutor_selecionado; ?>"><?php echo $condutor_nome; ?></option>
                                <?php } ?>
                            </select>
                        </label> 
                        <?php
                        /* CONDUTOR se solicitante É condutor */
                    } else {
                        if ($dias < -30) {
                            ?>
                            <label>
                                <span>Condutor*:</span><br />
                                <select name="motorista" id="motorista" onchange="meAddCarona()">
                                    <option value="" selected>Selecione</option>
                                    <option value="<?php echo $f['motorista']; ?>" <?php if(isset($f['motorista'])){ echo "selected"; } ?>><?php echo $f['motorista']; ?></option>
                                    <?php
                                    foreach ($readCondutor as $select_condutor) {
                                        $condutor_selecionado = $select_condutor['nome'] . ',' . $select_condutor['cnh'] . ',' . $select_condutor['cnh_vencimento'] . ',' . $select_condutor['cnh_categoria'] . ',' . $select_condutor['os_motorista'] . ',' . $select_condutor['os_motorista_data'] . ',' . $select_condutor['siape'] . ',' . $select_condutor['email'];
                                        $condutor_nome = $select_condutor['nome'];
                                        ?>
                                        <option value="<?php echo $condutor_selecionado; ?>"><?php echo $condutor_nome; ?></option>
                                    <?php } ?>
                                </select>
                            </label>
                            <?php
                        } else {
                            ?>
                            <!--box se solicitante não é condutor-->       
                            <label>
                                <span>Alterar Condutor:</span><br />
                                <select name="motorista" id="motorista" onchange="meAddCarona()">
                                    <option value="<?php echo $_SESSION['autUser']['nome']; ?>" selected><?php echo $_SESSION['autUser']['nome']; ?></option>
                                    <option value="<?php echo $f['motorista']; ?>" <?php if(isset($f['motorista'])){ echo "selected"; } ?>><?php echo $f['motorista']; ?></option>
                                    <?php
                                    foreach ($readCondutor as $select_condutor) {
                                        $condutor_selecionado = $select_condutor['nome'] . ',' . $select_condutor['cnh'] . ',' . $select_condutor['cnh_vencimento'] . ',' . $select_condutor['cnh_categoria'] . ',' . $select_condutor['os_motorista'] . ',' . $select_condutor['os_motorista_data'] . ',' . $select_condutor['siape'] . ',' . $select_condutor['email'];
                                        $condutor_nome = $select_condutor['nome'];
                                        ?>
                                        <option value="<?php echo $condutor_selecionado; ?>"><?php echo $condutor_nome; ?></option>
                                    <?php } ?>
                                </select>
                            </label>  
                            <?php
                        }
                    }
                    ?>

                    <!--box se solicitante é condutor-->
                    <label>
                        <span>Finalidade para uso da Viatura Oficial*:</span><br />
                        <select name="finalidade" id="finalidade" class="select_sexo">
                            <option value="" selected disabled>Selecione</option>
                            <option value="Convocação" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Convocação"){ echo "selected"; } ?>>Convocação</option>
                            <option value="Reunião" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Reunião"){ echo "selected"; } ?>>Reunião</option>
                            <option value="Eventos" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Eventos"){ echo "selected"; } ?>>Eventos</option>
                            <option value="Projetos de Pesquisa / Extensão" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Projetos de Pesquisa / Extensão"){ echo "selected"; } ?>>Projeto de Pesquisa / Extensão</option>
                            <option value="Divulgação" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Divulgação"){ echo "selected"; } ?>>Divulgação</option>
                            <option value="Visita Técnica" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Visita Técnica"){ echo "selected"; } ?>>Visita Técnica</option>
                            <option value="Banco e Correios" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Banco e Correios"){ echo "selected"; } ?>>Ir ao Banco e/ou Correios</option>
                            <option value="Outros" <?php if(isset($f['finalidade']) & $f['finalidade'] == "Outros"){ echo "selected"; } ?>>Outros</option>
                        </select> 
                    </label>
                    <label>
                        <span>Descrição*:</span><br />
                        <textarea id="descricao" name="descricao" rows="8" cols="50" /><?php if(isset($f['desc_finalidade'])){ echo $f['desc_finalidade']; } ?></textarea>
                    </label>
                    <label>
                        <span>Roteiro:</span>
                        <div class="solicit_dados_fixos">
                            <?php
                            if ($destino_3 != '') {
                                echo '<h3><i class="fa fa-caret-right"></i>&nbsp;' . $destino_vai . ' &nbsp;&nbsp;<i class="fa fa-caret-right"></i>&nbsp; ' . $destino_2 . ' &nbsp;&nbsp;<i class="fa fa-caret-right"></i>&nbsp; ' . $destino_3 . '</h3>';
                            } elseif ($destino_2 != '' && $destino_3 == '') {
                                echo '<h3><i class="fa fa-caret-right"></i>&nbsp;' . $destino_vai . ' &nbsp;&nbsp;<i class="fa fa-caret-right"></i>&nbsp; ' . $destino_2 . '</h3>';
                            } else {
                                echo '<h3><i class="fa fa-caret-right"></i>&nbsp;' . $destino_vai . '</h3>';
                            }
                            ?>
                        </div>
                    </label>

                    <label>
                        <span>Data para uso do veículo:</span>
                        <div class="solicit_dados_fixos">
                            <h3><?php echo $dia_vai . '/' . $mes_vai . '/' . $ano_vai; ?></h3>				
                        </div> 
                    </label>

                    <div class="solicitDadosRetorno">
                        <div class="sdr_left">
                            <label>
                                <span>Horario de Saída:</span><br />
                                <select name="hora_saida" id="hora_saida" class="horaPrevRetorno">
                                    <?php if(isset($f['hora_saida'])){ echo "<option value='{$f['hora_saida']}'>{$f['hora_saida']}</option>"; } ?>
                                    <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                   <option value='{$f['hora_saida']}'>{$f[ <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                </select>
                                
                                <select name="minuto_saida" id="minuto_saida" class="horaPrevRetorno">
                                    <?php if(isset($f['minuto_saida'])){ echo "<option value='{$f['minuto_saida']}'>{$f['minuto_saida']}</option>"; } ?>
                                    <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                    <option value="46">46</option>
                                    <option value="47">47</option>
                                    <option value="48">48</option>
                                    <option value="49">49</option>
                                    <option value="50">50</option>
                                    <option value="51">51</option>
                                    <option value="52">52</option>
                                    <option value="53">53</option>
                                    <option value="54">54</option>
                                    <option value="55">55</option>
                                    <option value="56">56</option>
                                    <option value="57">57</option>
                                    <option value="58">58</option>
                                    <option value="59">59</option>
                                </select>
                            </label>
                        </div>
                        <div class="sdr_center">
                            <label>
                                <span>Data de Retorno:</span><br />
                                <input type="text" readonly="true" name="calendario" id="calendario" class="infoDataRetorno"
                                       value="<?php
                                                if(isset($_POST['calendario'])){
                                                    echo $_POST['calendario'];
                                                }else{
                                                    echo $dia_vai . '/' . $mes_vai . '/' . $ano_vai;
                                                }
                                            ?>"
                            </label>
                        </div>
                        <div class="sdr_right">
                            <label>
                                <span>Horário de Retorno:</span><br />
                                <select name="hora_retorno" id="hora_retorno" class="horaPrevRetorno">
                                    <?php if(isset($f['hora_retorno'])){ echo "<option value='{$f['hora_retorno']}'>{$f['hora_retorno']}</option>"; } ?>
                                    <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                </select>
                                
                                <select name="minuto_retorno" id="minuto_retorno" class="horaPrevRetorno">
                                    <?php if(isset($f['minuto_retorno'])){ echo "<option value='{$f['minuto_retorno']}'>{$f['minuto_retorno']}</option>"; } ?>
                                    <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                    <option value="46">46</option>
                                    <option value="47">47</option>
                                    <option value="48">48</option>
                                    <option value="49">49</option>
                                    <option value="50">50</option>
                                    <option value="51">51</option>
                                    <option value="52">52</option>
                                    <option value="53">53</option>
                                    <option value="54">54</option>
                                    <option value="55">55</option>
                                    <option value="56">56</option>
                                    <option value="57">57</option>
                                    <option value="58">58</option>
                                    <option value="59">59</option>
                                </select>
                            </label> 
                        </div>
                       
                        <label style="margin: 35px 0;">
                            <span><i class="fa fa-file-pdf-o"></i> Comprovante de necessidade de uso de carro oficial:</span>
                            <div class="cadastra_doc">
                                <input type="file" name="comprovante_guia" id="comprovante_guia" />
                                <span class="info_desc">Formato PDF ou RTF</span>
                            </div>
                        </label>
                    </div>

                    <!--sistema de passageiros-->                   
                    <label>
                        <h4>Passageiros:</h4><br />
                        <h6>Para adicionar passageiros que não sejam servidores, utilize o menu <strong><em>Minhas Solicitações</em></strong>, após concluir sua solicitação </h6>
                        <select name="passageiros" id="passageiros" class="select_passageiros" onchange="optionCheck()">
                            <option value="Nao" <?php if(isset($passageiros) & $passageiros == "Nao"){ echo "selected"; } ?>>Não</option>
                            <option value="Sim" <?php if(isset($passageiros) & $passageiros != "Nao"){ echo "selected"; } ?>>Sim</option>
                        </select>
                    </label>

                    <div id="com_passageiros" <?php if($passageiro_um != ""){ echo "style= 'display: block; margin-top: 0; visibility: visible;'"; } ?>>
                        <label>
                            <span>Passageiro nº 1:</span><br />
                            <select name="passageiro_um" id="passageiro_um" onchange="valPass1()">
                                <option disabled <?php if($passageiro_um == ""){ echo "selected"; } ?>>Selecione</option>
                                <option value="<?php if($passageiro_um != ""){ echo $passageiro_um; } ?>" <?php if($passageiro_um != ""){ echo "selected"; } ?>> <?php if(isset($passageiro_um) & $passageiro_um != ""){ echo $passageiro_um; } ?> </option>
                                <?php
                                foreach ($readPassageiro as $select_passageiro_um) {
                                    $passageiro_um_selecionado = $select_passageiro_um['nome'];
                                    ?>
                                    <option value="<?php echo $passageiro_um_selecionado; ?>"><?php echo $passageiro_um_selecionado; ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-plus-circle fa-2x" style="font-size: 1.5em; color: #398431; cursor: pointer; float: left;  margin-top: 19px; margin-left: 10px;" title="Adicionar outro passageiro" id="img_add_um" onclick="optionPassDois()"></i>             
                        </label>
                    </div>

                    <div id="com_passageiros_dois" <?php if($passageiro_dois != ""){ echo "style= 'display: block; margin-top: 0; visibility: visible;'"; } ?>>
                        <label>
                            <span>Passageiro nº 2:</span><br />
                            <select name="passageiro_dois" id="passageiro_dois" onchange="valPass2()">
                                <option disabled <?php if($passageiro_dois == ""){ echo "selected"; } ?>>Selecione</option>
                                <option value="<?php if($passageiro_dois != ""){ echo $passageiro_dois; } ?>" <?php if($passageiro_dois != ""){ echo "selected"; } ?>> <?php if(isset($passageiro_dois) & $passageiro_dois != ""){ echo $passageiro_dois; } ?> </option>
                                <?php
                                foreach ($readPassageiro as $select_passageiro_um) {
                                    $passageiro_um_selecionado = $select_passageiro_um['nome'];
                                    ?>
                                    <option value="<?php echo $passageiro_um_selecionado; ?>"><?php echo $passageiro_um_selecionado; ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-plus-circle fa-2x" style="font-size: 1.5em; color: #398431; cursor: pointer; float: left;  margin-top: 19px; margin-left: 10px;" title="Adicionar outro passageiro" id="img_add_dois" onclick="optionPassTres()"></i>             
                            <i class="fa fa-minus-circle fa-2x" style="font-size: 1.5em; color: #F00; cursor: pointer; float: left; margin-top: 19px; margin-left: 10px;" title="Remover passageiro" id="img_rem_dois" onclick="optionDelPassDois()"></i>             
                        </label>
                    </div>

                    <div id="com_passageiros_tres" <?php if($passageiro_tres != ""){ echo "style= 'display: block; margin-top: 0; visibility: visible;'"; } ?>>
                        <label>
                            <span>Passageiro nº 3:</span><br />
                            <select name="passageiro_tres" id="passageiro_tres" onchange="valPass3()">
                                <option disabled <?php if($passageiro_tres == ""){ echo "selected"; } ?>>Selecione</option>
                                <option value="<?php if($passageiro_tres != ""){ echo $passageiro_tres; } ?>" <?php if($passageiro_tres != ""){ echo "selected"; } ?>> <?php if(isset($passageiro_tres) & $passageiro_tres != ""){ echo $passageiro_tres; } ?> </option>
                                <?php
                                foreach ($readPassageiro as $select_passageiro_um) {
                                    $passageiro_um_selecionado = $select_passageiro_um['nome'];
                                    ?>
                                    <option value="<?php echo $passageiro_um_selecionado; ?>"><?php echo $passageiro_um_selecionado; ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-plus-circle fa-2x" style="font-size: 1.5em; color: #398431; cursor: pointer; float: left;  margin-top: 19px; margin-left: 10px;" title="Adicionar outro passageiro" id="img_add_tres" onclick="optionPassQuatro()"></i>             
                            <i class="fa fa-minus-circle fa-2x" style="font-size: 1.5em; color: #F00; cursor: pointer; float: left; margin-top: 19px; margin-left: 10px;" title="Remover passageiro" id="img_rem_tres" onclick="optionDelPassTres()"></i>             
                        </label>
                    </div>

                    <div id="com_passageiros_quatro" <?php if($passageiro_quatro != ""){ echo "style= 'display: block; margin-top: 0; visibility: visible;'"; } ?>>
                        <label>
                            <span>Passageiro nº 4:</span><br />
                            <select name="passageiro_quatro" id="passageiro_quatro" onchange="valPass4()">
                                <option disabled <?php if($passageiro_quatro == ""){ echo "selected"; } ?>>Selecione</option>
                                <option value="<?php if($passageiro_quatro != ""){ echo $passageiro_quatro; } ?>" <?php if($passageiro_quatro != ""){ echo "selected"; } ?>> <?php if(isset($passageiro_quatro) & $passageiro_quatro != ""){ echo $passageiro_quatro; } ?> </option>
                                <?php
                                foreach ($readPassageiro as $select_passageiro_um) {
                                    $passageiro_um_selecionado = $select_passageiro_um['nome'];
                                    ?>
                                    <option value="<?php echo $passageiro_um_selecionado; ?>"><?php echo $passageiro_um_selecionado; ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-plus-circle fa-2x" style="font-size: 1.5em; color: #398431; cursor: pointer; float: left;  margin-top: 19px; margin-left: 10px;" title="Adicionar outro passageiro" id="img_add_quatro" onclick="optionPassCinco()"></i>             
                            <i class="fa fa-minus-circle fa-2x" style="font-size: 1.5em; color: #F00; cursor: pointer; float: left; margin-top: 19px; margin-left: 10px;" title="Remover passageiro" id="img_rem_quatro" onclick="optionDelPassQuatro()"></i>
                        </label>
                    </div>

                    <div id="com_passageiros_cinco" <?php if($passageiro_cinco != ""){ echo "style= 'display: block; margin-top: 0; visibility: visible;'"; } ?>>
                        <label>
                            <span>Passageiro nº 5:</span><br />
                            <select name="passageiro_cinco" id="passageiro_cinco" onchange="valPass5()">
                                <option disabled <?php if($passageiro_cinco == ""){ echo "selected"; } ?>>Selecione</option>
                                <option value="<?php if($passageiro_cinco != ""){ echo $passageiro_cinco; } ?>" <?php if($passageiro_cinco != ""){ echo "selected"; } ?>> <?php if(isset($passageiro_cinco) & $passageiro_cinco != ""){ echo $passageiro_cinco; } ?> </option>
                                <?php
                                foreach ($readPassageiro as $select_passageiro_um) {
                                    $passageiro_um_selecionado = $select_passageiro_um['nome'];
                                    ?>
                                    <option value="<?php echo $passageiro_um_selecionado; ?>"><?php echo $passageiro_um_selecionado; ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-plus-circle fa-2x" style="font-size: 1.5em; color: #398431; cursor: pointer; float: left;  margin-top: 19px; margin-left: 10px;" title="Adicionar outro passageiro" id="img_add_cinco" onclick="optionPassSeis()"></i>             
                            <i class="fa fa-minus-circle fa-2x" style="font-size: 1.5em; color: #F00; cursor: pointer; float: left; margin-top: 19px; margin-left: 10px;" title="Remover passageiro" id="img_rem_cinco" onclick="optionDelPassCinco()"></i>
                        </label>
                    </div>

                    <div id="com_passageiros_seis" <?php if($passageiro_seis != ""){ echo "style= 'display: block; margin-top: 0; visibility: visible;'"; } ?>>
                        <label>
                            <span>Passageiro nº 6:</span><br />
                            <select name="passageiro_seis" id="passageiro_seis" onchange="valPass6()">
                                <option disabled <?php if($passageiro_seis == ""){ echo "selected"; } ?>>Selecione</option>
                                <option value="<?php if($passageiro_seis != ""){ echo $passageiro_seis; } ?>" <?php if($passageiro_seis != ""){ echo "selected"; } ?>> <?php if(isset($passageiro_seis) & $passageiro_seis != ""){ echo $passageiro_seis; } ?> </option>
                                <?php
                                foreach ($readPassageiro as $select_passageiro_um) {
                                    $passageiro_um_selecionado = $select_passageiro_um['nome'];
                                    ?>
                                    <option value="<?php echo $passageiro_um_selecionado; ?>"><?php echo $passageiro_um_selecionado; ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-minus-circle fa-2x" style="font-size: 1.5em; color: #F00; cursor: pointer; float: left; margin-top: 19px; margin-left: 10px;" title="Remover passageiro" id="img_rem_seis" onclick="optionDelPassSeis()"></i>
                        </label>
                        <span class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Para adicionar mais de 6 passageiros, utilize o link <i>Minhas Solicitações</i></span>
                    </div>

                    <script type="text/javascript">
                        function verifBtn() {
                            document.getElementById("form_load").style.visibility = "visible";
                            document.getElementById("solicitar").style.visibility = "hidden";
                        }

                        function meAddCarona() {
                            var solicitante = document.getElementById("solicitante").value;
                            var motorista = document.getElementById("motorista").value;
                            if (solicitante != motorista) {
                                document.getElementById("passageiros").value = "Sim";
                                document.getElementById("com_passageiros").style.visibility = "visible";
                                document.getElementById("com_passageiros").style.marginTop = "0px";
                                document.getElementById("img_add_um").style.visibility = "visible";
                                document.getElementById("com_passageiros_dois").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_tres").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_quatro").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_cinco").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_seis").style.marginTop = "-100px";
                                document.getElementById("passageiro_um").value = solicitante;
                            }
                        }

                        function optionCheck() {

                            var solicitante = document.getElementById("solicitante").value;
                            var motorista = document.getElementById("motorista").value;
                            var option = document.getElementById("passageiros").value;

                            if (option == "Sim") {
                                document.getElementById("com_passageiros").style.visibility = "visible";
                                document.getElementById("com_passageiros").style.marginTop = "0px";
                                document.getElementById("img_add_um").style.visibility = "visible";
                                document.getElementById("com_passageiros_dois").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_tres").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_quatro").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_cinco").style.marginTop = "-100px";
                                document.getElementById("com_passageiros_seis").style.marginTop = "-100px";
                            }
                            if (option == "Nao") {
                                document.getElementById("com_passageiros").style.visibility = "hidden";
                                document.getElementById("com_passageiros").style.marginTop = "-400px";
                                document.getElementById("img_add_um").style.visibility = "hidden";
                                document.getElementById("passageiro_um").value = "";
                                document.getElementById("com_passageiros_dois").style.visibility = "hidden";
                                document.getElementById("com_passageiros_dois").style.marginTop = "-400px";
                                document.getElementById("img_add_dois").style.visibility = "hidden";
                                document.getElementById("img_rem_dois").style.visibility = "hidden";
                                document.getElementById("passageiro_dois").value = "";
                                document.getElementById("com_passageiros_tres").style.visibility = "hidden";
                                document.getElementById("com_passageiros_tres").style.marginTop = "-400px";
                                document.getElementById("img_add_tres").style.visibility = "hidden";
                                document.getElementById("img_rem_tres").style.visibility = "hidden";
                                document.getElementById("passageiro_tres").value = "";
                                document.getElementById("com_passageiros_quatro").style.visibility = "hidden";
                                document.getElementById("com_passageiros_quatro").style.marginTop = "-400px";
                                document.getElementById("img_add_quatro").style.visibility = "hidden";
                                document.getElementById("img_rem_quatro").style.visibility = "hidden";
                                document.getElementById("passageiro_quetro").value = "";
                                document.getElementById("com_passageiros_cinco").style.visibility = "hidden";
                                document.getElementById("com_passageiros_cinco").style.marginTop = "-400px";
                                document.getElementById("img_add_cinco").style.visibility = "hidden";
                                document.getElementById("img_rem_cinco").style.visibility = "hidden";
                                document.getElementById("passageiro_cinco").value = "";
                                document.getElementById("com_passageiros_seis").style.visibility = "hidden";
                                document.getElementById("com_passageiros_seis").style.marginTop = "-400px";
                                document.getElementById("img_add_seis").style.visibility = "hidden";
                                document.getElementById("img_rem_seis").style.visibility = "hidden";
                                document.getElementById("passageiro_seis").value = "";
                            }
                        }

                        function optionPassDois() {
                            var passUm = document.getElementById("passageiro_um").value;
                            if (passUm != '') {
                                document.getElementById("com_passageiros_dois").style.visibility = "visible";
                                document.getElementById("com_passageiros_dois").style.marginTop = "10px";
                                document.getElementById("img_rem_dois").style.visibility = "visible";
                                document.getElementById("img_add_dois").style.visibility = "visible";
                                document.getElementById("img_add_um").style.visibility = "hidden";
                            } else {
                                alert("Informe o Primeiro Passageiro");
                            }
                        }
                        function optionPassTres() {
                            var passDois = document.getElementById("passageiro_dois").value;
                            if (passDois != '') {
                                document.getElementById("com_passageiros_tres").style.visibility = "visible";
                                document.getElementById("com_passageiros_tres").style.marginTop = "10px";
                                document.getElementById("img_rem_dois").style.visibility = "hidden";
                                document.getElementById("img_rem_tres").style.visibility = "visible";
                                document.getElementById("img_add_tres").style.visibility = "visible";
                                document.getElementById("img_add_dois").style.visibility = "hidden";
                            } else {
                                alert("Informe o Segundo Passageiro");
                            }
                        }
                        function optionPassQuatro() {
                            var passTres = document.getElementById("passageiro_tres").value;
                            if (passTres != '') {
                                document.getElementById("com_passageiros_quatro").style.visibility = "visible";
                                document.getElementById("com_passageiros_quatro").style.marginTop = "10px";
                                document.getElementById("img_rem_tres").style.visibility = "hidden";
                                document.getElementById("img_rem_quatro").style.visibility = "visible";
                                document.getElementById("img_add_quatro").style.visibility = "visible";
                                document.getElementById("img_add_tres").style.visibility = "hidden";
                            } else {
                                alert("Informe o Terceiro Passageiro");
                            }
                        }
                        function optionPassCinco() {
                            var passQuatro = document.getElementById("passageiro_quatro").value;
                            if (passQuatro != '') {
                                document.getElementById("com_passageiros_cinco").style.visibility = "visible";
                                document.getElementById("com_passageiros_cinco").style.marginTop = "10px";
                                document.getElementById("img_rem_quatro").style.visibility = "hidden";
                                document.getElementById("img_rem_cinco").style.visibility = "visible";
                                document.getElementById("img_add_cinco").style.visibility = "visible";
                                document.getElementById("img_add_quatro").style.visibility = "hidden";
                            } else {
                                alert("Informe o Quarto Passageiro");
                            }
                        }
                        function optionPassSeis() {
                            var passCinco = document.getElementById("passageiro_cinco").value;
                            if (passCinco != '') {
                                document.getElementById("com_passageiros_seis").style.visibility = "visible";
                                document.getElementById("com_passageiros_seis").style.marginTop = "10px";
                                document.getElementById("img_rem_cinco").style.visibility = "hidden";
                                document.getElementById("img_add_cinco").style.visibility = "hidden";
                                document.getElementById("img_rem_seis").style.visibility = "visible";
                            } else {
                                alert("Informe o Quinto Passageiro");
                            }
                        }
                        function optionDelPassSeis() {
                            document.getElementById("com_passageiros_seis").style.visibility = "hidden";
                            document.getElementById("com_passageiros_seis").style.marginTop = "-100px";
                            document.getElementById("img_rem_cinco").style.visibility = "visible";
                            document.getElementById("img_add_cinco").style.visibility = "visible";
                            document.getElementById("img_rem_seis").style.visibility = "hidden";
                            document.getElementById("passageiro_seis").value = "";
                        }
                        function optionDelPassCinco() {
                            document.getElementById("com_passageiros_cinco").style.visibility = "hidden";
                            document.getElementById("com_passageiros_cinco").style.marginTop = "-100px";
                            document.getElementById("img_rem_quatro").style.visibility = "visible";
                            document.getElementById("img_add_quatro").style.visibility = "visible";
                            document.getElementById("img_rem_cinco").style.visibility = "hidden";
                            document.getElementById("img_add_cinco").style.visibility = "hidden";
                            document.getElementById("passageiro_cinco").value = "";
                        }
                        function optionDelPassQuatro() {
                            document.getElementById("com_passageiros_quatro").style.visibility = "hidden";
                            document.getElementById("com_passageiros_quatro").style.marginTop = "-100px";
                            document.getElementById("img_rem_tres").style.visibility = "visible";
                            document.getElementById("img_add_tres").style.visibility = "visible";
                            document.getElementById("img_rem_quatro").style.visibility = "hidden";
                            document.getElementById("img_add_quatro").style.visibility = "hidden";
                            document.getElementById("passageiro_quatro").value = "";
                        }
                        function optionDelPassTres() {
                            document.getElementById("com_passageiros_tres").style.visibility = "hidden";
                            document.getElementById("com_passageiros_tres").style.marginTop = "-100px";
                            document.getElementById("img_rem_dois").style.visibility = "visible";
                            document.getElementById("img_add_dois").style.visibility = "visible";
                            document.getElementById("img_rem_tres").style.visibility = "hidden";
                            document.getElementById("img_add_tres").style.visibility = "hidden";
                            document.getElementById("passageiro_tres").value = "";
                        }
                        function optionDelPassDois() {
                            document.getElementById("com_passageiros_dois").style.visibility = "hidden";
                            document.getElementById("com_passageiros_dois").style.marginTop = "-100px";
                            document.getElementById("img_add_um").style.visibility = "visible";
                            document.getElementById("img_rem_dois").style.visibility = "hidden";
                            document.getElementById("img_add_dois").style.visibility = "hidden";
                            document.getElementById("passageiro_dois").value = "";
                        }
                    </script><!--scripts de numero de passageiros-->

                    <script type="text/javascript">
                        function valPass1() {
                            var psgMot = document.getElementById("motorista").value;
                            var psgUm = document.getElementById("passageiro_um").value;
                            if (psgMot == psgUm) {
                                alert("ERRO: O Passageiro selecionado é o mesmo motorista.");
                                document.getElementById("passageiro_um").value = "";
                            }
                        }
                        function valPass2() {
                            var psgMot = document.getElementById("motorista").value;
                            var psgDois = document.getElementById("passageiro_dois").value;
                            if (psgMot == psgDois) {
                                alert("ERRO: O Passageiro selecionado é o mesmo motorista.");
                                document.getElementById("passageiro_dois").value = "";
                            }
                        }
                        function valPass3() {
                            var psgMot = document.getElementById("motorista").value;
                            var psgTres = document.getElementById("passageiro_tres").value;
                            if (psgMot == psgTres) {
                                alert("ERRO: O Passageiro selecionado é o mesmo motorista.");
                                document.getElementById("passageiro_tres").value = "";
                            }
                        }
                        function valPass4() {
                            var psgMot = document.getElementById("motorista").value;
                            var psgQuatro = document.getElementById("passageiro_quatro").value;
                            if (psgMot == psgQuatro) {
                                alert("ERRO: O Passageiro selecionado é o mesmo motorista.");
                                document.getElementById("passageiro_quatro").value = "";
                            }
                        }
                        function valPass5() {
                            var psgMot = document.getElementById("motorista").value;
                            var psgCinco = document.getElementById("passageiro_cinco").value;
                            if (psgMot == psgCinco) {
                                alert("ERRO: O Passageiro selecionado é o mesmo motorista.");
                                document.getElementById("passageiro_cinco").value = "";
                            }
                        }
                        function valPass6() {
                            var psgMot = document.getElementById("motorista").value;
                            var psgSeis = document.getElementById("passageiro_seis").value;
                            if (psgMot == psgSeis) {
                                alert("ERRO: O Passageiro selecionado é o mesmo motorista.");
                                document.getElementById("passageiro_seis").value = "";
                            }
                        }
                    </script><!--scripts para não duplicar passageiros-->

                </fieldset>
                <div class="btn_form_envia">
                    <input type="submit" name="solicitar" value="Enviar solicitação" id="solicitar" class="btn btn_green flt_right" style="margin-bottom: 50px;" onclick="verifBtn()" />  
                </div>
                <div id="form_load"><img src="../_assets/img/ico_load.gif" width="50px"  /></div>
            </form>

            </div><!--fecha div pagina-->    

            </div><!--Termina conteudo das páginas-->
            <?php
        }
        include_once "includes/inc_footer.php";
    }
}
?>