import { changeFragmentSite } from './site/main.js';
import { changeFragmentUser } from './user/main.js';

export function initializeNavigation() {
    // const loadingIndicator = document.getElementById('loadingIndicator');

    window.appState = {
        endLoadingState: false,
    };

    navigation.addEventListener('navigate', () => {
        // if (!window.appState.endLoadingState) {
        //     loadingIndicator.style.display = 'flex';
        // }
        window.appState.endLoadingState = false;
    });

    window.addEventListener('popstate', () => {
        window.appState.endLoadingState = true;
        // loadingIndicator.style.display = 'none';
    });
}

export function initializeFragments() {
    window.addEventListener('DOMContentLoaded', () => {
        if (window.location.pathname.startsWith('/user')) {
            changeFragmentUser();
        }
        
        if (window.location.pathname === '/') {
            changeFragmentSite(); 
        }

        window.appState.endLoadingState = true;
        // loadingIndicator.style.display = 'none';
    });
}