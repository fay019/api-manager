### Système de Promotions (Promos)

Le module `Promos` permet de gérer des bannières promotionnelles ou informatives diffusées via l'API vers des clients externes (applications mobiles, sites web).

#### 1. Fonctionnement Général
Le système permet de créer des annonces avec un titre, un contenu, une image et un bouton d'appel à l'action (CTA). 
- **Planification** : Chaque promo peut avoir une date de début (`starts_at`) et de fin (`ends_at`). La date de fin doit être postérieure ou égale à la date de début.
- **Priorité** : Si plusieurs promos sont actives en même temps, celle avec la `priority` la plus élevée (échelle de 1 à 10, via un curseur) est renvoyée par l'API.
- **Statuts** : Les promos passent par différents statuts pour une gestion explicite :
    - `draft` (Brouillon) : La promo est en cours de rédaction et n'est jamais diffusée. Les dates n'influencent pas ce statut.
    - `scheduled` (Programmé) : La promo est prête mais sa date de début est dans le futur.
    - `published` (Publié) : La promo est active et diffusée car nous sommes dans la période de validité.
    - `archived` (Archivé) : La promo est terminée car sa date de fin est passée.

#### 2. Gestion via Filament (Administration)
L'interface d'administration se trouve sous l'onglet **Marketing > Promotions**.

- **Création / Édition** :
    - **Informations** : Titre, Contenu détaillé, Texte du bouton et URL de destination.
    - **Paramètres** : 
        - Statut : Mis à jour automatiquement selon les dates (sauf si en `Brouillon`).
        - **Logique d'automatisation du statut** :
            - `starts_at` futur (ex: 30 jan) & `ends_at` futur -> `Programmé`.
            - `ends_at` passé (ex: 15 jan) -> `Archivé`.
            - `starts_at` passé ou vide & `ends_at` futur ou vide -> `Publié`.
            - `starts_at` passé & sans `ends_at` -> `Publié` (infini).
            - `starts_at` futur & sans `ends_at` -> `Programmé` (puis publiera à l'infini).
            - **Sans aucune date** (`starts_at` et `ends_at` vides) -> `Brouillon`.
            - **Note** : Les comparaisons de dates sont désormais strictes (sans marge de tolérance) pour correspondre exactement à l'heure du serveur.
            - **Validation en direct** : L'erreur de cohérence des dates disparaît instantanément dès qu'une date est corrigée grâce à la validation "live".
        - Priorité (curseur de 1 à 10 avec affichage de la valeur au survol, 10 étant le plus prioritaire). La barre est colorée si le flag de fonctionnalité est actif.
    - **Média** : Upload d'une image d'illustration.

#### 3. API - Récupération de la Banner
L'API permet de récupérer la promo active la plus prioritaire.

**Endpoint :** `GET /api/promo/banner.json`

**Headers requis :**
- `X-Api-Key` : Votre clé API valide.

**Requête :**
Pas de paramètres requis dans le corps de la requête.

**Réponse (Success - 200 OK) :**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Bienvenue sur l'API Manager",
        "content": "Découvrez toutes les fonctionnalités de notre nouvelle interface.",
        "image_url": "promos/nom-de-l-image.png",
        "cta_text": "Voir la doc",
        "cta_url": "https://example.com/docs",
        "priority": 10
    }
}
```

**Réponse (Aucune promo - 404 Not Found) :**
```json
{
    "status": "error",
    "message": "No active promo available"
}
```

#### 4. API - Tracking d'événements
Il est possible d'enregistrer les clics ou les vues via l'API.

**Endpoint :** `POST /api/promo/event`

**Payload :**
```json
{
    "promo_id": 1,
    "event_type": "click" 
}
```
*Note: `event_type` peut être `view` ou `click`.*

#### 5. Cache et Performance
Pour optimiser les performances, la promo active est mise en cache. 
- La durée par défaut du cache est de 60 minutes (configurable via `api.promo_cache_ttl`).
- Le cache est automatiquement vidé lors de la modification ou de la suppression d'une promo via l'interface Filament.
