<?php
include_once "includes/inc_header.php";

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 2 || $_SESSION['autUser']['admin'] == 3) {
    
    //PAGINAÇÃO
    $pag = $_GET["pag"];
    if ($pag >= '1'):
        $pag = $pag;
    else:
        $pag = '1';
    endif;
    $maximo = '15'; //RESULTADOS POR PÁGINA
    $inicio = ($pag * $maximo) - $maximo;
    
    //BUSCA SETORES
    $read_setores   = read('setores','ORDER BY setor ASC LIMIT ' . $inicio . ',' . $maximo);
    $count_setores  = read('setores');
    $num_setores    = count($count_setores);        
    
?>

    <h1 class="titulo-secao"><i class="fa fa-cubes"></i> Setores <span><?php echo $num_setores; ?> registros</span></h1>

    <a href="admin_cadSetores.php" class="btn btn_blue fl_right" style="margin-bottom: 30px;" title="Adicionar um setor"><i class="fa fa-plus-circle"></i> SETOR</a>
              
    <?php 
        //DELETAR SETOR
        if (isset($_GET['id_setor'])) {
            $del_id = $_GET['id_setor'];
            $delSetor = delete('setores',"id = {$del_id}");

            if($delSetor){
                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Setor deletado com sucesso!</h6>";
                header("Refresh: 2; url=admin_esetores.php"); 
            }else{
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Erro ao deletar o setor!</h4>";
            }
        }
        
        if($num_setores <= 0):
            echo "<h4 class='ms al'><i class='fa fa-exclamation-triangle fa-2x' style='color: #F90'></i> &nbsp;&nbsp;&nbsp; Não existem setores cadastrados no sistema!</h4>";
        else:
    ?>

    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tb_geral">
        <tr class="tr_header">
            <td align="left" style="padding-left: 10px; width: 40%;" >Setor</td>
            <td align="left">Sigla</td>
            <td align="left" style="padding-left: 25px;">Chefe do Setor</td>
            <td align="center">Ações</td>
        </tr>

        <?php
        foreach ($read_setores as $res) {
            $exibeId = $res['id'];
            $exibeSetor = $res['setor'];
            $exibeAbreviatura = $res['abreviatura'];
            $exibeChefe = $res['chefe'];

            $colorPage++;
            if ($colorPage % 2 == 0) {
                $cor = 'style="background:#f3f3f3;"';
            } else {
                $cor = 'style="background:#fff;"';
            }

            $pegaFoto = read('servidores', "WHERE nome = '$exibeChefe'");
            foreach ($pegaFoto as $foto);
            ?>

            <tr <?php echo $cor; ?> class="lista_itens">
                <td align="left" style="padding-left:10px;"><?php echo $exibeSetor; ?></td>
                <td align="left"><?php echo $exibeAbreviatura; ?></td>
                <td align="left" style="padding-left: 25px;"><?php echo '<img src="imagens/servidores/' . $foto['foto'] . '" height="27px" style="margin:0 20px -5px 0;">' . $exibeChefe; ?></td>  				
                <td align="center"><a href="admin_asetores.php?id_setor=<?php echo $exibeId; ?>" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Editar setor"><i class="fa fa-pencil-square-o"></i></a>&nbsp;&nbsp;&nbsp<a href="admin_esetores.php?id_setor=<?php echo $exibeId; ?>" style="text-decoration:none; color:#033; font-size: 1.7em;" title="Deletar setor"><i class="fa fa-trash-o"></i></a></td>
            </tr>

            <?php
        }
        ?>
    </table>

    <div id="paginator">
        <?php
        //PAGINAÇÃO
            $total = $num_setores;

            $paginas = ceil($total / $maximo);
            $links = '5'; //QUANTIDADE DE LINKS NO PAGINATOR

            echo "<a href=admin_esetores.php?pag=1>Primeira</a>&nbsp;&nbsp;&nbsp;";

            for ($i = $pag - $links; $i <= $pag - 1; $i++) {
                if ($i <= 0) {
                    
                } else {
                    echo"<a href=admin_esetores.php?;pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
                }
            }echo "<h1>$pag</h1>";

            for ($i = $pag + 1; $i <= $pag + $links; $i++) {
                if ($i > $paginas) {
                    
                } else {
                    echo "<a href=admin_esetores.php?pag=$i>$i</a>&nbsp;&nbsp;&nbsp;";
                }
            }
            echo "<a href=admin_esetores.php?pag=$paginas>Última</a>&nbsp;&nbsp;&nbsp;";
        ?>
    </div>
    <?php
    endif;
    } else {
        echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
    }
    ?>      

<?php include_once "includes/inc_footer.php"; ?>