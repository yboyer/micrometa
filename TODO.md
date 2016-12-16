## TODOs
> 30 novembre
> http://dev-21514838.users.info.unicaen.fr/devoir-umdn3c/
> Code source sur devoirs.info.unicaen.fr

- Accueil: `/`
  - [x] Affichage des images du dossier images
  - [x] Chaque image envoie vers une page de détail
  - [x] Microdata
- Détail: `/detail/{filename}`
  - [x] Affichage de l'image
  - [x] Affichage des métadonnées
    - [x] Microdata
    - [x] Open Graph
    - [x] Twitter Cards
  - [x] Lien pour télécharger l'image
  - [x] Lien pour télécharger le fichier XMP Sidecar
- Ajout: `/add`
  - [x] Upload de fichier
  - [x] Extraction des métadonnées dans un formulaire pour modification et validation
    - [x] Modification des métadonnées de l'image


- ***++***
  - Accueil: `/`
    - [x] Affiche les noms et auteurs
      - [x] Microdata
  - Détail: `/detail/{filename}`
    - [x] Recherche d'image correspondante par mot-clé, titre, géolocalisation, ...
  - Carte: `/map`
    - [x] Affichage des images sur une cartes
  - Global
    - [x] Stocker les métadonnées au lieu de les rechercher avec `exiftool` (sans base de données)
    - [x] Détection des incohérances des métadonnées dans une image (EXITF, IPTC, XMP)
      - [x] `git diff` like
