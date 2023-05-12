<?php 
define('SECCION', 'Error 403');
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="es">
<?php include $_SERVER['DOCUMENT_ROOT'] . 'web/head.php'; ?>

<body id="error">
    <?php include $_SERVER['DOCUMENT_ROOT'] . 'web/header.php';?>

    <main>
        <article class="clean red">
            <header><h1>Error 403</h1></header>
            <p>Permiso denegado</p>
        </article>
    </main>


    <?php include $_SERVER['DOCUMENT_ROOT'] . 'web/footer.php';?>

</body>

</html>