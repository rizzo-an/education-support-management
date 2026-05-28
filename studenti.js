document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("cercaStudente");
    const filtroComune = document.getElementById("filtroComune");
    const filtroClasse = document.getElementById("filtroClasse");
    const filtroSostegno = document.getElementById("filtroSostegno");
    const tableBody = document.getElementById("studentiTableBody");

    if (searchInput && filtroComune && filtroClasse && filtroSostegno && tableBody) {
        const tableRows = tableBody.querySelectorAll("tr");

        function aplicaFiltri() {
            const termineRicerca = searchInput.value.toLowerCase().trim();
            const comune = filtroComune.value.trim().toLowerCase();
            const classe = filtroClasse.value.trim().toLowerCase();
            const sostegno = filtroSostegno.value.trim().toLowerCase();

            tableRows.forEach(row => {
                const testoRiga = row.textContent.toLowerCase();
                const matchRicerca = !termineRicerca || testoRiga.includes(termineRicerca);
                const matchComune = !comune || (row.dataset.city || "").toLowerCase() === comune;
                const matchClasse = !classe || (row.dataset.class || "").toLowerCase() === classe;
                const matchSostegno = !sostegno || (row.dataset.sostegno || "").toLowerCase() === sostegno;

                row.style.display = (matchRicerca && matchComune && matchClasse && matchSostegno) ? "" : "none";
            });
        }

        searchInput.addEventListener("keyup", aplicaFiltri);
        filtroComune.addEventListener("change", aplicaFiltri);
        filtroClasse.addEventListener("change", aplicaFiltri);
        filtroSostegno.addEventListener("change", aplicaFiltri);
    }
});