jQuery(document).ready(
    function () {
        $("#deleteAll").on(
            'click',
            function () {
                $("input[name*='delete_famille']").prop('checked', true);
            }
        );
        $("#deleteAllUndo").on(
            'click',
            function () {
                $("input[name*='delete_famille']").prop('checked', false);
            }
        );
        $("input[name*='removePerm_']").on(
            'click',
            function () {
                let name = this.name
                let splitName = name.split('_')
                addRemoveReinitPerm('remove', splitName[1])
            }
        );
        $("input[name*='addPerm_']").on(
            'click',
            function () {
                let name = this.name
                let splitName = name.split('_')
                addRemoveReinitPerm('add', splitName[1])
            }
        );
        $("input[name*='reinitPerm_']").on(
            'click',
            function () {
                let name = this.name
                let splitName = name.split('_')
                addRemoveReinitPerm('reinit', splitName[1])
            }
        );
        $("input[name*='perm_']").on(
            'change',
            function () {
                let name = this.name
                let splitName = name.split('_')
                verifieLaValeurDuChampPermModifie(splitName[1])
            }
        );
        $("#validerPermanence").on(
            'click',
            function () {
                $.confirm({
                    title: 'Confirmation',
                    content: 'Etes vous sure de vouloir purger l\'année antérieure?',
                    buttons: {
                        confirmer: function () {
                            purgerAnneeAnterieure()
                        },
                        annuler: function () {
                        },
                    }
                });
            }
        );
    }
);

/**
 * Modifie la valeur de l'input des permanences
 * Permet d'ajouter, de supprimer (min 0) ou de réinitialiser la valeur d'origine
 * @param action - le type d'action (add / remove / reinit)
 * @param permId
 */
function addRemoveReinitPerm(action, permId)
{
    let value = $("#perm_" + permId).val()
    if (action === 'remove' && value !== '0') {
        value --
    } else if (action === 'add') {
        value ++
    } else if (action === 'reinit') {
        value = $("#perm_init_" + permId).val()
    }
    $("#perm_" + permId).val(value)
    let bgcolor = "white"
    if (value > $("#perm_init_" + permId).val()) {
        bgcolor = "#27ae60"
    } else if (value < $("#perm_init_" + permId).val()) {
        bgcolor = "#e74c3c"
    }
    $("#perm_" + permId).css("background-color", bgcolor)
}

/**
 * Vérifie si les champs de permanence sont bien numérique
 * @param permId
 */
function verifieLaValeurDuChampPermModifie(permId)
{
    let value = $("#perm_" + permId).val()
    if (!$.isNumeric(value)) {
        alert('La valeur des permanences doit être un chiffre ou un nombre')
        $("#perm_" + permId).val($("#perm_init_" + permId).val())
    }
}

function purgerAnneeAnterieure()
{
    let tableauPurge = [];
    $("input[name*='familleId_']").each(
        function () {
            let id = $(this).val();
            if ($("#delete_famille_"+id).is(':checked')) {
                tableauPurge[id] = 'delete';
            } else {
                tableauPurge[id] = $("#perm_"+id).val()
            }
        }
    )
    $.ajax({
        url: pathPurgerAnnee,
        method: "post",
        dataType : 'json',
        data: {purge: tableauPurge},
        success: function (result) {
            alert(result)
        }
    });
}