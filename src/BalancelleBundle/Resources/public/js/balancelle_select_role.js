jQuery(document).ready(function($) {
    
    if ($("#balancellebundle_user_roleParent").is(':checked')) {
        $('#listeEnfants').show('slow');
    }
    
    $("#balancellebundle_user_roleParent").click(function(){
       /*
        * On vérifie si la checkbox est coché 
        */
       if ($("#balancellebundle_user_roleParent").is(':checked') ){
           $('#listeEnfants').show('slow');
       } else {
           $('#listeEnfants').hide('slow');
       }
    });
});