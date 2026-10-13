<?php
    session_start();

    $FITXER = __DIR__ . '/records.txt';
    $records = [];

    // Llegir els rècords del fitxer
    if (file_exists($FITXER) && filesize($FITXER) > 0) {
        $linies = file($FITXER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($linies as $linia) {
            $parts = explode(';', $linia);
            if (count($parts) === 3) {
                $records[] = [
                    'nom'    => $parts[0],
                    'puntos' => (int) $parts[1],
                    'data'   => $parts[2],
                    'raw'    => $linia,
                ];
            }
        }
    }

    // Ordenar per puntuació descendent
    usort($records, function ($a, $b) {
        return $b['puntos'] <=> $a['puntos'];
    });

    // Identificar l'últim rècord registrat per ressaltar-lo
    $ultim = $_SESSION['ultim_record'] ?? '';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>El Penjat - Rànquing</title>
</head>
<body class="pagina-joc">
    <header>
        <a href="index.php" class="logo">EL PENJAT</a>
        <div class="info-jugador">
            <span class="etiqueta">RÀNQUING</span>
            <strong>RECORDS</strong>
        </div>
    </header>

    <main class="contenidor-joc">
        <section class="panell-joc">
            <div class="capcalera-joc">
                <p>TAULA DE RÈCORDS</p>
                <h1>RÀNQUING DE JUGADORS</h1>
            </div>

            <?php if (empty($records)) : ?>
                <p class="text-centrat">Encara no hi ha rècords registrats.</p>
            <?php else : ?>
                <table class="taula-ranking">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Puntuació</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $r) : ?>
                            <tr class="<?php echo $r['raw'] === $ultim ? 'ressaltat' : ''; ?>">
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($r['nom']); ?></td>
                                <td><?php echo $r['puntos']; ?></td>
                                <td><?php echo $r['data']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="botons-final">
                <a href="index.php" class="boto-enllac">Tornar a jugar</a>
            </div>
        </section>
    </main>
</body>
</html>
