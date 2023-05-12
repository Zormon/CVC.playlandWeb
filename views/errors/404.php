<?php 
define('SECCION', 'Error 404');
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<?php include $_SERVER['DOCUMENT_ROOT'] . 'web/head.php'; ?>

<body id="error">
    <?php include $_SERVER['DOCUMENT_ROOT'] . 'web/header.php';?>

    <main>
        <article class="clean red">
            <header><h1>Error 404</h1></header>
            <p>Ruta no encontrada</p>
        </article>
    </main>


    <?php include $_SERVER['DOCUMENT_ROOT'] . 'web/footer.php';?>

</body>

</html>