// import { changeFragmentUser } from "./main";

export function menuContextuel(data_fragment){
    document.addEventListener('contextmenu', (e) => {
        
        if (e.target && e.target.getAttribute('data-id')) {
            e.preventDefault();
            e.stopPropagation();

            const dossierId = e.target.getAttribute('data-id');
            showContextMenu(e, dossierId, data_fragment);
        }
    });

    document.addEventListener('click', (e) => {
        if (e.target && e.target.getAttribute('data-id')) {
            const dossierId = e.target.getAttribute('data-id');

            // Ouvrir le dossier
            const openDossierBtn = document.getElementById('openDossier');
            if (openDossierBtn) {
                openDossierBtn.setAttribute('data-dossier', dossierId);
                openDossierBtn.setAttribute('data-fragment', data_fragment);

                // Simuler un clic ou appeler une fonction pour ouvrir le dossier
                openDossierBtn.click();
            }
        }
    });

    // Cacher le menu contextuel si on clique ailleurs
    document.addEventListener('click', (e) => {
        const contextMenu = document.getElementById('contextMenu');
        if (contextMenu && !contextMenu.contains(e.target) && !e.target.classList.contains('repertoire')) {
            contextMenu.classList.remove('d-block');
            contextMenu.classList.add('d-none');
        }
    });


    const form = document.querySelector('#modalFormContainerbis');
    if (form) {
        const service = form.getAttribute('data-service');
        form.addEventListener('submit', function (event) {
        event.preventDefault(); // Empêche l'envoi du formulaire pour éviter un rechargement de la page

        const formData = new FormData(form);

        fetch(`/api/dossiers/${service}`, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(html => {
            form.innerHTML = html; // Remplace le contenu du container modal avec la nouvelle réponse
        })
        .catch(error => console.error('Erreur lors du chargement du formulaire:', error));
        });
    }
}

// Afficher le menu contextuel
function showContextMenu(e, dossierId, data_fragment) {
    const menu = document.getElementById('contextMenu');
    menu.classList.remove('d-none');
    menu.classList.add('d-block');
    menu.style.left = `${e.pageX}px`;
    menu.style.top = `${e.pageY}px`;

    setupContextMenuActions(dossierId, data_fragment);
}

// Configuration des actions du menu contextuel
function setupContextMenuActions(dossierId, data_fragment) {
    const dossierIdInt = parseInt(dossierId);
    const dossier = document.querySelector(`[data-id='${dossierId}']`);

    // Ouvrir le dossier
    const openDossierBtn = document.getElementById('openDossier');
    openDossierBtn.setAttribute('data-dossier', dossierId);
    openDossierBtn.setAttribute('data-fragment', data_fragment)

    // Renommer le dossier
    addEvent('#renameDossier', 'click', () => {
        $('#renameDossierModal').modal('show');
    });

    addEvent('#submitNewDossierName', 'click', () => {
        const newName = document.getElementById('newDossierName').value;
        const responseMessage = document.getElementById('responseMessagediv'); // Élément de la modal où afficher la réponse
        const responseMessageElement = document.getElementById('responseMessage'); // Élément de la modal où afficher la réponse

        if (newName) {
            fetch(`/api/dossiers/${dossierIdInt}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: newName }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.id && data.name) {
                    responseMessage.classList.add('alert-success');
                    responseMessage.style.display = 'block';
                    responseMessageElement.textContent = "Nom du dossier mis à jour avec succès !"; // Message de succès
                    // Met à jour le nom dans l'UI
                    const dossierPara = dossier ? dossier.nextElementSibling.nextElementSibling : null;
                    
                    dossierPara.textContent = newName;
                    
                    $('#renameDossierModal').modal('hide'); // Ferme le modal après la mise à jour
                } else {
                    responseMessage.classList.add('alert-danger');
                    responseMessage.style.display = 'block';
                    responseMessageElement.textContent = "Échec de la mise à jour du dossier."; // Message d'échec
                }
            })
            .catch(error => {
                responseMessage.classList.add('alert-danger');
                responseMessage.style.display = 'block';
                responseMessageElement.textContent = error.message; // Affiche l'erreur dans la modal
            });
        }
    });

    // Supprimer le dossier
    addEvent('#deleteDossier', 'click', () => {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?')) {
            fetch(`/delete_dossier/${dossierId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        removeDossier(dossierId);
                    }
                })
                .catch(handleError);
        }
    });
}

// Supprimer un dossier et gérer l'affichage
function removeDossier(dossierId) {
    const dossier = document.getElementById(`dossier_${dossierId}`);
    dossier.remove();

    const container = document.getElementById('dossier-container');
    if (!container.children.length) {
        container.classList.remove('justify-content-start');
        container.classList.add('justify-content-center');
        container.innerHTML = '<div class="text-center"><p>Aucun dossier.</p></div>';
    }

    const menu = document.getElementById('contextMenu');
    toggleClass(menu, 'd-none', 'd-block');
}

// Gérer les erreurs
function handleError(error) {
    console.error('Une erreur s\'est produite :', error);
    alert('Une erreur s\'est produite. Veuillez réessayer.');
}

// Gestion des événements pour un élément spécifique
function addEvent(selector, event, handler) {
    const element = document.querySelector(selector);
    if (element) {
        element.addEventListener(event, handler);
    }
}

// Fonction utilitaire pour ajouter et supprimer des classes
function toggleClass(element, addClass, removeClass) {
    if (element) {
        element.classList.add(addClass);
        element.classList.remove(removeClass);
    }
}
