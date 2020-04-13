$("#structures").change(function () {
    var selectedStructure = $(this).children("option:selected").val();
    window.location.href = pathSelectStructure + '/' + selectedStructure;
    /*$.ajax({
        url: pathSelectStructure,
        method: "post",
        data: {id: selectedStructure},
        success: function (result) {
            reponse($.map(result, function (objet) {
                //return objet.prenom + ' ' + objet.nom;
                return {
                    label : objet.label,
                    value : objet.value
                };
            }));
        }
    });*/
    //pas ajax redirect vers url + param
});