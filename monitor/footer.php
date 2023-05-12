<?php global $_CONFIG; ?>
<footer><?=EVENTO_ACTUAL['evNombre']?><?=$_CONFIG['debug']?' | '.P_EVENTO_ACTUAL:''?></footer>

<script type="module">
    import {$, fetchJson} from '/js/module-admin.min.js?v4'

    $('logout').onclick = ()=> {
        fetchJson('/api/monitor,logout').then( (data)=> {
            if (data.status == 0x0000)   { location = '/monitor' }
        })
    }


let el
if (el = $('backStart')) { el.onclick = ()=> { location = '/monitor' } }
</script>