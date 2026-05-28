document.addEventListener('DOMContentLoaded', function() {
    
    const searchCardInput = document.getElementById("cercaCard");
    if (searchCardInput) {
        searchCardInput.addEventListener("keyup", function() {
            const termineRicerca = this.value.toLowerCase();
         
            const tutorGrid = document.getElementById("tutorGrid");
            
            if(tutorGrid) {
                const cards = tutorGrid.querySelectorAll(".card");

                cards.forEach(card => {
                    const testoCard = card.textContent.toLowerCase();
                    if (testoCard.includes(termineRicerca)) {
                        card.style.display = ""; 
                    } else {
                        card.style.display = "none";
                    }
                });
            }
        });
    }
});