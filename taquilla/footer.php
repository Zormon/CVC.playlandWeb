<footer>
    <?php if (EVENTO_ACTUAL) { ?>
        <?=EVENTO_ACTUAL['evNombre']?> | <?=EVENTO_ACTUAL['evLugar']?>
    <?php } else { ?>
        Sin evento actualmente
    <?php } ?>
</footer>

<script type="module">
    import {$, $$$, fetchJson} from '/js/module-admin.min.js?v4'

    $('logout').onclick = ()=> {
        fetchJson('/api/admin,logout').then( (data)=> {
            if (data.status == 0x0000)   { location = '/taquilla' }
        })
    }

    for ( let but of $$$('.app > header > nav > button:not(#logout)') ) {
        but.onclick = ()=> {
            location = '/taquilla/' + but.dataset.link
        }
    }
</script>


<?php 
global $_CONFIG;
if ($_CONFIG['debug']) { 
?>
    <div id="dev_info">
        <span id="exec_time">
        <?php
            define('TIME_END', microtime(true));
            echo round(TIME_END-TIME_START, 3) . " secs";
        ?>
        </span>
    </div>
<?php } ?>