<?php
if (!EVENTO_ACTUAL || EVENTO_ACTUAL['evTipo']!='playland') { include(ROOT_DIR.'/monitor/noevent.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Monitor</title>
    <link rel="manifest" href="/monitor/monitor.webmanifest?2">
    <link rel="stylesheet" href="/css/admin/monitor.min.css?v4">
    <link rel="icon" type="image/png" href="/favicon.png">
    <script><?=print_javascript_constants()?></script>
</head>