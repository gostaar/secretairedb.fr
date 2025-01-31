import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        addLabel: String,
        deleteLabel: String
    }

    connect() {
        // Création du bouton "Ajouter"
        this.index = this.element.childElementCount;
        const btn = document.createElement('button');
        btn.setAttribute('class', 'btn btn-secondary');
        btn.innerText = this.addLabelValue || 'Ajouter une image';
        btn.setAttribute('type', 'button');
        btn.addEventListener('click', this.addElement);
        
        Array.from(this.element.children).forEach(child => {
            this.addEditButton(child); // Ajoute le bouton d'édition
            this.addDeleteButton(child); // Ajoute le bouton de suppression
        });
        
        this.element.append(btn);
    }
    
    /**
     * Ajoute une nouvelle entrée dans la structure HTML
     * 
     * @param {MouseEvent} item
     */
    addElement = (item) => {
        item.preventDefault();

        const newIndex = this.index++;
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replace(/__name__/g, newIndex)
        ).firstElementChild;
    
        element.classList.add('d-flex');
        
        // Sélectionne les inputs et applique les classes
        const inputs = element.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            const parentInput = input.parentElement;
            const container = parentInput.parentElement;
            parentInput.classList.add('col-6', 'px-2');
            container.classList.add('col-8');
    
            // Mise à jour du name pour éviter les conflits
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${this.index}]`);
            }
        });
    
        // Ajoute une classe au premier div trouvé
        const firstDiv = element.querySelector('div');
        if (firstDiv) {
            firstDiv.classList.add('d-flex');
        }
    
        this.addDeleteButton(element);
        this.index++;
        item.currentTarget.insertAdjacentElement('beforeBegin', element);
    }
    
    /**
     * Ajoute le bouton pour supprimer une ligne
     * 
     * @param {HTMLElement} item
     */
    addDeleteButton = (item) => {
        const btn = document.createElement('button');
        btn.setAttribute('class', 'btn btn-secondary mb-3');
        btn.innerHTML = this.deleteLabelValue || `<i class="fas fa-times"></i>`;
        btn.setAttribute('type', 'button');
        item.append(btn)


        btn.addEventListener('click', e => {
            e.preventDefault();
            item.remove();
        });
    }
    
    /**
     * Ajoute le bouton pour supprimer une ligne
     * 
     * @param {HTMLElement} item
     */
    addEditButton = (item) => {
        const btn = document.createElement('button');
        btn.setAttribute('class', 'btn btn-primary mb-3');
        btn.innerHTML = this.deleteLabelValue || `<i class="fas fa-pencil-alt"></i>`;
        btn.setAttribute('type', 'button');
        
        const fieldSet = item.querySelector('div');
        const fieldsetId = fieldSet.id;
        const imageCount = fieldsetId.split('_').pop();
        const element = document.getElementById(imageCount);
        const imageId = element.hasAttribute("data-image-id")
            ? element.getAttribute('data-image-id')
            : null;
            
        // Ajouter les attributs data-bs-toggle et data-bs-target
        btn.setAttribute('data-bs-toggle', 'modal');
        btn.setAttribute('data-bs-target', `#editImage${imageId}`);
    
        item.append(btn);
    };
}
