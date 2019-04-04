<?php
include_once "includes/inc_header.php";
	
$servidorId = strip_tags(trim($_GET['id_servidor']));
if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2 || $_SESSION['autUser']['admin'] == 3 || $_SESSION['autUser']['id'] == $servidorId)://Libera a página somente para os dados do próprio usuário

    //Selecionar o servidor
    $readBusca = read('servidores',"WHERE id = '$servidorId' ORDER BY nome ASC");   
    $countReadBusca = count($readBusca);
    if($countReadBusca < 1):
        header('Location: painel.php');
    else:
        foreach ($readBusca as $res);

?>
        <h1 class="titulo-secao"><i class="fa fa-pencil-square-o"></i> Editar dados do servidor</h1>    

<?php 
        echo "<a href='admin_eservidores.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Servidores'><i class='fa fa-list'></i> Listar Servidores</a>";

        if (isset($_POST['atualizar'])):

            //Receber dados do Formulário
            $f['nome']      	 = strip_tags(trim($_POST['nome']));
            $f['siape']      	 = strip_tags(trim($_POST['siape']));
            $f['setor']		 = strip_tags(trim($_POST['setor']));
            $f['funcao']      	 = strip_tags(trim($_POST['funcao']));
            $f['carga_horaria']  = strip_tags(trim($_POST['cargahoraria']));
            $f['lotacao']     	 = $_POST['lotacao'];
            $f['sexo']      	 = $_POST['sexo'];
            //Dados data nascimento
            $nascimento_dia  = $_POST['data_dia'];
            $nascimento_mes  = $_POST['data_mes'];
            $nascimento_ano  = $_POST['data_ano'];
            $f['nascimento'] 	 = $nascimento_ano.'-'.$nascimento_mes.'-'.$nascimento_dia;
            //Documentação do Servidor
            $f['cpf']      		 = strip_tags(trim($_POST['cpf']));
            $f['cnh']      		 = strip_tags(trim($_POST['cnh']));
            $f['cnh_categoria']       = strip_tags(trim($_POST['categcnh']));
            //Vencimento da CNH
            $venccnh_br     = $_POST['venc_cnh'];
            $venccnh_exp    = explode("/", $venccnh_br);
            $venccnh_dia    = $venccnh_exp[0];
            $venccnh_mes    = $venccnh_exp[1];
            $venccnh_ano    = $venccnh_exp[2];
            $f['cnh_vencimento'] = $venccnh_ano.'-'.$venccnh_mes.'-'.$venccnh_dia;
            //Dados de Contato do Servidor
            $f['end_rua']  		  = strip_tags(trim($_POST['end_rua']));
            $f['end_num']     	  = strip_tags(trim($_POST['end_numero']));
            $f['end_complemento'] = strip_tags(trim($_POST['end_complemento']));
            $f['end_bairro']      = strip_tags(trim($_POST['end_bairro']));
            $f['end_cidade']      = $_POST['end_cidade'];
            $f['end_cep']         = strip_tags(trim($_POST['end_cep']));
            $f['fone_celular']    = strip_tags(trim($_POST['celular']));
            $f['fone_outro']      = strip_tags(trim($_POST['outrotelefone']));
            $f['email']     	  = strip_tags(trim($_POST['email']));
            //Dados de Controle do Banco de Dados
            $f['modificadoEm'] = date('Y-m-d H:i:s');
            $sem_foto     = 'sem_foto.png';

            //Cadastra dados na base de dados
            if($f['nome'] and $f['siape'] and $f['carga_horaria'] and $f['funcao'] and $f['lotacao'] and $f['nascimento'] and $f['fone_celular'] and $f['email'] and $f['setor'] != ""):

                $fotoservidor = $_FILES["foto"]["tmp_name"];
                if($fotoservidor != ""):
                    if ($res['foto'] != $sem_foto):
                        $deletaFile  = \unlink('imagens/servidores/'.$res['foto']);
                        $_SESSION['autUser']['foto'] = 'sem_foto.png';
                    endif;
                    $foto_nomeTemporario = $_FILES["foto"]["tmp_name"];
                    $foto_nomeReal = $_FILES["foto"]["name"];
                    $foto = $foto_nomeReal; 
                    $_SESSION['autUser']['foto'] = $foto;
                    $f['foto'] = $foto;
                else:
                    $foto = $res['foto'];
                    $f['foto'] = $foto;
                endif;

                //Atualiza oos dados do servidor na tabela servidores
                $upServidor = update('servidores',$f,"id = '$servidorId'");

                //Grava a foto do servidor na pasta de imagens
                if ($_FILES["foto"]["tmp_name"] != ""):
                    copy($foto_nomeTemporario, 'imagens/servidores/'.$f['foto']);
                endif;

                //Se deu certo
                if($upServidor):
                    echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Dados do servidor atualizados com sucesso!</h4>";
                    header('Refresh: 2,url=admin_aservidores.php?id_servidor='.$servidorId);	
                else:
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao atualiar os dados do servidor!</h4>";
                endif;			

            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Por favor, preencha todos os campos obrigatórios!</h4>";
            endif;
        endif;
    endif;

    //Processa data de nascimento
    $processadata  = explode('-', $res['nascimento']);
    $diaNascimento = $processadata[2];
    $mesNascimento = $processadata[1];
    $anoNascimento = $processadata[0];
?>
        
    <form name="cad_servidor" id="cad_servidor" method="post" action="" enctype="multipart/form-data">

        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Dados pessoais</h2>
            
            <label>
              <span>Nome*:</span>
              <input name="nome" type="text" id="nome" value="<?php echo $res['nome']; ?>" maxlength="200"/>
            </label>
             <label>
                <span>Data de Nascimento*:</span>
                <select name="data_dia" id="data_dia" class="select_small">
                    <option value="<?php echo $diaNascimento; ?>" selected><?php echo $diaNascimento; ?></option>
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
                </select> 
                <select name="data_mes" id="data_mes"  class="select_small">
                    <?php
                        //Nomeia o dia da semana
                        switch($mesNascimento) {
                            case"1": $mesNascimento_nome = "Jan"; break;
                            case"2": $mesNascimento_nome = "Fev"; break;
                            case"3": $mesNascimento_nome = "Mar"; break;
                            case"4": $mesNascimento_nome = "Abr"; break;
                            case"5": $mesNascimento_nome = "Mai"; break;
                            case"6": $mesNascimento_nome = "Jun"; break;
                            case"7": $mesNascimento_nome = "Jul"; break;
                            case"8": $mesNascimento_nome = "Ago"; break;
                            case"9": $mesNascimento_nome = "Set"; break;
                            case"10": $mesNascimento_nome = "Out"; break;
                            case"11": $mesNascimento_nome = "Nov"; break;
                            case"12": $mesNascimento_nome = "Dez"; break;
                        }//Fecha o SWITCH
                    ?>
                    <option value="<?php echo $mesNascimento; ?>" selected><?php echo $mesNascimento_nome; ?></option>
                    <option value="01">Jan</option>
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
                <select name="data_ano" id="data_ano"  class="select_small">
                    <option value="<?php echo $anoNascimento; ?>" selected><?php echo $anoNascimento; ?></option>
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
                    <option value="1998">1998</option>
                </select>							
            </label> 
            <label>
              <span>SIAPE*:</span>
              <input name="siape" type="text" class="siape" id="siape" value="<?php echo $res['siape']; ?>" maxlength="9"/>
            </label>
            <label class="label_medio">
                <span>Setor*:</span>
                <select name="setor" size="1" id="setor" class="select_medio" >
                    <?php /*Busca Setores8*/ $readSetores = read('setores'); ?>
                    <option value="<?php echo $res['setor']; ?>" selected><?php echo $res['setor']; ?></option>
                    <?php foreach ($readSetores as $mostraSetor) { $setor_selecionado = $mostraSetor['abreviatura']; ?>
                    <option value="<?php echo $setor_selecionado; ?>"><?php echo $setor_selecionado; ?></option>
                    <?php } ?>
                </select> 
            </label> 
            <label class="label_medio">
                <span>Função*:</span>
                <select name="funcao" id="funcao" class="select_medio">
                            <?php /*Busca Funções*/ $readFuncoes = read('funcoes'); ?>
                                    <option value="<?php echo $res['funcao']; ?>" selected><?php echo $res['funcao']; ?></option>
                    <?php foreach ($readFuncoes as $mostraFuncao) { $funcao_selecionada = $mostraFuncao['abreviatura']; ?>
                    <option value="<?php echo $funcao_selecionada; ?>"><?php echo $funcao_selecionada; ?></option>
                    <?php } ?>
                </select>
            </label>
            <label class="label_medio">
                <span>Carga Horária*:</span>
                <select name="cargahoraria" id="cargahoraria" class="select_medio">
                    <?php /*Busca Carga Horaria*/ $readCargaHoraria = read('cargahoraria'); ?>
                                    <option value="<?php echo $res['carga_horaria']; ?>" selected><?php echo $res['carga_horaria']; ?></option>
                    <?php foreach ($readCargaHoraria as $mostraCargaHoraria) { $cargahoraria_selecionada = $mostraCargaHoraria['carga_horaria']; ?>
                    <option value="<?php echo $cargahoraria_selecionada; ?>"><?php echo $cargahoraria_selecionada; ?></option>
                    <?php } ?>
                </select> 
            </label>
            <label class="label_medio">
                <span>Lotação*:</span>
                <select name="lotacao" id="lotacao" class="select_medio">
                    <?php /*Busca Lotação*/ $readLotacao = read('lotacao'); ?>
                                    <option value="<?php echo $res['lotacao']; ?>" selected><?php echo $res['lotacao']; ?></option>
                    <?php foreach ($readLotacao as $mostraLotacao) { $lotacao_selecionada = $mostraLotacao['campus']; ?>
                    <option value="<?php echo $lotacao_selecionada; ?>"><?php echo $lotacao_selecionada; ?></option>
                    <?php } ?>
                </select> 
            </label>
            <label class="label_medio">
                <span>Sexo:</span>
                <select name="sexo" id="sexo" class="select_medio">
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                    <option value="<?php echo $res['sexo']; ?>" selected><?php echo $res['sexo']; ?></option>
                </select>							
            </label> 
            <label>
                <span style="margin-top:20px">Foto:</span>
                <div class="altera_foto_servidor">
                    <img src="imagens/servidores/<?php echo $res['foto']; ?>" width="75px" style="float:left; margin:15px;"/>
                    <input type="file" name="foto" id="foto" value="<?php echo $res['foto']; ?>" />
                    <span style="font:bold 10px 'Trebuchet MS', Arial, Helvetica, sans-serif; color:#000; margin:55px 0 0 -344px; float:left;">Formato JPG ou PNG</span>
                </div>
            </label>         
         </fieldset>

        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Documentação</h2>

            <label>
                <span>CPF*:</span>
                <input name="cpf" type="text" id="cpf" maxlength="14" value="<?php echo $res['cpf']; ?>"/>
            </label>
            <label>
                <span>Carteira Nacional de Habilitação*:</span>
                <input name="cnh" type="text" id="cnh" maxlength="15" value="<?php echo $res['cnh']; ?>"/>
            </label>
            <label>
                <span>Categoria CNH*:</span>
                <input name="categcnh" type="text" id="categcnh" maxlength="2" value="<?php echo $res['cnh_categoria']; ?>"/>
            </label>
            <label>
                <span>Vencimento CNH*:</span>
                <input name="venc_cnh" type="text" id="venc_cnh" maxlength="10" value="<?php echo date('d/m/Y', strtotime($res['cnh_vencimento'])); ?>"/>
            </label>
        </fieldset>

        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Contato</h2>
            <label>
                <span>Endereço / Rua*:</span>
                <input name="end_rua" type="text" id="end_rua" maxlength="200" value="<?php echo $res['end_rua']; ?>"/>
            </label>
            <label>
                <span>Número*:</span>
                <input name="end_numero" type="text" class="endnumero" id="end_numero" maxlength="5" value="<?php echo $res['end_num']; ?>"/>
            </label>
            <label>
                <span>Complemento:</span>
                <input name="end_complemento" type="text" id="end_complemento" maxlength="20" value="<?php echo $res['end_complemento']; ?>"/>
            </label>
            <label>
                <span>Bairro*:</span>
                <input name="end_bairro" type="text" id="end_bairro" maxlength="100" value="<?php echo $res['end_bairro']; ?>"/>
            </label>
            <label>
                <span>Cidade:</span>
                <select name="end_cidade" id="end_cidade"  class="select_medio">
                    <?php /*Busca Cidades*/ $readCidades = read('app_cidades'); ?>
                    <option value="<?php echo $res['end_cidade']; ?>" selected><?php echo $res['end_cidade']; ?></option>
                    <?php foreach ($readCidades as $mostraCidade) { $cidade_selecionada = $mostraCidade['cidade_nome'].'-'.$mostraCidade['cidade_uf']; ?>
                    <option value="<?php echo $cidade_selecionada; ?>"><?php echo utf8_encode($cidade_selecionada); ?></option>
                    <?php } ?>
                </select> 
            </label>
            <label>
                <span>CEP*:</span>
                <input name="end_cep" type="text" id="end_cep" class="endcep" value="<?php echo $res['end_cep']; ?>"/>
            </label>
            <label>
                <span>Celular*:</span>
                <input name="celular" type="text" id="celular" value="<?php echo $res['fone_celular']; ?>" class="telefone"/>
            </label>
            <label>
                <span>Outro Telefone:</span>
                <input name="outrotelefone" type="text" id="outrotelefone" class="telefone" value="<?php echo $res['fone_outro']; ?>"/>
            </label>
            <label>
                <span>Email*:</span>
                <input name="email" type="text" id="email" value="<?php echo $res['email']; ?>" maxlength="200"/>
            </label>
            <input type="submit" name="atualizar" value="Atualizar" class="btn btn_altera btn_green fl_right" /> 
        </fieldset>
    </form>
    
<?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Seu nível de acesso não permite visualizar esta página!</h4>";
endif;

include_once "includes/inc_footer.php";
?>