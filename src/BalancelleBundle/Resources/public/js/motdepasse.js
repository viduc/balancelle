jQuery(document).ready(function() {
    $( "#mdp_submit" ).submit(function( event ) {
        let mdp1 = $("#plainPassword").val();
        let mdp2 = $("#mdp2").val();
        if (mdp2 === "" || mdp1 !== mdp2) {
            alert( "Les mots de passes doivent correspondre" );
            event.preventDefault();
            $("#mdp2").focus();
        }

    });
});