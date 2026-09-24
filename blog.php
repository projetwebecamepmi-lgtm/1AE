


<?php
// Connexion à la base de données
$serveur = "localhost";
$base = "1a_epmiweb";
$utilisateur = "root";
$motDePasse = "";

try {
    $pdo = new PDO(
        "mysql:host=$serveur;dbname=1a_epmiweb;charset=utf8mb4",
        $utilisateur,
        $motDePasse
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erreur) {
    die("Erreur de connexion : " . $erreur->getMessage());
}

// Ajouter ou modifier un article
if (isset($_POST["enregistrer"])) {
    $titre = $_POST["titre"];
    $contenu = $_POST["contenu"];
    $auteur = $_POST["auteur"];
    $image = $_POST["image"];

    if (!empty($_POST["id"])) {
        // Modification
        $requete = $pdo->prepare(
            "UPDATE articles
             SET titre = ?, contenu = ?, auteur = ?, image = ?
             WHERE id = ?"
        );

        $requete->execute([
            $titre,
            $contenu,
            $auteur,
            $image,
            $_POST["id"]
        ]);
    } else {
        // Ajout
        $requete = $pdo->prepare(
            "INSERT INTO articles (titre, contenu, auteur, image)
             VALUES (?, ?, ?, ?)"
        );

        $requete->execute([$titre, $contenu, $auteur, $image]);
    }

    header("Location: blog.php");
    exit();
}

// Supprimer un article
if (isset($_GET["supprimer"])) {
    $requete = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $requete->execute([$_GET["supprimer"]]);

    header("Location: blog.php");
    exit();
}

// Récupérer l'article à modifier
$articleAModifier = null;

if (isset($_GET["modifier"])) {
    $requete = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $requete->execute([$_GET["modifier"]]);
    $articleAModifier = $requete->fetch(PDO::FETCH_ASSOC);
}

// Afficher tous les articles
$articles = $pdo->query(
    "SELECT * FROM articles ORDER BY date_publication DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Blog | ECAM EPMI</title>

   <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>1AE Ecam Epmi</title>
  <meta name="description" content="site web 1AE">
  <link rel="shortcut icon" href="./img/logoEcamEpmi.jpg" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/global.css">
  <script src="./js/global.js" defer></script>

  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f7fa;
    }

    .banniere-blog {
      padding: 100px 20px 60px;
      text-align: center;
      color: white;
      background:
        linear-gradient(rgba(0, 59, 112, 0.75), rgba(0, 59, 112, 0.75)),
        url("img/campus.jpg") center / cover;
    }

    .titre-section {
      color: #003b70;
      font-weight: 700;
    }

    .article-image {
      height: 210px;
      width: 100%;
      object-fit: cover;
    }

    .card {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    footer {
      background-color: #001f3b;
      color: white;
    }
  </style>
</head>

<body>

   <header class="header">
    <div class="container d-flex">
      <div class="logo">
         ECAM EPMI
      </div>
      <button class="burger" type="button"
        aria-label="Ouvrir le menu" aria-expanded="false">
  <span class="bar"></span>
</button>
      <nav class="navbar ">
        <ul class="menu list-unstyled">
          <li><a href="index.html">Accueil</a></li>
          <li><a href="aPropos.html">A propos</a></li>
          <li><a href="blog.php">Blog</a></li>      
          <li><a href="contact.html">Contact</a></li>
        </ul>
      </nav>
      <div class="right">
        <ul class="socials list-unstyled">
          <li>
            <a href="https://google.fr" target="_blank">
              <svg aria-hidden="true" class="icon facebook" focusable="false" data-prefix="fab" data-icon="facebook-f"
                class="svg-inline--fa fa-facebook-f fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 320 512">
                <path fill="currentColor"
                  d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z">
                </path>
              </svg>
            </a>
          </li>
          <li>
            <a href="https://google.fr">
              <svg aria-hidden="true" class="icon linkedin" focusable="false" data-prefix="fab" data-icon="linkedin-in"
                class="svg-inline--fa fa-linkedin-in fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 448 512">
                <path fill="currentColor"
                  d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z">
                </path>
              </svg>
            </a>
          </li>
          <li>
            <a href="https://google.fr">
              <svg aria-hidden="true" class="icon twitter" focusable="false" data-prefix="fab" data-icon="twitter"
                class="svg-inline--fa fa-twitter fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 512 512">
                <path fill="currentColor"
                  d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z">
                </path>
              </svg>
            </a>
          </li>
          <li>
            <a href="https://google.fr">
              <svg aria-hidden="true" class="icon insta" focusable="false" data-prefix="fab" data-icon="instagram"
                class="svg-inline--fa fa-instagram fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 448 512">
                <path fill="currentColor"
                  d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z">
                </path>
              </svg>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </header>


  <main>






 







 <section id="portfolio" class="portfolio">
  <div class="container">
      <div class="section-header">
        <h2 class="section-title">Porfolio</h2>
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Eius eos dolore obcaecati perspiciatis. Eos neque
          similique placeat doloremque cupiditate, ut vero autem officia repellat explicabo, doloribus vel? Culpa, fuga
          quod!</p>
      </div>
      <ul class="grid portfolio-filters list-unstyled">
        <li class="grid__item">
          <a href="#" class="active" data-filter="all">Tous les projets</a>
        </li>
        <li class="grid__item">
          <a href="#" data-filter="web">Développement web</a>
        </li>
        <li class="grid__item">
          <a href="#" data-filter="design">Refonte Graphique</a>
        </li>
        <li class="grid__item">
          <a href="#" data-filter="app">Applications</a>
        </li>
      </ul>
      <div class="grid">
        <div class="grid__item">
          <div class="card" data-category="web">
            <img src="https://picsum.photos/600/400?random=1" loading="lazy" width="365" height="243"
              alt="projet de la ville" class="card__image">
            <div class="card__inner">
              <h3 class="card__title">Titre du projet</h3>
              <p class="category">Développement Web</p>
            </div>
            <div class="card__overlay">
              <a href="#" class="card__link" data-id="modal-1">+</a>
            </div>
          </div>
          <div class="modal" id="modal-1">
            <button class="modal__close">&times;</button>

            <div class="modal__content">

              <div class="container">
                <div class="grid">
                  <div class="grid__item">
                    <img src="https://picsum.photos/600/400?random=2" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                    <img src="https://picsum.photos/600/400?random=22" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                  </div>
                  <div class="grid__item">
                    <h4 class="modal__title h2">Titre de la modal 1</h4>
                    <p>
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="far"
                        data-icon="calendar" class="svg-inline--fa fa-calendar fa-w-14" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="currentColor"
                          d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z">
                        </path>
                      </svg>
                      <i>Année : 2019</i>
                    </p>
                    <p class="category">
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tag"
                        class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512">
                        <path fill="currentColor"
                          d="M0 252.118V48C0 21.49 21.49 0 48 0h204.118a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882L293.823 497.941c-18.745 18.745-49.137 18.745-67.882 0L14.059 286.059A48 48 0 0 1 0 252.118zM112 64c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48z">
                        </path>
                      </svg>
                      Refonte graphique
                    </p>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Informations</h5>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Technologies</h5>

                    <ul>
                      <li>HTML, CSS</li>
                      <li>Javascript</li>
                      <li>React</li>
                      <li>Node.js</li>
                      <li>Express.js</li>
                      <li>MongoDB</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="grid__item">
          <div class="card" data-category="design">
            <img src="https://picsum.photos/600/400?random=2" loading="lazy" width="365" height="243"
              alt="projet de la ville" class="card__image">
            <div class="card__inner">
              <h3 class="card__title">Titre du projet</h3>
              <p class="category">Refonte graphique</p>
            </div>
            <div class="card__overlay">
              <a href="#" class="card__link" data-id="modal-2">+</a>
            </div>
          </div>
          <div class="modal" id="modal-2">
            <button class="modal__close">&times;</button>

            <div class="modal__content">

              <div class="container">
                <div class="grid">
                  <div class="grid__item">
                    <img src="https://picsum.photos/600/400?random=2" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                    <img src="https://picsum.photos/600/400?random=22" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                  </div>
                  <div class="grid__item">
                    <h4 class="modal__title h2">Titre de la modal 2</h4>
                    <p>
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="far"
                        data-icon="calendar" class="svg-inline--fa fa-calendar fa-w-14" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="currentColor"
                          d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z">
                        </path>
                      </svg>
                      <i>Année : 2019</i>
                    </p>
                    <p class="category">
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tag"
                        class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512">
                        <path fill="currentColor"
                          d="M0 252.118V48C0 21.49 21.49 0 48 0h204.118a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882L293.823 497.941c-18.745 18.745-49.137 18.745-67.882 0L14.059 286.059A48 48 0 0 1 0 252.118zM112 64c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48z">
                        </path>
                      </svg>
                      Refonte graphique
                    </p>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Informations</h5>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Technologies</h5>

                    <ul>
                      <li>HTML, CSS</li>
                      <li>Javascript</li>
                      <li>React</li>
                      <li>Node.js</li>
                      <li>Express.js</li>
                      <li>MongoDB</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="grid__item">
          <div class="card" data-category="app">
            <img src="https://picsum.photos/600/400?random=3" loading="lazy" width="365" height="243"
              alt="projet de la ville" class="card__image">
            <div class="card__inner">
              <h3 class="card__title">Titre du projet</h3>
              <p class="category">Applications</p>
            </div>
            <div class="card__overlay">
              <a href="#" class="card__link" data-id="modal-3">+</a>
            </div>
          </div>
          <div class="modal" id="modal-3">
            <button class="modal__close">&times;</button>

            <div class="modal__content">

              <div class="container">
                <div class="grid">
                  <div class="grid__item">
                    <img src="https://picsum.photos/600/400?random=2" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                    <img src="https://picsum.photos/600/400?random=22" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                  </div>
                  <div class="grid__item">
                    <h4 class="modal__title h2">Titre de la modal 3</h4>
                    <p>
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="far"
                        data-icon="calendar" class="svg-inline--fa fa-calendar fa-w-14" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="currentColor"
                          d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z">
                        </path>
                      </svg>
                      <i>Année : 2019</i>
                    </p>
                    <p class="category">
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tag"
                        class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512">
                        <path fill="currentColor"
                          d="M0 252.118V48C0 21.49 21.49 0 48 0h204.118a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882L293.823 497.941c-18.745 18.745-49.137 18.745-67.882 0L14.059 286.059A48 48 0 0 1 0 252.118zM112 64c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48z">
                        </path>
                      </svg>
                      Refonte graphique
                    </p>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Informations</h5>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Technologies</h5>

                    <ul>
                      <li>HTML, CSS</li>
                      <li>Javascript</li>
                      <li>React</li>
                      <li>Node.js</li>
                      <li>Express.js</li>
                      <li>MongoDB</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="grid__item">
          <div class="card" data-category="web">
            <img src="https://picsum.photos/600/400?random=4" loading="lazy" width="365" height="243"
              alt="projet de la ville" class="card__image">
            <div class="card__inner">
              <h3 class="card__title">Titre du projet</h3>
              <p class="category">Développement Web</p>
            </div>
            <div class="card__overlay">
              <a href="#" class="card__link" data-id="modal-4">+</a>
            </div>
          </div>
          <div class="modal" id="modal-4">
            <button class="modal__close">&times;</button>

            <div class="modal__content">

              <div class="container">
                <div class="grid">
                  <div class="grid__item">
                    <img src="https://picsum.photos/600/400?random=2" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                    <img src="https://picsum.photos/600/400?random=22" loading="lazy" width="560" height="373"
                      class="card__image" alt="Projet site de la ville de lyon" />
                  </div>
                  <div class="grid__item">
                    <h4 class="modal__title h2">Titre de la modal 4</h4>
                    <p>
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="far"
                        data-icon="calendar" class="svg-inline--fa fa-calendar fa-w-14" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="currentColor"
                          d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z">
                        </path>
                      </svg>
                      <i>Année : 2019</i>
                    </p>
                    <p class="category">
                      <svg width="15" height="15" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tag"
                        class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512">
                        <path fill="currentColor"
                          d="M0 252.118V48C0 21.49 21.49 0 48 0h204.118a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882L293.823 497.941c-18.745 18.745-49.137 18.745-67.882 0L14.059 286.059A48 48 0 0 1 0 252.118zM112 64c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48z">
                        </path>
                      </svg>
                      Refonte graphique
                    </p>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Informations</h5>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing
                      elit. Quaerat accusantium laborum aliquam enim. Vero
                      voluptate, animi, sint vel mollitia ut odio, eius quis
                      expedita quo at! Dignissimos ipsum odit dolores!
                    </p>

                    <h5 class="h3">Technologies</h5>

                    <ul>
                      <li>HTML, CSS</li>
                      <li>Javascript</li>
                      <li>React</li>
                      <li>Node.js</li>
                      <li>Express.js</li>
                      <li>MongoDB</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  </section>


























































    <!-- Formulaire ajout / modification -->
    <section class="py-5">
      <div class="container">
        <div class="card p-4">
          <h2 class="titre-section mb-4">
            <?php echo $articleAModifier ? "Modifier un article" : "Ajouter un article"; ?>
          </h2>

          <form method="POST" action="blog.php">
            <input type="hidden" name="id"
                   value="<?php echo $articleAModifier["id"] ?? ""; ?>">

            <div class="mb-3">
              <label for="titre" class="form-label">Titre de l'article</label>
              <input type="text" id="titre" name="titre" class="form-control"
                     required
                     value="<?php echo htmlspecialchars($articleAModifier["titre"] ?? ""); ?>">
            </div>

            <div class="mb-3">
              <label for="auteur" class="form-label">Auteur</label>
              <input type="text" id="auteur" name="auteur" class="form-control"
                     required
                     value="<?php echo htmlspecialchars($articleAModifier["auteur"] ?? ""); ?>">
            </div>

            <div class="mb-3">
              <label for="image" class="form-label">Chemin de l'image</label>
              <input type="text" id="image" name="image" class="form-control"
                     placeholder="img/mon-image.jpg"
                     value="<?php echo htmlspecialchars($articleAModifier["image"] ?? ""); ?>">
            </div>

            <div class="mb-3">
              <label for="contenu" class="form-label">Contenu</label>
              <textarea id="contenu" name="contenu" rows="6"
                        class="form-control" required><?php
                echo htmlspecialchars($articleAModifier["contenu"] ?? "");
              ?></textarea>
            </div>

            <button type="submit" name="enregistrer" class="btn btn-primary">
              <?php echo $articleAModifier ? "Modifier l'article" : "Publier l'article"; ?>
            </button>

            <?php if ($articleAModifier): ?>
              <a href="blog.php" class="btn btn-secondary">Annuler</a>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </section>

    <!-- Liste des articles -->
    <section class="pb-5">
      <div class="container">
        <h2 class="text-center titre-section mb-4">Nos derniers articles</h2>

        <div class="row g-4">
          <?php foreach ($articles as $article): ?>
            <div class="col-md-6 col-lg-4">
              <article class="card h-100">

                <?php if (!empty($article["image"])): ?>
                  <img src="<?php echo htmlspecialchars($article["image"]); ?>"
                       class="article-image"
                       alt="<?php echo htmlspecialchars($article["titre"]); ?>">
                <?php endif; ?>

                <div class="card-body d-flex flex-column">
                  <h3 class="h5 card-title">
                    <?php echo htmlspecialchars($article["titre"]); ?>
                  </h3>

                  <p class="small text-muted">
                    Publié le
                    <?php echo date("d/m/Y", strtotime($article["date_publication"])); ?>
                    — <?php echo htmlspecialchars($article["auteur"]); ?>
                  </p>

                  <p class="card-text">
                    <?php echo nl2br(htmlspecialchars($article["contenu"])); ?>
                  </p>

                  <div class="mt-auto">
                    <a href="blog.php?modifier=<?php echo $article["id"]; ?>"
                       class="btn btn-warning btn-sm">
                      Modifier
                    </a>

                    <a href="blog.php?supprimer=<?php echo $article["id"]; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Voulez-vous supprimer cet article ?');">
                      Supprimer
                    </a>
                  </div>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  </main>

  <footer class="py-4 text-center">
    <p class="mb-0">© 2026 ECAM EPMI — Tous droits réservés.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
