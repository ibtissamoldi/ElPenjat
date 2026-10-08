const formJugar = document.getElementById('form-jugar');
const nom = document.getElementById('nom');
const errorNom = document.getElementById('error-nom');

function validarNom() {
    if (nom.value.trim() === '') {
        errorNom.textContent = 'El nom no pot estar buit';
        nom.classList.add('invalid');
        nom.focus();
        return false;
    }
    errorNom.textContent = '';
    nom.classList.remove('invalid');
    return true;
}

formJugar.addEventListener('submit', (e) => {
    // evitar enviament sempre
    e.preventDefault(); 

    // validació i enviament manuals
    if (validarNom()) { 
        formJugar.submit();  
    }
});

// Netejar l'error quan l'usuari escriu
nom.addEventListener('input', () => {
    if (nom.value.trim() !== '') {
        errorNom.textContent = '';
        errorNom.classList.remove('visible');
        nom.classList.remove('invalido');
    }
});