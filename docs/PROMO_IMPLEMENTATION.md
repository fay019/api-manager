# 📖 Guide d'Implémentation - Réponse API Promo

Guide complet pour interpréter et implémenter la réponse API Promo dans votre application frontend.

---

## 📐 Structure de Base

La réponse API Promo suit toujours ce format:

```json
{
  "success": true|false,
  "data": { ... },
  "error": { ... }  // Seulement si success = false
}
```

---

## ✅ Réponse Réussie (success: true)

### Étape 1: Vérifier que success = true

```javascript
if (!response.success) {
  // Afficher l'erreur
  console.error(response.error.message);
  return;
}
```

### Étape 2: Extraire les données du promo

```javascript
const promo = response.data;

// Champs TOUJOURS présents:
const id = promo.id;                    // Identifiant unique
const version = promo.version;          // Numéro de version
const locale = promo.locale;            // Langue (fr, en, de, ar)
const title = promo.title;              // Titre du promo
const content = promo.content;          // Contenu/description
const image_url = promo.image_url;      // URL complète de l'image
const cta_text = promo.cta_text;        // Texte du bouton
const cta_url = promo.cta_url;          // Lien du bouton
const author_name = promo.author_name;  // Auteur
const author_role = promo.author_role;  // Rôle de l'auteur
const priority = promo.priority;        // Priorité (1-10)
const max_impressions = promo.max_impressions;    // Nombre max de vues
const cooldown_seconds = promo.cooldown_seconds;  // Délai après fermeture
const display_mode = promo.display_mode;          // Mode d'affichage
const start_date = promo.start_date;    // Date début (YYYY-MM-DD)
const end_date = promo.end_date;        // Date fin (YYYY-MM-DD)
```

### Étape 3: Gérer les Champs Optionnels

Ces champs ne sont **PRÉSENTS QUE** s'ils ont été configurés dans l'admin:

```javascript
// Fermeture automatique (en secondes)
const auto_close_timer = promo.auto_close_timer ?? null;
// Exemple: 15 = fermer après 15 secondes, null = pas de fermeture auto

// Afficher un countdown avant fermeture
const show_countdown = promo.show_countdown ?? false;
// Exemple: true = afficher "Fermeture dans 10s", false = sans countdown

// Style d'animation à l'apparition
const animation_style = promo.animation_style ?? 'fade';
// Exemple: 'fade' | 'slide' | 'zoom'
```

---

## 🎯 Implémentation Basique

### Afficher le Banner Simple

```javascript
function displayPromo(promo) {
  const banner = document.createElement('div');
  banner.className = 'promo-banner';
  banner.id = `promo-${promo.id}`;

  banner.innerHTML = `
    <div class="promo-content">
      <button class="promo-close" aria-label="Fermer">×</button>

      <img
        src="${promo.image_url}"
        alt="${promo.title}"
        class="promo-image"
      />

      <div class="promo-text">
        <h2 class="promo-title">${promo.title}</h2>
        <p class="promo-description">${promo.content}</p>
        <small class="promo-author">
          Par ${promo.author_name} (${promo.author_role})
        </small>
      </div>

      <a
        href="${promo.cta_url}"
        class="promo-cta"
        target="_blank"
        rel="noopener noreferrer"
      >
        ${promo.cta_text}
      </a>
    </div>
  `;

  // Bouton fermeture
  banner.querySelector('.promo-close').addEventListener('click', () => {
    banner.remove();
  });

  document.body.appendChild(banner);
  return banner;
}
```

### CSS de Base

```css
.promo-banner {
  position: fixed;
  top: 20px;
  right: 20px;
  max-width: 400px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  z-index: 9999;
}

.promo-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 16px;
}

.promo-close {
  position: absolute;
  top: 8px;
  right: 8px;
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
  transition: color 0.2s;
}

.promo-close:hover {
  color: #333;
}

.promo-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 4px;
}

.promo-title {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.promo-description {
  margin: 0;
  font-size: 14px;
  color: #666;
  line-height: 1.4;
}

.promo-author {
  display: block;
  font-size: 12px;
  color: #999;
  margin-top: 4px;
}

.promo-cta {
  display: inline-block;
  padding: 10px 16px;
  background: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  font-weight: 500;
  text-align: center;
  transition: background 0.2s;
}

.promo-cta:hover {
  background: #0056b3;
}

.promo-countdown {
  font-size: 12px;
  color: #999;
  text-align: center;
  padding: 8px 0;
  border-top: 1px solid #eee;
}
```

---

## 🚀 Fonctionnalités Avancées

### 1️⃣ Fermeture Automatique

```javascript
function displayPromoWithAutoClose(promo, bannerElement) {
  // SI fermeture auto configurée
  if (promo.auto_close_timer && promo.auto_close_timer > 0) {
    setTimeout(() => {
      bannerElement.remove();
    }, promo.auto_close_timer * 1000);
  }
}

// Usage
const banner = displayPromo(promo);
displayPromoWithAutoClose(promo, banner);
```

### 2️⃣ Countdown Visuel

```javascript
function displayCountdown(promo, bannerElement) {
  if (!promo.show_countdown || !promo.auto_close_timer) {
    return;
  }

  // Créer l'élément countdown
  const countdownEl = document.createElement('div');
  countdownEl.className = 'promo-countdown';
  bannerElement.appendChild(countdownEl);

  let remaining = promo.auto_close_timer;

  // Mettre à jour chaque seconde
  const interval = setInterval(() => {
    countdownEl.textContent = `Fermeture dans ${remaining}s`;
    remaining--;

    if (remaining < 0) {
      clearInterval(interval);
    }
  }, 1000);
}

// Usage
const banner = displayPromo(promo);
displayCountdown(promo, banner);
displayPromoWithAutoClose(promo, banner);
```

### 3️⃣ Animations

```javascript
const AnimationStyles = {
  fade: `
    @keyframes animate-fade {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .animate-fade { animation: animate-fade 0.5s ease-in-out; }
  `,

  slide: `
    @keyframes animate-slide {
      from { transform: translateY(-100%); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    .animate-slide { animation: animate-slide 0.5s ease-out; }
  `,

  zoom: `
    @keyframes animate-zoom {
      from { transform: scale(0.9); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    .animate-zoom { animation: animate-zoom 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
  `
};

function applyAnimation(promo, bannerElement) {
  const style = promo.animation_style || 'fade';

  if (AnimationStyles[style]) {
    // Ajouter les styles CSS
    const styleEl = document.createElement('style');
    styleEl.textContent = AnimationStyles[style];
    document.head.appendChild(styleEl);

    // Appliquer la classe
    bannerElement.classList.add(`animate-${style}`);
  }
}

// Usage
const banner = displayPromo(promo);
applyAnimation(promo, banner);
```

### 4️⃣ Solution Complète

```javascript
class PromoManager {
  constructor(apiKey) {
    this.apiKey = apiKey;
    this.apiUrl = 'https://api.moussouni.dev/api/v1/promo/banner.json';
  }

  async fetch() {
    try {
      const response = await fetch(this.apiUrl, {
        headers: {
          'X-API-KEY': this.apiKey
        }
      });

      const data = await response.json();

      if (!data.success) {
        if (response.status === 404) {
          console.log('Aucun promo actif');
          return null;
        }
        console.error('Erreur API:', data.error.message);
        return null;
      }

      return data.data;
    } catch (error) {
      console.error('Erreur chargement promo:', error);
      return null;
    }
  }

  display(promo) {
    const banner = document.createElement('div');
    banner.className = 'promo-banner';
    banner.id = `promo-${promo.id}`;

    // Appliquer l'animation
    if (promo.animation_style) {
      banner.classList.add(`animate-${promo.animation_style}`);
    }

    banner.innerHTML = `
      <div class="promo-content">
        <button class="promo-close" aria-label="Fermer">×</button>

        <img
          src="${promo.image_url}"
          alt="${promo.title}"
          class="promo-image"
        />

        <div class="promo-text">
          <h2 class="promo-title">${promo.title}</h2>
          <p class="promo-description">${promo.content}</p>
          <small class="promo-author">
            Par ${promo.author_name}
          </small>
        </div>

        <a
          href="${promo.cta_url}"
          class="promo-cta"
          target="_blank"
          rel="noopener noreferrer"
        >
          ${promo.cta_text}
        </a>

        ${promo.show_countdown ? '<div class="promo-countdown"></div>' : ''}
      </div>
    `;

    // Fermeture manuelle
    banner.querySelector('.promo-close').addEventListener('click', () => {
      banner.remove();
    });

    document.body.appendChild(banner);

    // Gestion countdown + auto-close
    if (promo.auto_close_timer && promo.auto_close_timer > 0) {
      if (promo.show_countdown) {
        let remaining = promo.auto_close_timer;
        const countdownEl = banner.querySelector('.promo-countdown');

        const interval = setInterval(() => {
          countdownEl.textContent = `Fermeture dans ${remaining}s`;
          remaining--;
        }, 1000);
      }

      setTimeout(() => {
        banner.remove();
      }, promo.auto_close_timer * 1000);
    }
  }

  async init() {
    const promo = await this.fetch();
    if (promo) {
      this.display(promo);
    }
  }
}

// Usage
const manager = new PromoManager('votre_clé_api');
manager.init();
```

---

## ❌ Gestion des Erreurs

### 404 - Aucun promo actif

```javascript
if (response.status === 404 && !data.success) {
  console.log('Pas de promo disponible actuellement');
  // Ne pas afficher le banner
}
```

### 401 - Non authentifié

```javascript
if (response.status === 401) {
  console.error('Clé API invalide ou expirée');
  // Vérifier la clé X-API-KEY
}
```

### 429 - Rate limit dépassé

```javascript
if (response.status === 429) {
  console.error('Trop de requêtes, veuillez patienter');
  const retryAfter = response.headers.get('Retry-After');
  console.log(`Réessayer après ${retryAfter}s`);
}
```

### Gestion Générale

```javascript
async function fetchPromo(apiKey) {
  try {
    const response = await fetch('https://api.moussouni.dev/api/v1/promo/banner.json', {
      headers: { 'X-API-KEY': apiKey }
    });

    const data = await response.json();

    if (!response.ok) {
      switch (response.status) {
        case 404:
          console.log('Aucun promo actif');
          break;
        case 401:
          console.error('Authentification échouée');
          break;
        case 429:
          console.error('Rate limit dépassé');
          break;
        default:
          console.error(`Erreur ${response.status}: ${data.error?.message}`);
      }
      return null;
    }

    return data.success ? data.data : null;
  } catch (error) {
    console.error('Erreur réseau:', error);
    return null;
  }
}
```

---

## 📋 Checklist d'Implémentation

- ✅ Vérifier `response.success` avant d'utiliser les données
- ✅ Extraire tous les champs standard (title, content, image_url, etc.)
- ✅ Vérifier la présence des champs optionnels avant utilisation
- ✅ Implémenter l'affichage du banner basique
- ✅ Si `auto_close_timer` > 0: fermer automatiquement
- ✅ Si `show_countdown` = true: afficher le décompte
- ✅ Si `animation_style` configuré: appliquer l'animation
- ✅ Gérer les erreurs (404, 401, 429)
- ✅ Ajouter un bouton fermeture manuel
- ✅ Tester avec tous les cas (avec/sans options avancées)
- ✅ Respecter les dates de début/fin pour l'affichage côté client

---

## 🔄 Flux Recommandé

```
1. Récupérer la réponse API
   ↓
2. Vérifier success = true
   ↓
3. Créer le DOM du banner
   ↓
4. Appliquer l'animation (si configurée)
   ↓
5. Ajouter les événements (fermeture manuelle)
   ↓
6. Afficher le countdown (si configuré)
   ↓
7. Gérer l'auto-close (si configuré)
   ↓
8. Attendre fermeture manuelle ou auto
```

---

## 📚 Ressources

- [Documentation complète des Promos](./PROMOS.md) - Structure et endpoints
- [API Reference](./API.md) - Détails techniques
- [Database Schema](./DATABASE.md) - Structure des données

---

**Dernière mise à jour:** 2026-01-28
**Version:** 1.0