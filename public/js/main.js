document.addEventListener('DOMContentLoaded', function() {
    const completeButton = document.querySelector('.btn-complete');

    if (completeButton) {
        completeButton.addEventListener('click', function() {
            const lessonId = this.dataset.lessonId;
            
            // Obtenir le chemin de base du site
            const baseUrl = window.location.origin + window.location.pathname.substring(0, window.location.pathname.indexOf('/SkillAfrik/') + '/SkillAfrik/'.length);

            fetch(baseUrl + 'progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ lesson_id: lessonId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur HTTP: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Changer l'état du bouton
                    completeButton.textContent = 'Terminé !';
                    completeButton.disabled = true;
                    completeButton.classList.add('completed');
                    
                    // Afficher un message de succès
                    alert('Leçon marquée comme terminée !');
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur technique est survenue: ' + error.message);
            });
        });
    }
});
