<?php
$link = mysqli_connect("localhost", "root", "guaranycpfeliz2014", "cpfeliz");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$estado = (string) strip_tags(trim($_POST['estado']));

$busca = "SELECT * FROM app_cidades WHERE cidade_uf = '$estado'";
$escreve = mysqli_query($busca);

sleep(1);

echo "<option value=\"\" disabled selected> Selecione a cidade </option>";
if ($result = mysqli_query($link, $busca )) {

    /* fetch associative array */
    while ($row = mysqli_fetch_assoc($result)) {
	$nome_cidade = utf8_encode($row["cidade_nome"]);
	echo "<option value=\"{$nome_cidade}-\"> {$nome_cidade} </option>";
    }

    /* free result set */
    mysqli_free_result($result);
}

?>
