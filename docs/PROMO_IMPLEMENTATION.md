# 📖 Guide d'Implémentation - Réponse API Promo

Guide complet pour interpréter et implémenter la réponse API Promo dans votre application frontend.

---

## 🚀 Requêtes Supportées

L'API Promo supporte 2 modes de récupération selon vos besoins:

### Mode 1️⃣: Une Seule Langue (Recommandé)

Récupère le promo dans la langue spécifiée (ou la langue par défaut):

```bash
# Français (par défaut)
GET /api/v1/promo/banner.json

# Anglais
GET /api/v1/promo/banner.json?lang=en

# Allemand
GET /api/v1/promo/banner.json?lang=de

# Arabe
GET /api/v1/promo/banner.json?lang=ar
```

**Langues supportées:** `fr`, `en`, `de`, `ar`

### Mode 2️⃣: Toutes les Langues

Récupère le promo avec tous les textes traduits:

```bash
GET /api/v1/promo/banner.json?all_langs=true
```

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

## ✅ Réponse Mode 1: Une Seule Langue

**Exemple de requête:**
```bash
curl -H "X-API-KEY: apk_xxx" \
  "https://api.moussouni.dev/api/v1/promo/banner.json?lang=en"
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "version": 2,
    "locale": "en",
    "author_name": "Marketing Team",
    "author_role": "Campaign Manager",
    "title": "Summer Sale",
    "content": "Get 50% off on selected items",
    "cta_text": "Shop Now",
    "image_url": "https://api.example.com/storage/summer-banner.jpg",
    "cta_url": "https://example.com/summer-sale",
    "priority": 10,
    "max_impressions": 5,
    "cooldown_seconds": 86400,
    "display_mode": "fixed_count",
    "start_date": "2026-01-25",
    "end_date": "2026-02-25",
    "auto_close_timer": 15,
    "show_countdown": true,
    "animation_style": "fade"
  }
}
```

### Extraction des données (Mode 1)

```javascript
const response = await fetch('https://api.moussouni.dev/api/v1/promo/banner.json?lang=en', {
  headers: { 'X-API-KEY': 'apk_your_key' }
});

const data = await response.json();

if (!data.success) {
  console.error('Erreur:', data.error.message);
  return;
}

const promo = data.data;

// Champs STRING (une langue seulement)
const title = promo.title;           // "Summer Sale"
const content = promo.content;       // "Get 50% off on selected items"
const cta_text = promo.cta_text;     // "Shop Now"
const locale = promo.locale;         // "en"

// Autres champs
const id = promo.id;
const version = promo.version;
const image_url = promo.image_url;
const cta_url = promo.cta_url;
const author_name = promo.author_name;
const author_role = promo.author_role;
const priority = promo.priority;
const max_impressions = promo.max_impressions;
const cooldown_seconds = promo.cooldown_seconds;
const display_mode = promo.display_mode;
const start_date = promo.start_date;
const end_date = promo.end_date;

// Champs OPTIONNELS (seulement si configurés)
const auto_close_timer = promo.auto_close_timer ?? null;
const show_countdown = promo.show_countdown ?? false;
const animation_style = promo.animation_style ?? null;
```

---

## ✅ Réponse Mode 2: Toutes les Langues

**Exemple de requête:**
```bash
curl -H "X-API-KEY: apk_xxx" \
  "https://api.moussouni.dev/api/v1/promo/banner.json?all_langs=true"
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "version": 2,
    "author_name": "Marketing Team",
    "author_role": "Campaign Manager",
    "translations": {
      "title": {
        "fr": "Soldes d'été",
        "en": "Summer Sale",
        "de": "Sommerschlussverkauf",
        "ar": "تخفيضات الصيف"
      },
      "content": {
        "fr": "Profitez de 50% de réduction sur les articles sélectionnés",
        "en": "Get 50% off on selected items",
        "de": "Erhalten Sie 50% Rabatt auf ausgewählte Artikel",
        "ar": "احصل على خصم 50٪ على العناصر المختارة"
      },
      "cta_text": {
        "fr": "Acheter maintenant",
        "en": "Shop Now",
        "de": "Jetzt einkaufen",
        "ar": "تسوق الآن"
      }
    },
    "image_url": "https://api.example.com/storage/summer-banner.jpg",
    "cta_url": "https://example.com/summer-sale",
    "priority": 10,
    "max_impressions": 5,
    "cooldown_seconds": 86400,
    "display_mode": "fixed_count",
    "start_date": "2026-01-25",
    "end_date": "2026-02-25",
    "auto_close_timer": 15,
    "show_countdown": true,
    "animation_style": "fade"
  }
}
```

### Extraction des données (Mode 2)

```javascript
const response = await fetch('https://api.moussouni.dev/api/v1/promo/banner.json?all_langs=true', {
  headers: { 'X-API-KEY': 'apk_your_key' }
});

const data = await response.json();

if (!data.success) {
  console.error('Erreur:', data.error.message);
  return;
}

const promo = data.data;

// Champs OBJECT (toutes les langues)
const translations = promo.translations;  // { title: {...}, content: {...}, cta_text: {...} }
const titleFr = translations.title.fr;    // "Soldes d'été"
const titleEn = translations.title.en;    // "Summer Sale"
const contentDe = translations.content.de; // "Erhalten Sie 50% Rabatt..."
const ctaAr = translations.cta_text.ar;   // "تسوق الآن"

// ⚠️ NOTE: Il n'y a PAS de "locale" en mode all_langs (on a toutes les langues)

// Autres champs (identiques)
const id = promo.id;
const version = promo.version;
const image_url = promo.image_url;
const cta_url = promo.cta_url;
const author_name = promo.author_name;
const author_role = promo.author_role;
const priority = promo.priority;
const max_impressions = promo.max_impressions;
const cooldown_seconds = promo.cooldown_seconds;
const display_mode = promo.display_mode;
const start_date = promo.start_date;
const end_date = promo.end_date;

// Champs OPTIONNELS (seulement si configurés)
const auto_close_timer = promo.auto_close_timer ?? null;
const show_countdown = promo.show_countdown ?? false;
const animation_style = promo.animation_style ?? null;
```

---

## 🎯 Implémentation Basique

### Afficher un Banner Simple (Mode 1)

```javascript
async function displayPromo(lang = 'fr') {
  try {
    const response = await fetch(
      `https://api.moussouni.dev/api/v1/promo/banner.json?lang=${lang}`,
      { headers: { 'X-API-KEY': 'apk_your_key' } }
    );

    const data = await response.json();

    if (!data.success) {
      console.log('Pas de promo disponible');
      return;
    }

    const promo = data.data;
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

    // Fermeture manuelle
    banner.querySelector('.promo-close').addEventListener('click', () => {
      banner.remove();
    });

    document.body.appendChild(banner);

    // Gérer auto-close si configuré
    if (promo.auto_close_timer && promo.auto_close_timer > 0) {
      setTimeout(() => {
        banner.remove();
      }, promo.auto_close_timer * 1000);
    }
  } catch (error) {
    console.error('Erreur chargement promo:', error);
  }
}

// Utilisation
displayPromo('en');  // Afficher en anglais
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
  position: relative;
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
  z-index: 1;
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
  align-self: flex-start;
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
  margin-top: 8px;
}
```

---

## 🚀 Fonctionnalités Avancées

### 1️⃣ Countdown Visuel

```javascript
function addCountdown(promo, bannerElement) {
  if (!promo.show_countdown || !promo.auto_close_timer) {
    return;
  }

  const countdownEl = document.createElement('div');
  countdownEl.className = 'promo-countdown';
  bannerElement.appendChild(countdownEl);

  let remaining = promo.auto_close_timer;

  const interval = setInterval(() => {
    countdownEl.textContent = `Fermeture dans ${remaining}s`;
    remaining--;

    if (remaining < 0) {
      clearInterval(interval);
    }
  }, 1000);
}
```

### 2️⃣ Animations

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
  const style = promo.animation_style;

  if (!style || !AnimationStyles[style]) {
    return;
  }

  const styleEl = document.createElement('style');
  styleEl.textContent = AnimationStyles[style];
  document.head.appendChild(styleEl);

  bannerElement.classList.add(`animate-${style}`);
}
```

### 3️⃣ Gestion Multilingue (Mode 2)

```javascript
async function displayPromoMultilingual() {
  const response = await fetch(
    'https://api.moussouni.dev/api/v1/promo/banner.json?all_langs=true',
    { headers: { 'X-API-KEY': 'apk_your_key' } }
  );

  const data = await response.json();

  if (!data.success) return;

  const promo = data.data;

  // Déterminer la langue de l'utilisateur
  const userLang = navigator.language.split('-')[0]; // 'en', 'fr', etc.
  const supportedLangs = Object.keys(promo.translations.title);
  const lang = supportedLangs.includes(userLang) ? userLang : 'fr'; // Fallback à FR

  // Utiliser les textes de la langue détectée
  const banner = document.createElement('div');
  banner.className = 'promo-banner';

  banner.innerHTML = `
    <div class="promo-content">
      <img src="${promo.image_url}" alt="${promo.translations.title[lang]}" />

      <h2>${promo.translations.title[lang]}</h2>
      <p>${promo.translations.content[lang]}</p>

      <a href="${promo.cta_url}" class="promo-cta">
        ${promo.translations.cta_text[lang]}
      </a>
    </div>
  `;

  document.body.appendChild(banner);
}
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
}
```

### 429 - Rate limit

```javascript
if (response.status === 429) {
  console.error('Trop de requêtes');
  const retryAfter = response.headers.get('Retry-After');
  console.log(`Réessayer après ${retryAfter}s`);
}
```

---

## 📋 Différences Mode 1 vs Mode 2

| Aspect | Mode 1 (`?lang=fr`) | Mode 2 (`?all_langs=true`) |
|--------|---------------------|---------------------------|
| **URL** | `/api/v1/promo/banner.json?lang=en` | `/api/v1/promo/banner.json?all_langs=true` |
| **title** | STRING | OBJECT `{ fr: "...", en: "..." }` |
| **content** | STRING | OBJECT `{ fr: "...", en: "..." }` |
| **cta_text** | STRING | OBJECT `{ fr: "...", en: "..." }` |
| **locale** | STRING (la langue demandée) | ❌ ABSENT |
| **Use Case** | Application monolingue ou langue fixe | Application multilingue dynamique |
| **Overhead** | ✅ Minimal | ⚠️ Plus de données |

---

## 🔄 Checklist d'Implémentation

- ✅ Choisir Mode 1 ou Mode 2 selon vos besoins
- ✅ Vérifier `response.success` avant utilisation
- ✅ Extraire correctement les champs (STRING vs OBJECT)
- ✅ Gérer les champs optionnels (`auto_close_timer`, etc.)
- ✅ Implémenter l'affichage basique
- ✅ Ajouter le bouton fermeture manuelle
- ✅ Si `auto_close_timer` > 0 : fermer automatiquement
- ✅ Si `show_countdown` = true : afficher countdown
- ✅ Si `animation_style` configuré : appliquer animation
- ✅ Gérer les erreurs API (404, 401, 429)

---

## 📚 Ressources

- [Documentation Promos](./PROMOS.md) - Structure complète
- [API Reference](./API.md) - Détails techniques
- [Database Schema](./DATABASE.md) - Structure données

---

**Dernière mise à jour:** 2026-01-28
**Version:** 1.1