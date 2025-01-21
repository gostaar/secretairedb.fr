### Sur l'ordinateur doit être installé node et composer 

# **A FAIRE**
```bash
docker-compose up -d
symfony console d:m:m
symfony console d:f:l
```
## **Docker**
```bash
docker ps
docker exec -it symf-php-1 sh
```
### **Tout supprimer:**
```bash 
docker system prune -a --volumes
```

## **Commandes utiles**
```bash 
composer install
npm install
npm run dev
php bin/console make:migration
docker exec -it symf-php-1 php /code/bin/console doctrine:migrations:migrate --no-interaction
```

## **INFOS**
```bash
docker exec symf-mysql-1 mysql -u root -p
use symfony;
```
password: secret

| Utilisateur       | Mot de passe        |
|-------------------|---------------------|
| user@example.com  | user                |
| admin@example.com | admin               |

## **HASH**
```bash
php bin/console security:hash-password
```

## **APACHE**
```bash
docker-compose exec apache service apache2 restart
```

<details>
  <summary><h1>Procédure d'ajout de fragment</h1></summary>

## **1. Dans le template**
### **a) Définir les besoins**
- Déterminez si un **fragment supplémentaire** est nécessaire au niveau de l'URL.

### **b) Création d'un bouton pour le nouveau fragment**
- Créez un bouton avec les attributs `data-fragment` appropriés pour accéder au nouveau template.

**Exemple :**
```html
<div class="d-flex flex-column align-items-center p-1" style="position: relative; width: 130px;">
    <button 
        type="button" 
        data-fragment="link-PageRepertoire"
        data-dossier="{{ dossier.id }}"  <!-- Nouveau fragment dans l'URL -->
        class="change-fragment btn d-flex flex-column flex-start align-items-center text-decoration-none"
        style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: transparent; border: none; z-index: 1; padding: 0;">
    </button>
    <i class="fas fa-folder fa-3x text-center text-warning"></i>
    <p class='text-black'>{{ dossier.name }}</p>
</div>
```
##  2. Dans le contrôleur
### a) Vérifiez la présence du nouveau fragment
Utilisez `Request` pour récupérer le fragment depuis l'URL :
```php
$dossierId = $request->query->get('dossier');
```

### b) Gérez la condition d'existence
Si le fragment est requis, passez sa valeur (ou `null`) au service :
```php
$formData = $this->routeDataService->getFormData($fragment, $dossierId ? (int) $dossierId : null);
```

### c) Mettez à jour la route AJAX
Vérifiez que la route `/changefragment` gère aussi le nouveau fragment. Cela permettra de mettre à jour les données via les requêtes AJAX.

## 3. Dans le service
### a) Préparez la route
Ajoutez le support du nouveau fragment dans `RouteDataService.php`.

### b) Mettez à jour `getFormData()`
Ajoutez un nouvel argument optionnel pour prendre en charge le fragment :
```php
public function getFormData(string $fragment, ?int $dossierId = null): array
```

### c) Mettez à jour `getTemplateMapping()`
Associez le fragment à son template correspondant :
```php
'link-Acceuil' => 'userPage/_mainContent.html.twig',
```

### d) Ajoutez le fragment dans `getRouteConfig()`
Vérifiez si le fragment est défini avant de l'envoyer à la fonction :
```php
$dossierId 
    ? $routeConfig = $this->getRouteConfig($dossiersDocuments, $user, $dossierId) 
    : $routeConfig = $this->getRouteConfig($dossiersDocuments, $user);
```

Adaptez la signature de la fonction pour inclure le fragment :
```php
private function getRouteConfig(array $dossiersDocuments, $user, ?int $dossierId = null): array
```

### e) Construisez les données nécessaires
Ajoutez les entités et formulaires requis dans la configuration des routes.

**Exemple :**
```php
'link-PageRepertoire' => $this->createLinkConfig([
    'addContact' => \App\Form\ContactType::class,   
    'addRepertoire' => \App\Form\RepertoireType::class,
], [
    'dossier' => $dossierId ? $this->entityManager->getRepository(\App\Entity\Dossier::class)->find($dossierId) : null,
    'repertoires' => $dossierId ? $this->entityManager->getRepository(\App\Entity\Repertoire::class)->findBy(['dossier' => $dossierId]) : [],
    'contacts' => $this->entityManager->getRepository(\App\Entity\Contact::class)->findAll(),
]),
```

### f) Ajoutez les recherches d'entités
Vérifiez la présence du fragment et appliquez les fonctions de recherche nécessaires :
```php
[
    'dossier' => $dossierId ? $this->entityManager->getRepository(\App\Entity\Dossier::class)->find($dossierId) : null,
    'repertoires' => $dossierId ? $this->entityManager->getRepository(\App\Entity\Repertoire::class)->findBy(['dossier' => $dossierId]) : [],
    'contacts' => $this->entityManager->getRepository(\App\Entity\Contact::class)->findAll(),
],
```

### g) Référencement dans les switches
Si une nouvelle entité est introduite, ajoutez-la dans le `switch` pour gérer les vues :
```php
...\App\Entity\Contact)->createView();
```

## IMPORTANT
Les données supplémentaires (au-delà de `services` et `user`) doivent être vérifiées et ajoutées dans le deuxième tableau lors de l'appel de `createLinkConfig()`.

##  4. Dans le assets/js/user/main.js
### a) Ajouter le nouveau fragment à loadFragment()
```js
loadFragment(dossierId = null)
```

### b) Préparer le fragment pour l'url dans loadFragment()
```js
if (dossierId) { url += `&dossier=${dossierId}`;}
history.pushState(null, '', `?fragment=${fragment}${dossierId ? '&dossier=' + dossierId : ''}`);
```

### c) Si besoin d'autres fonctions, ajouter dans updateFragmentContent(fragment) 
Où fragment est le fragment de base, rien à changer ici
Ajouter l'import de la fonction...
```js
import { repertoire } from './repertoire.js';
case 'link-Repertoire':
    repertoire();
    break;
```

### d) Dans la fonction d'appel UserContent.addEventListener ('click', async function(event)) -> button
On récupère le nouveau fragment et on vérifie s'il existe
```js
const dossier = button.getAttribute('data-dossier');
dossier ? await loadFragment(fragment, dossier) : await loadFragment(fragment); 
```

### e) Dans la fonction d'appel window.addEventListener('popstate', async function() {}) -> actualisation
On récupère le nouveau fragment et on vérifie s'il existe
```js
const dossierIdFromUrl = urlParams.get('dossier');
    
if (fragmentFromUrl) {
    dossierIdFromUrl ? await loadFragment(fragmentFromUrl, dossierIdFromUrl) : await loadFragment(fragmentFromUrl);
} 
```

</details>