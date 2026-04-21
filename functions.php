<?php

/**
 * je récupère toutes les données enregistrées dans ma database
 * 
 * Cette fonction renvoie toutes les données pour les afficher sur une page
 * 
 * @param {var} $pdo - échange les informations avec la database
 * @return {number, string} - ID, name, email
 */
function getContacts($pdo) {
    $query = $pdo->prepare("SELECT * FROM contacts");
    $query->execute();
    $infos = $query->fetchAll(PDO::FETCH_ASSOC);

    // Je vérifie si des valeurs sont retournées, sinon j'affiche un message d'annonce à l'utilisateur
    if ($infos > 1) {
        return $infos;
    } else {
        print('Aucun contact trouvé.');
    }
}

/**
 * Je récupère des données dans ma database selon une condition précise
 * 
 * Cette fonction récupère les données nécessaires à l'affichage lors de la demande de modifications
 * 
 * @param {var} $pdo - échange les informations avec la database
 * @param {var} id - récupère la value de l'enregistrement dans le GET
 * @return {number, string} - ID, name, email
 */
function getContact($pdo, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $get = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
            if($get->execute([$id])) {
                $contact = $get->fetch();
                return $contact;
            }
    }
}

/**
 * Je récupère des données saisies pour les envoyer à la databse
 * 
 * Cette fonction enregistre les données saisies par l'utilisateur dans la database
 * 
 * @param {var} $pdo - échange les informations avec la database
 * @param {array} $data - contient les données saisies à envoyer
 * @throws {ValidationError} - Si une information est manquante
 */
function addContact($pdo, $data) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['name'])) {
            if(!empty($_POST['name']) && !empty($_POST['email'])) {
                $add = $pdo->prepare("INSERT INTO contacts (name, email) VALUES (?, ?)");
                $data['name'] = $_POST['name'];
                $data['email'] = filter_var($_POST['email']);
                $add->execute([$data['name'], $data['email']]);
                header('location:index.php');
            } else {
                print('Informations manquantes. Recommencez votre saisie.');
            }
        }
    }
}

/**
 * Je récupère des données de modifications saisies par l'utilisateur
 * 
 * Cette fonction enregistre les informations saisies par l'utiisateur et met à jour les informations dans la database
 * 
 * @param {var} $pdo - échange les informations avec la database
 * @param {var} id - récupère la value de l'enregistrement dans le GET
 * @param {array} $data - contient les données saisies à envoyer
 */
function updateContact($pdo, $id, $data) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET['id'])) {
            $modify = $pdo->prepare("UPDATE contacts SET name = ?, email = ? WHERE id = ?");
            $modify->execute([$data['name'], $data['email'], $id]);
            header('location:index.php');
        }
    }
    

}

/**
 * Je supprime les données demandées par l'utilisateur
 * 
 * Cette fonction supprime toutes les données liées à un enregistrement
 * @param {var} $pdo - échange les informations avec la database
 * @param {var} id - récupère la value de l'enregistrement dans le GET
 */
function deleteContact($pdo, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $del = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $del->execute([$id]);
        }
    }
}

// TOKEN

/**
 * Je génère un token de session
 * 
 * Cette fonction génère un token à l'ouverture de session de l'utilisateur
 * 
 * @throws {ConfigError} - Si un token de session est déjà existant
 * @return {var} $token - contient le hashage du token de session
 */
function generateToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Je vérifie si un token n'est pas déjà existant
    if (empty($_SESSION['token'])) {
        $token = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['token'];
    return $token;
}

/**
 * Je vérifie la validité du token de session
 * 
 * Cette fonction a pour intérêt d'ajouter une couche de sécurité à mon token
 * 
 * @param {var} $token - contient la value de mon token
 * @throws {boolean} - Renvoie false si le token n'est pas strictement égal
 * @return {boolean} - Renvoie true si le token est strictement égal
 */
function verifyToken($token) {
    if ($token === $_SESSION['token']) {
        return true;
    } else {
        return false;
    }
}
?>