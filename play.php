<?php
    session_start();


    $MAX_ERRORES = 6;


    // ------------------ FUNCIONES ------------------

    // funcion para medir la frase  
    function medir($frase){
        $net = preg_replace('/[^A-ZÀ-Ü]/u', '', mb_strtoupper($frase)); // dejamos solo las letras en mayusculas sin espacios ni nada 
        $ratio = count(array_unique(mb_str_split($net))) / mb_strlen($net);
        return [mb_strlen($frase), $ratio]; // devuelve [caracteres totales, ratio de las letras distintas]
    }

    // funcion para definir el nivel 
        
    function classificar($frase){
        $MIN_LARGA = 28; // puede ser mas larga (rango 20-35)
        $MAX_REPETIDA = 0.55; // ajustar con el script de abajo



        [$caracters, $ratio] = medir($frase); // desempacamos lo que nos devuelve la funcion en un array 

        $llarga = $caracters >= $MIN_LARGA; // si es true es dificil
        $moltesRepetides = $ratio <= $MAX_REPETIDA; // si es tru es facil
        
        
        if (!$llarga && $moltesRepetides) {return 'facil';}
        if ($llarga && !$moltesRepetides) {return 'dificl';}
        return 'normal';
    }




    //  -------------------------------- INICIO DE PARTIDA  --------------------------------

    if (isset($_POST['nom'],$_POST['dificultat'])){


        // Validar la dificultad: solo aceptamos los 3 valores permitidos
        $nivells = ['facil', 'normal', 'dificil'];
        $dificultat = in_array($_POST['dificultat'], $nivells) ? $_POST['dificultat'] : 'normal';



        // Nombre si viene vacio le ponemos uno por defecto "anónim"
        $nom = trim($_POST['nom']);
        if ($nom === ""){$nombre = 'Anónim';}


        // leer las frases y quedarnos con el que el jugador a elegido

        $linies = file('sentences_ca.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); //quita el salto de línea del final de cada frase e ignora las líneas vacías.
        $cantidates = [];  // guardaremos las frases elegidas por el nivel
        foreach ($linies as $frase){
            if (classificar($frase) === $dificultat){
                $cantidates = $frase;    // guardamos las frases elejidas por classificar
            }
        }

        // Si no tenemos nivel = volver al inicio solo es por si acaso no sucedera

        if (empty($cantidates)) {
            header('Location: index.php');
            exit;
        }


        // GUARDAMOS LA PARTIDA 
        $_SESSION['frase'] = mb_strtoupper($cantidades[array_rand($cantidades)]);
        $_SESSION['nom'] = $nom;
        $_SESSION['dificultat'] = $dificultat;
        $_SESSION['encertades'] = [];
        $_SESSION['fallades'] = [];
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
            if (mb_strpos($_SESSION['frase'], $lletra) !== false){  //if la letra existe me devolvera el indice entonces si me devuelve un numero 
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
    foreach (mb_str_split($_SESSION['frase']) as $c) {          // por cada letra de la frase se llamara $c
        if ($c === ' ') {                                       //si es vacio 
            $oculta .= '&nbsp;&nbsp;';                          //añade dos espacios invisible ne html para separar las palabas visualmente
        } elseif (in_array($c, $_SESSION['encertades'])) {         // busca si la letra esta en acertades si esta  
            $oculta .= $c . ' ';                                    //la muestra seguida de un espacio
        } else {
            $oculta .= '_ ';                                        // sino la ha encontrado devuelve un guio bajo paraa esconder la palabra 
            $completa = false;                          // y claramete no la encuentra 
        }
    }




    // ---------- 5. FIN DE PARTIDA ----------
    if ($completa || $errors >= $MAX_ERRORS) {                                  // si esta completa o los errores mayores que los maximos osea 6
        $_SESSION['resultat'] = $completa ? 'guanya' : 'perd';                  // nos muestra el resultado si ha ganado o no
        $_SESSION['temps']    = time() - $_SESSION['inici'];                    // el tiempo restamos la hora actual menos la hroa que empezo para calcular los segundos totales en los que jugo
        header('Location: gameover.php');                                       // lo enviamos a la pagina de gameover.php ´
        exit;
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

<body class="pagina-joc">

    <header class="capcalera">
        <a href="index.php" class="logo">EL PENJAT</a>

        <div class="info-jugador">
            <span class="etiqueta">JUGADOR</span>
            <strong id="nom-jugador">Ibtissam</strong>
        </div>
    </header>


    <main class="contenidor-joc">

        <section class="panell-joc">

            <div class="capcalera-joc">

                <p class="subtitol">LA MANSIÓ EMBRUIXADA</p>

                <h1>DESCOBREIX EL MISTERI</h1>
    
            </div>


            <div class="zona-principal">

                <div class="zona-penjat">

                    <img src="assets/images/penjat-0.png" alt="Dibuix del penjat" class="imatge-penjat" id="imatge-penjat">

                </div>


                <div class="zona-frase">

                    <p class="indicacio-frase">DESCOBREIX LA FRASE</p>
                        
                    

                    <div class="frase-oculta" id="frase-oculta" aria-label="Frase oculta">
                        <?php echo $oculta  ?>
                    </div>

                </div>

            </div>


            <div class="zona-teclat">

                <p class="indicacio-teclat">TRIA UNA LLETRA</p>
                    
                

                <div class="teclat">

                    <button class="tecla" type="button">A</button>
                    <button class="tecla" type="button">B</button>
                    <button class="tecla" type="button">C</button>
                    <button class="tecla" type="button">D</button>
                    <button class="tecla" type="button">E</button>
                    <button class="tecla" type="button">F</button>
                    <button class="tecla" type="button">G</button>

                    <button class="tecla" type="button">H</button>
                    <button class="tecla" type="button">I</button>
                    <button class="tecla" type="button">J</button>
                    <button class="tecla" type="button">K</button>
                    <button class="tecla" type="button">L</button>
                    <button class="tecla" type="button">M</button>
                    <button class="tecla" type="button">N</button>

                    <button class="tecla" type="button">O</button>
                    <button class="tecla" type="button">P</button>
                    <button class="tecla" type="button">Q</button>
                    <button class="tecla" type="button">R</button>
                    <button class="tecla" type="button">S</button>
                    <button class="tecla" type="button">T</button>
                    <button class="tecla" type="button">U</button>

                    <button class="tecla" type="button">V</button>
                    <button class="tecla" type="button">W</button>
                    <button class="tecla" type="button">X</button>
                    <button class="tecla" type="button">Y</button>
                    <button class="tecla" type="button">Z</button>

                </div>

            </div>

        </section>

    </main>

</body>

</html>