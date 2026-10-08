<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Penjat</title>
    <link rel="stylesheet" href="style.css">
    <script src="./scripts/index.js" defer></script>
</head>
<body id="index">
    <div class="superficie-clara" id="div-titol">
        <h1 id="titol">El Penjat</h1>
        <img id="icona" src="./assets/images/hangman_icon.png" alt="Icona del penjat" >
    </div>
    <div class="superficie-clara" id="descripcio">
        Estaves explorant una mansió encantada. De sobte, va aparèixer la bruixa, la mestressa de la mansió.
        T'ha acorralat i ha conjurat un encanteri per condemnar-te a viure junt amb els seus esperits per sempre.<br>
        Podràs fer servir la teva habilitat lèxica per superar el seu joc malvat i escapar amb vida? 
    </div>
    <div class="superficie-clara">
        <a id="a-ranking" href="ranking.php">Veure rànking</a>
    </div>
    <div class="superficie-clara">
        <form id="form-jugar" action="play.php" method="post" novalidate>
            <div>
                <label for="nom">Nom: </label>
                <input type="text" name="nom" id="nom">
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
            <input type="submit" value="Jugar">
        </form> 
    </div>
</body>
</html>
