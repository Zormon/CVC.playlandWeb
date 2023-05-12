const MIN_EDAD = 2
const ERRCODE = new Map([
    [0x0100, 'Permiso denegado'],
    [0x0114, 'DNI ya registrado'],
    [0x0115, 'Email ya registrado'],
    [0x0116, 'Telefono ya registrado'],
    [0x0120, 'Cuenta no activada'],
    [0x0121, 'Se requiere cambio de contraseña'],
    [0x0122, 'Credenciales inválidas'],
    [0x030B, 'La reserva no se puede borrar, ya que ha sido pagada']
])

// Alias de selectores generales
var $ = document.getElementById.bind(document)
var $$ = document.querySelector.bind(document)
var $$$ = document.querySelectorAll.bind(document)

function may(f,...args){try{ return f(...args)}catch{}}

function showError(code) {
    code = parseInt(code)
    let msg = ERRCODE.get(code)
    if (!!!msg) { msg = '' }
    alert( `❌ Error: ${msg} (0x${code.toString(16).padStart(4, '0')})` )
}

function create(type, id='', className='', html=false) {
    var el = document.createElement(type)
    el.id = id; el.className = className
    if (html) { el.innerHTML = html }

    return el
}

function calcEdad(nacimimento) { 
    const date = new Date(nacimimento)
    const diff = Date.now() - date.getTime()
    const years = new Date(diff)

    return { years:years.getUTCFullYear() - 1970, negative: diff<0 }
}

function formDataToJSON(formData) {
    var object = {}
    formData.forEach((value, key) => {
        if (!Reflect.has(object, key)) {
            object[key] = value
            return
        }
        if (!Array.isArray(object[key])) {
            object[key] = [object[key]]
        }
        object[key].push(value)
    })
    return object
}

/**
 * Pide datos a una URL con la Fetch API enviando datos JSON y devuelve la promesa fetch a json si existe
 * @param {String} url La url al que enviar la peticion
 * @param {Object} send Los datos a enviar en objeto
 * @returns {Promise} Devuelve una promise con la respuesta. Incluye un campo '_httpCode' en el objeto devuelto con el codigo http de la respuesta
 */
 function fetchJson(url, send=null, method='POST') {
    return new Promise(async (resolve) => {
        let resp = await fetch(url, {method: method, headers: {'Accept': 'application/json','Content-Type': 'application/json'}, body:JSON.stringify(send)})
        let json = {}
        try { json = await resp.json() }catch{}
        json._httpCode = resp.status
        resolve(json)
    })
}

function simpleModal(content) {
    let el = $('simpleModal')
    if (!content && !!el) { el.remove() }
    else if ( !!!el ) {
        el = create('div', 'simpleModal')
        let innerDiv = create('div')
        innerDiv.innerHTML = content

        el.appendChild(innerDiv)
        document.body.appendChild(el)
    }
}

function changeScreen(number, element=document.body) {
    for (let s of element.querySelectorAll('.screen')) { s.classList.remove('active') }
    element.querySelector(`.screen[data-screen="${number}"]`).classList.add('active')
}

class slideShow {
    constructor(element, time=2) {
        this.time = time*1000

        this.imgs = $$$( `#${element} div` )
        this.count = this.imgs.length
        this.current = 0
        
        this.interval
    }
    
    next() {
        this.imgs.forEach( s=>{ s.className = '' } )
        const nx = this.current % this.count
        this.imgs[nx].className = 'visible'
        this.current++
    }

    start() {
        this.next()
        var self = this
        this.interval = setInterval(()=>self.next(), this.time)
    }

    stop() {
        clearInterval( this.interval )
    }
}

class gallery {
    constructor(element) {
        this.element = element
        this.imgs = element.querySelectorAll('img')
        this.count = this.imgs.length
        
        this.current = 0

        // Init
        for (let i=0;i<this.count;i++) { this.imgs[i].onclick = ()=> { 
            history.pushState({},'', '#'+i)
            this.show()
        }}

        this.div = create('div', 'galleryModal')
        this.div.onclick = ()=> { history.back() }
        this.img = create('img')

        this.div.appendChild(this.img)
        document.body.appendChild(this.div)

        window.addEventListener('popstate', ()=>{ this.show() })

        this.show()
    }

    show() {
        if (!!!location.hash) { //hide
            this.img.src = ''
            this.div.classList.remove('visible')
        } else {
            const n = location.hash.substring(1)
            if (!!this.imgs[n]) {
                this.img.src = this.imgs[n].src.replace('thumbs/','')
                this.div.classList.add('visible')
            } else { history.replaceState('','',window.location.pathname) }
        }
    }
}


class USER {
    static checkProfileForm(newuser) {
        const f = $('formProfile')
        let errors = []
        let e,v,p

        // Recortar espacios en blanco delante y detras de los campos
        for ( let input of f.querySelectorAll('[type="text"],[type="tel"],[type="email"]')) {
            input.value = input.value.trim()
        }

        // Nombre
        e = f.nombre; p = e.parentElement; v = e.value
        if      ( v == '' )                             { errors.push( '❌ Se requiere el nombre y apellidos del adulto' ); p.classList.add('invalid') }
        else if ( v.length < 3 )                        { errors.push( '❌ El nombre debe contener al menos tres letras' ); p.classList.add('invalid') }
        else if ( !!!v.split(' ')[1] )                  { errors.push( '❌ Debes introducir al menos un apellido' ); p.classList.add('invalid') }
        else if ( !e.checkValidity() )                  { errors.push( '❌ El nombre solo puede contener letras' ); p.classList.add('invalid') }
        else                                            { p.classList.remove('invalid') }

        // DNI
        e = f.DNI; p = e.parentElement; v = e.value
        if      ( v == '' )                             { errors.push( '❌ Se requiere DNI o NIE' ); p.classList.add('invalid') }
        else if ( v.length < 5 )                        { errors.push( '❌ El DNI/NIE debe contener al menos 5 cifras' ); p.classList.add('invalid') }
        else if ( !e.checkValidity() )                  { errors.push( '❌ El DNI/NIE solo puede contener números y letras' ); p.classList.add('invalid') }
        else                                            { p.classList.remove('invalid') }

        // email
        e = f.email; p = e.parentElement; v = e.value
        if      ( v == '' )                             { errors.push( '❌ Se requiere correo electrónico válido' ); p.classList.add('invalid') }
        else if ( !e.checkValidity() )                  { errors.push( '❌ El correo electrónico no es válido' ); p.classList.add('invalid') }
        else                                            { p.classList.remove('invalid') }

        // telefono
        e = f.telefono; p = e.parentElement; v = e.value
        if      ( v == '' )                             { errors.push( '❌ Se requiere teléfono' ); p.classList.add('invalid') }
        else if ( !e.checkValidity() )                  { errors.push( '❌ El teléfono no es válido' ); p.classList.add('invalid') }
        else                                            { p.classList.remove('invalid') }

        // password
        e = f.pass; p = e.parentElement; v = e.value
        if (newuser) { // Nuevo perfil
            if      ( f.pass.value.length < 6 )             { errors.push( '❌ La contraseña debe tener más de 6 caracteres' ); p.classList.add('invalid') }
            else if ( f.pass.value != f.passConfirm.value ) { errors.push( '❌ Las contraseñas deben coincidir' ); p.classList.add('invalid') }
            else                                            { p.classList.remove('invalid') }
        } else { // Modificar perfil
            if ( !!f.pass.value ) {
                if      ( f.pass.value.length < 6 )             { errors.push( '❌ La contraseña debe tener más de 6 caracteres' ); p.classList.add('invalid') }
                else if ( f.pass.value != f.passConfirm.value ) { errors.push( '❌ Las contraseñas deben coincidir' ); p.classList.add('invalid') }
                else                                            { p.classList.remove('invalid') }
            } else { p.classList.remove('invalid') }
        }

        // Acepto
        if (newuser) {
            e = f.acepto; p = e.parentElement; v = e.checked
            if ( !v )                                       { errors.push( '❌ Debe aceptar las condiciones legales' ); p.classList.add('invalid') }
            else                                            { p.classList.remove('invalid') }
        }




        if (errors.length) {
            alert( 'Se encontraron los siguientes errores:\n' + errors.join('\n') )
            return false
        } else {
            return true
        }
    }
    

    static async sendProfileForm(newuser) {
        const f = $('formProfile')
        const sendData = new FormData(f)
        const action = newuser? 'nuevo' : 'modificar'

        let resp = await fetchJson(`/api/adulto,${action}`, Object.fromEntries(sendData))
        if ( resp.status == 0x0000 )  {
            if (newuser) {
                $('sentMail').textContent = sendData.get('email')
                changeScreen(2)
            } else {
                location = '/perfil'
            }
        } else { showError(resp.status); return false; }
    }

    static checkTeamForm() {
        const f = $('formTeam')
        let errors = []
        let e,v,p

        // Recortar espacios en blanco delante y detras de los campos
        for ( let input of f.querySelectorAll('[type="text"]')) { input.value = input.value.trim() }
        
        // Titulo
        e = f.titulo; p = e.parentElement; v = e.value
        if      ( v == '' )                             { errors.push( '❌ Se requiere el título del equipo' ); p.classList.add('invalid') }
        else if ( v.length < 3 )                        { errors.push( '❌ El título debe contener al menos tres letras' ); p.classList.add('invalid') }
        else if ( !e.checkValidity() )                  { errors.push( '❌ El título solo puede contener letras y números' ); p.classList.add('invalid') }
        else                                            { p.classList.remove('invalid') }

        // Nombre
        e = f.nombre; p = e.parentElement; v = e.value
        if      ( v == '' )                             { errors.push( '❌ Se requiere el nombre y apellidos del menor' ); p.classList.add('invalid') }
        else if ( v.length < 3 )                        { errors.push( '❌ El nombre debe contener al menos tres letras' ); p.classList.add('invalid') }
        else if ( !!!v.split(' ')[1] )                  { errors.push( '❌ Debes introducir al menos un apellido' ); p.classList.add('invalid') }
        else if ( !e.checkValidity() )                  { errors.push( '❌ El nombre solo puede contener letras' ); p.classList.add('invalid') }
        else                                            { p.classList.remove('invalid') }

        // Nacimiento
        e = f.nacimiento; p = e.parentElement; v = e.value
        if ( v == '' )  { errors.push( '❌ Se requiere el nacimiento' ); p.classList.add('invalid') }
        else { 
            const edad = calcEdad(v)
            if ( edad.negative )                { errors.push( `❌ La fecha de nacimimento no puede ser en el futuro` ); p.classList.add('invalid') }
            else if ( edad.years < MIN_EDAD )   { errors.push( `❌ El menor debe tener al menos ${MIN_EDAD} años` ); p.classList.add('invalid') }
            else                                { p.classList.remove('invalid') }
        }

        if (errors.length) {
            alert( 'Se encontraron los siguientes errores:\n' + errors.join('\n') )
            return false
        } else {
            return true
        }
    }

    static async sendTeamForm() {
        const f = $('formTeam')
        const sendData = new FormData(f)
        const action = !!!sendData.get('id')? 'nuevo' : 'modificar'

        let resp = await fetchJson(`/api/equipo,${action}`, Object.fromEntries(sendData))
        switch ( Number(resp.status) ) {
            case 0x0000:
                location = '/perfil'
            break;
            case 0x020B:
                alert('❌ Ya existe un equipo con ese titulo')
            break;
            default:
                showError(resp.status);
            break;
        }
        return false;
    }

    static async sendTeamDelete() {
        const f = $('formTeam')
        const sendData = new FormData(f)
        let resp = await fetchJson(`/api/equipo,borrar,${sendData.get('id')}`)
        if ( resp.status == 0x0000 )  {
            location = '/perfil'
        } else { showError(resp.status); return false; }
    }
    

    static async sendLoginForm() {
        const f = $('formLogin')
        const sendData = new FormData(f)

        let resp = await fetchJson(`/api/adulto,entrar`, Object.fromEntries(sendData))
        if ( resp.status == 0x0000 )  { location = '/perfil' }
        else                          { showError(resp.status); return false; }
    }

    static async unlog() {
        let resp = await fetchJson(`/api/adulto,salir`)
        if ( resp.status == 0x0000 )  { location.reload() }
        else                        { showError(resp.status); return false; }
    }
}

class PICS {
    static init() {
        new gallery($$('.galleryPics'))
    }
}

class RESERVAS {

    static init() {
        const section = $('misreservas')
        for ( let el of section.querySelectorAll('.showCancelRes') ) {
            el.onclick = (e)=> {
                const id = e.currentTarget.dataset.rid
                changeScreen(2, $(`ev${id}`))
            }
        }

        for ( let el of $$$('.cancelCancel') ) {
            el.onclick = (e)=> {
                const id = e.currentTarget.dataset.rid
                changeScreen(1, $(`ev${id}`))
            }
        }

        for ( let el of $$$('.cancelRes') ) {
            el.onclick = (e)=> {
                const id = e.currentTarget.dataset.rid
                this.cancelReserva(id)
            }
        }
    }


    static checkReservaForm() {
        const f = $('formReserva')
        let errors = []
        let e,v,p, num

        // Equipos
        e = f.equipos; p = e.parentElement; num = e.querySelectorAll('option:checked').length

        if ( num == 0 ) { errors.push( '❌ Se requiere al menos un equipo' ); p.classList.add('invalid') }
        else            { p.classList.remove('invalid') }

        if (errors.length) {
            alert( 'Se encontraron los siguientes errores:\n' + errors.join('\n') )
            return false
        } else {
            return true
        }
    }

    static async sendReservaForm() {
        const f = $('formReserva')
        const sendData = new FormData(f)
        const action = !!!sendData.get('id')? 'nueva' : 'modificar'
        
        let resp = await fetchJson(`/api/reserva,${action}`, formDataToJSON(sendData))
        switch ( Number(resp.status) ) {
            case 0x0000:
                location = '/perfil/reservas'
            break;
            case 0x0307:
                alert('❌ Algún equipo no está disponible en la fecha seleccionada')
            break;
            default:
                showError(resp.status);
            break;
        }
        return false;
    }

    static async cancelReserva(rid) {
        let resp = await fetchJson(`/api/reserva,cancelar,${rid}`)
        if ( resp.status == 0x0000 )    { location.reload() }
        else                            { showError(resp.status); return false; }
    }
}

function hideMainMenu(e) {
    const inside = (e.target.closest('#mainMenu'));
    if( !inside ) { // CLick fuera, ocultar
        $('mainMenu').classList.remove('visible')
        document.removeEventListener('click', hideMainMenu)
     }
}
function hideProfileMenu(e) {
    const inside = e.target.closest('#profileMenu');
    if( !!!inside ) { // CLick fuera, ocultar
        $('profileMenu').classList.remove('visible')
        document.removeEventListener('click', hideProfileMenu)
     }
}

class WEB {
    static init() {
        const mainBut = $('toggle-mainMenu')
        if (!!mainBut) {
            mainBut.onclick = (e)=> {
                e.stopPropagation()
                const mainMenu = $('mainMenu')
                if ( !mainMenu.classList.contains('visible') )  { document.addEventListener('click', hideMainMenu) }
                else                                            { document.removeEventListener('click', hideMainMenu) }
                mainMenu.classList.toggle('visible')
            }
        }

        const profBut = $('toggle-profileMenu')
        if (!!profBut) {
            profBut.onclick = (e)=> {
                e.stopPropagation()
                const profileMenu = $('profileMenu')
                if ( !profileMenu.classList.contains('visible') )   { document.addEventListener('click', hideProfileMenu) }
                else                                                { document.removeEventListener('click', hideProfileMenu) }
                profileMenu.classList.toggle('visible')
            }
        }

        const unlogBtn = $('userUnlog')
        if (!!unlogBtn) {
            $('userUnlog').onclick = ()=> {
                fetchJson('/api/adulto,salir').then( (data)=> {
                    if (data.status == 0x0000)   { location.reload() }
                })
            }
        }
    }
}










export {
    $, $$, $$$,
    may,
    create,
    fetchJson,
    showError,
    simpleModal,
    changeScreen,
    slideShow,
    USER,
    PICS,
    RESERVAS,
    WEB
}