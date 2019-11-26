[![BALANCELLE](https://labalancelle.yo.fr/prod/web/bundles/app/images/logos/balancelle.jpeg)](https://labalancelle.yo.fr/prod/web/)

**©LA BALANCELLE**   
*Autheur*: Tristan Fleury   
tristan.fleury@labalancelle.yo.fr   
**Site intranet de l'association La Balancelle**    

------------
## [26/11/2019] [v0.0.2][bug38]
    - supression de la possiblité de créer des enfants lors de la création d'une
        famille.
    - modification de la vue famille (edit)
    - correction de bug:
        - pathEditFamille demande id (bug avec création)
        - redirect lors de la suppression enfant (forward -> redirecttoroute)
        
## [23/11/2019] [v0.0.2][task40]
    - correction ajout enfant déjà enregistré à une famille (était redirigé vers
        supprimé si action avant était suppression
    - ajout de la modification de la structure dans l'édition enfant
    - correction bug ajout structure avec path envoyer mail (attente de l'id
        de la structure)
    
## [21/11/2019] [v0.0.2][task35]
    - bloquage de la suppression d'un utilisateur si il est lié à une famille
        - plus de bouton dans la vue edit
        - vérification dans méthode delete du controller
    
## [10/11/2019] [v0.0.2][task53]
    - mise en place envoie de mail par structure
    - gestion des pièces jointes
    - installation ckeditor: sudo apt-get install php7.2-zip
    - ajout du ckeditor
    - ajout du from dans la méthode envoie de mail
    - from de la structure passé en paramètre pour envoie depuis structure
    - ajout d'un envoi de mail utilisateur
    - ajout envoi mail famille
    
## [08/11/2019] [v0.0.2][bug58]
    - correction du bug: séparation de l'instanciation des dates matin et am
        pour la génération des permanences (le setTime de l'am écrasait
        l'instance du matin)
    - récupération des heures de la structure pour générer les permanences
    
## [04/11/2019] [v0.0.2][bug57]
    - correction du bug (test de l'existance de la variable permanence)
    
## [30/10/2019] [v0.0.2][bug52]
    - réinitialisation de la variable to (array) à chaque permanence ciblée
       
## [28/10/2019] [v0.0.2][bug56]
    - correction déconnexion impossible
    - désactivation des assests chart

## [28/10/2019] [v0.0.1][task49]
    Mise en place du chagelog. Mise en place du numéro de
    version.
    Ajout d'un bouton + dans la vue edit user
    
[Plus d'informations](https://labalancelle.yo.fr/prod/web/ "Plus d'informations")