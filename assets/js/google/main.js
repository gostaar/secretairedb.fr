import { handlePageSpecificUI, updateUIBasedOnState, callFetchCalendar, handleSyncClick, handleSyncSymfonyClick, showDetailsEvent } from './ui.js';
import { handleAuthButtonClick, handleSubmitClick, gapiLoaded, gisLoaded} from './auth.js';
import { initialisationFullCalendar, handleSignoutClick } from './utils.js';
import {updateEventList} from './google-calendar.js';

window.gapiLoaded = gapiLoaded;
window.gisLoaded = gisLoaded;

document.addEventListener('DOMContentLoaded', async () => {
    
    const authButton = document.getElementById('authorize_button');
    if(authButton){authButton.addEventListener('click', handleAuthButtonClick);}
    
    const isPageSpecific = handlePageSpecificUI();
    if (isPageSpecific) { ready();}
});

async function ready() {
    initialisationFullCalendar();
    callFetchCalendar();
    // await updateEventList('eventGoogle');
    updateUIBasedOnState();

    const signoutButton = document.getElementById('signout_button');
    const createEventForm = document.getElementById('create-event-form');
    const syncButton = document.getElementById("syncButton");
    const syncButtonSymfony = document.getElementById("syncButtonSymfony");
    const listEventDetails = document.querySelectorAll('#eventList li');

    if(createEventForm){
        console.log("createEventForm");
    };

    // signoutButton.addEventListener('click', handleSignoutClick);
    createEventForm.addEventListener('submit', handleSubmitClick);
    // syncButton.addEventListener('click', async () => { await handleSyncClick();}); 
    // syncButtonSymfony.addEventListener('click', async () => { await handleSyncSymfonyClick(); }); 
    // listEventDetails.forEach(item => { item.addEventListener('click', showDetailsEvent); });
}