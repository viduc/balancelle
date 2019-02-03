


$(document).ready(function(){
    $("#btnAjouterEnfant").click(function(){
        $("#modalAjoutEnfant").appendTo("body").modal('show');
    });

    /* gestion de l'autocomplétion sur l'input rechercheEnfant */
    $(document).on("click", "#rechercherEnfant", function() {
        $(this).autocomplete({
            source: function(requete, reponse){
                $.ajax({
                    url: pathEnfantAUtocomplete,
                    method: "post",
                    data: {recherche: $('#rechercherEnfant').val()},
                    success: function (result) {
                        reponse($.map(result, function (objet) {
                            return objet.prenom + ' ' + objet.nom;
                        }));
                    }
                });
            },
            minLength : 3
        });
    });

});




