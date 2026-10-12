const teclat = document.getElementById("teclat");

teclat.addEventListener("click", async function (e) {
    const boto = e.target.closest(".tecla");
    if (!boto || boto.disabled) {
        return;
    }

    boto.disabled = true;
    const url = "play.php?lletra=" + encodeURIComponent(boto.dataset.lletra);

    try {
        const resposta = await fetch(url, {
            headers: {"X-Requested-With": "XMLHttpRequest"}
        });
        const text = await resposta.text();
        const nou = new DOMParser().parseFromString(text, "text/html");

         document.getElementById("frase-oculta").innerHTML =
            nou.getElementById("frase-oculta").innerHTML;
        document.getElementById("imatge-penjat").src =
            nou.getElementById("imatge-penjat").getAttribute("src");
        teclat.innerHTML = nou.getElementById("teclat").innerHTML;

        const resultat = (nou.body.dataset.resultat || "").trim();

        if (resultat === "guanya" || resultat === "perd") {

            teclat.style.pointerEvents = "none"; 

            setTimeout(() => {
                window.location.href = "gameover.php";
            }, resultat === "guanya" ? 2500 : 1500);
        }

    } catch (error) {
        console.error("ERROR:", error);
        window.location.href = "play.php";
    }
});