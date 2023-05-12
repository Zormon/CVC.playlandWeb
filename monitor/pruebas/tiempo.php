<div class="prueba tiempo">
    <div class="controls">
        <input type="text" id="resultado" value="00:00:000" readonly>
        <button class="btn BGgrd1" id="start-stop">Comenzar</button>
    </div>

    <div class="actions">
        <button class="green" id="savePrueba" disabled>Guardar</button>
    </div>
</div>

<script type="module">
    import {$} from '/js/module-admin.min.js?v4'
    var timeRunning = false
    var intervalTime, initTime, endTime, result

    $('start-stop').onclick = ()=>{
        if ( !timeRunning ){ // start
            initTime = Date.now()
            $('start-stop').textContent = 'Terminar'
            intervalTime = setInterval(()=> { // print time in screen
                let raw = Date.now() - initTime
                let formatted = new Date(raw).toISOString().slice(14,23)
                $('resultado').value = formatted
                },
            23)
            $('resultado').dataset.raw = ''
            $('savePrueba').disabled = true
        } else { // stop
            $('savePrueba').disabled = false
            $('resultado').dataset.raw = Date.now() - initTime
            $('start-stop').textContent = 'Reiniciar'
            clearInterval(intervalTime)
        }
        timeRunning = !timeRunning

    }
</script>