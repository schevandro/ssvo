<?php
include_once "includes/inc_header.php";

//Selecionar o servidor
$sql_busca = read('servidores', "WHERE ativo = '1' ORDER BY nome ASC");

foreach ($sql_busca as $res):
    $exibeId = $res['id'];
    $exibeNome = $res['nome'];
    $exibeSiape = $res['siape'];
endforeach;

if ($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 3):

?>

    <h1 class="titulo-secao"><i class="fa fa-building"></i> Gerar Folha Ponto</h1>

    <form name="seleciona_servidor" method="post" action="print.php" target="_blank" enctype="multipart/form-data">
        <h3 class="ano_feriados"><?php $este_ano = date('Y');
        echo $este_ano; ?></h3>
        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100">Selecione o servidor e o mês:</h2>
            <label class="label_medio">
                <span>Servidor:</span>
                <select name="servidor" id="servidor" class="select_medio">
                    <option value="-1" selected>Servidores</option>
                    <?php foreach ($sql_busca as $select_busca) {
                    $busca_selecionada = $select_busca['nome']; ?>
                    <option value="<?php echo $busca_selecionada; ?>"><?php echo $busca_selecionada; ?></option>
                    <?php } ?>
                </select> 
            </label>
            
            <label class="label_medio">
                <span>Selecione o mês:</span>
                <select name="mes" id="mes" class="select_medio">
                    <option value="-1" selected disabled>Selecione</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select> 
            </label>
            
            <label class="label_medio" style="margin-top: 14px;">
                <input type="submit" id="gera_ponto" name="gera_ponto" value="GERAR FOLHA PONTO" class="btn btn_altera btn_blue fl_right" />
            </label>
        </fieldset>        
    </form>


<?php

else:
    echo '<div class="ms no" style="margin-bottom:30px">Seu nível de acesso não permite visualizar esta página!</div>';
endif;
include_once "includes/inc_footer.php";

?>