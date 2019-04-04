<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2):

    //PAGINAÇÃO
    $pag = $_GET["pag"];
    if ($pag >= '1'):
        $pag = $pag;
    else:
        $pag = '1';
    endif;
    $maximo = '20'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;

    //BUSCA MOTORISTAS
    $sql_busca = read('servidores', "WHERE os_motorista != 'n' ORDER BY nome ASC LIMIT {$inicio},{$maximo}");
    $sql_conta = read('servidores', "WHERE os_motorista != 'n'");
    $num_condutores = count($sql_conta);
?>
    
    <h1 class="titulo-secao"><i class="fa fa-group"></i> Condutores <span><?php echo $num_condutores; ?> registros</span></h1>
    
    
    <span class="btn_addCondutor btn btn_blue fl_right" style="margin-bottom: 30px; margin-left: 10px;" title="Adicionar um condutor"><i class="fa fa-plus-circle"></i> CONDUTOR</span>
    <a href="print_condutores.php" target="_blank" class="btn btn_orange fl_right" title="Imprimir lista de condutores"><i class="fa fa-print"></i> IMPRIMIR</a>

    <!-- DIV para informar cadastro de novo servidor como condutor-->
    <div id="modalEncerra" class="modal_encerra_guia" style="display: none;">
        <div class="modal_encerra_guia_content" style="display: block;"> 
            <div class="modal_encerra_guia_header">Selecione o servidor</div>
            <div class="msg_retorno"></div>
            <?php
                $buscaServidor = read('servidores',"WHERE ativo = 1 AND os_motorista = 'n' ORDER BY nome ASC");
            ?>
            
            <form name="cad_servidor" method="post" action="admin_cadMotoristas.php">
                 <fieldset class="user_altera_dados">    
                    <label>
                        <span style="margin-left: -12px;">Servidores cadastrados:</span>
                        <select name="condutor" id="condutor" class="select_80">
                            <option value="" selected>Servidores</option>
                            <?php foreach ($buscaServidor as $busca) { $busca_selecionada = $busca['nome']; $siape_cond = $busca['siape']; ?>
                            <option value="<?php echo $siape_cond; ?>"><?php echo $busca_selecionada; ?></option>;
                            <?php
                                }
                            ?>
                        </select>                        
                        <a href="admin_cadServidores.php" title="Adicionar um novo servidor"><i class="fa fa-user-plus" style="margin: 10px 0 0 20px; text-decoration:none; color:#033; font-size: 1.7em; cursor: pointer;"></i></a>
                    </label>
                     <h4 class="modal_add_close btn btn_red" style="margin-top: 6px;" value="<?php echo  $exibeId; ?>">x</h4>
                    <input type="submit" name="avanc_condutor" value="Avançar >>" class="btn btn_altera btn_green fl_right" />
                </fieldset>  
            </form>
            <div class="clear"></div>
        </div>
    </div>    

<?php
    //Verifica se foi selecionado algum servidor para adicona-la como condutor
    if(isset($_GET['msg']) && $_GET['msg'] == 'true'):
        echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Selecione um servidor para adicioná-lo como condutor de veículos!</h4>';	
    endif;
    
    if(isset($_GET['cond']) && $_GET['cond'] == 'true'):
        echo '<h4 class="ms al"><i class="fa fa-comments fa-2x" style="color: #F90"></i> &nbsp;&nbsp;&nbsp;Este servidor já esta cadastrado como condutor de veículos!</h4>';	
    endif; 

    //Script para deletar motorista
    if (isset($_GET['delid'])) {
        $delid = $_GET['delid'];
        $procuraId = read('servidores', "WHERE id = '$delid'");
        $countId = count($procuraId);
        if ($countId < 1) {
            header('Location: admin_emotoristas.php?pag=' . $pag);
        } else {
            foreach ($procuraId as $cond);
            $pasta = 'os/';
            $up['os_motorista'] = 'n';
            $up['os_motorista_data'] = '';
            $up['os_digitalizada'] = '';

            //Deleta arquivo
            unlink('os/' . $cond['os_digitalizada']);

            //Atualiza Dados excluindo a OS
            $upCond = update('servidores', $up, "id = '$delid'");
            if ($upCond) {
                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Condutor deletado com sucesso!</h6>";
                header('Refresh: 1;url=admin_emotoristas.php?pag=' . $pag);
            } else {
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Erro ao deletar o condutor selecionado!</h4>";
            }
        }
    }
    if($num_condutores <= 0):
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Não existem condutores cadastrados no sistema!</h4>";
    else:
    ?>
        
    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        <tr class="tr_header">
            <td align="center"></td>
            <td align="left" width="250px">Nome</td>
            <td align="center">CNH</td>
            <td align="center">Vencimento</td>
            <td align="center">Faltam</td>
            <td align="center">OS</td>
            <td align="center" colspan="2">Ações</td>
        </tr>

        <?php
        foreach ($sql_busca as $res):
            $exibeId = $res['id'];
            $exibeCadastradoEm = $res['criadoEm'];
            $exibeModificadoEm = $res['modificadoEm'];
            $exibeNome = $res['nome'];
            $exibeFoto = $res['foto'];
            $exibeCnh = $res['cnh'];
            $exibeVencCnh = $res['cnh_vencimento'];
            $exibeCategCnh = $res['cnh_categoria'];
            $exibeOS = $res['os_motorista'];
            $exibeOSData = $res['os_motorista_data'];
            $exibeOSDigitalizada = $res['os_digitalizada'];
            $exibeCnhDigitalizada = $res['cnh_digitalizada'];
            $exibeSiape = $res['siape'];
            
            //Mostrar Link para o arquivo da CNH se existir
            if(!empty($exibeCnhDigitalizada)):
                $formataData = date('Y', strtotime($exibeOSData));
                $linkCnh = "<a href='cnh/{$exibeCnhDigitalizada}' target='_blank' title='Visualizar CNH'>{$exibeCnh} / {$exibeCategCnh}</a>";
            else:
                $linkCnh = "{$exibeCnh} / {$exibeCategCnh}";
            endif;
            
            //Coloração da tabela de exibição dos dados
            $colorPage++;
            if ($colorPage % 2 == 0):
                $cor = 'style="background:#f3f3f3;"';
            else:
                $cor = 'style="background:#fff;"';
            endif;
        ?>

        <tr <?php echo $cor; ?> class="lista_itens">
            <td align="center"><img src="imagens/servidores/<?php echo $exibeFoto; ?>" width="30px" style="margin-right:5px;"></td>
            <td align="left"><?php echo $exibeNome; ?></td>
            <td align="center" class="link_os"><?php echo $linkCnh; ?></td>
            <td align="center">
            <?php
                $hoje = date('Y-m-d');
                $time_inicial = strtotime($exibeVencCnh);
                $time_final = strtotime($hoje);
                $diferenca = $time_inicial - $time_final;
                $dias = (int) floor($diferenca / (60 * 60 * 24));
                if ($dias > 0) {
                    if ($dias > 30) {
                        echo '<div>' . date('d/m/Y', strtotime($exibeVencCnh)) . '</div>';
                    } else {
                        echo '<div>' . date('d/m/Y', strtotime($exibeVencCnh)) . '</div>';
                    }
                } else if ($dias == 0) {
                    echo '<div>' . date('d/m/Y', strtotime($exibeVencCnh)) . '</div>';
                } else {
                    $dias_vencida = -$dias;
                    echo '<div style="color:#F00;">' . date('d/m/Y', strtotime($exibeVencCnh)) . '</div>';
                }
            ?>
            </td> 
            
            <td align="center">
            <?php
                $hoje = date('Y-m-d');
                $time_inicial = strtotime($exibeVencCnh);
                $time_final = strtotime($hoje);
                $diferenca = $time_inicial - $time_final;
                $dias = (int) floor($diferenca / (60 * 60 * 24));
                if ($dias > 0) {
                    if ($dias > 30) {
                        echo '<div style="color:#32A041; font-weight:bold;">' . $dias . ' dias</div>';
                    } else {
                        echo '<div style="color:#F90; font-weight:bold;">' . $dias . ' dias</div>';
                    }
                } else if ($dias == 0) {
                    echo '<div style="color:#F60; font-weight:bold;">Vencendo HOJE';
                } else {
                    $dias_vencida = -$dias;
                    echo '<div style="color:#F00; font-weight:bold;">Venceu há ' . $dias_vencida . ' dias<br />';
                }
            ?>
            </td> 
            <td align="center" class="link_os"><a href="os/<?php echo $exibeOSDigitalizada; ?>" target="_blank"><?php echo $exibeOS . ' de ' . date('Y', strtotime($exibeOSData)); ?></a></td>    				
            
            <td align="center" style="width: 40px;">
                <a href="admin_amotoristas.php?upcondutor=<?php echo $exibeId; ?>" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Editar dados do condutor"><i class="fa fa-pencil-square-o"></i></a>
            </td>
            <td align="center" style="width: 40px;">
                <a href="admin_emotoristas.php?delid=<?php echo $exibeId; ?>" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Excluir este condutor"><i class="fa fa-user-times"></i></a>
            </td>  
        </tr>
        <?php
        endforeach;
        ?>
    </table>

    <div id="paginator">
    <?php
    //PAGINAÇÃO
        $total = $num_condutores;

        $paginas = ceil($total / $maximo);
        $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

        echo "<a href=admin_emotoristas.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

        for ($i = $pag - $links; $i <= $pag - 1; $i++) {
            if ($i <= 0) {

            } else {
                echo"<a href=admin_emotoristas.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
            }
        }echo "<h1>$pag</h1>";

        for ($i = $pag + 1; $i <= $pag + $links; $i++) {
            if ($i > $paginas) {

            } else {
                echo "<a href=admin_emotoristas.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
            }
        }
        echo "<a href=admin_emotoristas.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";
    ?>
    </div>
        
<?php
    endif;
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;

include_once "includes/inc_footer.php";
?>

<script type="text/javascript">
    $(".modal_add_close").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra").style.display = "none";
    });

    $(".btn_addCondutor").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra").style.display = "block";
    });
</script>