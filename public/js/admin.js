document.addEventListener('DOMContentLoaded', function () {
    const modulesContainer = document.getElementById('modules-list');

    if (modulesContainer) {
        // Rendre la liste des modules sortable
        new Sortable(modulesContainer, {
            animation: 150,
            handle: '.module-header', // On ne peut glisser qu'en saisissant le header du module
            onEnd: function (evt) {
                updateOrder('module', modulesContainer);
            }
        });

        // Rendre chaque liste de leçons sortable
        const lessonLists = document.querySelectorAll('.lesson-list');
        lessonLists.forEach(list => {
            new Sortable(list, {
                animation: 150,
                group: 'lessons', // Permet de glisser entre les modules si nécessaire (optionnel)
                onEnd: function (evt) {
                    updateOrder('lesson', list);
                }
            });
        });
    }
});

function updateOrder(type, container) {
    const items = [];
    container.querySelectorAll('[data-id]').forEach((item, index) => {
        items.push({
            id: item.dataset.id,
            order: index + 1
        });
    });

    fetch('update_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ type: type, items: items })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status !== 'success') {
            console.error('Failed to update order:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
