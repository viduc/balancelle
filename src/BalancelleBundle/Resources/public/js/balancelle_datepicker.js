/**
 * Activation du datepicker au chargement de la page
 * @type type
 */
jQuery(document).ready(function() {
    //$.datepicker.setDefaults($.datepicker.regional['{{ app.request.locale }}']);
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
});