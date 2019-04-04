<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2 || $_SESSION['autUser']['admin'] == 3):

    //PAGINAÇÃO
    $pag = $_GET["pag"];
    if ($pag >= '1'):
        $pag = $pag;
    else:
        $pag = '1';
    endif;
    $maximo = '20'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;

    //BUSCA SERVIDORES
    $read_servidores = read("servidores", "WHERE ativo = 1 ORDER BY nome ASC LIMIT {$inicio},{$maximo}");
    $count_servidores = read("servidores", "WHERE ativo = 1");
    $num_servidores = count($count_servidores);
?>

    <h1 class="titulo-secao"><i class="fa fa-users"></i> Servidores <span><?php echo $num_servidores; ?> registros</span></h1>

    <a href="admin_cadServidores.php" class="btn btn_blue fl_right" style="margin-bottom: 30px; margin-left: 10px;" title="Adicionar um servidor"><i class="fa fa-plus-circle"></i> SERVIDOR</a>
    <a href="admin_eservidores_desat.php" class="btn btn_red fl_right" title="Listar servidores inativos"><i class="fa fa-user-times"></i> INATIVOS</a>

    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        <tr class="tr_header">
            <td align="center"></td>
            <td align="left" style="padding-left:10px; width: 25%;">Servidor</td>
            <td align="center">Siape</td>
            <td align="center">Setor</td>
            <td align="center">Função</td>
            <td align="center" colspan="3">Ações</td>
        </tr>

        <?php
        foreach ($read_servidores as $res):
            $exibeId = $res['id'];
            $exibeCadastradoEm = $res['criadoEm'];
            $exibeModificadoEm = $res['modificadoEm'];
            $exibeNome = $res['nome'];
            $exibeEmail = $res['email'];
            $exibeAdmin = $res['admin'];
            $exibeSetor = $res['setor'];
            $exibeFoto = $res['foto'];
            $exibeFuncao = $res['funcao'];
            $exibeLotacao = $res['lotacao'];
            $exibeSiape = $res['siape'];
            $exibeAdmin = $res['admin'];

            $colorPage++;
            if ($colorPage % 2 == 0):
                $cor = 'style="background:#f3f3f3;"';
            else:
                $cor = 'style="background:#fff;"';
            endif;
            ?>

            <tr <?php echo $cor; ?> class="lista_itens">
                
                <?php
                    //Verifica nível de acesso do usuário
                    if ($exibeAdmin == 1):
                        $listNivel = "<i class='fa fa-cog' style='font-size: 1.3em;' title='Administrador do Sistema'></i>&nbsp;&nbsp;";
                    elseif ($exibeAdmin == 2):
                        $listNivel = "<i class='fa fa-car' style='font-size: 1.3em;' title='Operador Viaturas'></i>&nbsp;&nbsp;";
                    elseif ($exibeAdmin == 3):
                        $listNivel = "<i class='fa fa-group' style='font-size: 1.3em;' title='Operador CGP'></i>&nbsp;&nbsp;";
                    else:
                       $listNivel = NULL;
                    endif;
                 ?>
                <td align="center"><img src="imagens/servidores/<?php echo $exibeFoto; ?>" width="30px" style="margin-right:5px;"></td>
                <td align="left"><?php echo $listNivel.' '.$exibeNome; ?></td>                                                      
                <td align="center"><?php echo $exibeSiape; ?></td>
                <td align="center"><?php echo $exibeSetor; ?></td>
                <td align="center"><?php echo $exibeFuncao; ?></td>    				
                <td align="center" style="width: 40px;">
                    <a href="admin_alterasenha.php?id_servidor=<?php echo $exibeId; ?>&id_senha=true" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Alterar senha de acesso do servidor"><i class="fa fa-lock"></i></a>
                </td>
                <td align="center" style="width: 40px;">
                    <a href="admin_aservidores.php?id_servidor=<?php echo $exibeId; ?>" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Editar dados do servidor"><i class="fa fa-pencil-square-o"></i></a>
                </td>
                <td align="center" style="width: 40px;">
                    <i class="fa fa-user-times btn_ativaServidor" style="text-decoration:none; color:#033; font-size: 1.7em; cursor: pointer;" title="Desativar este servidor" id="btn_encerraGuia" value="<?php echo $exibeId; ?>"></i>
                </td>

            </tr>
            
            <!-- DIV para confirmar ativação do servidor-->
            <div id="modalEncerra<?php echo $exibeId; ?>" class="modal_encerra_guia" style="display: none;">
                <div class="modal_encerra_guia_content" style="display: block;"> 
                    <div class="modal_encerra_guia_header">Desativar o servidor(a) <?php echo $exibeNome; ?></div>
                    <div class="msg_retorno"></div>
                    
                    <div class="formEncerradaBlock">
                        <h4 class="ms green"><i class="fa fa-refresh" style="font-size: 1.4em color:#F06;"></i>&nbsp;&nbsp;&nbsp; Esta solicitação já esta encerrada!</h4>    
                    </div>
                    
                    <form name="desativaServidor" method="post">

                        <h2 class="info_ativa_servidor"><strong>Nome:</strong> <?php echo  $exibeNome; ?></h2><br />
                        <h2 class="info_ativa_servidor"><strong>Email:</strong> <?php echo  $exibeEmail; ?></h2><br />
                        <h2 class="info_ativa_servidor"><strong>Siape:</strong> <?php echo  $exibeSiape; ?></h2><br />
                        <h2 class="info_ativa_servidor"><strong>Setor:</strong> <?php echo  $exibeSetor; ?></h2><br />
                        
                        <div class="modal_encerra_guia_actions">
                            <input type="hidden" id="servidor_id" name="servidor_id" value="<?php echo $exibeId; ?>" />
                            <button type="button" class="btn btn_green fl_right j_button">Sim, desativar!</button>
                            <h4 class="modal_encerra_close btn btn_red" value="<?php echo  $exibeId; ?>">x</h4>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </form>
                </div>
            </div>
            
        <?php
         endforeach;
        ?>

    </table>

    <div id="paginator">
        <?php
//PAGINAÇÃO
        $total = $num_servidores;

        $paginas = ceil($total / $maximo);
        $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

        echo "<a href=admin_eservidores.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

        for ($i = $pag - $links; $i <= $pag - 1; $i++):
            if ($i <= 0):
                
            else:
                echo"<a href=admin_eservidores.php?pag={$i}>{$i}</a>&nbsp;&nbsp;&nbsp;";
            endif;
        endfor;
        echo "<h1>$pag</h1>";

        for ($i = $pag + 1; $i <= $pag + $links; $i++):
            if ($i > $paginas):
                
            else:
                echo "<a href=admin_eservidores.php?pag={$i}>{$i}</a>&nbsp;&nbsp;&nbsp;";
            endif;
        endfor;
        echo "<a href=admin_eservidores.php?pag={$paginas}>Última</a>&nbsp;&nbsp;&nbsp;";
        ?>
    </div>
    <?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
?>      

<?php include_once "includes/inc_footer.php"; ?>

<script src="ajax/js/desativa_servidor.js"></script>    
<script type="text/javascript">
    $(".modal_encerra_close").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra"+str).style.display = "none";
    });

    $(".btn_ativaServidor").click(function() {
        var str = $(this).attr("value");
        document.getElementById("modalEncerra"+str).style.display = "block";
    });
</script>  