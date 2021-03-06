$(function () {
    
    //ENVIO DE FORMULARIOS
    var errmsg = $('.msg_retorno');
    var forms = $('form[name="ativaServidor"]');
    var botao = $('.j_button');
    var urlpost = 'ajax/ativa_servidor.ajax.php';
    
    botao.attr("type", "submit");
    forms.submit(function () {
        errmsg.fadeOut("fast");
        return false;
    });
    
    function carregando() {
        botao.attr("disabled", "disabled");
        botao.addClass("disabled");
        errmsg.empty().html('<p style="padding: 15px; text-align: center;"><img src="../_assets/img/loader.gif" alt="ativando..." />&nbsp;&nbsp;&nbsp; Aguarde, ativando...</p>').fadeIn("fast");
    }

    function errosend() {
        botao.removeClass("disabled");
        botao.removeAttr("disabled");
        errmsg.empty().html('<p class="ms no"><i class="fa fa-exclamation-circle" style="color: #F00; font-size: 1.4em;"></i>&nbsp;&nbsp; O servidor não pode ser ativado. Informe este erro no email <strong>suporte@feliz.ifrs.edu.br</strong></p>').fadeIn("fast");
    }

    function errodados(mensagem) {
        botao.removeClass("disabled");
        botao.removeAttr("disabled");
        errmsg.empty().html('<p class="ms no">' + mensagem + '</p>').fadeIn("fast");
    }

    function sucesso(mensagem) {
        errmsg.empty().html('<p class="ms ok">'+mensagem+'</p>').fadeIn("fast");       
        setTimeout(function () { location.reload(1); }, 3000);
        setTimeout();
    }

    $.ajaxSetup({
        url: urlpost,
        type: 'POST',
        beforeSend: carregando,
        error: errosend
    });

    //ATIVAÇÃO
    var encerramento = $('form[name="ativaServidor"]');
    encerramento.submit(function () {
        var dados = $(this).serialize();
        var acao = "&acao=reativar";
        var sender = dados + acao;

        $.ajax({
            data: sender,
            success: function (resposta) {
                if (resposta == '1') {
                    errodados('<i class="fa fa-exclamation-circle" style="color: #F00; font-size: 1.4em;"></i>&nbsp;&nbsp;&nbsp; Preencha todos os campos obrigatórios (*)');
                } else if (resposta == '3') {
                    $('form[name="ativaServidor"]')[0].reset();
                    sucesso('<i class="fa fa-check-square-o" style="font-size: 1.4em; color: #337AB7;"></i>&nbsp;&nbsp;&nbsp; Servidor ativado com sucesso!');
                } else {
                    errosend();
                }
            }
        });
    });

});