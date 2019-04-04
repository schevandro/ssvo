$(function(){
	//Botão para fechar div de encerramento de guia
	$( ".fechar_divEncerra" ).click(function() {	
		$( ".encerrarGuia" ).hide("fast");
		$( ".encerraForm" ).hide( "fast" );
	});	
});

//Somente autorizar números na hora de chagada
function SomenteNumero(e){
	var tecla=(window.event)?event.keyCode:e.which;   
	if((tecla>47 && tecla<59)) return true;
	else{
		if (tecla==8 || tecla==0) return true;
		else  return false;
	}
}

//Limpa exemplo de hora de chegada
function limpaExemplo(){
	document.getElementById("hora_chegada").value = "";
}

function mostraExemplo(){
	if(document.getElementById("hora_chegada").value == ""){
		document.getElementById("hora_chegada").value = "Ex: 10:15"	
	}
}

//Calcula quilometragem rodada
function calculaKm(){
	var km_cheg = document.getElementById("km_chegada").value;
	var km_said = document.getElementById("km_saida").value;
	if(km_said > km_cheg){
		alert ('ERRO: Quilometragem de chegada menor que quilometragem de saída!');
		var km_perc = '';
	}else{
		var km_perc = km_cheg - km_said;
		document.getElementById("km_percorridos").value = km_perc;  
	}
}






