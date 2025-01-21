import { isAccessTokenValid } from './auth.js';
import { fetchCalendars, handleSync, handleSyncSymfony } from './google-calendar.js';

export async function updateUIBasedOnState() {
    const isAuthorized = await isAccessTokenValid();

    if (isAuthorized) {
        console.log('authorized');
        //document.getElementById('authorize_button').classList.add('d-none');
        // document.getElementById('signout_button').classList.remove('d-none');
        // document.getElementById('create-event-button').classList.remove('d-none');
        // document.getElementById('message').classList.remove('d-none');
        // document.getElementById('content').classList.add('d-none');
        // document.getElementById('syncButtonsEvent').classList.remove('d-none');
    } else {
        console.log('none authorized');
        // document.getElementById('authorize_button').classList.remove('d-none');
        // document.getElementById('signout_button').classList.add('d-none');
        // document.getElementById('create-event-button').classList.remove('d-none');
        // document.getElementById('syncButtonsEvent').classList.add('d-none');
        // document.getElementById('message').classList.add('d-none');
        // document.getElementById('authorize_button').innerHTML = `
        //     <img src="build/images/google.png" alt="Google logo" style="width: 20px; margin-right: 10px;">
        //     Connexion avec Google
        // `;
    } 
}

export function resetFormFields() {
    document.getElementById('event-title').value= "";
    document.getElementById('event-description').value= "";
    document.getElementById('event-start-date').value= "";
    document.getElementById('event-end-date').value= "";
}

export function handlePageSpecificUI() {
    const isUserAgendaPage = window.location.pathname.startsWith("/user_agenda");
    
    if (isUserAgendaPage) {
        console.log("isUserAgendaPage");
        // document.getElementById('message').classList.add('d-none');
        // document.getElementById('authorize_button').classList.add('d-none');
        // document.getElementById('signout_button').classList.add('d-none');
        // document.getElementById('create-event-button').classList.add('d-none');
        // document.getElementById('content').classList.add('d-none');
        // document.getElementById('syncButtonsEvent').classList.add('d-none');
    }
    
    return !!isUserAgendaPage;
}

export async function callFetchCalendar(){
    const isAuthorized = await isAccessTokenValid();

    if (isAuthorized) {
        const cookie = document.cookie.split('; ').find(row => row.startsWith('access_token='));
        const accessToken = cookie.split('=')[1];
        fetchCalendars(accessToken);
    }
}

export async function handleSyncClick(){
    const loadingSpinner = document.getElementById('loadingSpinner');
    const successSyncModal = new bootstrap.Modal(document.getElementById('successSyncModal'));
    
    const userConfirmed = await showConfirmationModal(); 
    if (!userConfirmed) {
        return;
    }
    loadingSpinner.style.display = 'block';
    await handleSync(); 
    successSyncModal.show();
    showSuccessModalClose(successSyncModal);
    loadingSpinner.style.display = 'none';
}

export async function handleSyncSymfonyClick(){
    const loadingSpinner = document.getElementById('loadingSpinner');
    const successSyncModal = new bootstrap.Modal(document.getElementById('successSyncModal'));
    
    const userConfirmed = await showConfirmationModal(); 
    if (!userConfirmed) {
        return;
    }
    loadingSpinner.style.display = 'block';
    await handleSyncSymfony(); 
    successSyncModal.show();
    showSuccessModalClose(successSyncModal);
    loadingSpinner.style.display = 'none';
}

async function showConfirmationModal() {
    return new Promise((resolve, reject) => {
        const confirmButton = document.getElementById('confirmSyncButton');
        const cancelButton = document.getElementById('cancelSyncButton'); 
        const backdrops = document.querySelectorAll('.modal-backdrop');

        const onConfirm = () => {
            cleanup();
            confirmModal.hide();
            resolve(true);
        };

        const onCancel = () => {
            cleanup(); 
            confirmModal.hide();
            resolve(false); 
            backdrops.forEach(backdrop => backdrop.classList.add('d-none'));
        };

        const cleanup = () => {
            confirmButton.removeEventListener('click', onConfirm);
            cancelButton.removeEventListener('click', onCancel);
        };

        confirmButton.addEventListener('click', onConfirm);
        cancelButton.addEventListener('click', onCancel);

        const confirmModal = new bootstrap.Modal(document.getElementById('confirmSyncModal'));
        confirmModal.show();

    });
}

function showSuccessModalClose(successSyncModal) {
    const onClose = () => {
        successSyncModal.hide();
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.classList.add('d-none'));
        window.removeEventListener('click', onClose); 
    };

    window.addEventListener('click', onClose); 
} 

export function showDetailsEvent(event) {
    const listItem = event.target.closest('li');
    const title = listItem.getAttribute('data-title');
    const start = listItem.getAttribute('data-start');
    const description = listItem.getAttribute('data-description');
    const location = listItem.getAttribute('data-location');
    const end = listItem.getAttribute('data-end');
    const google = listItem.getAttribute('data-google') === '' ? "Non" : "Oui";

    document.getElementById('eventTitle').textContent = `Titre : ${title}`;
    document.getElementById('eventDate').textContent = `Date : ${start}`;
    document.getElementById('eventDescription').textContent = `Description : ${description}`;
    document.getElementById('eventLocation').textContent = `Lieu : ${location}`;
    document.getElementById('eventEnd').textContent = `Date de fin : ${end}`;
    document.getElementById('eventGoogle').textContent = `Importé de google : ${google}`;

    const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    modal.show();
}