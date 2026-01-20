# 👥 Gestion des Clients API

Documentation complète pour gérer les clients API dans le système. Un client API représente une application ou un partenaire externe consommant vos services.

---

## 📋 Vue d'ensemble

### Qu'est-ce qu'un Client API?

Un client API est une **application ou un service** externe qui utilise vos API. Chaque client:
- ✅ Possède une ou plusieurs **clés API** pour l'authentification
- ✅ A ses propres **limites de débit** (rate limiting)
- ✅ Peut être **activé ou désactivé** instantanément
- ✅ Peut avoir un **quota mensuel** de requêtes
- ✅ Peut restreindre ses origines avec **CORS**

### Exemples de Clients

| Exemple | Type | Cas d'Usage |
|---------|------|------------|
| Application mobile iOS | MOBILE | App consommant vos APIs |
| Dashboard web | WEB | Site web consultant les promos |
| Partenaire externe | PARTNER | Intégration avec un service tiers |
| Microservice interne | INTERNAL | Service à service dans votre infrastructure |

---

## 🎯 Informations Générales

### Identification du Client

Chaque client est identifié par un ensemble d'informations essentielles:

| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| **name** | String | ✅ Oui | Nom unique (ex: "App Mobile iOS") |
| **is_active** | Boolean | ✅ Oui | État du client (true = accès autorisé, false = bloqué) |
| **client_type** | String | ❌ Non | Type: `MOBILE`, `WEB`, `PARTNER`, `INTERNAL` |
| **activated_at** | DateTime | ❌ Non | Date de début du partenariat |

### États du Client

| État | Icône | Signification |
|------|-------|--------------|
| **Active** | 🟢 | Client peut utiliser l'API |
| **Disabled** | 🔴 | Client bloqué, toutes les requêtes retournent 403 |

---

## 👤 Coordonnées & Contact

### Informations de Contact

Essentielles pour identifier le responsable technique ou commercial:

| Champ | Type | Usage |
|-------|------|-------|
| **contact_name** | String | Nom de la personne à contacter |
| **contact_email** | Email | Notifications techniques, dépassement de quota |
| **website** | URL | Site officiel du projet/entreprise |

**Exemple:**
```
Contact Name: Jean Dupont
Contact Email: jean@example.com
Website: https://example.com
```

---

## ⚙️ Configuration Technique

### Limitation de Débit (Rate Limiting)

Contrôlez combien de requêtes chaque client peut faire:

| Paramètre | Défaut | Description |
|-----------|--------|-------------|
| **rate_limit_per_minute** | 60 | Max requêtes/minute pour ce client |
| **monthly_quota** | ∞ | Quota total mensuel (optionnel) |

**Exemple:**
- Client A: 60 requêtes/min, pas de limite mensuelle
- Client B: 30 requêtes/min, 100k requêtes/mois max

**Comportement:**
- Si limite/minute ❌ → Code 429 (Too Many Requests)
- Si quota mensuel ❌ → Code 403 (Forbidden)

### Sécurité & CORS

Contrôlez d'où viennent les requêtes:

| Paramètre | Description |
|-----------|-------------|
| **allowed_origins** | Liste des domaines CORS autorisés |
| **webhook_url** | URL pour les webhooks (notifications) |

#### Allowed Origins (CORS)

**Exemple:**
```
https://example.com
https://app.example.com
https://mobile.example.com
```

**Comportement:**
- Liste **vide** = Accepte **toutes les origines** ⚠️ (non sécurisé)
- Liste **remplie** = Accepte **seulement** ces domaines ✅ (recommandé)

**Serveur à Serveur:**
- CORS ignoré (pas d'header Origin)
- Seule la clé API compte

---

## 🎨 Interface d'Administration (Filament)

### Accès

**Admin Panel** → **API Management** → **API Clients**

### Créer un Nouveau Client

**Étapes:**

1. **Cliquez** "Create" button
2. **Remplissez la section "Client Information":**
   - Name: Nom unique (ex: "Mobile App V2")
   - Active: Toggle activé/désactivé
   - Client Type: Choisir MOBILE, WEB, PARTNER ou INTERNAL
   - Activated At: Date de début (optionnel)

3. **Remplissez la section "Contact Details":**
   - Contact Name: Responsable technique
   - Contact Email: Email de notification
   - Website: Site officiel

4. **Configurez la section "Technical Configuration":**
   - Rate Limit: Requêtes par minute (défaut: 60)
   - Monthly Quota: Limite mensuelle (optionnel)
   - Webhook URL: URL de notification (optionnel)
   - Allowed Origins: Domaines CORS autorisés (balises)

5. **Ajoutez la section "About":**
   - Description: Brève description du client
   - Notes: Notes internes (privées)

6. **Sauvegardez** avec le bouton "Save"

### Gérer les Clés API

Une fois le client créé, vous pouvez gérer ses clés:

**Dans la fiche client:**
- Voir tous les **API Keys** associés
- Voir statut de chaque clé (Active, Expired, Scheduled, Revoked)
- **Créer une nouvelle clé**
- **Révoquer** une clé
- **Régénérer** une clé si perdue

### Surveiller l'Activité

La fiche client affiche des statistiques en temps réel:

| Métrique | Affichée | Usage |
|----------|----------|-------|
| **API Keys** | Nombre de clés | Voir combien de clés le client a |
| **Requests** | Nombre de requêtes | Volume d'utilisation |
| **Status** | 🟢 Active / 🔴 Disabled | État actuel |

---

## 🔑 Gestion des Clés API

### Créer une Clé API pour un Client

**Dans la fiche client:**

1. **Allez** à l'onglet "API Keys"
2. **Cliquez** "Create API Key"
3. **Remplissez:**
   - Key Name: Nom descriptif (ex: "iOS v2.0")
   - Starts At: Date d'activation (optionnel)
   - Expires At: Date d'expiration (optionnel)
   - Is Active: Activé/désactivé

4. **Sauvegardez**
5. **Copiez immédiatement** la clé générée
   - ⚠️ Elle ne sera **plus jamais affichée**
   - Sauvegarder ailleurs si perte de clé

### Statuts de Clé

| Statut | Signification |
|--------|--------------|
| 🟢 **Active** | Clé valide et utilisable maintenant |
| 🔵 **Scheduled** | Clé deviendra active dans le futur |
| 🟠 **Expired** | Date `expires_at` est dépassée |
| 🔴 **Revoked** | Clé désactivée manuellement |

### Actions sur les Clés

**View:** Voir tous les détails de la clé
**Edit:** Modifier la clé
**Regenerate:** Générer une nouvelle clé (ancienne révoquée)
**Revoke:** Désactiver la clé manuellement
**Delete:** Supprimer la clé (seulement si révoquée)

---

## 📊 Notes Internes

Un champ **Notes** privé est disponible pour:
- 📝 Détails du contrat/partenariat
- 📱 Configurations spécifiques
- 💬 Messages pour l'équipe interne
- ⚠️ Alertes ou restrictions

**Exemple:**
```
Client premium avec SLA 99.5%
Limite de débit augmentée à 500/min (approbation directeur)
Contact urgent: +33 1 23 45 67 89
```

---

## 🎯 Cas d'Usage Pratiques

### Cas 1: Partenaire Externe (Limite Stricte)

```
Name: "Partner Company API"
Type: PARTNER
Rate Limit: 30 req/min
Monthly Quota: 50,000 req
Allowed Origins: https://partner.com
Contact: partner@example.com
```

### Cas 2: Application Mobile (Limite Standard)

```
Name: "Mobile App iOS"
Type: MOBILE
Rate Limit: 60 req/min
Monthly Quota: Illimité
Allowed Origins: vide (accepte toutes)
Contact: mobile-team@example.com
```

### Cas 3: Service Interne (Pas de Limite)

```
Name: "Microservice Promo"
Type: INTERNAL
Rate Limit: 1000 req/min
Monthly Quota: Illimité
Allowed Origins: vide
Contact: devops@example.com
```

---

## 🚀 Flux de Création Complet

### Étape 1: Créer le Client

1. Allez à **Admin Panel** → **API Management** → **API Clients**
2. Cliquez **"Create"**
3. Remplissez toutes les informations
4. Sauvegardez

### Étape 2: Créer une Clé API

1. Dans la fiche client, allez à **API Keys**
2. Cliquez **"Create API Key"**
3. Donnez un nom à la clé
4. Sauvegardez
5. **Copiez la clé immédiatement** ⚠️

### Étape 3: Communiquer avec le Client

Envoyez au client:
- 🔑 La clé API (sécurisée)
- 📖 Lien vers [API Documentation](./API.md)
- 📝 Exemple d'appel API
- ✋ Votre contact technique

### Étape 4: Surveiller l'Utilisation

1. Allez à **Admin Panel** → **API Management** → **Request Logs**
2. Filtrez par client
3. Vérifiez le volume d'utilisation
4. Vérifiez les erreurs (codes 4xx, 5xx)

---

## 🔐 Sécurité Recommandée

### En Production

✅ **Obligatoire:**
- [ ] `is_active = true` seulement pour clients approuvés
- [ ] `allowed_origins` rempli (ne pas laisser vide)
- [ ] `monthly_quota` défini si contrat limité
- [ ] Contact email valide et à jour
- [ ] HTTPS utilisé (jamais HTTP)

⚠️ **À Éviter:**
- ❌ Laisser des clients orphelins actifs
- ❌ Partager une clé entre plusieurs clients
- ❌ Garder des clés sans expiration
- ❌ Origins vide en production

### Rotation des Clés

**Tous les 90 jours minimum:**
1. Générez une **nouvelle clé**
2. Donnez-la au client
3. Attendez **7 jours** confirmation
4. **Révoquez l'ancienne clé**

---

## 📊 Voir les Statistiques

### Par Client

**Admin Panel** → **API Management** → **Request Logs**

Filtrez par client pour voir:
- Nombre de requêtes
- Codes de statut (succès/erreur)
- Endpoints les plus utilisés
- Durées de réponse

### Global

**Admin Dashboard** affiche:
- Total clients actifs
- Total requêtes ce mois
- Erreurs/alertes
- Top clients

---

## 🆘 Troubleshooting

### Client reçoit 401 Unauthorized

**Problème:** Clé API invalide

**Solutions:**
1. ✅ Vérifier que la clé commence par `apk_`
2. ✅ Vérifier que la clé n'est pas expirée
3. ✅ Vérifier que le client `is_active = true`
4. ✅ Vérifier que la clé `is_active = true`
5. ✅ Régénérer la clé si doute

### Client reçoit 403 Forbidden

**Problème:** Origine CORS non autorisée

**Solutions:**
1. ✅ Vérifier `allowed_origins` n'est pas restreint
2. ✅ Ajouter le domaine du client à `allowed_origins`
3. ✅ Si serveur-à-serveur, `allowed_origins` est ignoré

### Client reçoit 429 Rate Limited

**Problème:** Trop de requêtes

**Solutions:**
1. ✅ Vérifier que `rate_limit_per_minute` est suffisant
2. ✅ Vérifier que `monthly_quota` n'est pas dépassé
3. ✅ Client doit implémenter retry/backoff
4. ✅ Augmenter les limites si justifié

### Client bloqué accidentellement

**Problème:** Client `is_active = false`

**Solutions:**
1. Allez à la fiche client
2. Changez `is_active = true`
3. Sauvegardez
4. Attendez 10 secondes (cache)
5. Testez avec curl

---

## 📞 Documentation Liée

- [API Documentation](./API.md) - Référence complète des endpoints
- [Database Schema](./DATABASE.md) - Structure des données
- [Deployment Guide](./DEPLOYMENT.md) - Déploiement en production

---

## 📝 Résumé

| Action | Où | Quand |
|--------|-----|-------|
| **Créer client** | Admin → API Clients → Create | Nouveau partenaire |
| **Créer clé** | Admin → Client fiche → API Keys → Create | Nouveau client ou rotation |
| **Surveiller** | Admin → Request Logs | Quotidien |
| **Révoquer clé** | Admin → API Key → Revoke | Sécurité, fin de contrat |
| **Désactiver client** | Admin → Client → Toggle is_active | Arrêt service |

---

**Last Updated:** 2026-01-20
**Version:** 1.0.0