import {$, fetchJson, virtualKeyboard} from '/js/module-admin.min.js?v4'
import QrScanner from '/js/qr-scanner.min.js'


/*
===========================================================================================
========================================  START  ========================================
===========================================================================================
*/
class START {
    static participar(prueba,dorsal) {
        location = `/monitor/${prueba}/${dorsal.toString().padStart(4,'0')}/`
    }

    static init() {
        new virtualKeyboard( 'teclado' )

        if ( !!!localStorage.prueba ) { localStorage.prueba = $('prueba').firstElementChild.value }
        $('prueba').value = localStorage.prueba
        // Si tiene un valor invalido, por ejemplo, una prueba que ya no existe en el evento, carga la primera
        if (!!!$('prueba').value) { 
            $('prueba').value = $('prueba').querySelector('option').value 
            localStorage.prueba = $('prueba').value
        } 
        $('prueba').dataset.sel = localStorage.prueba


        $('prueba').onchange = (e)=> { 
            if ( confirm('¿Seguro que quieres cambiar de prueba este dispositivo?')) {
                e.currentTarget.dataset.sel = e.currentTarget.value
                localStorage.prueba = e.currentTarget.value
            } else {
                e.currentTarget.value = e.currentTarget.dataset.sel
            }
        }

        $('participar').onclick = (e)=>{
            e.preventDefault()
            const dorsal = $('dorsal').value
            if ( dorsal.length > 0 ) { this.participar($('prueba').value, dorsal) }
        }
        

        // ========== QR ===========
        const qrScan = new QrScanner( $('qrVideo'), async (res) => {
            if ( res.data.substr(0, 2) == 'e:' ) { // Es un QR de equipo
                const eData = res.data.substr(2).split(',')
                qrScan.stop()
                const respQr = await checkQr(eData)
                switch ( respQr ) {
                    case 0x0000: this.participar( $('prueba').value, parseInt(eData[0], 16)); break;
                    case 0x0424: alert('❌ QR de equipo inválido'); break;
                    default: alert('ERROR: ',`${data.data}`); break;
                }
            }
        }, {returnDetailedScanResult: true})


        function checkQr(data) {
            return new Promise(async (resolve) => {
                fetchJson('/api/monitor,checkQr', {eq:parseInt(data[0], 16), qr:data[1]}).then( (resp)=> {
                    resolve(Number(resp.status))
                })
            })

        }
        
        $('QR').onclick = (e)=> {
            e.preventDefault()
            $('qrContainer').style.display = 'flex'
            qrScan.start()

            $('cancelQr').onclick = ()=> {
                qrScan.stop()
                $('qrContainer').style.display = 'none'
            }

            return false
        }
        // ========== / QR ===========
   }
}

/*
===========================================================================================
========================================  PRUEBA  ========================================
===========================================================================================
*/
class PRUEBA {
    constructor(prueba, equipo) {
        this.prueba = prueba
        this.equipo = equipo

        this.init()
    }

    init() {
        let el
        
        if ( el = $('savePrueba') ) { 
            el.onclick = ()=> { 
                let res = $('resultado')
                this.send( res? res.dataset.raw : null) 
            }
        }
        if ( el = $('superarObstaculo') )   { el.onclick = ()=> { this.send('true') } }
        if ( el = $('fallarObstaculo') )    { el.onclick = ()=> { this.send('false') } }
    }

    send(resul) {
        const data = { prueba: this.prueba, equipo: this.equipo, resultado: resul }
                
        fetchJson('/api/monitor,participacion', data).then( (data)=> {
            switch (Number(data.status)) {
                case 0x0000: location='/monitor'; break;
                case 0x0411: alert('❌ El equipo no tiene intentos'); break; // sin intentos
                case 0x0420: alert('❌ El equipo ya ha empezado la race'); break; // race activa
                case 0x0421: alert('❌ El equipo no ha empezado la race'); break;
                case 0x0422: alert('❌ Obstaculo inválido'); break;
                case 0x0423: alert('❌ El equipo ya ha superado el obstáculo'); break;
                case 0x0424: alert('❌ El equipo ya ha fallado el obstáculo'); break;
                default: alert('ERROR: ',`${data.data}`); break;
            }
        })
   }
}

/*
===========================================================================================
========================================  LOGIN  ========================================
===========================================================================================
*/
class LOGIN {
    static init() {
        const form = $('loginForm')
        form.onsubmit = (e)=> {
            e.preventDefault()

            const data = { pass: $('pass').value }
            fetchJson('/api/monitor,login', data).then( (resp)=> {
                switch ( Number(resp.status) ) {
                    case 0x0000:
                        location = '/monitor'
                    return true
                    case 0x0400:
                        alert('❌ Ya estás dentro del sistema')
                    return false
                    case 0x0402:
                        alert('❌ Contraseña vacía')
                    return false
                    case 0x0403:
                        alert('❌ Credenciales inválidas')
                    return false
                
                    default:
                        alert(`❌ ${resp.data}`)
                    return false
                }
            })
        }
   }
}


export {
    START,
    PRUEBA,
    LOGIN
}