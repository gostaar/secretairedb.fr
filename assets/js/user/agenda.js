export function agenda(){
    function handleGoogleEventButton() {
        const btnNewGoogleEvent = document.getElementById('btnNewGoogleEvent');
        if (btnNewGoogleEvent) {
            console.log("btnNewGoogleEvent");
            btnNewGoogleEvent.addEventListener('click', () => toggleSectionAgenda('link-newGoogleEvent'));
        }
    }


    function clearData() {
        sessionStorage.clear();
        localStorage.clear();
        const cookies = document.cookie.split(';');
        cookies.forEach(cookie => {
            const cookieName = cookie.split('=')[0].trim();
            document.cookie = `${cookieName}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/; secure; sameSite=None`;
        });
    }

    // Détails event
    function detailsEvent(){
        const eventList = document.querySelectorAll('#eventList .list-group-item');
        const modalTitle = document.getElementById('eventTitle');
        const modalDate = document.getElementById('eventDate');
        const modalDescription = document.getElementById('eventDescription');
        const modalLocation = document.getElementById('eventLocation');
        const modalEnd = document.getElementById('eventEnd');
        const modalGoogle = document.getElementById('eventGoogle');
        const modalGoogleId = document.getElementById('eventGoogleId');

        eventList.forEach(item => {
            item.addEventListener('click', () => {
                modalTitle.textContent = `Titre : ${item.dataset.title}`;
                modalDate.textContent = `Début : ${item.dataset.start}`;
                modalEnd.textContent = `Fin : ${item.dataset.end}`;
                modalDescription.textContent = `Description : ${item.dataset.description}`;
                modalLocation.textContent = `Lieu : ${item.dataset.location}`;
                modalGoogle.textContent = `Google Calendar ID : ${item.dataset.google}`;
                modalGoogleId.textContent = `Google Event ID : ${item.dataset.googleid}`;
            });
        });
    }
}
