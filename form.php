<?php
require 'config.php';
require 'functions.php';

$contact = null;

if (isset($_GET['id'])) {
    $contact = getContact($pdo, $_GET['id']);
}

// condition si le name du formulaire renvoie 'create'
if (isset($_POST['create'])) {
    $data = [$_POST['name'], $_POST['email']];
    if (verifyToken($_POST['token'])) {
        addContact($pdo, $data);
    }
}
if (isset($_GET['id'])) {
    $contact = getContact($pdo, $_GET['id']);
}

// Condition si le name du formulaire renvoie 'update'
if (isset($_POST['update'])) {
    $data = ["name"=>$_POST['name'], "email"=>$_POST['email']];
    if (verifyToken($_POST['token'])) {
        updateContact($pdo, $_GET['id'], $data);
    }
}

$token = generateToken();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formulaire</title>
</head>
<body>

<form method="POST">
    <input type="hidden" name="token" value="<?= $token ?>">

    <input type="text" name="name" placeholder="Nom" value="<?= $contact['name'] ?? '' ?>">
    <input type="email" name="email" placeholder="Email" value="<?= $contact['email'] ?? '' ?>">

    <button name="<?= $contact ? 'update' : 'create' ?>">Valider</button>
</form>


</body>
</html>