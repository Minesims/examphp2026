<?php

function getContacts($pdo) {
    $query = $pdo->prepare("SELECT * FROM contacts");
    $query->execute();
    $infos = $query->fetchAll(PDO::FETCH_ASSOC);
    if ($infos > 1) {
        return $infos;
    } else {
        print('Aucun contact trouvé.');
    }
}

function getContact($pdo, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $get = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
            if($get->execute([$id])) {
                $contact = $get->fetch();
                return $contact;
            }
    }
}

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

function updateContact($pdo, $id, $data) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET['id'])) {
            $modify = $pdo->prepare("UPDATE contacts SET name = ?, email = ? WHERE id = ?");
            $modify->execute([$data['name'], $data['email'], $id]);
            header('location:index.php');
        }
    }
    

}

function deleteContact($pdo, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $del = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $del->execute([$id]);
        }
    }
}

// TOKEN
function generateToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['token'])) {
        $token = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['token'];
    return $token;
}

function verifyToken($token) {
    if ($token === $_SESSION['token']) {
        return true;
    } else {
        return false;
    }
}
?>