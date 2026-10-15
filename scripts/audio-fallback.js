(() => {
    function playIfUnplayed(audio) {
        if (audio.dataset.hasPlayed === "true") {
            return;
        }

        if (!audio.paused) {
            audio.dataset.hasPlayed = "true";
            return;
        }

        try {
            audio.play()
        } catch (error) {
            console.error("No s'ha pogut reproduir l'àudio:", error);
        }
    }

    // Reprodueix l'àudio si encara no s'ha reproduït
    function trackAudio(audio) {
        audio.addEventListener("playing", () => {
            audio.dataset.hasPlayed = "true";
        }, { once: true });
        playIfUnplayed(audio);
    }

    document.querySelectorAll("audio").forEach(trackAudio);

    // Reprodueix tots els àudios quan es fa clic a la pàgina (complint amb restriccions dels navegadors)
    document.addEventListener("click", () => {
        document.querySelectorAll("audio").forEach(playIfUnplayed);
    }, true);

    // Detecta àudio afegit dinàmicament al DOM (quan es fa clic a una lletra en play.php)
    new MutationObserver(records => {
        for (const record of records) {
            for (const node of record.addedNodes) {
                if (!(node instanceof Element)) {
                    continue;
                }

                if (node.matches("audio")) {
                    trackAudio(node);
                }
                node.querySelectorAll("audio").forEach(trackAudio);
            }
        }
    }).observe(document.body, { childList: true, subtree: true });
})();
