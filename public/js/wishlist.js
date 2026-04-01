document.addEventListener('DOMContentLoaded', () => {
    // Sélectionne tous les boutons de favoris
    const buttons = document.querySelectorAll('.btn-wishlist');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const offerId = this.getAttribute('data-id');
            const isActive = this.classList.contains('active');
            
            // Détermine l'URL en fonction de l'état actuel (Ajout ou Suppression)
            const url = isActive ? `/wishlist/delete/${offerId}` : `/wishlist/add/${offerId}`;

            // Appel AJAX vers le contrôleur PHP
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Si le contrôleur confirme le succès en BDD
                    if (data.success) {
                        // Alterne la classe 'active' pour changer la couleur via CSS
                        this.classList.toggle('active');
                        console.log("Succès:", data.action);
                    } else {
                        alert("Erreur lors de la mise à jour de la wishlist.");
                    }
                })
                .catch(error => {
                    console.error('Erreur de connexion au serveur:', error);
                });
        });
    });
});