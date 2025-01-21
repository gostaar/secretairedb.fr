import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        addLabel: String,
        deleteLabel: String
    }

    /**
    * Injecte dynamiquement le bouton "Ajouter" et les boutons "Supprimer"
    */
    connect() {
        this.index = this.element.childElementCount
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.innerText = this.addLabelValue || 'Ajouter une ligne'
        btn.setAttribute('type', 'button')
        btn.addEventListener('click', this.addElement)
        this.element.childNodes.forEach(this.addDeleteButton)
        this.element.append(btn)
    }

    /**
     * Ajoute une nouvelle entrée dans la structure HTML
     * 
     * @param {MouseEvent} e
     */
    addElement = (e) => {
        e.preventDefault()
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild
        element.setAttribute('class', 'd-flex  justify-content-between align-items-end');
        const firstDiv = element.querySelector('div'); // Sélectionne le premier div de l'élément
        if (firstDiv) {
            firstDiv.classList.add('d-flex');
        }
        this.addDeleteButton(element)
        this.index++
        e.currentTarget.insertAdjacentElement('beforebegin', element)
    }

    /**
     * Ajoute le bouton pour supprimer une ligne
     * 
     * @param {HTMLElement} item
     */
    addDeleteButton = (item) => {
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-danger mb-3')
        btn.innerHTML = this.deleteLabelValue || `<i class="fas fa-times"></i>`
        btn.setAttribute('type', 'button')
        item.append(btn)
        btn.addEventListener('click', e => {
            e.preventDefault()
            item.remove()
        })
    }
}