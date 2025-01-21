import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import {handleLogout} from './auth.js';
import {emptyEvents} from './google-calendar.js';

let calendar = null;

export async function initialisationFullCalendar() {
    const calendarEl = document.getElementById('calendar');

    // const eventsFromSymfony = await fetchSymfonyEvents();

    calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        locale: 'fr',
        timeZone: 'Europe/Paris',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
        events: [] 
    });
    calendar.render();
}

async function fetchSymfonyEvents() {
    try {
        const response = await fetch('/events', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Erreur lors de la récupération des événements');
        }

        const events = await response.json();

        return events.map(event => ({
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            description: event.description,
            location: event.location,
        }));
    } catch (error) {
        console.error('Erreur lors de la récupération des événements :', error);
        return [];
    }
}

export function injectEventsToFullCalendar(events) {
    if (calendar) {
        events.forEach(event => {
            const existingEvent = calendar.getEvents().find(existing => 
                existing.title === event.title &&
                existing.extendedProps.calendarId === event.calendarId 
            );

            if (!existingEvent) {
                calendar.addEvent(event); 
            }
        });
    }
}


export function injectEventsToFullCalendarAfterEmpty(event) {
    if (calendar) {
        calendar.removeAllEventSources();
        if (event.length > 0) {
            calendar.addEventSource(event);
        }
    }
}

export async function handleSignoutClick() {
    await handleLogout();
    const eventSources = calendar.getEventSources();
    eventSources.forEach(source => source.remove());
    emptyEvents();
    try {
        const response = await fetch('/events', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const events = await response.json();

        // Ajouter les événements récupérés au calendrier
        events.forEach(event => {
            calendar.addEvent({
                id: event.id,
                title: event.title,
                start: event.start,
                end: event.end,
                description: event.description,
                location: event.location,
            });
        });
    } catch (error) {
        console.error('Failed to fetch events:', error);
    }
}
