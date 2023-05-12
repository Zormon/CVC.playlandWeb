<div class="prueba puntos">
    <div class="controls">
        <button data-op="-5">-5</button>
        <button data-op="-1">-1</button>
        <input type="number" id="resultado" value="0" readonly>
        <button data-op="1">+1</button>
        <button data-op="5">+5</button>
    </div>

    <div class="actions">
        <button class="green" id="savePrueba">Guardar</button>
    </div>
</div>

<script type="module">
    import {$, $$$} from '/js/module-admin.min.js?v4'
    for (let con of $$$('.controls button')) {
        con.onclick = ()=>{
            const calc = parseInt($('resultado').value) + parseInt(con.dataset.op)
            $('resultado').value = calc <0? 0 : calc
            $('resultado').dataset.raw = $('resultado').value
        }
    }
</script>