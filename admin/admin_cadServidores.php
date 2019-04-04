<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2 || $_SESSION['autUser']['admin'] == 3):
?>

    <h1 class="titulo-secao"><i class="fa fa-user-plus"></i> Cadastrar novo servidor</h1>

    <?php
    echo "<a href='admin_eservidores.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Servidores'><i class='fa fa-list'></i> Listar Servidores</a>";
    
    if (isset($_POST['cadastrar'])):

        //Receber dados do formulário
        $f['nome']          = strip_tags(trim($_POST['nome']));
        $f['siape']         = strip_tags(trim($_POST['siape']));
        $f['setor']         = strip_tags(trim($_POST['setor']));
        $f['funcao']        = strip_tags(trim($_POST['funcao']));
        $f['carga_horaria'] = strip_tags(trim($_POST['cargahoraria']));
        $f['lotacao']       = $_POST['lotacao'];
        $f['code']          = strip_tags(trim($_POST['senha']));
        $repetesenha        = strip_tags(trim($_POST['repetesenha']));
        $f['senha']         = md5($f['code']);
        $f['mudarsenha']    = strip_tags(trim($_POST['mudasenha']));
        if ($f['mudarsenha'] != '' ? $f['mudarsenha'] = $f['mudarsenha'] : $f['mudarsenha'] = 0);

        //Dados data nascimento
        $nascimento_dia     = $_POST['data_dia'];
        $nascimento_mes     = $_POST['data_mes'];
        $nascimento_ano     = $_POST['data_ano'];
        $f['nascimento']    = $nascimento_ano . '-' . $nascimento_mes . '-' . $nascimento_dia;

        //Dados de Contato do Servidor
        $f['fone_celular']  = strip_tags(trim($_POST['celular']));
        $f['email']         = strip_tags(trim($_POST['email']));

        //Dados de Controle do Banco de Dados
        $f['criadoEm']      = date('Y-m-d H:i:s');
        $f['modificadoEm']  = date('Y-m-d H:i:s');
        $f['chave']         = date('dHi') .'-'. $f['email'];

        //Cadastra dados na base de dados
        if ($f['nome'] and $f['siape'] and $f['carga_horaria'] and $f['funcao'] and $f['lotacao'] and $f['nascimento'] and $f['fone_celular'] and $f['email'] and $f['senha'] and $repetesenha != ""):
            if (!valMail($f['email'])):
                echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;O formato de E-MAIL informado, não é válido!</h4>';
            else:
                if ($f['code'] != $repetesenha):
                    echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;As senhas digitadas não conferem!</h4>';
                else:
                    if (strlen($f['code']) < 8 || strlen($f['code']) > 12):
                        echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;A senha deve possuir entre 8 e 12 caracteres!</h4>';
                    else:
                        $confEmail = $f['email'];
                        $readDupMail = read('servidores', "WHERE email = '$confEmail'");
                        $countReadDupMail = count($readDupMail);
                        if ($countReadDupMail >= 1):
                            echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;O email informado já se encontra cadastrado em nossa base de dados!</h4>';
                        else:
                            $confSiape = $f['siape'];
                            $readDupSiape = read('servidores', "WHERE siape = '$confSiape'");
                            $countReadDupSiape = count($readDupSiape);
                            if ($countReadDupSiape >= 1):
                                echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;O SIAPE informado já se encontra cadastrado em nossa base de dados!</h4>';                                
                            else:
                                //Informações da foto do servidor
                                $fotoservidor = $_FILES["foto"]["tmp_name"];
                                if ($fotoservidor == ""):
                                    $f['foto'] = 'sem_foto.png';
                                else:
                                    $foto_nomeTemporario = $_FILES["foto"]["tmp_name"];
                                    $foto_nomeReal = $_FILES["foto"]["name"];
                                    $f['foto'] = $email . '_' . $foto_nomeReal;
                                endif;
                                $createServidor = create('servidores', $f);

                                //Grava a foto do servidor na pasta de imagens
                                if ($fotoservidor != ""):
                                    copy($foto_nomeTemporario, 'imagens/servidores/' . $f['foto']);
                                endif;
                                //Mensagem se o cadastro foi feito com sucesso
                                if ($createServidor):
                                    echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp;&nbsp;&nbsp; Servidor cadastrado com sucesso!</h6>";
                                    header('Refresh: 2,url=admin_cadServidores.php');
                                endif;
                           endif;
                        endif;
                    endif;
                endif;
            endif;
        else:
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Por favor, preencha todos os campos obrigatórios!</h4>";
        endif;
    endif;
    ?>

    <form name="cad_servidor" method="post" action="" enctype="multipart/form-data">
        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Informe os seguintes dados</h2>

            <label>
                <span>Nome*:</span>
                <input name="nome" type="text" id="nome" maxlength="200" value="<?php if ($nome) echo $nome; ?>"/>
            </label>
            <label>
                <span>Data de Nascimento*:</span>
                <select name="data_dia" id="data_dia" class="select_small">
                    <option value="01" selected>01</option>
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
                </select> 
                <select name="data_mes" id="data_mes" class="select_small">
                    <option value="01" selected>Jan</option>
                    <option value="02">Fev</option>
                    <option value="03">Mar</option>
                    <option value="04">Abr</option>
                    <option value="05">Mai</option>
                    <option value="06">Jun</option>
                    <option value="07">Jul</option>
                    <option value="08">Ago</option>
                    <option value="09">Set</option>
                    <option value="10">Out</option>
                    <option value="11">Nov</option>
                    <option value="12">Dez</option>
                </select>
                <select name="data_ano" id="data_ano" class="select_small">
                    <option value="1930">1930</option>
                    <option value="1931">1931</option>
                    <option value="1932">1932</option>
                    <option value="1933">1933</option>
                    <option value="1934">1934</option>
                    <option value="1935">1935</option>
                    <option value="1936">1936</option>
                    <option value="1937">1937</option>
                    <option value="1938">1938</option>
                    <option value="1939">1939</option>
                    <option value="1940">1940</option>
                    <option value="1941">1941</option>
                    <option value="1942">1942</option>
                    <option value="1943">1943</option>
                    <option value="1944">1944</option>
                    <option value="1945">1945</option>
                    <option value="1946">1946</option>
                    <option value="1947">1947</option>
                    <option value="1948">1948</option>
                    <option value="1949">1949</option>
                    <option value="1950">1950</option>
                    <option value="1951">1951</option>
                    <option value="1952">1952</option>
                    <option value="1953">1953</option>
                    <option value="1954">1954</option>
                    <option value="1955">1955</option>
                    <option value="1956">1956</option>
                    <option value="1957">1957</option>
                    <option value="1958">1958</option>
                    <option value="1959">1959</option>
                    <option value="1960">1960</option>
                    <option value="1961">1961</option>
                    <option value="1962">1962</option>
                    <option value="1963">1963</option>
                    <option value="1964">1964</option>
                    <option value="1965">1965</option>
                    <option value="1966">1966</option>
                    <option value="1967">1967</option>
                    <option value="1968">1968</option>
                    <option value="1969">1969</option>
                    <option value="1970">1970</option>
                    <option value="1971">1971</option>
                    <option value="1972">1972</option>
                    <option value="1973">1973</option>
                    <option value="1974">1974</option>
                    <option value="1975">1975</option>
                    <option value="1976">1976</option>
                    <option value="1977">1977</option>
                    <option value="1978">1978</option>
                    <option value="1979">1979</option>
                    <option value="1980">1980</option>
                    <option value="1981">1981</option>
                    <option value="1982">1982</option>
                    <option value="1983">1983</option>
                    <option value="1984">1984</option>
                    <option value="1985">1985</option>
                    <option value="1986">1986</option>
                    <option value="1987">1987</option>
                    <option value="1988">1988</option>
                    <option value="1989">1989</option>
                    <option value="1990">1990</option>
                    <option value="1991">1991</option>
                    <option value="1992">1992</option>
                    <option value="1993">1993</option>
                    <option value="1994">1994</option>
                    <option value="1995">1995</option>
                    <option value="1996">1996</option>
                    <option value="1997">1997</option>
                    <option value="1998" selected>1998</option>
                </select>							
            </label>  
            <label>
                <span>SIAPE*:</span>
                <input name="siape" type="text" class="selects" id="siape" maxlength="9"/>
            </label>
            <label class="label_medio">
                <span>Setor*:</span>
                <?php $readSetor = read('setores'); ?>
                    <select name="setor" id="setor" class="select_medio">
                        <option value="" selected>Selecionar</option>
                        <?php foreach ($readSetor as $select_setor) {
                        $setor_selecionado = $select_setor['abreviatura']; ?>
                        <option value="<?php echo $setor_selecionado; ?>"><?php echo $setor_selecionado; ?></option>
                <?php } ?>
                    </select> 
            </label>          
            <label class="label_medio">
                <span>Função*:</span>
                <?php $readFuncoes = read('funcoes'); ?>
                <select name="funcao" id="funcao" class="select_medio">
                    <option value="" selected>Selecionar</option>
                    <?php foreach ($readFuncoes as $select_funcao) {
                    $funcao_selecionada = $select_funcao['abreviatura']; ?>
                    <option value="<?php echo $funcao_selecionada; ?>"><?php echo $funcao_selecionada; ?></option>
                <?php } ?>
                </select>
            </label>
            <label class="label_medio">
                <span>Carga Horária*:</span>
                <?php $readCarga = read('cargahoraria'); ?>
                <select name="cargahoraria" id="cargahoraria" class="select_medio">
                    <option value="" selected>Selecionar</option>
                    <?php foreach ($readCarga as $select_cargahoraria) {
                    $cargahoraria_selecionada = $select_cargahoraria['carga_horaria']; ?>
                    <option value="<?php echo $cargahoraria_selecionada; ?>"><?php echo $cargahoraria_selecionada; ?></option>
                <?php } ?>
                </select> 
            </label>
            <label class="label_medio">
                <span>Lotação*:</span>
                <?php $readLotacao = read('lotacao'); ?>
                <select name="lotacao" id="lotacao" class="select_medio">
                    <option value="" selected>---</option>
                    <?php foreach ($readLotacao as $select_lotacao) {
                    $lotacao_selecionada = $select_lotacao['campus']; ?>
                    <option value="<?php echo $lotacao_selecionada; ?>"><?php echo $lotacao_selecionada; ?></option>
                <?php } ?>
                </select> 
            </label>
            <label>
                <span style="margin-top:20px">Foto:</span>
                <div class="cadastra_foto_servidor">
                    <input type="file" name="foto" id="foto"/>
                    <span>Formato JPG ou PNG</span>
                </div>
            </label>       
            <label>
                <span>Celular*:</span>
                <input name="celular" type="text" id="celular" class="telefone"/>
            </label>

            <label>
                <span>Email*:</span>
                <input name="email" type="text" id="email" maxlength="200"/>
            </label>

            <label>
                <span>Senha:</span>
                <input name="senha" type="password" maxlength="12" class="senha"/>
            </label>

            <label>
                <span>Repetir a senha:</span>
                <input name="repetesenha" type="password" maxlength="12" class="senha"/>
            </label>
            
            <div class="check_altera_senha">
                <input class="checksenha" type="checkbox" name="mudasenha" value="1" <?php if ($f['mudarsenha']) echo 'checked="checked"'; ?>/>
                <span>Mudar senha no primeiro acesso</span>
            </div>

            <input type="submit" name="cadastrar" value="Cadastrar" id="cadastrar" class="btn btn_altera btn_green fl_right" />
        </fieldset>
    </form>
    <?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
?>

<!--Encerra conteúdo das páginas-->
<?php include_once "includes/inc_footer.php"; ?>