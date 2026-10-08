const teclat = document.getElementById("teclat");
const fraseElement = document.getElementById("frase-oculta");
const imatgePenjat = document.getElementById("imatge-penjat");

let errors = 0;
let jocAcabat = false;

const maxErrors = 6;


function normalitzarLletra(lletra) {
    return lletra
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toUpperCase();
}


function crearFraseOculta() {

    fraseElement.innerHTML = "";

    for (let i = 0; i < fraseOriginal.length; i++) {

        const caracter = fraseOriginal[i];
        const lletra = document.createElement("span");

        if (caracter === " ") {
            lletra.textContent = " ";
        } else {
            lletra.textContent = "_";
            lletra.dataset.lletra = normalitzarLletra(caracter);
        }

        fraseElement.appendChild(lletra);
    }
}


function mostrarLletra(lletra) {

    const lletraNormalitzada = normalitzarLletra(lletra);
    const posicions = fraseElement.querySelectorAll("span");

    let trobada = false;

    posicions.forEach(function(element, index) {

        if (element.dataset.lletra === lletraNormalitzada) {

            element.textContent = fraseOriginal[index];
            trobada = true;
        }
    });

    return trobada;
}


function comprovarVictoria() {

    const lletres = fraseElement.querySelectorAll("span");

    for (const lletra of lletres) {

        if (lletra.dataset.lletra && lletra.textContent === "_") {
            return false;
        }
    }

    return true;
}


function actualitzarPenjat() {

    imatgePenjat.src = "assets/images/penjat-" + errors + ".png";
}


function anarGameover(resultat) {

    if (jocAcabat) {
        return;
    }

    jocAcabat = true;

    const nomElement = document.getElementById("nom-jugador");
    const nom = nomElement.textContent.trim();

    const url =
        "gameover.php" +
        "?resultat=" + encodeURIComponent(resultat) +
        "&nom=" + encodeURIComponent(nom);

    window.location.href = url;
}


function jugarLletra(boto) {

    if (jocAcabat || boto.disabled) {
        return;
    }

    boto.disabled = true;

    const lletra = boto.textContent;
    const correcta = mostrarLletra(lletra);

    if (correcta) {

        boto.classList.add("correcta");

        if (comprovarVictoria()) {
            anarGameover("victoria");
        }

    } else {

        boto.classList.add("incorrecta");

        errors++;

        actualitzarPenjat();

        if (errors >= maxErrors) {
            anarGameover("derrota");
        }
    }
}


const botons = teclat.querySelectorAll(".tecla");

botons.forEach(function(boto) {

    boto.addEventListener("click", function() {
        jugarLletra(boto);
    });

});


crearFraseOculta();