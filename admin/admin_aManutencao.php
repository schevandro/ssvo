<?php
include_once "includes/inc_header.php";

$id_manutencao      = $_GET['idManut'];
$id_veiculo         = $_GET['idVeic'];

$readManutencao		=	read('vt_manutencao',"WHERE id = '$id_manutencao'");
$contaManutencao	=	count($readManutencao);
if($contaManutencao < 1):
    header("Location: admin_listManutencao.php?idVeic=$id_veiculo");
endif;

$readVeiculo  = read('vt_veiculos',"WHERE id = '$id_veiculo' AND deletado = 0");
$contaVeiculo = count($readVeiculo);
if($contaVeiculo < 1):
    header("Location: admin_listManutencao.php?idVeic=$id_veiculo");
endif;

foreach ($readManutencao as $manutencao);

$id_servidor	= $manutencao['servidor'];
$readServidor   = read('servidores',"WHERE id = '$id_servidor'");
$contaServidor  = count($readServidor);
if($contaServidor < 1):
    header("Location: admin_listManutencao.php?idVeic=$id_veiculo");
endif;

foreach ($readServidor as $dados_servidor);

foreach ($readVeiculo as $veiculo);

if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):
?>
    <h1 class="titulo-secao"><i class="fa fa-pencil-square-o"></i> Editar dados da manutenção</h1>    

<?php 
    echo "<a href='admin_listManutencao.php?idVeic={$id_veiculo}' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Voltar ao histórico de manutenção do veículo'><i class='fa fa-list'></i> Voltar ao histórico de manutenção do veículo</a>";

    if (isset($_POST['atualizar'])):

        //Formatar data recebida da manutenção
        $data_manut     = $_POST['data_manutencao'];
        $sep_data       = explode('/',$data_manut);
        $manut_dia      = $sep_data[0];
        $manut_mes      = $sep_data[1];
        $manut_ano      = $sep_data[2];
        $data_manut_vai = $manut_ano.'-'.$manut_mes.'-'.$manut_dia;

        //Dados da manutencao
        $up['criada_em']	= date('Y-m-d H:i:s');
        $up['data']			= $data_manut_vai;
        $up['empresa']		= strip_tags(trim($_POST['empresa']));
        $up['veiculo']		= $id_veiculo;
        $up['km_revisao']	= strip_tags(trim($_POST['km']));
        $up['servidor']     = $_POST['servidor'];
        $up['oleo']		    = $_POST['oleo'];
        $up['correa'] 	  	= $_POST['correa'];
        $up['geometria']	= $_POST['geometria'];
        $up['revisao']		= $_POST['revisao'];

        //Cadastra dados na base de dados
        if($up['data'] and $up['veiculo'] and $up['km_revisao'] and $up['servidor'] and $up['empresa'] != ""):

            //Verifiac se pelo menos um ítem foi verificado
            if($up['oleo'] or $up['correa'] or $up['geometria'] or $up['revisao'] == '1'):

                    $upRevisao = update('vt_manutencao',$up,"id = '$id_manutencao'");	

                    if($upRevisao):		
                        //Se OK, atualiza dados na tabela veículos
                        $idAtualiza		=	$id_veiculo;
                        $km_atual		=	$up['km_revisao'];
                        $rev_oleo		=	$up['oleo'];
                        $rev_geometria	=	$up['geometria'];
                        $rev_correa		=	$up['correa'];
                        $rev_revisao	=	$up['revisao'];
                        //Verifica Oleo
                        if($rev_oleo == 1):
                            $prox_oleo	=	($km_atual + 5000);
                        else:
                            $prox_oleo	=	$veiculo['troca_oleo'];
                        endif;
                        //Verifica Geometria/Balanceamento
                        if($rev_geometria == 1):
                            $prox_geometria		=	($km_atual + 10000);
                        else:
                            $prox_geometria		=	$veiculo['geometria'];
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

                        $upV['troca_oleo']		=	$prox_oleo;
                        $upV['geometria']		=	$prox_geometria;
                        $upV['correa']			=	$prox_correa;
                        $upV['revisao']			=	$prox_revisao;

                        //Atualiza campos na tabela veiculos
                        $upVeiculos = update('vt_veiculos',$upV,"id = '$idAtualiza'");

                        if($upVeiculos):
                            //mensagem se OK
                            echo "<h4 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Revisão atualizada com sucesso!</h4>";
                            header ("Refresh: 2,url=admin_aManutencao.php?idManut=$id_manutencao&idVeic=$idAtualiza");
                        else:
                            //mensagem se ERRO
                            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao atualizar os dados na tabela de veículos. Contate o administrador.</h4>";
                        endif;
                    else:
                        //mensagem se ERRO
                       echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Erro ao atualizar os dados na tabela de veículos. Contate o administrador.</h4>";
                    endif;
                else:
                    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Pelo menos UM ítem deve ter sido verificado!</h4>";
                endif;
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Por favor, informe todos os dados obrigatórios!</h4>";
            endif;
        endif;
?>

    <form name="cad_servidor" id="cad_servidor" method="post" action="" enctype="multipart/form-data">

        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Atualizar dados da manutenção para o veículo <?php echo $veiculo['veiculo'].'-'.$veiculo['placa']; ?></h2>
            <label style="margin-bottom:35px;">
                <span>Veículo:</span>
                <?php echo '<h1 style="font-size: 1em; margin-top:10px; color:#32A041;">'.$veiculo['veiculo'].' - '.$veiculo['placa'].'</h1>' ?>
            </label>

            <label class="label_medio">
                <span>Data da Manutenção:</span>
                <input name="data_manutencao" type="text" id="data_os" value="<?php echo date('d/m/Y',strtotime($manutencao['data'])); ?>" class="select_80"/>
            </label>
            
            <label class="label_medio">
                <span>KM:</span>
                <input name="km" type="text" class="select_80" value="<?php echo $manutencao['km_revisao']; ?>" maxlength="6"/>
            </label>
            
            <label class="label_medio">
                <span>Servidor:</span>
                <select name="servidor" id="servidor" class="select_80">
                    <option value="<?php echo $dados_servidor['id']; ?>" selected><?php echo $dados_servidor['nome']; ?></option>
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
                <input name="empresa" type="text" id="empresa" value="<?php echo $manutencao['empresa']; ?>"/>
            </label>

            <label class="label_medio">
                <span>Troca de Óleo:</span>
                <select name="oleo" id="oleo" class="select_80" style="height:40px">
                    <?php
                    if($manutencao['oleo'] == 1){
                        echo '<option value="1" selected="selected">SIM</option>';
                        echo '<option value="0">NÃO</option>';	
                    }else{
                        echo '<option value="0" selected="selected">NÃO</option>';
                        echo '<option value="1">SIM</option>';	
                    }
                    ?>                
                </select>
            </label>  

            <label class="label_medio">
                <span>Troca da Corrêa:</span>
                <select name="correa" id="correa" class="select_80" style="height:40px">
                    <?php
                    if($manutencao['correa'] == 1){
                        echo '<option value="1" selected="selected">SIM</option>';
                        echo '<option value="0">NÃO</option>';	
                    }else{
                        echo '<option value="0" selected="selected">NÃO</option>';
                        echo '<option value="1">SIM</option>';	
                    }
                ?>
                </select>
            </label>

            <label class="label_medio">
                <span>Geometria e Balanceamento:</span>
                <select name="geometria" id="geometria" class="select_80" style="height:40px">
                    <?php
                    if($manutencao['geometria'] == 1){
                        echo '<option value="1" selected="selected">SIM</option>';
                        echo '<option value="0">NÃO</option>';	
                    }else{
                        echo '<option value="0" selected="selected">NÃO</option>';
                        echo '<option value="1">SIM</option>';	
                    }
                    ?>
                </select>
            </label>

            <label class="label_medio">
                <span>Revisão Geral:</span>
                <select name="revisao" id="revisao" class="select_80" style="height:40px">
                    <?php
                    if($manutencao['revisao'] == 1){
                        echo '<option value="1" selected="selected">SIM</option>';
                        echo '<option value="0">NÃO</option>';	
                    }else{
                        echo '<option value="0" selected="selected">NÃO</option>';
                        echo '<option value="1">SIM</option>';	
                    }
                    ?>
                </select>
            </label>
            <label>&nbsp;</label>
            <input type="submit" name="atualizar" value="ATUALIZAR" id="atualizar" class="btn btn_green btn_altera fl_right" />  
        </fieldset>
    </form>    

<?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i>&nbsp;&nbsp;&nbsp; Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
include_once "includes/inc_footer.php";
?>