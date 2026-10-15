<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Penjat</title>
    <link rel="stylesheet" href="style.css?v=2">
    <script src="./scripts/index.js" defer></script>
</head>
<body id="index">

    <noscript>
        <div class="noscript-capa">
            <div class="noscript-missatge">
                ⚠ Aquest joc necessita JavaScript.<br>
                Activa'l al navegador i recarrega la pàgina per poder jugar.
            </div>
        </div>
    </noscript>


    
    <div id="panel-index">

        <div id="div-titol">
            <h1 id="titol">
                El Penjat
                <img id="icona" src="./assets/images/barret-bruixa.png" alt="icono bruixa">
            </h1>
        </div>

        <div id="descripcio">
            <p>Estaves explorant una mansió encantada. De sobte, va aparèixer la bruixa, la mestressa de la mansió. T'ha acorralat i ha conjurat un encanteri per condemnar-te a viure junt amb els seus esperits per sempre.</p>
            <p class="pregunta">Podràs fer servir la teva habilitat lèxica per superar el seu joc malvat i escapar amb vida?</p>
        </div>
        
        <form id="form-jugar" action="play.php" method="post" novalidate>
            <div id="camps">
                <div>
                    <label for="nom">Nom: </label>
                    <input type="text" name="nom" id="nom" placeholder="El teu nom...">
                    <div id="error-nom"></div>
                </div>

                <div>
                    <label for="dificultat">Dificultat: </label>
                    <select name="dificultat" id="dificultat">
                        <option value="facil">Fàcil</option>
                        <option value="normal">Normal</option>
                        <option value="dificil">Difícil</option>
                    </select>
                </div>
            </div>

            <input type="submit" value="Jugar">
        </form>
        <div id="zona-ranking">
            <a id="a-ranking" href="ranking.php">Veure rànking</a>
        </div>

    </div>

</body>
</html>
