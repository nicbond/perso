les-habitues :-)

Bonjour Alain,

Pour commencer, tu trouveras ci-dessous la liste des librairies utilisées pour réaliser le test:
 
 - FOSRestBundle.
 - JMSSerializerBundle.
 - CSA/GuzzleBundle.
 - PagerFanta.
 - NelmioApiDoc.
 - Willdurand/HateoasBundle.
 - Swagger.
 
L'environnement:
 
 - php 7.4.7
 - Symfony 4.4.11

Voici mes explications concernant le test. Contairement à la dernière fois, il y a huit fichiers cette fois-ci: 
 
 - ShopController dans le dossier Controller.
 - Shop dans le dossier Entity.
 - ShopRepository dans le dossier Repository.
 - Shops dans le dossier Representation.
 - ResourceValidationException dans le dossier Exception.
 - ExceptionSubscriber dans le dossier EventSubscriber.
 - 2 services: Call et ShopService dans le dossier Service.

Comme je l'avais vu avec toi par téléphone, j'ai procédé à un gros cut des données. Mon entité Shop contiendra donc les données suivantes:

 - le nom du shop (l'enseigne).
 - son adresse.
 - le code postal.
 - la ville.
 - l'image.
 - une offre de réduction.
 - ainsi que l'id_shop enregistré dans votre base de données. Concernant les boutiques d'une enseigne, mon choix s'est toujours porté sur la première.


1). Lors de mon premier test, tu m'avais signifié de ne pas passer par des appels cURL, mais plutôt d'utiliser Guzzle (que je n'avais pas encore vu).
Par conséquent, suite à mon auto-formation, j'ai utilisé Guzzle en passant par la librairie csa/guzzle-bundle (permettant une meilleure intégration de la librairie Guzzle).
Tu trouveras la configuration de mon client dans le dossier config/packages/csa_guzzle.yaml.
 
A partir de l'url: https://www.leshabitues.fr/testapi/shops dans la méthode shop du contrôleur(ligne 114), j'ai fait appel à un service nommé Call.
A l'époque, je ne savais pas comment déclarer les services avec Symfony 3 (par rapport à la 2 ième version) mais depuis j'ai appris et je sais le faire maintenant avec Symfony 4 :)
Tu trouveras la déclaration de mon service dans le fichier services.yaml présent dans le dossier config.
Ce service dispose d'un constructeur et de 3 méthodes:
 
 - getConnexion permettant de se connecter à votre API.
 - getData de procéder au traitement des données et à leur enregistrement en base de données.
 - validatorData de contrôler les données par le biais du service validator.
 
Au niveau du constructeur, j'initialise tout ce dont j'aurai besoin par le biais de l'injection de dépendances (m'évitant de faire appel au container afin de lui demander le service voulu).

Ensuite, je fais appel à la méthode getConnexion() qui me renverra une réponse de type Symfony\Component\HttpFoundation\Response.
Dans cette méthode, je précise une uri '/testapi/shops' qui s'ajoutera à la base_uri configurée dans le fichier csa_guzzle.yaml.
A partir d'un TRY/CATCH, je fais appel à votre API (par le biais de la méthode GET) et s'il y a une erreur quelconque (par exemple le serveur ne répond pas) celle-ci sera catchée et logguée par le biais du service logger initialisé dans mon constructeur.
Je récupére le code status de la réponse et j'ai mis en place une vérification sur celui-ci.
Si le httpCode (ligne 71) est bien égale à 200, alors je récupére les données et je les déserialize (par le biais de JMSSerializer) en indiquant que je souhaite que les résultats soient sous forme de tableaux en JSON.

Les données récupérées, je fais appel à la méthode getData (avec en paramètre un array $data).
Je compte le nombre de données récupérées et je retire un 1 afin de procéder au traitement par le biais d'une boucle Do - While (je retire 1 tout simplement parce que l'on commencera à partir de l'indice 0).
En fonction des données et grâce à l'id_shop des habitués, j'interrogerai ma base de données afin de savoir si celle-ci est déjà présente:

 - Si oui, alors je mettrai à jour les données dans ma base.
 - Si non, j'enregistrerai cette nouvelle Shop dans ma base.
 
Cependant, avant tout enregistrement dans la base de données, je fais appel à la méthode validatorData qui me permettra de contrôler les données (récupérées ou saisies) afin de m'assurer que:

 - l'utilisateur n'a pas tapé n'importe quoi.
 - que les données provenant de l'API sont conformes.
 
Pour ce faire, des contraintes d'assertions ont été ajoutées au sein des différents champs de l'entité Shop.
Par exemple, pour le code postal (ligne 67 de l'entité Shop), j'ai mis en place une regex sur 5 chiffres.
Par conséquent, si l'utilsateur rentre le code de son département en lieu et place du code postal, une exception sera lancée et un message d'erreur "Invalid data" lui sera envoyé.
Ce message lui précisera le champ concerné et lui indiquera la contrainte à respecter pour que le champ soit valide.

Une fois tous les traitements terminés, je retourne une réponse avec le code HTTP_CREATED suivi du message: 'SHOP CREATED OR UPDATED'.

-----------------------------------------------------BONUS------------------------------------------------------------------------------------------------

Afin de créer et d'updater les shops récupérés (pour moi, pour le fun), j'ai mis en place un CRUD en utilisant FOSRestBundle.
Pour que tu puisses comprendre aisément tout ce que j'ai implémenté (à cause d'un trop grand nombre d'annotations dans le contrôleur), tu pourras à partir de cette URL accéder à la documentation que j'ai créée.
La voici : http://127.0.0.1:8000/api/doc

La documentation a été créée à l'aide de NelmioApiDoc et d'annotations Swagger.
Après différentes recherches, j'ai pu m'apercevoir que la configuration de Nelmio sur Symfony 3 (que j'avais vu pendant mon auto-formation) était totalement différente sur la 4 et c'est pour cette raison que je me suis forcé à créer une documentation histoire de me dire que: je l'ai vu sous Symfony 3, je l'ai vu sous Symfony 4 et bien maintenant je sais le faire sur les deux versions :)
Tu pourras jeter un oeil à la configuration dans config/packages/nelmio_api_doc.yaml et dans routes.yaml les routes correspondantes.
La documentation se basant sur le pattern par défaut: /les-habitues/
Lorsque tu ouvriras la doc, tu verras deux tags:

 - Shops, qui indiquera toutes les opérations disponibles concernant les Shops.
 - Technical Test, qui concernera la première partie du test tout simplement :)
 
Pour chaques méthodes, j'ai indiqué les paramètres requis et pour les réponses de la signification des codes retournés.
Concernant la création d'un Shop par exemple, j'ai utilisé un model de données afin de donner un exemple à la personne qui pourra être amené à lire la documentation.

Pour les méthodes POST ainsi que PUT, j'ai utilisé le body converter du FOSRestBundle grâce à l'annotation @ParamConverter, permettant de déserialiser automatiquement le contenu du body de ma requête en un objet Shop.
Tu trouveras la config du FOSRestBundle dans confif/packages/fos_rest.yaml.
Dedans, je précise :
 
 - les formats que je souhaite pouvoir gérer pour la sérialisation par le biais de fos_rest.view.formats (je n'accepte que le JSON).
 - lorsqu'un champ est null dans un objet, lors de la sérialisation, il ne sera pas considéré.
 - que le versionning d'API sera accepté dans l'entête Http Accept par le biais d'une regex.
 - que les exceptions sont activées avec les codes 400 et 500 (d'ou la création du fichier ResourceValidationException dans le dossier Exception).
 
Bien évidemment, avant chaque enregistrement, j'appelle la méthode validatorData du service Call pour contrôler les données saisies.
J'ai bien retenu ta remarque ^^

Pour la méthode GET, permettant de récupérer l'ensemble des Shops (/les-habitues/shops/list), j'ai créée une représentation pour l'entité Shop ainsi qu'une pagination par le biais de PagerFanta afin de paginer mes résultats et de ne pas avoir à afficher l'ensemble des résultats sur une seule et même page.
Ainsi dans le constructeur de ma représentation, je déclare mes datas comme étant de type PagerFanta et je définis:
 
 - le nombre total de résultats.
 - la limite de résultats à afficher par page (par défaut la limite est à 25).
 - la liste des éléments à récupérer de la page courante.
 - le nombre de page.
 
Dans le ShopRepository  ET grâce aux annotations @QueryParam ajoutées dans le contrôleur, tu pourras: 

 - rechercher le shop directement par son nom.
 - préciser la limite de résultats à afficher.
 - choisir d'afficher les résultats par ordre croissant ou décroissant selon ta recherche.
 
J'ai également traité la gestion des erreurs, afin de les personnaliser via un event subscriber.
En effet, si tu demandes une ressource qui n'existe pas en base de données, que tu saisisses une URL qui n'existe pas ou tout simplement que la donnée renseignée soit invalide, alors je renverrai différents codes de retours et son message associé:
 
 - 400 pour Invalid Data.
 - 404 pour Resource not found / No url found.
 - 500 pour Internal Server Error.

Pour terminer, j'ai rajouté l'élément _links à l'objet JSON représentant ma ressource par le biais de la librairie BazingaHateaosBundle (qui requiert le JMSSerializerBundle pour fonctionner), en indiquant également les URL absolues.

--------------------------------------------------------------------------------------------------------------------------------------------------------

2). Pour la seconde partie du test, je suis encore parti sur l'idée d'un service nommé ShopService.
Ce service comprend 1 constructeur (identique à mon permier service Call) et de 4 méthodes.

Pour la première méthode, j'ai imaginé une méthode qui se chargerait de faire des appels HTTP par le biais de Guzzle.
En fonction des différentes données transmises au service par le contrôleur, comme:
 
 - la méthode HTTP (GET, POST, PUT, DELETE).
 - l'url: ici, même si je ne connais pas la vraie URL, çà serait juste de passer l'id_shop comme ceci /761 par exemple (en plus de la base_uri dans le fichier de configuration csa_guzzle). 
 - l'objet Shop, récupéré par la déserialisation du body converter (çà serait uniquement pour traiter le cas du DELETE afin d'avoir mon propre id en base).
 - les options à passer lors de l'appel guzzle qui sont: de préciser le type de données que je vais envoyer dans le header et bien évidemment les données sérialisées dans le body.
 
Pour les méthodes POST et DELETE, je passerai l'url et les options par contre pour les méthodes DELETE et GET, je ne passerai que l'url.
Ensuite, je récupèrerai le code de la réponse et je passerai dans ma seconde méthode getHttpCode.
 
En fonction du code HTTP retourné, à partir d'un SWITCH/CASE, je ferai les différents traitements:
 
  - 200, je ferai un update.
  - 201, çà serait pour un nouvel enregistrement dans ma base de données.
  - 204, pour une suppresion de la ressource.
  - tandis que les codes 400, 404 et 500 pour les messages erreurs.
  
Petite précision pour les codes 200 et 201: je ferai appel aux méthodes getData et validatorData pour la vérification et le traitement des données en base.
Si vous retournez un code 204, alors je supprimerai également le shop dans ma base grâce à l'id. 

J'en ai terminé pour mes explications.
J'ai essayé de mettre en place les bonnes pratiques (injection de dépendances, services, nom des méthodes pour une meilleure compréhension), de respecter les normes PSR (même si à la fin j'utilise php-cs-fixer), de faire du DRY pour ne pas trop me répéter.
J'ai fait en sorte que ma seconde tentative soit plus concluante ^_^