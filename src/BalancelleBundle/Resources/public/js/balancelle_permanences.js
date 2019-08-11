document.addEventListener('DOMContentLoaded', () => {
    var calendarEl = document.getElementById('calendar-holder');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        defaultView: 'dayGridMonth',
        editable: true,
        lazyFetching: true,
        navLinks: true,
        selectable: true,
        eventDurationEditable: false,
        displayEventTime: false,
        eventSources: [
            {
                url: load,
                type: 'POST',
                data: {
                    filters: JSON.stringify({})
                },
                error: function () {
                    alert('There was an error while fetching FullCalendar!');
                }
            }
        ],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        plugins: [ 'interaction', 'dayGrid', 'timeGrid' ], // https://fullcalendar.io/docs/plugin-index
        timeZone: 'UTC',
        columnHeaderFormat: {
            weekday: 'long'
        },
        buttonText: {
            today:    "aujourd'hui",
            month:    'mois',
            week:     'semaine',
            day:      'jour',
            list:     'list'
        }
    });
    calendar.render();
});

window.onload = function() {
    progessBar();
    console.log(pourcentagePermanenceFaite);

}

function progessBar()
{
    var color = '#fff6f8'
    if (pourcentagePermanenceFaite <= 5) {
        color = '#f63a0f';
    } else if (pourcentagePermanenceFaite <= 25) {
        color = '#f27011';
    } else if (pourcentagePermanenceFaite <= 50) {
        color ='#f2b01e';
    } else if (pourcentagePermanenceFaite <= 75) {
        color = '#f2d31b';
    } else if (pourcentagePermanenceFaite < 100) {
        color = '#b1e02c';
    } else if (pourcentagePermanenceFaite <= 100) {
        color = '#46e035';
    }
    $('#progessBarPermanence').css({
        'width': pourcentagePermanenceFaite+'%',
        'background-color': color
    })
}

$(document).ready(function(){
    /* ajout de l'autocomplétion sur le bouton ajout */
    $("#btnAjouterFamille").click(function(){
        $("#modalAjoutFamille").appendTo("body").modal('show');
    });

    /* gestion de l'autocomplétion sur l'input rechercheFamille */
    $(document).on("click", "#rechercherFamille", function() {
        $(this).autocomplete({
            source: function(requete, reponse){
                $.ajax({
                    url: pathFamilleAUtocomplete,
                    method: "post",
                    dataType : 'json',
                    data: {recherche: $('#rechercherFamille').val()},
                    success: function (result) {
                        reponse($.map(result, function (objet) {
                            return {label : objet.label, value : objet.value};
                        }));
                    }
                });
            },
            minLength : 3,
            select : function(event, ui){ // lors de la sélection d'une proposition
                event.preventDefault();
                $("#rechercherFamille").val( ui.item.label );
                $("#modalFamilleSelectionAffiche").val( ui.item.label ); // on ajoute la description de l'objet dans un bloc
                $("#modalFamilleSelectionId").val(ui.item.value);
            },
            focus: function(event, ui) {
                event.preventDefault();
                $("#rechercherFamille").val(ui.item.label);
            }
        });
    });

    /* ajout de l'action sur le bouton ajouter de la fenêtre modal famille */
    $("#btnAjouterFamilleModal").click(function(){
        let idFamille = $("#modalFamilleSelectionId").val();console.log(idFamille);
        //let idPermanence = $("#idPermanence").val();
        if (idFamille) {
            $.ajax({
                url: pathFamilleAjouter,
                method: "post",
                data: {idFamille: idFamille, idPermanence: idPermanence},
                success: function (result) {
                    parent.location.reload();
                }
            });
        }
    });

    /* ajout de l'action sur le select enfant de la fenêtre modal enfant */
    $("#famille-select").change(function(){
        $("#modalFamilleSelectionAffiche").val($( "select#famille-select option:selected" ).text());
        $("#modalFamilleSelectionId").val($( "select#famille-select").val());
    });

});