import { facture } from './facture.js';
import { devis } from './devis.js';
import { menuContextuel } from './repertoire.js';
import { agenda } from './agenda.js';

export async function changeFragmentUser() {

    const UserContent = document.getElementById('userContent');
    const ContextMenuContent = document.getElementById('contextMenu');
    const ContextMenuContentDossier = document.getElementById('contextMenuDossier');
    const urlParams = new URLSearchParams(window.location.search);
    const fragmentFromUrl = urlParams.get('fragment');

    if (fragmentFromUrl) {
        const dossierIdFromUrl = urlParams.get('dossier');
        const documentIdFromUrl = urlParams.get('document');
        const repertoireIdFromUrl = urlParams.get('repertoire');
        await loadFragment(fragmentFromUrl, dossierIdFromUrl, documentIdFromUrl, repertoireIdFromUrl);
    }

    if (ContextMenuContent) {
        ContextMenuContent.addEventListener('click', handleFragmentChange);
    }

    if (ContextMenuContentDossier) {
        ContextMenuContentDossier.addEventListener('click', handleFragmentChange);
    }

    if (UserContent) {
        UserContent.addEventListener('click', (event) => {
            handleFragmentChange(event);
            const fragment = new URLSearchParams(window.location.search).get('fragment');
        });
    }

    window.addEventListener('popstate', async function() {
        const urlParams = new URLSearchParams(window.location.search);
        const fragmentFromUrl = urlParams.get('fragment');
        const dossierIdFromUrl = urlParams.get('dossier');
        const documentIdFromUrl = urlParams.get('document');
        const repertoireIdFromUrl = urlParams.get('repertoire');
    
        if (fragmentFromUrl) {
            if (dossierIdFromUrl) {
                await loadFragment(fragmentFromUrl, dossierIdFromUrl, documentIdFromUrl || repertoireIdFromUrl);
            } else if (documentIdFromUrl) {
                await loadFragment(fragmentFromUrl, documentIdFromUrl);
            } else if (repertoireIdFromUrl) {
                await loadFragment(fragmentFromUrl, repertoireIdFromUrl);
            }
        }
    });

    async function loadFragment(fragment, dossierId, documentId, repertoireId) {
        let url = `/changefragment?fragment=${fragment}`;
        if (dossierId) { url += `&dossier=${dossierId}`;}
        if (documentId) { url += `&document=${documentId}`}
        if (repertoireId) { url += `&repertoire=${repertoireId}`}
        history.pushState(null, '', `?fragment=${fragment}${dossierId ? '&dossier=' + dossierId : ''}${documentId ? '&document=' + documentId : ''}${repertoireId ? '&repertoire=' + repertoireId : ''}`);
    
        const response = await fetch(url, { 
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache', 
                'Pragma': 'no-cache',        
                'Expires': '0'
            },
        });

        if (!response.ok) { throw new Error(`Erreur HTTP : ${response.status}`);}
        const data = await response.json();

        updateFragmentContent(fragment);

        document.getElementById('fragmentContent').innerHTML = data.fragmentContent;
        document.addEventListener('click', handleFragmentChange);

        updateMenu(data);
        
        //il faut réatacher les click droit sur const ContextMenuContent = document.getElementById('contextMenu'); et  const ContextMenuContentDossier = document.getElementById('contextMenuDossier'); s'il sont présent dès qu'on change de fragment
      
        handleFormActions();
    }
    
    function updateFragmentContent(fragment) {
        switch (fragment) {
            case 'link-Factures':
                facture(urlParams.get('facture'));
                break;
            case 'link-Devis':
                devis(urlParams.get('devi'));
                break;
            case 'link-Administratif':
            case 'link-Commercial':
            case 'link-Numerique':
            case 'link-Telephone':
                menuContextuel('link-PageDocument');
                break;
            case 'link-PageDocument':
                menuContextuel('link-DocumentEdit');
                break;
            case 'link-Repertoire':
                menuContextuel('link-PageRepertoire');
                break;
            case 'link-espacepersonnel':
                // menuContextuel('link-PageRepertoire');
                // break;
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

    function handleFragmentChange(event) {
        const button = event.target.closest('.change-fragment');
        if (button) {
            event.preventDefault();
            const fragment = button.getAttribute('data-fragment');
            const dossier = button.getAttribute('data-dossier') || null;
            const document = button.getAttribute('data-document') || null;
            const repertoire = button.getAttribute('data-repertoire') || null;
            loadFragment(fragment, dossier, document, repertoire);
        }
    }

 

    function handleFormActions() {
        const searchForm = document.getElementById('searchForm');
        const searchFormFacture = document.getElementById('searchFormFacture');
        const fragmentElement = document.getElementById('fragment');
        const fragmentFacture = document.getElementById('fragmentFacture');
        const fragmentDevis = document.getElementById('fragmentDevis');
    
        let fragment = "";
        let dossierId = "";
        let facture = "";
        let devis = "";

        if(fragmentElement){
            fragment = fragmentElement?.getAttribute('data-fragment') || '';
            dossierId = fragmentElement?.getAttribute('data-dossier') || '';
            facture = fragmentElement?.getAttribute('data-facture') || '';
            devis = fragmentElement?.getAttribute('data-devi') || '';
        }

        if(fragmentFacture){
            fragment = fragmentFacture?.getAttribute('data-fragment') || '';
            facture = fragmentFacture?.getAttribute('data-facture') || '';
        }  

        if(fragmentDevis){
            fragment = fragmentDevis?.getAttribute('data-fragment') || '';
            devis = fragmentDevis?.getAttribute('data-devi') || '';
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

    async function updateMenu(data) { 
        const serviceLink = data.fragment;
        let dataService = "";
    
        dataService = await fetchService(data); 
    
        const visibleMenus = [...document.querySelectorAll('.header-menu')].filter(item => {
            return item.offsetParent !== null;
        });
        
        visibleMenus.forEach(item => {
            const itemFragment = item.querySelector('button').getAttribute('data-fragment');
            const itemService = item.querySelector('button').getAttribute('data-service');

            if (itemFragment === serviceLink || itemService === dataService) {
                item.classList.add('bg-success', 'rounded');
            } else {
                item.classList.remove('bg-success', 'rounded');
            }
        });
    }

    async function fetchService(data) {
        let service = "";

        if (data.dossier !== null) {
            // On attend la réponse du fetch avant de récupérer la donnée
            const response = await fetch(data.dossier.services);
            const serviceData = await response.json();
            service = serviceData.name;  // Assurez-vous que la propriété name existe
        } else if (data.document !== null) {
            let dossier = "";
            const responseDossier = await fetch(data.document.dossier);
            const dossierData = await responseDossier.json();
            dossier = dossierData.id;
            
            const responseServices = await fetch(`/api/dossiers/${dossier}`);
            const servicesData = await responseServices.json();
            dossier = servicesData.services;

            const responseService = await fetch(dossier);
            const serviceData = await responseService.json();
            service = serviceData.name;
        } else if (data.repertoire !== null) {
            let dossier = "";
            const responseDossier = await fetch(data.repertoire.dossier);
            const dossierData = await responseDossier.json();
            dossier = dossierData.id;
    
            const responseServices = await fetch(`/api/dossiers/${dossier}`);
            const servicesData = await responseServices.json();
            dossier = servicesData.services;

            const responseService = await fetch(dossier);
            const serviceData = await responseService.json();
            service = serviceData.name;
        }
    
        return service;
    }

}
