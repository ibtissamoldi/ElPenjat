const teclat = document.getElementById("teclat");
 
teclat.addEventListener("click", function (e) {
 
    const boto = e.target.closest(".tecla");
 
    if (!boto || boto.disabled) {
        return;
    }
 
    // (aquí, a la tasca 15, es podrà afegir el so d'encertar/fallar)
 
    window.location.href = "play.php?lletra=" + encodeURIComponent(boto.dataset.lletra);
});
