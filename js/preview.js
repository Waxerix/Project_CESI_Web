const fileInput = document.getElementById('file-upload');
  const uploadCircle = document.querySelector('.upload-circle');
  const plusSign = document.querySelector('.plus-sign');
  const uploadText = document.querySelector('.upload-text');
 
  fileInput.addEventListener('change', function () {
    const file = this.files[0];
 
    // Vérification que c'est bien une image
    if (!file || !file.type.startsWith('image/')) {
      alert('Veuillez sélectionner un fichier image valide (jpg, png, gif...)');
      this.value = '';
      return;
    }
 
    // Remplace le rond par la photo
    const reader = new FileReader();
    reader.onload = function (e) {
      uploadCircle.style.backgroundImage = `url('${e.target.result}')`;
      uploadCircle.style.backgroundSize = 'cover';
      uploadCircle.style.backgroundPosition = 'center';
      plusSign.style.display = 'none';
      uploadText.textContent = 'Modifier la photo';
    };
    reader.readAsDataURL(file);
  });