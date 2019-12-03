/**
 * Activation du datepicker au chargement de la page
 * @type type
 */
jQuery(document).ready(function() {
    $(".js-datepicker").datepicker({
        format: 'dd/mm/yyyy',
        changeYear: true,
        changeMonth: true, 
        yearRange: "-100:",
        onChangeMonthYear: function (year, month) {
            let $datepicker = jQuery(this);
            let date = new Date($datepicker.datepicker("getDate"));
            let lastDayOfMonth = new Date(year, month, 0).getDate();
            let preservedDay = Math.min(
                lastDayOfMonth,
                Math.max(1,date.getDate())
            );
            $datepicker.datepicker(
                "setDate",
                preservedDay + "/" + month  + "/" + year
            );
        },
    });

    /**
     * vérification du mail de l'utilisateur en cas de modification
     * si le mail est déjà utilisé par un autre user on envoie une demande
     * de confirmation
     */
    $("#balancellebundle_user_email").focusout(function () {
        verifierMail(
            $("#balancellebundle_user_email").val(),
            userId
        );
    });

    /**
     * cache le bloc bouton en entrée de page
     */
    afficherBoutonValider("cacher");

    if (typeof fileCount !== 'undefined') {
        createAddFile(fileCount);
        fileCount++;
    }

});

/**
 * verifie si un mail est déjà utilisé par un autre utilisateur
 * Si oui demande une confirmation et renvoie un boolean
 */
function verifierMail(mail, id)
{
    $.ajax({
        url: pathVerifierMailUser,
        method: "post",
        data: {
            email: mail,
            id: id
        },
        success: function (result) {
            if (result === "nok") {
                confirmMail();
            } else {
                afficherBoutonValider("voir");
            }
        },
        error: function ( result) {
            alert(result);
        }
    });
}

/**
 * Méthode pour afficher une fenêtre d'alerte pour valider l'utilisation de
 * l'email. Si l'utilisateur annule on supprime l'email entré
 */
function confirmMail()
{
    $.confirm({
        title: "Confirmation de l'email",
        content: "L'email que vous avez entré est déjà utilisé, êtes vous sure" +
            " de vouloir utiliser cet email?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    afficherBoutonValider("voir");
                    return true;
                }

            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                {
                    $("#balancellebundle_user_email").val("");
                }

            }
        }
    });
}

function afficherBoutonValider(action)
{
    $('#bouton_validation').hide();
    if(action === "voir" && $("#balancellebundle_user_email").val()!== "") {
        $('#bouton_validation').show();
    }

}

/**
 * gestion du bouton de suppression d'une famille d'une permanence
 */
$("#btnRenvoyerMail").click(function(){

    $.ajax({
        url: pathREvoyerMailUser,
        method: "post",
        dataType : 'json',
        data: {id: userId},
        success: function (result) {
            //alert(result);
        }
    });

});

/**
 * Gère la gestion des pièces jointes dans un email
 * @param fileCount
 */
function createAddFile(fileCount)
{
    // grab the prototype template
    var newWidget = $("#filesProto").attr('data-prototype');
    // replace the "__name__" used in the id and name of the prototype
    newWidget = newWidget.replace(/__name__/g, fileCount);
    $("#filesBox").append("<div class='row'>" + "<div class='col-md-10'>" + newWidget + "</div></div>");

    // Once the file is added
    $('#balancellebundle_structure_documents_' + fileCount + '_file').on('change', function() {
        // Create another instance of add file button and company
        createAddFile(parseInt(fileCount)+1);
    });
}