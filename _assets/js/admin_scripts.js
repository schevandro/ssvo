$(function(){
	//Botões de Filtro de Solicitações
	$( ".btn_buscaData" ).click(function() {
	  $( ".search_data" ).show("slow");
	  $( ".btn_buscaData" ).addClass( "selecionada" );
	  $( ".search_servidor" ).hide("slow");
	  $( ".search_veiculo" ).hide("slow");
	  $( ".btn_buscaServidor" ).removeClass( "selecionada" );
	  $( ".btn_buscaVeiculo" ).removeClass( "selecionada" );
	  $( ".btn_fechar" ).show( "slow" );
	});
	
	
	$( ".btn_buscaServidor" ).click(function() {
	  $( ".search_servidor" ).show("slow");
	  $( ".btn_buscaServidor" ).addClass( "selecionada" );
	  $( ".search_data" ).hide("slow");
	  $( ".search_veiculo" ).hide("slow");
	  $( ".btn_buscaData" ).removeClass( "selecionada" );
	  $( ".btn_buscaVeiculo" ).removeClass( "selecionada" );
	  $( ".btn_fechar" ).show( "slow" );
	});
	
	$( ".btn_buscaVeiculo" ).click(function() {
	  $( ".search_veiculo" ).show("slow");
	  $( ".btn_buscaVeiculo" ).addClass( "selecionada" );
	  $( ".search_data" ).hide("slow");
	  $( ".search_servidor" ).hide("slow");
	  $( ".btn_buscaServidor" ).removeClass( "selecionada" );
	  $( ".btn_buscaData" ).removeClass( "selecionada" );
	  $( ".btn_fechar" ).show( "slow" );
	});
	
	$( ".btn_fechar" ).click(function() {
	  $( ".search_data" ).hide("slow");
	  $( ".search_servidor" ).hide("slow");
	  $( ".search_veiculo" ).hide("slow");
	  $( ".btn_buscaData" ).removeClass( "selecionada" );
	  $( ".btn_buscaServidor" ).removeClass( "selecionada" );
	  $( ".btn_buscaVeiculo" ).removeClass( "selecionada" );
	  
	  $( ".btn_fechar" ).hide( "slow" );
	});
	
	//Mostra detalhes das guias encerradas
	$( ".detalhaEncerrada" ).click(function() {
	  $( ".mostraDetalhes" ).show("slow");
	  $( ".detalhaEncerrada" ).hide("fast");
	  $( ".detalhaEncerradaOculta" ).show("fast");
	});
	
	$( ".detalhaEncerradaOculta" ).click(function() {
	  $( ".mostraDetalhes" ).hide("slow");
	  $( ".detalhaEncerradaOculta" ).hide("fast");
	  $( ".detalhaEncerrada" ).show("fast");
	});
	
	//Mostra form para cancelar uma solicitação passada e autorizada
	$( ".cancelaAutorizada" ).click(function() {
	  $( ".formCancelaAutorizada" ).show("slow");
	  $( ".cancelaAutorizada" ).hide("fast");
	  $( ".cancelaAutorizadaOculta" ).show("fast");
	});
	$( ".cancelaAutorizadaOculta" ).click(function() {
	  $( ".formCancelaAutorizada" ).hide("slow");
	  $( ".cancelaAutorizada" ).show("fast");
	  $( ".cancelaAutorizadaOculta" ).hide("fast");
	});
	
	//Mostra seleção de veículo quando autorizar uma guia
	//Autorizar
	$( ".btn_autorizarGuia" ).click(function() {
	  $( ".naoAutorizarGuia" ).hide();
	  $( ".autorizarGuia" ).show();
	  $( ".cancelaGuia" ).hide();
	});
	$( ".fechar_listaVeiculos" ).click(function() {
	  $( ".autorizarGuia" ).hide();
	});
	//Não autorizar
	$( ".btn_naoAutorizarGuia" ).click(function() {
	  $( ".autorizarGuia" ).hide();
	  $( ".naoAutorizarGuia" ).show();
	  $( ".cancelaGuia" ).hide();
	});
	$( ".fechar_motivo" ).click(function() {
	  $( ".naoAutorizarGuia" ).hide();
	});
	//Cancelar
	$( ".btn_cancelarGuia" ).click(function() {
	  $( ".cancelaGuia" ).show();
	  $( ".autorizarGuia" ).hide();
	  $( ".naoAutorizarGuia" ).hide();
	});
	
	$( ".fechar_cancela" ).click(function() {
	  $( ".cancelaGuia" ).hide();
	});
	
	//Exibe situação da página de edição dos veículos
	$( ".btn_desativaVeiculo" ).click(function() {
	  $( ".formMotivoDesativa" ).show();
	});
	
	$( ".cancelaDesativacao" ).click(function() {
	  $( ".formMotivoDesativa" ).hide();
	});
	
	$( ".btn_ativaVeiculo" ).click(function() {
	  $( ".formAtivaVeiculo" ).fadeIn();
	});
	
	$( ".cancelaAtivacao" ).click(function() {
	  $( ".formAtivaVeiculo" ).fadeOut();
	});
	
	//Exibição dos avisos de veículos no painel inicial
	$( ".atencaoVeiculo" ).click(function() {
	  var str = $( this ).attr("value");
	  //"."+str para pegar o nome específico da div da referida viatura (que é definido pela placa do veículo
	  $( "."+str ).slideToggle("slow");
	});
        
        $( ".atencaoManut" ).click(function() {
	  var str = $( this ).attr("value");
	  //"."+str para pegar o nome específico da div da referida viatura (que é definido pela placa do veículo
	  $( "."+str ).slideToggle("slow");
	});
	
	$( ".cancelaDesativacao" ).click(function() {
	  $( ".formMotivoDesativa" ).hide("slow");
	});
});
