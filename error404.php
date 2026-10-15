<?php
http_response_code(404);   // sigue siendo un 404 de verdad

// Carpeta donde vive el proyecto (/ElPenjat en local, vacío si está en la raíz)
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - El Penjat</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/style.css">
</head>
<body class="pagina-joc">

    <header>
        <a href="<?php echo $base; ?>/index.php" class="logo">EL PENJAT</a>
    </header>

    <main class="contenidor-joc">
        <section class="panell-joc">

            <div class="capcalera-joc">
                <p>ERROR 404</p>
                <h1>T'HAS PERDUT A LA MANSIÓ</h1>
            </div>
            <div class="zona-penjat" style="margin:0 auto 25px; max-width:320px;">
                <img src="<?php echo $base; ?>/assets/images/penjat-6.png" alt="Penjat" class="imatge-penjat">
            </div>

            <p class="text-centrat">
                Aquesta habitació no existeix... o potser la bruixa l'ha fet desaparèixer.<br>
                Torna enrere abans que sigui massa tard.
            </p>

            <div class="botons-final">
                <a class="boto-enllac" href="<?php echo $base; ?>/index.php">Tornar a l'inici</a>
                <a class="boto-enllac" href="<?php echo $base; ?>/ranking.php">Veure el rànquing</a>
            </div>

        </section>
    </main>

</body>
</html>