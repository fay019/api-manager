# Gestion des Clients API

Cette documentation détaille la gestion des clients API dans le système. Un client API représente une application ou un partenaire externe consommant vos services.

## Informations Générales

Chaque client est identifié par un nom unique et possède un statut global.

| Champ | Type | Description |
|-------|------|-------------|
| `name` | String | Nom de l'application ou du service (ex: "App Mobile iOS"). |
| `is_active` | Boolean | État du client : `true` (accès autorisé) ou `false` (accès bloqué). |
| `client_type` | String | Catégorie du client : `MOBILE`, `WEB`, `PARTNER`, `INTERNAL`. |
| `activated_at` | DateTime | Date d'activation ou de début du partenariat. |

## Coordonnées & Contact

Informations permettant d'identifier le responsable technique ou commercial du client.

- **Nom du contact** (`contact_name`) : Personne à contacter.
- **Email de contact** (`contact_email`) : Utilisé pour les notifications techniques ou dépassement de quota.
- **Site Web** (`website`) : URL officielle du projet ou de l'entreprise.

## Configuration Technique

Ces paramètres contrôlent comment le client interagit avec l'API.

### Limitation de Débit (Rate Limiting)
- **Rate Limit (min)** : Nombre maximum de requêtes autorisées par minute (par défaut : 60).
- **Quota Mensuel** : Limite totale de requêtes autorisées sur un mois calendaire. L'accès est bloqué une fois le quota atteint.

### Sécurité & CORS
- **Allowed Origins** : Liste des domaines autorisés pour les requêtes provenant d'un navigateur (CORS). Si la liste est vide, toutes les origines sont acceptées (non recommandé en production).
- **Webhook URL** : URL de destination pour les notifications automatisées envoyées par le système.

## Administration (Filament)

L'interface d'administration permet de :
1.  **Créer/Modifier** les informations des clients.
2.  **Surveiller** l'activité (nombre de clés API actives, volume de requêtes).
3.  **Désactiver** instantanément un client suspect ou en fin de contrat.
4.  **Consulter** les logs détaillés de chaque requête effectuée par le client.
5.  **Gérer les Clés API** : Voir la liste complète des clés associées à un client directement depuis sa fiche, avec leur statut en temps réel (Active, Expired, etc.).

---

## Notes Internes
Un champ `notes` (invisible pour le client) est disponible pour stocker des informations privées sur le partenariat ou des configurations spécifiques.
