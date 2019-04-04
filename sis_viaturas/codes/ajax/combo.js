$(function() {
    $('.j_loadstate').change(function() {
        var uf = $('.j_loadstate');
        var city = $('.j_loadcity');

        city.attr('disabled', 'true');
        uf.attr('disabled', 'true');
		
        city.html('<option value=""> Carregando cidades... </option>');

        $.post('codes/ajax/city.php', {estado: $(this).val()}, function(cityes) {
            city.html(cityes).removeAttr('disabled');
            uf.removeAttr('disabled');
        });
    });
});

$(function() {
    $('.j_loadstate_dois').change(function() {
        var uf = $('.j_loadstate_dois');
        var city = $('.j_loadcity_dois');

        city.attr('disabled', 'true');
        uf.attr('disabled', 'true');
		
        city.html('<option value=""> Carregando cidades... </option>');

        $.post('codes/ajax/city.php', {estado: $(this).val()}, function(cityes) {
            city.html(cityes).removeAttr('disabled');
            uf.removeAttr('disabled');
        });
    });
});

$(function() {
    $('.j_loadstate_tres').change(function() {
        var uf = $('.j_loadstate_tres');
        var city = $('.j_loadcity_tres');

        city.attr('disabled', 'true');
        uf.attr('disabled', 'true');
		
        city.html('<option value=""> Carregando cidades... </option>');

        $.post('codes/ajax/city.php', {estado: $(this).val()}, function(cityes) {
            city.html(cityes).removeAttr('disabled');
            uf.removeAttr('disabled');
        });
    });
});