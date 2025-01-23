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

    async function loadFragment(fragment, dossierId, documentId) {
        loadingIndicator.style.display = 'flex'; 
        let url = `/changefragment?fragment=${fragment}`;
        if (dossierId) { url += `&dossier=${dossierId}`;}
        if (documentId) { url += `&document=${documentId}`}
        history.pushState(null, '', `?fragment=${fragment}${dossierId ? '&dossier=' + dossierId : ''}${documentId ? '&document=' + documentId : ''}`);

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
                facture(urlParams.get('facture'));
                break;
            case 'link-Devis':
                devis(urlParams.get('devis'));
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
        handleFormActions();
    }

    UserContent.addEventListener('click', async function(event) {
        const button = event.target;

        if (button.classList.contains('change-fragment')) {
            event.preventDefault();  
            const fragment = button.getAttribute('data-fragment');
            const dossier = button.getAttribute('data-dossier') || null;
            const document = button.getAttribute('data-document') ||null;

            await loadFragment(fragment, dossier, document); 
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
        const documentIdFromUrl = urlParams.get('document');
    
        if (fragmentFromUrl) {
            if(dossierIdFromUrl){
                documentIdFromUrl ? await loadFragment(fragmentFromUrl, dossierIdFromUrl, documentIdFromUrl) : await loadFragment(fragmentFromUrl, dossierIdFromUrl);
            } else {
                documentIdFromUrl ? await loadFragment(fragmentFromUrl, documentIdFromUrl) : await loadFragment(fragmentFromUrl);
            }
        }
    });

    function handleFormActions() {
        const searchForm = document.getElementById('searchForm');
        const searchFormFacture = document.getElementById('searchFormFacture');
        const fragmentElement = document.getElementById('fragment');
        const fragmentFacture = document.getElementById('fragmentFacture');
    
        let fragment = "";
        let dossierId = "";
        let facture = "";
        let devis = "";

        if(fragmentElement){
            fragment = fragmentElement?.getAttribute('data-fragment') || '';
            dossierId = fragmentElement?.getAttribute('data-dossier') || '';
            facture = fragmentElement?.getAttribute('data-facture') || '';
            devis = fragmentElement?.getAttribute('data-devis') || '';
        }

        if(fragmentFacture){
            fragment = fragmentFacture?.getAttribute('data-fragment') || '';
            dossierId = fragmentFacture?.getAttribute('data-dossier') || '';
            facture = fragmentFacture?.getAttribute('data-facture') || '';
            devis = fragmentFacture?.getAttribute('data-devis') || '';
        }        
    
        const handleFormSubmit = (event, additionalParams = {}) => {
            event.preventDefault();
    
            const formData = new FormData(event.target);
            const searchParams = new URLSearchParams(formData);
    
            if(fragment){searchParams.set('fragment', fragment)};
            if (dossierId) searchParams.set('dossier', dossierId);
    
            Object.entries(additionalParams).forEach(([key, value]) => {
                if (value) searchParams.set(key, value);
            });
    
            const url = `/user?${searchParams.toString()}`;
            window.location.href = url;
        };
    
        if (searchForm) {
            searchForm.addEventListener('submit', (event) =>
                handleFormSubmit(event)
            );
        }
    
        if (searchFormFacture) {
            searchFormFacture.addEventListener('submit', (event) =>
                handleFormSubmit(event, { facture, devis })
            );
        }
    }
}
