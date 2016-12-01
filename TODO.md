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
  - [ ] Affichage des métadonnées
    - [ ] Microdata
    - [ ] Open Graph
    - [ ] Twitter Cards
  - [ ] Lien pour télécharger l'image
  - [ ] Lien pour télécharger le fichier XMP Sidecar
- Ajout: `/add`
  - [ ] Upload de fichier
  - [ ] Extraction des métadonnées dans un formulaire pour modification et validation
    - [ ] Modification des métadonnées de l'image


- ***++***
  - Accueil: `/`
    - [x] Affiche les noms et auteurs
      - [x] Microdata
  - Détail: `/detail/{filename}`
    - [ ] Recherche d'image correspondante par mot-clé, titre, géolocalisation, ...
  - Carte: `/map`
    - [ ] Affichage des images sur une cartes
  - Global
    - [ ] Stocker les métadonnées au lieu de les rechercher avec `exiftool` (sans base de données)
    - [ ] Détection des incohérances des métadonnées dans une image (EXITF, IPTC, XMP)
      - [ ] `git diff` like
