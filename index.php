<?php
ob_start();
session_start();
require('dts/dbaSis.php');
require('dts/outSis.php');

if (!empty($_SESSION['autUser'])) {
    header('Location: sis_viaturas/painel.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Sistema de Solicitação de Viaturas Oficiais - Campus Feliz</title>

        <meta name="title" content="Painel Administrativo - Campus Feliz" />
        <meta name="description" content="Área restrita aos administradores dos sistemas administrativos do Campus Feliz" />
        <meta name="keywords" content="Login, Recuperar Senha, Campus Feliz" />

        <meta name="author" content="Evandro Schlumpf" />   
        <meta name="url" content="http://sistemas.feliz.ifrs.edu.br" />

        <meta name="language" content="pt-br" /> 
        <meta name="robots" content="NOINDEX,NOFOLLOW" /> 

        <link rel="stylesheet" href="_assets/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="_assets/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="_assets/css/default.css"/>

        <link rel="shortcut icon" href="_assets/img/fav.fw.png"/>
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>

        <script src="_assets/js/jquery.js"></script>

    </head>

    <body>
        <?php
        if (isset($_POST['sendLogin'])) {
            $f['siape'] = mysql_real_escape_string($_POST['siape']);
            $f['senha'] = mysql_real_escape_string($_POST['senha']);
            $f['salva'] = mysql_real_escape_string($_POST['remember']);

            if (strlen($f['senha']) < 8 || strlen($f['senha']) > 12) {
                echo '<div class="trigger-msg alert">A senha deve possuir de <strong>8</strong> a <strong>12</strong> caracteres</div>';
            } else {
                $autSiape = $f['siape'];
                $autSenha = md5($f['senha']);
                $readAutServ = read('servidores', "WHERE siape = '$autSiape'");
                if ($readAutServ) {
                    foreach ($readAutServ as $autUser)
                        ;
                    if ($autUser['ativo'] == 1) {
                        if ($autSiape == $autUser['siape'] && $autSenha == $autUser['senha'] && $autUser['mudarsenha'] == 1) {
                            $_SESSION['mdPass'] = $autUser;
                            header('Location: index.php');
                        } elseif ($autSiape == $autUser['siape'] && $autSenha == $autUser['senha'] && $autUser['mudarsenha'] == 0) {
                            if ($autUser['admin'] == 1 || $autUser['admin'] == 2 || $autUser['admin'] == 3 || $autUser['admin'] == 9) {
                                if ($f['salva']) {
                                    $cookiesalva = base64_encode($autSiape) . '&' . base64_encode($f['senha']);
                                    setcookie('autUser', $cookiesalva, time() + 60 * 60 * 24 * 30, '/');
                                } else {
                                    setcookie('autUser', '', time() - 3600, '/');
                                }
                                $_SESSION['autUser'] = $autUser;
                                //Insere dados na tabela acessos
                                $acess['nivel'] = $_SESSION['autUser']['admin'];
                                $acess['siape'] = $_SESSION['autUser']['siape'];
                                $acess['email'] = $_SESSION['autUser']['email'];
                                $acess['login'] = date('Y-m-d H:i:s');
                                $acess['chave'] = md5($acess['login']);
                                $_SESSION['autUser']['chave_acesso'] = $acess['chave'];
                                create('acessos', $acess);
                                header('Location: sis_viaturas/painel.php');
                            } else {
                                echo '<div class="trigger-msg error">ERRO: informe o erro de nível de acesso ao administrador do sistema!</div>';
                            }
                        } else {
                            echo '<div class="trigger-msg error">A SENHA não confere!</div>';
                        }
                    } else {
                        echo '<div class="trigger-msg alert">Conta Desativada. Contate o Administrador.</div>';
                    }
                } else {
                    echo '<div class="trigger-msg alert">O SIAPE informado não existe em nossa base de dados!</div>';
                }
            }
        } 
        
        
        if(!$_SESSION['mdPass']){
        
            if (!$_GET['remember']) {
        ?>

        <div class="index-left"></div>

        <div class="container">
            <h1>Solicitação de Viaturas Oficiais</h1>
            <h2>SSVO</h2>
            <form name="login" class="form-login" action="" method="post">
                <fieldset class="form_left">
                    <div class="logoif"></div>
                </fieldset>
                <fieldset class="form_right">
                    <div class="input-group col-lg-12">
                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                        <input type="text" class="form-control input-lg" name="siape" value="<?php if ($f['siape']) echo $f['siape']; ?>" placeholder="siape" required>
                    </div>

                    <div class="input-group col-lg-12 input-pass">
                        <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                        <input type="password" class="form-control input-lg" name="senha" value="<?php if ($f['senha']) echo $f['senha']; ?>" placeholder="senha" required>
                    </div>

                    <div class="form_action">
                        <div class="logo-mini"></div>
                        <input class="btn btn-acessar" type="submit" name="sendLogin" value="Acessar" />
                    </div>
                </fieldset>
            </form>
            
            <a href="index.php?remember=true" class="link" title="Esqueci minha senha!">Esqueci minha senha</a>                        
        </div>
        
        <?php
            }else {
            if (isset($_POST['sendRecover'])) {
                $recover = mysql_real_escape_string($_POST['email']);
                if (!$recover || !valMail($recover)) {
                    echo '<div class="trigger-msg error">Erro: Campo E-mail está em branco ou em um formato inválido!</div>';
                } else {
                    $readRec = read('servidores', "WHERE email = '$recover'");
                    if (!$readRec) {
                        echo '<div class="trigger-msg alert">Erro: E-mail informado não existe em nossa base de dados ou esta em um formato inválido!</div>';
                    } else {
                        foreach ($readRec as $rec)
                            ;
                        if ($rec['admin'] == 1 || $rec['admin'] == 2 || $rec['admin'] == 3 || $rec['admin'] == 9) {
                            $msg = '<h3 style="font:16px \'Trebuchet MS\', Arial, Helvetica, sans-serif; color:#099;">Prezado(a) ' . $rec['nome'] . ', recupere seu acesso!</h3><p style="font:bold 12px Arial, Helvetica, sans-serif; color:#666;">Estamos entrando em contato pois foi solicitada uma recuperação de senha através de nosso painel administrativo. Verifique logo abaixo os dados de seu usuário:</p><br><p style="font:italic 14px \'Trebuchet MS\', Arial, Helvetica, sans-serif; color:#069">E-mail: ' . $rec['email'] . '<br>Siape (Matricula): ' . $rec['siape'] . '<br>Senha: ' . $rec['code'] . '</p><br><br><p style="font:bold 12px Arial, Helvetica, sans-serif; color:#F00;">Recomendamos que você altere seus dados em seu perfil após efetuar o login!</p><br><br><p style="font:bold 12px Arial, Helvetica, sans-serif; color:#666;">Atenciosamente,<br><br><br><a style="color:#A8CF45" href="http://sistemas.feliz.ifrs.edu.br" title="IFRS - Campus Feliz">IFRS - Campus Feliz</a><br><br><br>em ' . date('d/m/Y à\s H:i:s') . '<br><br><img alt="IFRS - Campus Feliz" title="IFRS - Campus Feliz" src="http://suporte.feliz.ifrs.edu.br/imagens/ifrafeliz.png"></p>';
                            sendMail('Recupere seus dados', $msg, MAILUSER, SITENAME, $rec['email'], $rec['nome']);
                            echo '<div class="trigger-msg accept">Seus dados foram enviados com sucesso para <strong>' . $rec['email'] . '</strong>. Verifique sua caixa de entrada.</div>';
                        } else {
                            echo '<div class="trigger-msg alert">Seu nível não permite acesso a esta área. Você será redirecionado ao login de usuários.</div>';
                            header('Refresh: 5;url=' . BASE . '/pagina/login');
                        }
                    }
                }
            }
        ?>
        
        <div class="index-left"></div>

        <div class="container">
            <h1>Recuperação de Senha de Acesso</h1>
            <h2>Recuperar Senha</h2>
            <form name="recover" action="" method="post" class="form-login">
                <fieldset class="form_left">
                    <div class="logoif"></div>
                </fieldset>
                <fieldset class="form_right">
                    <span>Informe seu e-mail cadastrado:</span><br />
                    <div class="input-group col-lg-12 input-recover">
                        <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                        <input type="email" class="form-control input-lg" name="email" value="<?php if (isset($recover)) echo $recover; ?>" placeholder="e-mail" required>
                    </div>

                    <div class="form_action">
                        <div class="logo-mini"></div>
                        <input class="btn btn-acessar" type="submit" name="sendRecover" value="Enviar" />
                    </div>
                </fieldset>
            </form>
            <a href="index.php" class="link" title="Voltar">Voltar</a>            
        </div>

    <?php
        }
    }else{
        
        //SOLICITAÇÃO DE MUDANÇA DE SENHA PELO SISTEMA
        
        $passId		= $_SESSION['mdPass']['id'];
        $passMuda	= 1;	
        $readMdPass     = read('servidores',"WHERE id = '$passId' AND mudarsenha = '$passMuda'");
        
        if($readMdPass){
            foreach($readMdPass as $mdPass);
            if($mdPass['mudarsenha'] < 1){
                header('Location: index.php');
            }
        }else{
            header('Location: index.php');
        }
	
	if(isset($_POST['sendCancel'])){
            unset($_SESSION['mdPass']);
            header('Location: index.php');
	}

	if(isset($_POST['sendPass'])){
            $f['senha'] = mysql_real_escape_string($_POST['senha']);
            $f['novasenha'] = mysql_real_escape_string($_POST['novasenha']);
            $f['rp_novasenha'] = mysql_real_escape_string($_POST['rp_novasenha']);

            $verfiSenhaAtual	=	read('servidores',"WHERE code = '$f[senha]'");
            $countVerifSenha	=	count($verfiSenhaAtual);
            if($countVerifSenha <= 0){
                    echo '<div class="trigger-msg error">A Senha Atual não confere com a senha cadastrada na base de dados!</div>';
            }else{
                if(strlen($f['novasenha']) < 8 || strlen($f['rp_novasenha']) > 12){
                    echo '<div class="trigger-msg alert">A nova senha deve possuir de <strong>8</strong> a <strong>12</strong> caracteres</div>';
                }else{
                    if($f['novasenha'] != $f['rp_novasenha']){
                        echo '<div class="trigger-msg error">As novas senhas digitadas não conferem!</div>';
                    }else{
                        $up['senha']        = md5($f['novasenha']);
                        $up['code']         = $f['novasenha'];
                        $up['mudarsenha']   = 0;
                        $autSiape           = $mdPass['siape'];

                        $upPass = update('servidores',$up,"siape = '$autSiape'");

                        if($upPass){
                                echo '<div class="trigger-msg accept">Sua senha foi alterada com sucesso. Efetue novo login!</div>';
                                unset($_SESSION['mdPass']);
                                header('Refresh: 2;url=index.php');
                        }else{
                                echo '<div class="trigger-msg error">Erro ao alterar sua senha!</div>';
                        }					
                    }
                }
            }
	}        
    ?>
        
        <div class="index-left"></div>

        <div class="container">
            <h1>Alteração de senha de acesso</h1>
            <h2>Alterar Senha</h2>
            <form name="recover" action="" method="post" class="form-login">
                <fieldset class="form_left">
                    <div class="logoif"></div>
                </fieldset>
                <fieldset class="form_right">
                    <div class="input-group col-lg-12 input-recover">
                        <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                        <input type="password" class="form-control input-lg" name="senha" value="<?php if($f['senha']) echo $f['senha']; ?>" placeholder="Senha atual">
                    </div>
                    <div class="input-group col-lg-12 input-recover">
                        <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                        <input type="password" class="form-control input-lg" name="novasenha" value="<?php if($f['novasenha']) echo $f['novasenha']; ?>" placeholder="Nova senha">
                    </div>
                    <div class="input-group col-lg-12 input-recover">
                        <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                        <input type="password" class="form-control input-lg" name="rp_novasenha" value="<?php if($f['rp_novasenha']) echo $f['rp_novasenha']; ?>" placeholder="Repetir nova senha">
                    </div>

                    <div class="form_action">
                        <div class="logo-mini"></div>
                        <input class="btn btn-success" type="submit" name="sendPass" value="ALTERAR" />
                        <input class="btn btn-danger" type="submit" name="sendCancel" value="CANCELAR" />
                    </div>
                </fieldset>
            </form>           
        </div>
        
        
    <?php
    }
    ?>

    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-63922369-5', 'auto');
        ga('send', 'pageview');

    </script>

    </body>
</html>
<?php ob_end_flush(); ?>
