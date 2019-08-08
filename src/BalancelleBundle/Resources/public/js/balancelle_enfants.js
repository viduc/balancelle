$(document).ready(function(){
    /* ajout de l'autocomplétion sur le bouton ajout */
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

    /* ajout de l'action sur le bouton ajouter de la fenêtre modal enfant */
    $("#btnAjouterEnfantModal").click(function(){
        let idEnfant = $("#modalEnfantSelectionId").val();
        let idFamille = $("#idFamille").val();
        if (idEnfant) {
            $.ajax({
                url: pathEnfantAjouter,
                method: "post",
                data: {idEnfant: idEnfant, idFamille: idFamille},
                success: function (result) {
                    parent.location.reload();
                }
            });
        }
    });

    /* ajout de l'action sur le bouton créer de la fenêtre modal enfant */
    $("#btnCreerEnfantModal").click(function(){
        let prenomEnfant = $("#prenomEnfantModal").val();
        let nomEnfant = $("#nomEnfantModal").val();
        let dateNaissanceEnfant = $("#dateNaissanceEnfantModal").val();
        let idStructure = $("select#structureSelect").val();
        let idFamille = $("#idFamille").val();
        $.ajax({
            url: pathEnfantCreer,
            method: "post",
            data: {
                prenomEnfant: prenomEnfant,
                nomEnfant : nomEnfant,
                dateNaissanceEnfant: dateNaissanceEnfant,
                idFamille: idFamille,
                idStructure: idStructure
            },
            success: function (result) {
                parent.location.reload();
            }
        });
    });

    /* ajout de l'action sur le select enfant de la fenêtre modal enfant */
    $("#enfant-select").change(function(){
        $("#modalEnfantSelectionAffiche").val($( "select#enfant-select option:selected" ).text());
        $("#modalEnfantSelectionId").val($( "select#enfant-select").val());
    });

});




