/**
 * Activation du datepicker au chargement de la page
 * @type type
 */
jQuery(document).ready(function() {

    $(".js-datepicker").datepicker({
        dateFormat: "dd/mm/yy", 
        changeYear: true, 
        changeMonth: true, 
        yearRange: "-100:",
        onChangeMonthYear: function (year, month) {
            var $datepicker = jQuery(this);
            var date = new Date($datepicker.datepicker("getDate"));
            var lastDayOfMonth = new Date(year, month, 0).getDate();
            var preservedDay = Math.min(lastDayOfMonth, Math.max(1, date.getDate()));
            $datepicker.datepicker("setDate", preservedDay + "/" + month  + "/" + year);
        }
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