<?php
include_once "includes/inc_header.php";

$id_veiculo = $_GET['idVeic'];

$readVeiculo  = read('vt_veiculos',"WHERE id = '$id_veiculo' AND deletado = 0");
$contaVeiculo = count($readVeiculo);

if($contaVeiculo < 1):
    header('Location: admin_emanutencao.php');
endif;

foreach ($readVeiculo as $veiculo);

if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):

?>
    <h1 class="titulo-secao"><i class="fa fa-cog"></i> Cadastrar nova manutenção</h1>

<?php 
    echo "<a href='admin_listManutencao.php?idVeic={$id_veiculo}' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar manutenções deste veículos'><i class='fa fa-list'></i> Listar Manutenções</a>";

    if (isset($_POST['cadastrar'])):

        //Formatar data recebida da manutenção
        $data_manut     = $_POST['data_manutencao'];
        $sep_data       = explode('/',$data_manut);
        $manut_dia      = $sep_data[0];
        $manut_mes      = $sep_data[1];
        $manut_ano      = $sep_data[2];
        $data_manut_vai = $manut_ano.'-'.$manut_mes.'-'.$manut_dia;

        //Dados da manutencao
        $f['criada_em']		= date('Y-m-d H:i:s');
        $f['data']			= $data_manut_vai;
        $f['empresa']		= strip_tags(trim($_POST['empresa']));
        $f['veiculo']		= $id_veiculo;
        $f['km_revisao']	= strip_tags(trim($_POST['km']));
        $f['servidor']     	= $_POST['servidor'];
        $f['oleo']		    = $_POST['oleo'];
        $f['correa'] 	  	= $_POST['correa'];
        $f['geometria']		= $_POST['geometria'];
        $f['revisao']		= $_POST['revisao'];

        //Cadastra dados na base de dados
        if($f['data'] and $f['veiculo'] and $f['km_revisao'] and $f['servidor'] and $f['empresa'] != ""):
	
            //Verifiac se pelo menos um ítem foi verificado
            if($f['oleo'] or $f['correa'] or $f['geometria'] or $f['revisao'] == '1'):
	
		$cadRevisao = create('vt_manutencao',$f);	
		
		if($cadRevisao):
                    //Se OK, atualiza dados na tabela veículos
                    $idAtualiza		= $id_veiculo;
                    $km_atual		= $f['km_revisao'];
                    $rev_oleo		= $f['oleo'];
                    $rev_geometria	= $f['geometria'];
                    $rev_correa		= $f['correa'];
                    $rev_revisao	= $f['revisao'];
                    //Verifica Oleo
                    if($rev_oleo == 1):
                        $prox_oleo	= ($km_atual + 5000);
                    else:
                        $prox_oleo	= $veiculo['troca_oleo'];
                    endif;
                    //Verifica Geometria/Balanceamento
                    if($rev_geometria == 1):
                        $prox_geometria	= ($km_atual + 10000);
                    else:
                        $prox_geometria	= $veiculo['geometria'];
                    endif;
                    //Verifica Correa Dentada
                    if($rev_correa == 1):
                        $prox_correa	=	($km_atual + 50000);
                    else:
                        $prox_correa	=	$veiculo['correa'];
                    endif;
                    //Verifica Revisão Geral
                    if($rev_revisao == 1):
                        $prox_revisao	=	($km_atual + 10000);
                    else:
                        $prox_revisao	=	$veiculo['revisao'];
                    endif;

                    $up['troca_oleo']	=	$prox_oleo;
                    $up['geometria']	=	$prox_geometria;
                    $up['correa']	=	$prox_correa;
                    $up['revisao']	=	$prox_revisao;

                    //Atualiza campos na tabela
                    $upRevisao = update('vt_veiculos',$up,"id = '$idAtualiza'");

                    if($upRevisao):
                        //mensagem se OK
                        echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp;&nbsp;&nbsp; Revisão cadastrada com sucesso!</h6>";
                        header ("Refresh: 2,url=admin_dadosProxManutencao.php?idVeic=$idAtualiza");
                    else:
                        //mensagem se ERRO
                        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao atualizar os dados na tabela de veículos. Contate o administrador.</h4>";
                    endif;
			
		else:
                    //mensagem se ERRO
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao atualizar os dados na tabela de veículos. Contate o administrador.</h4>";
		endif;
	
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Pelo menos UM ítem deve ter sido verificado!</h4>";
            endif;
	
        else:
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Por favor, preencha todos os campos obrigatórios!</h4>";
        endif;

    endif;
?>

    <form name="cad_servidor" id="cad_servidor" method="post" action="" enctype="multipart/form-data">
        
        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Informe os seguintes dados</h2>

            <label style="margin-bottom:35px;">
                <span>Veículo:</span>
                <?php echo '<h1 style="font-size: 1em; margin-top:5px; color:#32A041;">'.$veiculo['veiculo'].' - '.$veiculo['placa'].'</h1>' ?>
            </label>
          
            <label class="label_medio">
                <span>Data da Manutenção:</span>
                <input name="data_manutencao" type="text" id="data_os" value="" class="select_80"/>
            </label>
            
            <label class="label_medio">
                <span>KM:</span>
                <input name="km" type="text" class="select_80" value="<?php echo $veiculo['km']; ?>" maxlength="6"/>
            </label>
            
            <label class="label_medio">
                <span>Servidor:</span>
                <select name="servidor" id="servidor" class="select_80">
                    <option value="" selected disabled="disabled">Servidores</option>
                    <?php
                    $buscaServidor = read('servidores',"ORDER BY nome ASC"); 
                    foreach ($buscaServidor as $busca) { $busca_selecionada = $busca['nome']; $id_servidor = $busca['id']; ?>
                    <option value="<?php echo $id_servidor; ?>"><?php echo $busca_selecionada; ?></option>;
                    <?php
                    }
                    ?>
                </select>
            </label>
          
            <label>
                <span>Empresa:</span>
                <input name="empresa" type="text" id="empresa" value=""/>
            </label>

            <label class="label_medio">
            <span>Troca de Óleo:</span>
                <select name="oleo" id="oleo" class="select_80" style="height:40px">
                    <option value="0">NÃO</option>
                    <option value="1">SIM</option>
                </select>
            </label>  
          
            <label class="label_medio">
                <span>Troca da Corrêa:</span>
                <select name="correa" id="correa" class="select_80" style="height:40px">
                    <option value="0">NÃO</option>
                    <option value="1">SIM</option>
                </select>
            </label>
          
            <label class="label_medio">
                <span>Geometria e Balanceamento:</span>
                <select name="geometria" id="geometria" class="select_80" style="height:40px">
                    <option value="0">NÃO</option>
                    <option value="1">SIM</option>
                </select>
            </label>
          
            <label class="label_medio">
                <span>Revisão Geral:</span>
                <select name="revisao" id="revisao" class="select_80" style="height:40px">
                    <option value="0">NÃO</option>
                    <option value="1">SIM</option>
                </select>
            </label>
            <label>&nbsp;</label>
            <input type="submit" name="cadastrar" value="Cadastrar" id="cadastrar" class="btn btn_green btn_altera fl_right" />  
        </fieldset>
    </form>
    
<?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
include_once "includes/inc_footer.php"; 
?>