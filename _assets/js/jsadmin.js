$(function () {
    //MOBILE MENU CONTROL
    $('.mobile_menu').click(function () {
        if ($('.dashboard_nav').css('left') !== '-220px') {
            $('.dashboard_nav').animate({left: '-220px'}, 300);
            $('.dashboard_fix').animate({'margin-left': '0px'}, 300);
        } else {
            $('.dashboard_nav').animate({left: '0px'}, 300);
            $('.dashboard_fix').animate({'margin-left': '200px'}, 300);
        }
    });
});


//SCRIPTS DE PÁGINAS

//Página asolicitacoes.php
function optionCheck(){
	var option = document.getElementById("muda_sit").value;
	if(option == ""){
		document.getElementById("guia_autorizada").style.visibility ="hidden";
		document.getElementById("guia_Nautorizada").style.visibility ="hidden";
		document.getElementById("guia_autorizada").style.marginTop = "-200px";
		document.getElementById("guia_Nautorizada").style.marginTop = "-250px";
	}
	if(option == "Autorizada"){
		document.getElementById("guia_autorizada").style.visibility ="visible";
		document.getElementById("guia_autorizada").style.marginTop = "0";
		document.getElementById("guia_Nautorizada").style.visibility ="hidden";
		document.getElementById("guia_Nautorizada").style.marginTop = "-250px";
	}
	if(option == "Nao Autorizada"){
		document.getElementById("guia_autorizada").style.visibility ="hidden";
		document.getElementById("guia_autorizada").style.marginTop = "-200px";
		document.getElementById("guia_Nautorizada").style.visibility ="visible";
		document.getElementById("guia_Nautorizada").style.marginTop = "0";
	}
}
