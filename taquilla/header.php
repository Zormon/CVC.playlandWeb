<?php
    if (!EVENTO_ACTUAL || EVENTO_ACTUAL['evTipo']!='playland') { include(ROOT_DIR.'/taquilla/noevent.php'); exit(); }

    require(__DIR__.'/../lib/class.admin.php');

    $ADMIN = new admin();
    if (!$ADMIN->isLogged) { header('Location: /taquilla/login'); exit(); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Taquilla</title>
    <link rel="stylesheet" href="/css/admin/taquilla.min.css?v4">
    <link rel="manifest" href="/taquilla/taquilla.webmanifest?2">
    <link rel="icon" type="image/png" href="/favicon.png">
    <script src="/js/mustache.min.js"></script>
    <link rel="stylesheet" href="/css/admin/choices.css">
    <script src="/js/choices.min.js"></script>
    <script><?=print_javascript_constants()?></script>
</head>