/**
 * page chargée
 */
$(document).ready(function(){
    /**
     * génère la progressbar
     */
    progessBarAdmin();
});

/**
 * gère la progess barre des permaence famille
 */
function progessBarAdmin()
{
    console.log(familles);
    familles.forEach((item, index) => {
        if (index !== "undefined" && typeof item !== "undefined") {
            $('#progessBarPermanence'+index).css({
                'width': item + '%',
                'background-color': getColorProgressBar(item)
            })
        }
    });
}

/**
 * Définit la couleur de la barrre de progression en fonction du pourcentage
 * @param pourcentage - le pourcentage de progression
 * @returns {string}
 */
function getColorProgressBar(pourcentage) {
    if (pourcentage <= 5) {
        return '#f63a0f';
    } else if (pourcentage <= 25) {
        return '#f27011';
    } else if (pourcentage <= 50) {
        return '#f2b01e';
    } else if (pourcentage <= 75) {
        return '#f2d31b';
    } else if (pourcentage < 100) {
        return '#b1e02c';
    } else if (pourcentage <= 100) {
        return '#46e035';
    }
    return '#fff6f8';
}
