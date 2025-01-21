import { facture } from './facture.js';
import { devis } from './devis.js';
import { menuContextuel } from './repertoire.js';
import { agenda } from './agenda.js';

export async function changeFragmentUser() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    const UserContent = document.getElementById('userContent');
    let locationReload = false;

    window.addEventListener('dblclick', () => {
        if (loadingIndicator.style.display='flex'){loadingIndicator.style.display='none'}
    })

    async function loadFragment(fragment, dossierId = null) {
        loadingIndicator.style.display = 'flex'; 
        let url = `/changefragment?fragment=${fragment}`;
        if (dossierId) { url += `&dossier=${dossierId}`;}
        history.pushState(null, '', `?fragment=${fragment}${dossierId ? '&dossier=' + dossierId : ''}`);

        if (locationReload === false) {
            location.reload();
            locationReload === true;
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }

        const data =  await response.json();

        updateFragmentContent(fragment);
        document.getElementById('fragmentContent').innerHTML = data.fragmentContent;

        handleFormActions();

        loadingIndicator.style.display = 'none';
    }

    function updateFragmentContent(fragment) {
        switch (fragment) {
            case 'link-Factures':
                facture();
                break;
            case 'link-Devis':
                devis();
                break;
            case 'link-Administratif':
            case 'link-Commercial':
            case 'link-Numerique':
            case 'link-Telephone':
                menuContextuel('link-PageDocument');
                break;
            case 'link-Repertoire':
            case 'link-espacepersonnel':
                menuContextuel('link-PageRepertoire');
                break;
            case 'link-Agenda':
                // agenda();
                break;
            case 'link-Contact':
                // contact();
                break;
            default:
                break;
        }
    }

    UserContent.addEventListener('click', async function(event) {
        const button = event.target;

        if (button.classList.contains('change-fragment')) {
            event.preventDefault();  
            const fragment = button.getAttribute('data-fragment');
            const dossier = button.getAttribute('data-dossier');
            dossier ? await loadFragment(fragment, dossier) : await loadFragment(fragment); 
        } 
    });

    handleFormActions();

    const urlParams = new URLSearchParams(window.location.search);
    const fragmentFromUrl = urlParams.get('fragment');

    if (fragmentFromUrl) {
        updateFragmentContent(fragmentFromUrl);
    }

    window.addEventListener('popstate', async function() {
        const urlParams = new URLSearchParams(window.location.search);
        const fragmentFromUrl = urlParams.get('fragment');
        const dossierIdFromUrl = urlParams.get('dossier');
    
        if (fragmentFromUrl) {
            dossierIdFromUrl ? await loadFragment(fragmentFromUrl, dossierIdFromUrl) : await loadFragment(fragmentFromUrl);
        }
    });

    function handleFormActions() {
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();  // Empêcher la soumission standard du formulaire
            
                // Récupération du fragment
                const fragmentElement = document.getElementById('fragment');
                const fragment = fragmentElement ? fragmentElement.getAttribute('data-fragment') : '';

                // Récupération de l'élément dossier (si il est défini dans le DOM)
                const dossierElement = document.getElementById('fragment');
                const dossierId = dossierElement && dossierElement.hasAttribute('data-dossier') ? dossierElement.getAttribute('data-dossier') : null;

                // Création de l'objet FormData et ajout du fragment aux paramètres
                const formData = new FormData(this);
                const searchParams = new URLSearchParams(formData);
                searchParams.set('fragment', fragment); 

                if (dossierId) {
                    searchParams.set('dossier', dossierId);
                }
            
                // Construction de l'URL et redirection
                const url = `/user?${searchParams.toString()}`;
                window.location.href = url;  // Rediriger vers l'URL
            });
        }
    }
}
