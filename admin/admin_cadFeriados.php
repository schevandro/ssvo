<?php
include_once "includes/inc_header.php";

//Selecionar o servidor
$sql_busca = read('servidores',"ORDER BY nome ASC");
		  	  
foreach ($sql_busca as $res):
  $exibeId 		      = $res['id'];
  $exibeNome		  = $res['nome'];
  $exibeSiape		  = $res['siape'];
endforeach;	

if($_SESSION['autUser']['admin'] == 1 || $_SESSION['autUser']['admin'] == 3):
    
?>

    <h1 class="titulo-secao"><i class="fa fa-calendar-plus-o"></i> Cadastrar novo feriado</h1>

<?php
    echo "<a href='admin_eferiados.php' class='btn btn_green fl_right' style='margin-bottom: 30px; margin-left= 10px;' title='Listar Feriados'><i class='fa fa-list'></i> Listar Feriados</a>";
    if (isset($_POST['cad_feriado'])):
		
        //Receber dados do Formulário
        $f['dia']            = $_POST['dia'];
        $f['mes']            = $_POST['mes'];
        $f['ano']            = $_POST['ano'];
        $f['descricao']      = $_POST['descricao'];
        $f['feriado']        = $_POST['feriado'];

        if($f['dia'] and $f['mes'] and $f['descricao'] != ""):

            $sql_cadastra = create('feriados', $f);
            
            if($sql_cadastra):
                echo "<h6 class='ms ok'><i class='fa fa-check-square-o'></i>&nbsp&nbsp&nbsp Feriado cadastrado com sucesso!</h6>";
                header('Refresh: 2;url=admin_eferiados.php');
            else:
                echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Erro ao cadastrar o feriado!</h4>";
                header('Refresh: 2;url=admin_cadFeriados.php');
            endif;

        else:
            echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp; Informe todos os dados solicitados! </h4>";
        endif;
    endif;
?> 
      
    <form name="cad_feriado" method="post" action="" enctype="multipart/form-data">
        <fieldset class="user_altera_dados">
            <h2 class="form_sub_titulo_100"><?php $este_ano = date('Y'); echo $este_ano; ?> | Informe os seguintes dados:</h2>
            <label class="label_medio" style="margin-top: 25px;">
                <span>Data do Feriado:</span>
                <select name="dia" id="dia" class="select_small">
                    <option value="-1" selected disabled>Dia</option>
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
                <select name="mes" id="mes" class="select_small">
                    <option value="-1" selected disabled>Mês</option>
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
            </label>
            <label class="label_medio" style="margin-top: 25px;">
                <span>Descrição do Feriado</span>
                <input type="text" name="descricao" id="descricao" />
            </label>  
            <label>*Não é necessário cadastrar sábados e domingos</label>
            <input type="hidden" id="ano" name="ano" value="<?php $mostra_ano = date('Y'); echo $mostra_ano;?>" />
            <input type="hidden" id="feriado" name="feriado" value="1" />
            <input type="submit" name="cad_feriado" value="CADASTRAR" id="cad_feriado" class="btn btn_altera btn_green fl_right" />      
        </fieldset>        
    </form>
     
<?php
else:
    echo "<h4 class='ms no'><i class='fa fa-exclamation-circle fa-2x' style='color: #F00'></i> &nbsp;&nbsp;&nbsp;Seu nível de acesso não permite visualizar esta página!</h4>";
endif;
include_once "includes/inc_footer.php";
?>