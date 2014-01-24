jQuery(document).ready(function(){
    jQuery('.szyfrator #szyfruj').click(function(){
        var tekst = jQuery('.szyfrator #tekst').val();
        var met = jQuery('.szyfrator #szyfr option:selected').val();

        var dane = {
                action: "szyfruj",
                metoda: met,
                t: tekst
            };

        jQuery(".szyfrator #wynik").empty();
        jQuery(".szyfrator #wynik").html("<img src='"+czekajIco+"' />");

        jQuery.post(ajaxUrl, dane, function(response){
            jQuery(".szyfrator #wynik").empty();
            jQuery(".szyfrator #wynik").html(response);
        });

    });
});
