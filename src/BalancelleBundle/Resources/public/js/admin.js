$("#structures").change(function () {
    var selectedStructure = $(this).children("option:selected").val();
    let checked = 0;
    if ($("#activeCheckbox").is(':checked')) {
        checked = 1;
    }
    let path = pathSelectStructure + '/' + selectedStructure + '/' + checked
    window.location.href = path;
});

/**
 * Gère les boutons pour voir les objets actifs ou non dans un dataTable
 * Il faut déclarer un path nommé pathActiveCheckbox pour le chemin de redirection
 * dans le template concerné.
 * Il faut utiliser une checkbox nommée activeCheckbox
 */
$("#activeCheckbox").on('click', function () {
    let checked = 0;
    if ($("#activeCheckbox").is(':checked')) {
        checked = 1;
    }
    window.location.href = pathActiveCheckbox + "/" + checked;
})