<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 3):

    //PAGINAÇÃO
    $pag = $_GET["pag"];
    if($pag >= '1'):
        $pag = $pag;
    else:
        $pag = '1';
    endif;

    $maximo = '15'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;

    $este_ano = date('Y');

    $sql_busca  = read('feriados',"WHERE ano = {$este_ano} ORDER BY mes ASC LIMIT {$inicio},{$maximo}");
    $sql_conta  = read('feriados',"WHERE ano = {$este_ano}");
    $num        = count($sql_conta);
?>
      
    <h1 class="titulo-secao"><i class="fa fa-calendar-times-o"></i> Feriados <span><?php echo $num; ?> registros</span></h1>    
    <a href="admin_cadFeriados.php" class="btn btn_blue fl_right" style="margin-bottom: 30px; margin-left: 10px;" title="Adicionar um feriado"><i class="fa fa-plus-circle"></i> FERIADO</a>
    <a href="admin_eferiadosAnteriores.php" class="btn btn_orange fl_right" title="Feriados de anos anteriores"><i class="fa fa-calendar-times-o"></i> FERIADOS DE ANOS ANTERIORES</a>
          
<?php
    //Deletar um feriado
    if (isset($_GET['id_feriado'])):

        $del_id = $_GET['id_feriado'];

        $sql_deleta = delete('feriados', "id = {$del_id}");

        if($sql_deleta):
            echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Feriado deletado com sucesso!</h6>";
            header("Refresh: 2; url=admin_eferiados.php");
        else:
            echo 'Erro ao deletar o feriado cadastrado'.$error_atualizaDados->getMessage();
        endif;
    endif;
    
    if($num <= 0):
        echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Não existem feriados cadastrados no sistema!</h4>";
    else:
?>      

    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        <tr class="tr_header">
            <td align="center">Data</td>
            <td align="left">Ano</td>
            <td align="left">Descrição</td>
            <td align="center">Deletar</td>
        </tr>                                        
        <?php
            foreach ($sql_busca as $res):
                $exibeId 		= $res['id'];
                $exibeDia  		= $res['dia'];
                $exibeMes 		= $res['mes'];
                $exibeAno		= $res['ano'];
                $exibeDescricao         = $res['descricao'];
                $exibeFeriado   	= $res['feriado'];				  
                
                $colorPage++;
                if ($colorPage % 2 == 0):
                    $cor = 'style="background:#f3f3f3;"';
                else:
                    $cor = 'style="background:#fff;"';
                endif;
	?>
        <tr <?php echo $cor; ?> class="lista_itens">
            <td align="center"><?php echo $exibeDia."/".$exibeMes; ?></td>
            <td align="left"><?php echo $exibeAno; ?></td>
            <td align="left"><?php echo $exibeDescricao; ?></td>
            <td align="center"><a href="admin_eferiados.php?id_feriado=<?php echo $exibeId; ?>" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Deletar setor"><i class="fa fa-trash-o"></i></a></td>
        </tr>
        <?php
            endforeach;
        ?>
    </table>

    <div id="paginator">
        <?php
            //PAGINAÇÃO
            $total = $num;

            $paginas = ceil($total/$maximo);
            $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

            echo "<a href=admin_eferiados.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

            for ($i = $pag-$links; $i <= $pag-1; $i++):
                if ($i <= 0):
                else:
                    echo"<a href=admin_eferiados.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
                endif;
            endfor;
            echo "<h1>$pag</h1>";

            for($i = $pag + 1; $i <= $pag+$links; $i++):
                if($i > $paginas):
                else:
                    echo "<a href=admin_eferiados.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
                endif;
            endfor;
            echo "<a href=admin_eferiados.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";
        ?>
    </div>

<?php
    endif;
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
include_once "includes/inc_footer.php";
?>