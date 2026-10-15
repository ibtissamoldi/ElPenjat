<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $permis = !empty($_SESSION['gameover_form']);
    } else {
        $permis = !empty($_SESSION['gameover_ok']);
        if ($permis) {
            unset($_SESSION['gameover_ok']);
            $_SESSION['gameover_form'] = true;
        }
    }

    if (!$permis) {
        http_response_code(403);
        include __DIR__ . '/error403.php';
        exit;
    }

    
    $FITXER = __DIR__ . '/records.txt';

    $guanya    = ($_SESSION['resultat'] === 'guanya');
    $puntuacio = (int)($_SESSION['puntuacio'] ?? 0);
    $temps     = (int)($_SESSION['temps'] ?? 0);
    $frase     = $_SESSION['frase'] ?? '';
    $error     = '';


    // ------------------ REGISTRAR EL RÈCORD ------------------
    // empty($_SESSION['registrat']) evita registrar-lo dues vegades si es refresca la pàgina
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar']) && empty($_SESSION['registrat'])) {

        // el nom no pot portar ";" ni salts de línia (és el separador del fitxer)
        $nom = trim(preg_replace('/[;\r\n\t]+/', ' ', $_SESSION['nom'] ?? ''));
        $nom = mb_substr($nom, 0, 20);
        if ($nom === '') { $nom = 'Anònim'; }

        $linia = $nom . ';' . $puntuacio . ';' . date('Y-m-d H:i:s');

        if (file_put_contents($FITXER, $linia . "\n", FILE_APPEND | LOCK_EX) !== false) {
            $_SESSION['registrat']    = true;
            $_SESSION['ultim_record'] = $linia;     // per ressaltar-lo al rànquing
             unset($_SESSION['gameover_form']);
            header('Location: ranking.php');
            exit;
        }
        $error = "No s'ha pogut desar el rècord (revisa els permisos de records.txt).";
    }

    $jaRegistrat = !empty($_SESSION['registrat']);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>El Penjat - Final de la partida</title>
</head>
<body class="pagina-joc">
    <header>
        <a href="index.php" class="logo">EL PENJAT</a>
        <div class="info-jugador">
            <span class="etiqueta">JUGADOR</span>
            <strong><?php echo htmlspecialchars($_SESSION['nom']); ?></strong>
        </div>
    </header>

    <main class="contenidor-joc">
        <section class="panell-joc">
            <div class="capcalera-joc">
                <?php
                    if($guanya){
                        echo "<p>HAS ESCAPAT DE LA MANSIÓ</p>";
                    }else{
                        echo "<p>LA BRUIXA T'HA ENXAMPAT</p>";
                        echo "<h1>ARA ETS UN DELS SEUS ESPERITS</h1>";
                    }
                ?>
            </div>

            <div class="resum-partida">
                <p>La frase era: <strong><?php echo htmlspecialchars($frase); ?></strong></p>
                <p>Temps: <strong><?php echo gmdate('H:i:s', $temps); ?></strong></p>
                <p>Puntuació: <strong><?php echo $puntuacio; ?></strong></p>
            </div>

            <?php
                if ($error !== ''){
                    echo '<p class="error-final">' .$error. '</p>';
                }
            ?>
            <?php if ($jaRegistrat){ ?>
                <p class="text-centrat">El teu rècord ja està registrat.</p>
                <div class="botons-final">
                    <a href="ranking.php" class="boto-enllac">Veure rànquing</a>
                    <a href="index.php" class="boto-enllac">Tornar a jugar</a>
                </div>
            <?php }else{ ?>
                <p class="text-centrat">Vols registrar el teu rècord al rànquing?</p>
                <form method="post" action="gameover.php" class="botons-final">
                    <button type="submit" name="registrar" value="1" class="boto-enllac">Registrar rècord</button>
                    <a href="index.php" class="boto-enllac">No, gràcies</a>
                </form>
            <?php } ?>
        </section>
    </main>

   
</body>
</html>