import { updateUIBasedOnState, resetFormFields } from './ui.js';
import { addEventCalendars, updateEventList, addEventToGoogle } from './google-calendar.js';

let tokenClient;
let gapiInited = false;
let gisInited = false;

export async function gapiLoaded() {
    gapi.load('client', initializeGapiClient);
}

export async function initializeGapiClient() {
    try {
        const apiKey = process.env.GOOGLE_API_KEY;
        const discoveryDoc = process.env.DISCOVERY_DOC;
        const clientId = process.env.GOOGLE_CLIENT_ID;
        const scopes = process.env.GOOGLE_OAUTH_SCOPE; 

        if (!apiKey || !discoveryDoc || !clientId) {
            return;
        }

        await gapi.client.init({
            apiKey: apiKey,
            discoveryDocs: [discoveryDoc],
        });

        await gapi.auth2.init({
            client_id: clientId,
            scope: scopes,
        });

        gapiInited = true;

        maybeEnableButtons(); 
    } catch (error) {
        return;
    }
}

export async function gisLoaded() {
    tokenClient = google.accounts.oauth2.initTokenClient({
        client_id: process.env.GOOGLE_CLIENT_ID,
        scope: process.env.GOOGLE_OAUTH_SCOPE,
        callback: handleTokenCallback,
    });
    gisInited = true;
    maybeEnableButtons();
}

function maybeEnableButtons() {
    if (gapiInited && gisInited) {
        updateUIBasedOnState();
    }
}

function handleTokenCallback(response) {
    if (response.error) {
        console.error('Authorization failed:', response.error);
        return;
    }
    console.log('Access token received:', response.access_token);
}

export async function setAccessToken(token, expiresIn = 3600) {
    const expirationTime = new Date(Date.now() + expiresIn * 1000);
    document.cookie = `access_token=${token}; expires=${expirationTime.toUTCString()}; path=/; secure; samesite=none`;
    localStorage.setItem('access_token_expiration', expirationTime.toISOString());
}

export async function isAccessTokenValid() {
    const cookie = document.cookie.split('; ').find(row => row.startsWith('access_token='));
    if (!cookie) return false;

    const tokenValue = cookie.split('=')[1];
    const expirationString = localStorage.getItem('access_token_expiration');

    if (!tokenValue || !expirationString) return false;

    const expirationTime = new Date(expirationString).getTime();
    return Date.now() < expirationTime;
}

export async function handleAuthButtonClick() {
    if (!tokenClient) {
        console.error('TokenClient is not initialized.');
        return;
    }

    tokenClient.callback = async (response) => {
        if (response.error) {
            console.error('Authorization failed:', response.error);
            return;
        }
        try {
            window.location.href = "http://localhost:8080/user_agenda";
            setAccessToken(response.access_token, response.expires_in || 3600);
        } catch (error) {
            console.error('Error during authentication:', error);
        }
    };

    const currentToken = gapi.client.getToken();
    if (currentToken && await isAccessTokenValid()) {
        tokenClient.requestAccessToken({ prompt: '' });
    } else {
        tokenClient.requestAccessToken({ prompt: 'consent' });
    }

}

export async function handleLogout() {
    const tokenCookie = document.cookie.split('; ').find(row => row.startsWith('access_token='));
    const token = tokenCookie.split('=')[1];

    if (token) {
        google.accounts.oauth2.revoke(token, () => {
            gapi.client.setToken(null);
        });

        document.cookie = "access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; secure, sameSite=None";
        localStorage.clear();
        sessionStorage.clear();

        await updateUIBasedOnState();
    }
}

export async function handleChangePage() {
    await handleLogout();
}

export async function handleSubmitClick(event) {
    event.preventDefault();

    const tokenCookie = document.cookie.split('; ').find(row => row.startsWith('access_token='));
    const token = tokenCookie ? tokenCookie.split('=')[1] : null;

    const eventData = {
        title: document.getElementById('event-title').value,
        description: document.getElementById('event-description').value,
        start: document.getElementById('event-start-date').value,
        end: document.getElementById('event-end-date').value,
        location: document.getElementById('event-location').value,
    };

    async function getPrimaryCalendarId(token) {
        try {
            const response = await fetch('https://www.googleapis.com/calendar/v3/calendars/primary', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la récupération du calendarId principal');
            }

            const calendarData = await response.json();
            return calendarData.id; 
        } catch (error) {
            console.error(error);
            return null;
        }
    }

    let calendarId = null;

    if (token) {
        calendarId = await getPrimaryCalendarId(token);
    }

    if (!calendarId) {
        calendarId = "";
    }
    const googleEventId = await addEventToGoogle(token, eventData);

    const eventDataSymfony = {
        ...eventData,
        google_calendar_event_id: [calendarId, googleEventId],
    };

    await addEventCalendars(eventData, eventDataSymfony, token);

    
    await updateEventList('eventGoogle');
    // window.location.reload();
}

