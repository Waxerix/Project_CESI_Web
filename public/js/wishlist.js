
    // On sélectionne tous les boutons coeur de la page
    const heartButtons = document.querySelectorAll('.btn-heart');

    heartButtons.forEach(button => {
        button.addEventListener('click', function() {
            
            // 1. On récupère l'ID de l'offre
            const offerId = this.dataset.id;
            
            // 2. On vérifie s'il était déjà rouge (actif)
            const isActive = this.classList.contains('active');

            // 3. ON CHANGE LE VISUEL IMMÉDIATEMENT (Pour que l'utilisateur ait un retour fluide)
            this.classList.toggle('active');

            // 4. On choisit la bonne URL selon l'action (ajouter ou supprimer)
            const url = isActive ? `/wishlist/delete/${offerId}` : `/wishlist/add/${offerId}`;

            // 5. On envoie la requête en arrière-plan (AJAX)
            fetch(url)
                .then(response => {
                    // Si la page /wishlist/add plante (ex: utilisateur non connecté)
                    if (!response.ok) {
                        // On annule le rougissement du coeur
                        this.classList.toggle('active');
                        alert("Vous devez être connecté pour ajouter une offre à votre wishlist !");
                        window.location.href = "/connexion"; // Optionnel : redirection vers la connexion
                    }
                })
                .catch(error => {
                    console.error("Erreur de connexion au serveur", error);
                });
        });
    });