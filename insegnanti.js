document.addEventListener('DOMContentLoaded', () => {
    const insegnantiGrid = document.getElementById('insegnantiGrid');

    fetch('prendi_insegnanti.php') 
        .then(response => response.json())
        .then(insegnanti => {
            insegnantiGrid.innerHTML = ''; 

            insegnanti.forEach(insegnante => {
            
                const isCompleta = insegnante.tipo_cattedra === 'Completa (18 ore)';
                const badgeClass = isCompleta ? 'badge-green' : 'badge-yellow';
                const badgeText = isCompleta ? 'COMPLETA' : 'SPEZZONE';

                const cardHTML = `
                    <div class="card card-orange">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                            <span class="${badgeClass}">${badgeText}</span>
                        </div>
                        <h3>${insegnante.cognome} ${insegnante.nome}</h3>
                        <p class="coop">Docente MIUR</p>
                        <a href="mailto:${insegnante.email}" class="email-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            ${insegnante.email}
                        </a>
                    </div>
                `;
                insegnantiGrid.innerHTML += cardHTML;
            });
        })
        .catch(error => console.error('Errore nel caricamento degli insegnanti:', error));
});