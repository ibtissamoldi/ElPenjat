const teclat = document.getElementById("teclat");

teclat.addEventListener("click", async function (e) {

    const boto = e.target.closest(".tecla");

    if (!boto || boto.disabled) {
        return;
    }

    boto.disabled = true;

    const url = "play.php?lletra=" + encodeURIComponent(boto.dataset.lletra);

    try {
        const resposta = await fetch(url);

        // Si la partida ha acabado, PHP redirige a gameover.php
        if (resposta.redirected && resposta.url.includes("gameover.php")) {
            window.location.href = resposta.url;
            return;
        }

        // Si no, solo cambiamos lo que ha cambiado(frase, imagen, teclat)
        const text = await resposta.text();
        const nou = new DOMParser().parseFromString(text, "text/html");

        document.getElementById("frase-oculta").innerHTML = nou.getElementById("frase-oculta").innerHTML;
        document.getElementById("imatge-penjat").src = nou.getElementById("imatge-penjat").getAttribute("src");
        teclat.innerHTML = nou.getElementById("teclat").innerHTML;

    } catch (error) {
        window.location.href = url;
    }
});