export function menuContextuel(data_fragment) {
    document.removeEventListener('contextmenu', (e) => handleContextMenu(e, data_fragment)); 
    document.addEventListener('contextmenu', (e) => handleContextMenu(e, data_fragment)); 

    document.addEventListener('click', (e) => closeContextMenu(e), true);
}

// function closeContextMenu(e) {
//     const contextMenus = document.querySelectorAll('.context-menu');
//     console.log(`ContextMenus: ${contextMenus}`);
//     contextMenus.forEach((menu) => {
//         console.log(menu);
//         const isClickInsideMenu = menu.contains(e.target);
//         const isClickInsideTrigger = e.target.closest('.repertoire, .document'); // Vous devez avoir des classes "repertoire" et "document"
//         console.log(isClickInsideMenu, isClickInsideTrigger);
//         if (!isClickInsideMenu && !isClickInsideTrigger) {
//             menu.classList.replace('d-block', 'd-none');
//         }
//     });
// }

function closeContextMenu(e) {
    const contextMenus = document.getElementById('contextMenuDossier');
    const contextMenusDocument = document.getElementById('contextMenu');
    if(contextMenus && !contextMenus.contains(e.target)){
        contextMenus.classList.replace('d-block', 'd-none');
    }

    if(contextMenusDocument && !contextMenusDocument.contains(e.target)){
        contextMenusDocument.classList.replace('d-block', 'd-none');
    }
}

function handleContextMenu(e, data_fragment) {
    const target = e.target;
    if (target && target.getAttribute('data-id')) {
        e.preventDefault();
        e.stopPropagation();
        const dossierId = target.getAttribute('data-id');
        
        const ContextMenu = document.getElementById('contextMenu');
        const ContextMenuDossier = document.getElementById('contextMenuDossier');
        const openDocumentBtn = document.querySelector('#opendDocument');
        const openDossierBtn = document.querySelector('#openDossier');

        if (ContextMenu && openDocumentBtn) {
            showContextMenu(e, dossierId, ContextMenu);
            openDocumentBtn.setAttribute('type', 'button');
            openDocumentBtn.setAttribute('data-id', dossierId);
            openDocumentBtn.setAttribute('data-document', dossierId);
            openDocumentBtn.setAttribute('data-fragment', data_fragment);
            openDocumentBtn.addEventListener('click', () => {
                ContextMenu.classList.replace('d-block', 'd-none');
            });
        }
        
        if (ContextMenuDossier && openDossierBtn) {
            showContextMenu(e, dossierId, ContextMenuDossier);
            openDossierBtn.setAttribute('type', 'button');
            openDossierBtn.setAttribute('data-id', dossierId);
            openDossierBtn.setAttribute('data-dossier', dossierId);
            openDossierBtn.setAttribute('data-fragment', data_fragment);
            openDossierBtn.addEventListener('click', () => {
                ContextMenuDossier.classList.replace('d-block', 'd-none');
            });
        }
    }
}

function showContextMenu(e, dossierId, menu) {
    menu.classList.replace('d-none', 'd-block');
    menu.style.left = `${e.pageX}px`;
    menu.style.top = `${e.pageY}px`;
    setupContextMenuActions(dossierId, menu);
}

function setupContextMenuActions(dossierId, menu) {
    // Actions génériques : ouverture, renommage, suppression
    setupRenameAction('#renameDossier', dossierId, 'dossiers', 'Nom du dossier mis à jour avec succès !', menu);
    setupRenameAction('#renameDocument', dossierId, 'documents', 'Nom du document mis à jour avec succès !', menu);
    setupDeleteAction('#deleteDossier', dossierId, 'dossiers', menu);
    setupDeleteAction('#deleteDocument', dossierId, 'documents', menu);
}

function setupRenameAction(selector, dossierId, type, successMessage, menu) {
    const button = document.querySelector(selector);
    if (button) {
        button.addEventListener('click', () => {
            const myModal = new bootstrap.Modal(document.getElementById(`rename${capitalize(type)}Modal`));
            if (myModal) {
                myModal.show();
            }
           menu.classList.replace('d-block', 'd-none');
        });
    }

    const submitButton = document.getElementById(`submitNew${capitalize(type)}Name`);
    if (submitButton) {
        submitButton.addEventListener('click', () => {
            const newName = document.getElementById(`new${capitalize(type)}Name`).value;
            if (newName) {
                console.log(`/api/${type}/${dossierId}`);
                fetch(`/api/${type}/${dossierId}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: newName }),
                })
                .then(response => response.json(
                    console.log(response)
                ))
                .then(data => {
                    if (data.id && data.name) {
                        updateUI(dossierId, newName);
                        showSuccess(successMessage);
                        const modal = document.getElementById(`rename${capitalize(type)}Modal`);
                        if (modal) {
                            modal.classList.remove('show');
                        }

                        location.reload(); 
                    } else {
                        showError('Échec de la mise à jour.');
                    }
                })
                .catch(() => showError('Erreur lors de la mise à jour.'));
            }
        });
    }
}

function setupDeleteAction(selector, dossierId, type, menu) {
    const button = document.querySelector(selector);
    if (button) {
        button.addEventListener('click', () => {
            if (confirm(`Êtes-vous sûr de vouloir supprimer ce ${type.slice(0, -1)} ?`)) {
                fetch(`/api/${type}/${dossierId}`, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) removeItem(dossierId, type);
                    })
                    .catch(() => handleError());
            }
            menu.classList.replace('d-block', 'd-none');
        });
    }
}

function updateUI(dossierId, newName) {
    const dossier = document.querySelector(`[data-id='${dossierId}']`);
    if (dossier) {
        const nameElement = dossier.nextElementSibling ? dossier.nextElementSibling.nextElementSibling : null;
        if (nameElement) {
            nameElement.textContent = newName;
        }
    }
}

function removeItem(dossierId, type, menu) {
    const item = document.getElementById(`${type.slice(0, -1)}_${dossierId}`);
    if (item) item.remove();

    const container = document.getElementById(`${type.slice(0, -1)}-container`);
    if (container && !container.children.length) {
        container.classList.replace('justify-content-start', 'justify-content-center');
        container.innerHTML = `<div class="text-center"><p>Aucun ${type.slice(0, -1)}.</p></div>`;
    }

    if (menu) {
        menu.classList.replace('d-block', 'd-none');
    }
}

function showSuccess(message) {
    const responseMessage = document.getElementById('responseMessagediv');
    const responseMessageElement = document.getElementById('responseMessage');
    if (responseMessage && responseMessageElement) {
        responseMessage.classList.replace('alert-danger', 'alert-success');
        responseMessage.style.display = 'block';
        responseMessageElement.textContent = message;
    }
}

function showError(message) {
    const responseMessage = document.getElementById('responseMessagediv');
    const responseMessageElement = document.getElementById('responseMessage');
    if (responseMessage && responseMessageElement) {
        responseMessage.classList.replace('alert-success', 'alert-danger');
        responseMessage.style.display = 'block';
        responseMessageElement.textContent = message;
    }
}

function handleError() {
    alert('Une erreur s\'est produite. Veuillez réessayer.');
}

function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
