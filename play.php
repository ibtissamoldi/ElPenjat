<?php
    session_start();


    $MAX_ERRORES = 6;


    // ------------------ FUNCIONES ------------------

    // Normalitzacion: quita los acentos (É -->E, Ç --> C...)
    function normalitzar($text){
        $amb  = ['À','Á','È','É','Ì','Í','Ï','Ò','Ó','Ù','Ú','Ü','Ç','Ñ','à','á','è','é','ì','í','ï','ò','ó','ù','ú','ü','ç','ñ'];
        $sense = ['A','A','E','E','I','I','I','O','O','U','U','U','C','N','a','a','e','e','i','i','i','o','o','u','u','u','c','n'];   
        $lletraSense = str_replace($amb, $sense, $text);
        return $lletraSense;
    }

    function esLletra($c){
        return preg_match('/^\p{L}$/u', $c) == true;
    }

    // ------------------ FUNCIONES para hacer la punctuation ------------------
    function contarLletres($frase){
        $comptador = 0;
        foreach (mb_str_split($frase) as $ll) {
            if (esLletra($ll)) {
                $comptador++;
            }
        }
        return $comptador;
    }

    function calcularPuntuacio($frase, $errors, $temps, $guanya){
        if (!$guanya) {         //partida perdida= 0 puntos
            return 0; 
        }                     

        $lletres =contarLletres($frase);
        $puntosBase= $lletres *10;                  //frase mas larga=mas puntos
        $penalErrors = $errors * 20;                   //cada error resta 20
        $tempsMax=$lletres * 4;                   //4 sec por letra

        //'max'handles negative numbers  shorter version(returns largest number between the 2 values)
        $bonusTemps = max(0, $tempsMax -$temps);   //bonus puntos por rapidez
        return max(0, $puntosBase - $penalErrors + $bonusTemps);
            
        /*
        if($temps > $tempsMax){
            $bonusTemps = 0;
        }else{
            $bonusTemps = $tempsMax -$temps;
        }
        
        $puntosFinal = ($puntosBase - $penalErrors) + $bonusTemps;

        if ($puntosFinal < 0) {
            return 0;
        } else {
            return $puntosFinal;
        }*/
        
    }


    // funcion para medir la frase  
    function medir($frase){
        $net = preg_replace('/[^A-ZÀ-Ü]/u', '', mb_strtoupper($frase)); // dejamos solo las letras en mayusculas sin espacios ni nada 
        $totalLletres =mb_strlen($net);
        if($totalLletres > 0){
            $ratio = count(array_unique(mb_str_split($net))) / $totalLletres;
            return [$totalLletres, $ratio]; // devuelve [caracteres totales, ratio de las letras distintas]
        }
        return null;
    }



        // puntuacion de dificultad = numero de letras distintas de la frase
    function puntuacioDificultat($frase){
        $m = medir($frase);
        if ($m === null) return null;
        [$total, $ratio] = $m;
        return round($total * $ratio);
    }

    // calcula los 2 puntos de corte una sola vez (cache) con todo el archivo
    function llindars(){
        static $cache = null;
        if ($cache !== null) return $cache;

        $linies = file('data/sentences_ca.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $scores = [];
        foreach ($linies as $f) {
            $s = puntuacioDificultat($f);
            if ($s !== null) $scores[] = $s;
        }
        sort($scores);
        $n = count($scores);
        $cache = [$scores[intdiv($n, 3)], $scores[intdiv(2 * $n, 3)]];
        return $cache;
    }

    // funcion para definir el nivel 
    function classificar($frase){
        $s = puntuacioDificultat($frase);
        if ($s === null) return null;

        [$t1, $t2] = llindars();
        if ($s < $t1)  return 'facil';
        if ($s < $t2)  return 'normal';
        return 'dificil';
    }


    ///test
    $linies = file('data/sentences_ca.txt',FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $comptador = ['facil' => 0,'normal' => 0,'dificil' => 0];

    foreach ($linies as $frase) {
        $categoria = classificar($frase);

        if ($categoria !== null) {
            $comptador[$categoria]++;
        }
    }
    //test


    //  -------------------------------- INICIO DE PARTIDA  --------------------------------

    if (isset($_POST['nom'],$_POST['dificultat'])){


        // Validar la dificultad: solo aceptamos los 3 valores permitidos
        $nivells = ['facil', 'normal', 'dificil'];
        $dificultat = in_array($_POST['dificultat'], $nivells) ? $_POST['dificultat'] : 'normal';



        // Nombre si viene vacio le ponemos uno por defecto "anónim"
        $nom = trim($_POST['nom']);
        if ($nom === ""){$nom = 'Anónim';}


        // leer las frases y quedarnos con el que el jugador a elegido

        $linies = file('data/sentences_ca.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); //quita el salto de línea del final de cada frase e ignora las líneas vacías.
        $cantidates = [];  // guardaremos las frases elegidas por el nivel
        foreach ($linies as $frase){
            if (classificar($frase) === $dificultat){
                $cantidates[]= $frase;    // guardamos las frases elejidas por classificar
            }
        }

        // Si no tenemos nivel = volver al inicio solo es por si acaso no sucedera

        if (empty($cantidates)) {
            header('Location: index.php');
            exit;
        }


        // GUARDAMOS LA PARTIDA 
        $_SESSION['frase'] = mb_strtoupper($cantidates[array_rand($cantidates)]);
        $_SESSION['nom'] = $nom;
        $_SESSION['dificultat'] = $dificultat;
        $_SESSION['encertades'] = [];
        $_SESSION['fallades'] = [];
        unset($_SESSION['resultat']);
        unset($_SESSION['registrat']);
        unset($_SESSION['ultim_record']); 
        unset($_SESSION['gameover_ok']);   // para error 403 en gameover.php
        $_SESSION['inici'] = time();

        header('Location: play.php');  // evita reenviar el formulario al refrescar
        exit;
        
    }




    // EN UN DADO CASO alguien entra en play.php sin pasar por el formulario lo enviamos a index.php 
    // si no tenemos frase que se va buscando con los datos del formulario 
    if (!isset($_SESSION['frase'])){
        header('Location: index.php');
        exit;
    }



    // AHORA VIENE EL JUEGO 
    // LETRAS JUGADA que se obtine de el teclado virtual 
    if (isset($_GET['lletra'])) {               // verificamos que el usuario haya clickeado una letra
        $lletra = mb_strtoupper($_GET['lletra']);  // letra mayuscula
        $valida = preg_match('/^[A-ZÀ-Ü]$/u', $lletra); // verificamos que solo sea una leta
        $jaUsada = in_array($lletra, $_SESSION['encertades']) || in_array($lletra, $_SESSION['fallades']);


        if ($valida && !$jaUsada){
            if (mb_strpos(normalitzar($_SESSION['frase']), $lletra) !== false){  //if la letra existe me devolvera el indice entonces si me devuelve un numero 
                $_SESSION['encertades'][] = $lletra;        // es un indice 
            }else{
                $_SESSION['fallades'][] = $lletra;   // de lo contrario no existe y me da un false 
            }
        }

    }



    // CALCULAMOS EL ESTADO DE LA PARTIDA 

    $errors = count($_SESSION['fallades']);     // contamos los errores las letras incorrectas del jugador 
    $oculta = '';                                 //     lo qeu hacemos aqui es esconder o el guion _ o la letra correcta del jugador     
    $completa = true;                                   // para verificar si esta econtrada o no 

    // Agrupamos la frase en palabras: cada palabra se envuelve en un <span>
    // para que el salto de línea se haga entre palabras y nunca a mitad de una palabra
    $paraules = preg_split('/\s+/u', trim($_SESSION['frase']));

    foreach ($paraules as $paraula) {
        $bloc = '';
        foreach (mb_str_split($paraula) as $c) {          // por cada letra de la palabra
            if (!esLletra($c)) {                                   // coma, apóstrofe, guion, "·", "?"... se muestran siempre
                $bloc .= $c;
            } elseif (in_array(normalitzar($c), $_SESSION['encertades'])) {         // busca si la letra esta en acertades si esta  
                $bloc .= $c;                                    //la muestra 
            } else {
                $bloc .= '_';                                        // sino la ha encontrado devuelve un guio bajo paraa esconder la palabra 
                $completa = false;                          // y claramete no la encuentra 
            }
        }
        $oculta .= '<span class="paraula">' . $bloc . '</span>';
    }




        // ---------- 5. FIN DE PARTIDA ----------
    if ($completa || $errors >= $MAX_ERRORES) {

        // Esto solo se calcula UNA vez (la primera que se detecta el final)
        if (!isset($_SESSION['resultat'])) {
            $_SESSION['resultat']  = $completa ? 'guanya' : 'perd';
            $_SESSION['temps']     = time() - $_SESSION['inici'];
            $_SESSION['puntuacio'] = calcularPuntuacio($_SESSION['frase'], $errors, $_SESSION['temps'], $completa);
            $_SESSION['gameover_ok'] = true;   // permiso de un solo uso para entrar a gameover.php
        }

        // lo enviamos a la pagina de gameover.php
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Location: gameover.php');
            exit;
        }
    }
?>


<!DOCTYPE html>
<html lang="ca">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Penjat - Partida</title>
    <link rel="stylesheet" href="style.css">
</head>


<body class="pagina-joc" data-resultat="<?php echo isset($_SESSION['resultat']) ? htmlspecialchars($_SESSION['resultat']) : ''; ?>">

    <!-- test: <?php echo $_SESSION['frase']; ?> -->
     <!-- TEST DE CLASSIFICACIÓ
        Facils: <?php echo $comptador['facil']; ?>
        Normals: <?php echo $comptador['normal']; ?>
        Dificils: <?php echo $comptador['dificil']; ?>
        -->

    <noscript>
        <div class="noscript-capa">
            <div class="noscript-missatge">
                ⚠ Aquest joc necessita JavaScript.<br>
                Activa'l al navegador i recarrega la pàgina per poder jugar.
            </div>
        </div>
    </noscript>


    <header>
        <a href="index.php" class="logo">EL PENJAT</a>
        <div class="info-jugador">
            <span class="etiqueta">JUGADOR</span>
            <strong id="nom-jugador"><?php echo htmlspecialchars($_SESSION['nom']); ?></strong>
        </div>
    </header>

    <main class="contenidor-joc">

        <section class="panell-joc">
            <div class="capcalera-joc">
                <p>LA MANSIÓ EMBRUIXADA</p>
                <h1>DESCOBREIX EL MISTERI</h1>
            </div>


            <div class="zona-principal">
                <div class="zona-penjat">
                    <img src="assets/images/penjat-<?php echo $errors; ?>.png" alt="Dibuix del penjat" class="imatge-penjat" id="imatge-penjat">
                </div>


                <div class="zona-frase">
                    <p>DESCOBREIX LA FRASE</p>
                    <div class="frase-oculta" id="frase-oculta" aria-label="Frase oculta"><?php echo $oculta; ?></div>
                </div>
            </div>

            <div class="zona-teclat">

                <p>TRIA UNA LLETRA</p>
                <div class="teclat" id="teclat">
                    <?php
                        for ($i = 65; $i <= 90; $i++) {
                            $l = chr($i);

                            if (in_array($l, $_SESSION['encertades'])) {
                                $classe = ' correcta';
                            } elseif (in_array($l, $_SESSION['fallades'])) {
                                $classe = ' incorrecta';
                            } else {
                                $classe = '';
                            }
                    ?>
                        <button class="tecla<?php echo $classe; ?>" type="button" data-lletra="<?php echo $l; ?>"<?php echo $classe !== '' ? ' disabled' : ''; ?>><?php echo $l; ?>
                    </button>
                    <?php } ?>
                </div>

            </div>


        </section>

    </main>

    <script src="scripts/play.js"></script>

</body>

</html>