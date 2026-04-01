const params = new URLSearchParams(window.location.search);

if (window.location.hash === '#success') {
    alert('Votre candidature a bien été envoyée !');
    // Nettoie l'URL
    window.history.replaceState({}, '', '/');
}