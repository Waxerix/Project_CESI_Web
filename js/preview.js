document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    const profilePreview = document.getElementById('profile-preview');

    if (photoInput && profilePreview) {
        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remplace simplement l'image par défaut par la nouvelle !
                    profilePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
});