<?php
require(__DIR__.'/../lib/class.monitor.php');
$MONITOR = new monitor();
if ($MONITOR->isLogged) { header('Location: /monitor'); exit(); }
?>

<?php include('header.php');?>

<body class="login">
    <header><img class="logo" src="/img/playlandLogoPlain.webp"></header>
    <main>
        <form id="loginForm">
            <input class="txt" type="password" id="pass" placeholder="Clave" required>
            <button id="entrar" class="btn wide BGgrd1 feedback">Entrar</button>
        </form>
    </main>

    <script type="module">
        import {LOGIN} from '../js/module-monitor.min.js?v4'
        LOGIN.init()
    </script>
</body>
</html>