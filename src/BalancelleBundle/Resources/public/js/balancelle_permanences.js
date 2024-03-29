
/**
 * Chargement de la page
 */
window.onload = function() {

};

/**
 * page chargée
 */
$(document).ready(function(){
    /**
     * génère la progressbar
     */
    progessBar();
});

/**
 * Chargement des évènements du calendrier
 */
if (typeof load !== "undefined") {console.log(load)
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
            plugins: ['interaction', 'dayGrid', 'timeGrid'], // https://fullcalendar.io/docs/plugin-index
            timeZone: 'UTC',
            columnHeaderFormat: {
                weekday: 'long'
            },
            buttonText: {
                today: "aujourd'hui",
                month: 'mois',
                week: 'semaine',
                day: 'jour',
                list: 'list'
            }
        });
        calendar.render();
    });
}
/**
 * gère la progess barre des permaence famille
 */
function progessBar()
{
    if (typeof pourcentagePermanenceFaite !== "undefined") {
        for (let i in pourcentagePermanenceFaite) {
            console.log(pourcentagePermanenceFaite[i]); // affiche 0, 1, 2, "toto" dans la console
            var color = '#fff6f8';
            if (pourcentagePermanenceFaite[i] <= 5) {
                color = '#f63a0f';
            } else if (pourcentagePermanenceFaite[i] <= 25) {
                color = '#f27011';
            } else if (pourcentagePermanenceFaite[i] <= 50) {
                color = '#f2b01e';
            } else if (pourcentagePermanenceFaite[i] <= 75) {
                color = '#f2d31b';
            } else if (pourcentagePermanenceFaite[i] < 100) {
                color = '#b1e02c';
            } else if (pourcentagePermanenceFaite[i] <= 100) {
                color = '#46e035';
            }
            $('#progessBarPermanence'+i).css({
                'width': pourcentagePermanenceFaite[i] + '%',
                'background-color': color
            })
        }
    }
}

/**
 * gestion du bouton de suppression d'une famille d'une permanence
 */
$("#btnSeDesinscrire").click(function(){
    $.confirm({
        title: "Confirmation de la désinscription",
        content: "Etes vous sure de vouloir vous désinscrire de cette permanence?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    $.ajax({
                        url: pathPermanenceDesinscription,
                        method: "post",
                        dataType : 'json',
                        data: {idPermanence: idPermanence},
                        success: function (result) {
                            parent.location.reload();
                        }
                    });
                }
            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                {}
            }
        }
    });
});

/**
 * gestion du bouton de demande d'échange d'une permanence
 */
$("#btnEchanger").click(function(){
    $.confirm({
        title: "Confirmation de la demande d'échange",
        content: "Etes vous sure de vouloir vous proposer cette permanence à l'échange ?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    echangerPermanence(true);
                }
            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                {}
            }
        }
    });
});

/**
 * gestion du bouton de demande d'échange d'une permanence
 */
$("#btnAnnulerEchanger").click(function(){
    $.confirm({
        title: "Confirmation de la demande d' annulation d' échange",
        content: "Etes vous sure de vouloir annuler l'échange de cette permanence?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    echangerPermanence(false);
                }
            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                {}
            }
        }
    });
});

/**
 * gestion du bouton de demande d'échange d'une permanence
 */
$("#btnAccepterEchanger").click(function(){
    $.confirm({
        title: "Echange de permanence",
        content: "Etes vous sure de vouloir accepter cet échange de permanence?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    echangerPermanence('accept');
                }
            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                {}
            }
        }
    });
});

function echangerPermanence(action)
{
    $.ajax({
        url: pathPermanenceEchange,
        method: "post",
        dataType : 'json',
        data: {idPermanence: idPermanence, action: action},
        success: function (result) {
            parent.location.reload();
        }
    });
}

/**
 * gestion du bouton de suppression d'une famille d'une permanence
 */
$("#btnSupprimerFamille").click(function(){
    $.confirm({
        title: "Confirmation de la désinscription",
        content: "Etes vous sure de vouloir désinscrire cette famille?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    $.ajax({
                        url: pathFamilleDesinscrire,
                        method: "post",
                        dataType : 'json',
                        data: {idPermanence: idPermanence},
                        success: function (result) {
                            parent.location.reload();
                        }
                    });
                }
            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                {}
            }
        }
    });
});

/* ajout de l'autocomplétion sur le bouton ajout */
$("#btnAjouterFamille").click(function(){
    $("#modalAjoutFamille").appendTo("body").modal('show');
});

/**
 * gestion du bouton de suppression d'une permanence
 */
$("#btnSupprimerPermanence").click(function(){
    $.confirm({
        title: "Confirmation de la suppression",
        content: "Etes vous sure de vouloir supprimer cette permanence?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    $.ajax({
                        url: pathPermanenceSuppression,
                        method: "post",
                        dataType : 'json',
                        data: {idPermanence: idPermanence},
                        success: function (result) {
                            window.location.replace(result);
                        }
                    });
                }
            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                {}
            }
        }
    });
});

/* ajout de l'action sur le bouton ajouter de la fenêtre modal famille */
$("#btnAjouterFamilleModal").click(function(){
    let idFamille = $("#modalFamilleSelectionId").val();
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

/**
 * lorsque l'on change la demie journée de la permence
 */
$('input[type=radio][name=demiejournee]').change(function() {
    if (this.value == 'matinPermanence') {
        changeDatePermanence('matinPermanence');
    } else {
        changeDatePermanence('amPermanence')
    }
});

/**
 * Change l'heure de la permanence (input)
 * @param demieJournee
 */
function changeDatePermanence(demieJournee) {
    if (typeof debutMatin !== "undefined"
        && typeof finMatin !== "undefined"
        && typeof debutAM !== "undefined"
        && typeof finAM !== "undefined") {
        let date = $('#balancellebundle_permanence_debut').val();
        if (typeof date !== "undefined") {
            let splitDate = date.split((' '));
            if (demieJournee == 'matinPermanence') {
                $('#balancellebundle_permanence_debut').val(splitDate[0] + ' ' + debutMatin);
                $('#balancellebundle_permanence_fin').val(splitDate[0] + ' ' + finMatin);
            } else {
                $('#balancellebundle_permanence_debut').val(splitDate[0] + ' ' + debutAM);
                $('#balancellebundle_permanence_fin').val(splitDate[0] + ' ' + finAM);
            }
        }
    }
}

/**
 * gestion du bouton de suppression d'une famille d'une permanence
 */
$("#btnEnvoyerRappelPermanence").click(function(){
    $.confirm({
        title: "Confirmation",
        content: "Etes vous sur de vouloir envoyer un rappel pour cette" +
            "  permanence ?",
        buttons: {
            confirmer: {
                text: 'Confirmer',
                btnClass: 'btn-blue',
                action: function()
                {
                    $.ajax({
                        url: pathEvoyerRappelPermanence,
                        method: "post",
                        dataType : 'json',
                        data: {id: idPermanence},
                        success: function (result) {
                            alert('Le rappel a bien été envoyé aux familles');
                        },
                        error: function (error) {
                            alert('Une erreur est apparue, veillez ressayer plus tard');
                        }
                    });
                    return true;
                }

            },
            annuler: {
                text: 'Annuler',
                btnClass: 'btn-red',
                action: function()
                { }
            }
        }
    });
});
changeDatePermanence('matinPermanence');