# Wooden

Thème racine pour Wordpress - prise en charge de Woodkit v2 & Gutenberg.
Ce thème est voué à être surchargé, il ne propose aucun style par défaut. Il permet la prise en charge de Woodkit v2 et propose un contexte Gutenberg.

# Les mises à jour

Ce thème est mis à jour par Woodkit, il est donc fortement recommandé de le surcharger lorsque vous développez votre thème.
La mise à jour se fait comme tous les autres thème, par action manuelle de votre part depuis le BO de WP.

# Les particularités

* Wooden intègre certains tools (encapsulé par Woodkit v2). Le paramétrage des tools est donc faite depuis le BO de WP dans la section Woodkit (comme pour les tool de Woodkit)

**Note :** Wooden nécessite Woodkit v2 et donc nécessite Wordpress > v5 avec l'édieur Gutenberg activé.
  
------------------------------------------------------------------------------------------------------------------------
## Les développements dans Wooden : 

# Context Gutenberg

**Pour ajouter un block :**

*Définition : un block Gutenberg est un élément qui peut s'ajouter dans le contenu*

* Dupliquer le dossier *src/gutenberg/blocks/_blank_* dans le dossier de votre choix sous *src/* (afin de profiter du context Webpack) et renommer ce nouveau dossier avec 'votre_slug' (Important : nommage en snake_case)
* Dans ce nouveau dossier, faire un rechercher/remplacer global dans ce nouveau dossier sur "_blank_" par 'votre_slug'
* Faites en sorte d'appeller le fichier présent dans votre nouveau dossier index.php
* Ouvrir webpack.config.js qui est à la racine du projet et ajouter la référence à votre nouveau block comme ceci : 
  * {'entry': 'index.jsx', 'name': 'votre_slug', 'path': 'PATH_TO_YOUR_BLOCK_DIR', 'entry': 'index.jsx'},
* Lancez *$ npm run dev* (si webpack est déjà en route, vous devez le redémarrer)
* Commencez à developper
* Pour builder en production : lancez un *$ npm run build*

**Pour ajouter un plugin :**

*Définition : un plugin Gutenberg est un élément qui s'ajoute à l'interface (sidebar / header / ...)*

* Dupliquer le dossier *src/gutenberg/plugins/_blank_* dans le dossier de votre choix sous *src/* (afin de profiter du context Webpack) et renommer ce nouveau dossier avec 'votre_slug' (Important : nommage en snake_case)
* Dans ce nouveau dossier, faire un rechercher/remplacer global dans ce nouveau dossier sur "_blank_" par 'votre_slug'
* Faites en sorte d'appeller le fichier présent dans votre nouveau dossier index.php
* Ouvrir webpack.config.js qui est à la racine du projet et ajouter la référence à votre nouveau block comme ceci : 
  * {'entry': 'index.jsx', 'name': 'votre_slug', 'path': 'PATH_TO_YOUR_PLUGIN_DIR', 'entry': 'index.jsx'},
* Lancez *$ npm run dev* (si webpack est déjà en route, vous devez le redémarrer)
* Commencez à developper
* Pour builder en production : lancez un *$ npm run build*

**Pour ajouter un store :**

*Définition : un store permet de gérer les états des blocks/plugins de façon globale*

* Dupliquer le dossier *src/gutenberg/stores/_blank_* dans le dossier de votre choix sous *src/* (afin de profiter du context Webpack) et renommer ce nouveau dossier avec 'votre_slug' (Important : nommage en snake_case)
* Dans ce nouveau dossier, faire un rechercher/remplacer global dans ce nouveau dossier sur "_blank_" par 'votre_slug'
* Faites en sorte d'appeller le fichier présent dans votre nouveau dossier index.php
* Ouvrir webpack.config.js qui est à la racine du projet et ajouter la référence à votre nouveau block comme ceci : 
  * {'entry': 'index.jsx', 'name': 'votre_slug', 'path': 'PATH_TO_YOUR_STORE_DIR', 'entry': 'index.jsx'},
* Lancez *$ npm run dev* (si webpack est déjà en route, vous devez le redémarrer)
* Commencez à developper
* Pour builder en production : lancez un *$ npm run build*

**Les composants/assets Woodkit**

* les composants React proposés par Woodkit sont accessible via *import COMPONENT_NAME from 'wkgcomponents/....'
  * exemple : import WKG_Media_Selector from 'wkgcomponents/media-selector'
* les assets React proposés par Woodkit sont accessible via *import ASSET_NAME  from 'wkgassets/...'*
  * exemple : import WKG_Icons from 'wkgassets/icons'

------------------------------------------------------------------------------------------------------------------------
## Les développements dans un thème enfant : 

# Les tools

Votre thème enfant peut lui aussi définir des tools, pour cela vous devez respecter l'architecture des tools existants et les placer dans le dossier src/tools/ de votre thème enfant.

# Gutenberg

Pour créer des composant Gutenberg (ReactJS) dans un thème enfant, vous devez créer un contexte de développement. Vous pouvez simplement vous inspirer de celui de Wooden en faisant les actions suivantes : 
* copier à la racine de votre thème les fichiers suivants : 
  * webpack.config.js
  * package.json
* Lancez un $ npm install (afin d'installer les node_modules défini par package.json)
* modifiez la variable 'gutenberg_modules' dans webpack.config.js pour correspondre à votre thème enfant
* tous vos développements doivent être fait dans le dossier src/ de votre thème enfant
