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

/*
$(function () {
    $('#calendar-holder').fullCalendar({
        locale: 'fr',
        header: {
            left: 'prev, next, today',
            center: 'title',
            right: 'month, basicWeek, basicDay'
        },
        businessHours: {
        start: '08:00',
            end: '18:00',
            dow: [1, 2, 3, 4, 5]
        },
        lazyFetching: true,
        navLinks: true,
        selectable: true,
        editable: false,
        eventDurationEditable: false,
        displayEventTime: false,
        eventSources: [
            {
                url: loadEvent,
                type: 'POST',
                data: {
                    filters: {}
                },
                error: function () {
                    alert('There was an error while fetching FullCalendar!');
                }
            }
        ]
    });

});*/

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