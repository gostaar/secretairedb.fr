import { initializeNavigation, initializeFragments } from './js/mainFunctions.js';
import { Application } from 'stimulus';
import { definitionsFromContext } from 'stimulus/webpack-helpers';
import FormCollectionController from './controllers/form-collection_controller'; // Importer le contrôleur Stimulus

function main() {
    document.addEventListener('DOMContentLoaded', () => {
        // Créer et démarrer l'application Stimulus
        const application = Application.start();

        // Charger tous les contrôleurs Stimulus depuis le dossier controllers
        const context = require.context('./controllers', true, /.js$/);
        application.load(definitionsFromContext(context));

        // Vous pouvez aussi enregistrer le contrôleur manuellement
        application.register('form-collection', FormCollectionController);

        // Initialiser les autres fonctionnalités
        initializeFragments();
        initializeNavigation();
    });
}

main();