import {$, $$$, modalBox2, contentBox, fetchJson, sortJson} from '/js/module-admin.min.js?v4'


/*
===========================================================================================
========================================  RESERVAS  ========================================
===========================================================================================
*/
class RESERVAS {
    constructor (targetElement, templateElement, searchElement, orderElement=false) {
        this.target = targetElement
        this.template = templateElement
        this.search = searchElement
        this.order = orderElement
        this.json = []
    }

    modal (id=false)  {
        if (!id) {
            let html = Mustache.render( $('entryModal').innerHTML, {} )
            modalBox2 (['Nueva compra','var(--color-pink)'], html, [ {text:'Confirmar', color: 'var(--color-pink)', action:()=> { return this.send() } } ])
        } else {
            let item = this.json[id]
            let html = Mustache.render( $('entryModal').innerHTML, item )
            modalBox2 (['Editar compra','var(--color-pink)'], html, [
                {text:'Borrar', color: 'var(--color-red)', action:()=> { return this.delete(id) }},
                {text:'Editar', color: 'var(--color-pink)', size:2, action:()=> { return this.send(id) }}
            ])

            let form = $('entryForm');
            form.elements["equipo"].value = item.equipoId;
            form.elements["entrada"].value = item.entradaId;
            form.elements["pagado"].value = item.pagado;
            form.elements["dia"].value = item.dia;
        }
        new Choices( $('formEquipo'), { renderChoiceLimit:8} )
    }

    quickBuyModal (id=false)  {
        let html = Mustache.render( $('quickBuyModal').innerHTML, {} )
        modalBox2 (['Nueva compra rápida','var(--color-cyan)'], html, [ {text:'Confirmar', color: 'var(--color-cyan)', action:()=> { return this.sendQuickBuy() } } ], 80, 95)
    }

    async send (id=false) {
        if ( !$('entryForm').checkValidity() ) { return false }

        const entryForm = new FormData($('entryForm'))
        let action = id? 'edit' : 'add'

        let resp = await fetchJson(`/api/taquilla,reservas,${action}`, Object.fromEntries(entryForm))
        switch ( Number(resp.status) ) {
            case 0x0000:
                this.printList(true)
            return true
            case 0x0600:
                alert('❌ El equipo ya ha pagado para este evento')
            return false
            default:
                alert(`❌ ${resp.data}`)
            return false
        }
    }

    async sendQuickBuy () {
        const formEl = $('quickBuyForm')
        if ( !formEl.checkValidity() ) { formEl.reportValidity(); return false }
        const quickBuyForm = new FormData(formEl)

        const resp = await fetchJson(`/api/taquilla,reservas,quickBuy`, Object.fromEntries(quickBuyForm))
        switch ( Number(resp.status) ) {
            case 0x0000:
                this.printList(true);
            return true
            case 0x0601:
                alert('❌ DNI ya registrado')
            return false
            case 0x0602:
                alert('❌ El equipo 1 ya existe')
            return false
            case 0x0603:
                alert('❌ El equipo 2 ya existe')
            return false
            case 0x0604:
                alert('❌ El equipo 3 ya existe')
            return false
            default:
                alert(`❌ ${resp.data}`)
            return false
        }
    }

    async delete (id) {
        const html = `<p>¿Borrar compra del equipo <em>${this.json[id].equipo}</em>?</p><p><strong>Esto no se puede deshacer</strong></p>`
        modalBox2 (['Borrar compra','var(--color-red)'], html, [ {text:'Borrar', color: 'var(--color-red)', action:async()=> { 
            const resp = await fetchJson(`/api/taquilla,reservas,delete`, {id:id})

            switch ( Number(resp.status)) {
                case 0x0000:
                    this.printList(true)
                return true
                default:
                    alert(`❌ ${resp.data}`)
                return false
            }
        } } ])
    }

    async printList (refresh=false) {
        if (refresh) {
            let resp = await fetchJson('/api/taquilla,reservas,lista')
            switch (resp._httpCode) {
                case 200:
                    this.json = resp.data
                break
                case 204:
                    contentBox( this.target.parentElement, 'info', 'No hay reservas' )
                break
                default:
                    alert(`❌ HTTP_CODE: ${data._httpCode}<br>RESPONSE: ${data.data}`)
                break
            }
        }
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const name = el.equipo.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            const adulto = el.adulto.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || name.search( textFilter ) != -1 || adulto.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        }
        if (this.order) { sortJson(filtered, this.order.value) }
        
        let children = []
        if (filtered.length == 0) {
            contentBox( this.target.parentElement, 'info', 'No hay reservas' )
        } else {
            contentBox( this.target.parentElement, 'info', false )
            for (let item of filtered) {
                let el = document.createElement('li')
                el.dataset.id = item.id;
                el.innerHTML = Mustache.render( this.template.innerHTML, item )

                el.onclick = ()=>{ this.modal(item.id) }
                children.push(el)
            }
        }
        this.target.replaceChildren(...children)
    }
}



/*
===========================================================================================
========================================  ADULTOS  ========================================
===========================================================================================
*/
class ADULTOS {
    constructor (targetElement, templateElement, searchElement, orderElement=false) {
        this.target = targetElement
        this.template = templateElement
        this.search = searchElement
        this.order = orderElement
        this.json = []
    }

    modal (id=false)  {
        if (!id) {
            let html = Mustache.render( $('entryModal').innerHTML, {} )
            modalBox2 (['Nuevo adulto','var(--color-purple)'], html, [ {text:'Confirmar', color: 'var(--color-purple)', action:()=> { return this.send() } } ])
        } else {
            let item = this.json[id]
            let html = Mustache.render( $('entryModal').innerHTML, item )
            modalBox2 (['Editar adulto','var(--color-purple)'], html, [
                {text:'Borrar', color: 'var(--color-red)', action:()=> { return this.delete(id) }},
                {text:'Editar', color: 'var(--color-purple)', size:2, action:()=> { return this.send(id) }}
            ], 70)

            let form = $('entryForm');
            form.elements["estado"].value = item.estado;
        }
    }

    async send (id=false) {
        if ( !$('entryForm').checkValidity() ) { return false }

        const entryForm = new FormData($('entryForm'))
        const action = id? 'edit' : 'add'

        let data = await fetchJson(`/api/taquilla,adultos,${action}`, Object.fromEntries(entryForm))
        switch ( Number(data.status) ) {
            case 0x0000:
                this.printList(true)
            return true
            case 0x0114:
                alert('❌ Ya existe el DNI' )
            return false
            case 0x0115:
                alert('❌ Ya existe el email' )
            return false
            case 0x0116:
                alert('❌ Ya existe el telefono' )
            return false
            default:
                alert(`❌ RESPONSE: ${data.data}`)
            return false
        }
    }

    async delete (id) {
        const html = `<p>¿Borrar cuenta de <em>${this.json[id].nombre}</em>?</p><p><strong>¡SE BORRARÁN TODOS SUS EQUIPOS ASOCIADOS, RESERVAS Y PARTICIPACIONES DE TODOS LOS EVENTOS!</strong></p>`
        modalBox2 (['Borrar adulto','var(--color-red)'], html, [ {text:'Borrar', color: 'var(--color-red)', action:async()=> { 
            const resp = await fetchJson(`/api/taquilla,adultos,delete`, {id:id})
            switch ( Number(resp.status) ) {
                case 0x0000:
                    this.printList(true)
                return true
                default:
                    alert(`❌ RESPONSE: ${data.data}`)
                return false
            }
        } } ])
    }

    async printList (refresh=false) {
        if (refresh) {
            await fetchJson('/api/taquilla,adultos,lista').then( (data)=> { this.json = data.data })
        }
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const DNI = el.DNI.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            const nombre = el.nombre.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || DNI.search( textFilter ) != -1 || nombre.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        }
        if (this.order) { sortJson(filtered, this.order.value) }
        
        let children = []
        if (filtered.length == 0) {
            contentBox( this.target.parentElement, 'info', 'No hay adultos' )
        } else {
            contentBox( this.target.parentElement, 'info', false )
            for (let item of filtered) {
                let el = document.createElement('li')
                el.dataset.id = item.id;
                el.innerHTML = Mustache.render( this.template.innerHTML, item )

                el.onclick = ()=> { this.modal(item.id) }
                children.push(el)
            }
        }
        this.target.replaceChildren(...children)
    }
}



/*
===========================================================================================
========================================  EQUIPOS  ========================================
===========================================================================================
*/
class EQUIPOS {
    constructor (targetElement, templateElement, searchElement, orderElement=false) {
        this.target = targetElement
        this.template = templateElement
        this.search = searchElement
        this.order = orderElement
        this.json = []
    }

    modal (id=false)  {
        if (!id) {
            let html = Mustache.render( $('entryModal').innerHTML, {} )
            modalBox2 (['Nuevo equipo','var(--color-green)'], html, [ {text:'Confirmar', color: 'var(--color-green)', action:()=> { return this.send() } } ])
        } else {
            let item = this.json[id]
            item.nacimientoISO = item.nacimiento.split('-').reverse().join('-')
            let html = Mustache.render( $('entryModal').innerHTML, item )
            modalBox2 (['Editar equipo','var(--color-green)'], html, [
                {text:'Borrar', color: 'var(--color-red)', action:()=> { return this.delete(id) }},
                {text:'Editar', color: 'var(--color-green)', size:2, action:()=> { return this.send(id) }}
            ])

            let form = $('entryForm')
            form.elements["adulto"].value = item.adultoId
            form.elements["bando"].value = item.bando
        }
        new Choices( $('formAdulto'), { renderChoiceLimit:6} )
    }

    async send (id=false) {
        if ( !$('entryForm').checkValidity() ) { return false }

        const entryForm = new FormData($('entryForm'))
        const action = id? 'edit' : 'add'

        let data = await fetchJson(`/api/taquilla,equipos,${action}`, Object.fromEntries(entryForm))
        switch ( Number(data.status) ) {
            case 0x0000:
                this.printList(true)
            return true
            case 0x0610:
                alert('❌ El equipo ya existe' )
            return false
            default:
                alert(`❌ RESPONSE: ${data}`)
            return false
        }
    }

    async delete (id) {
        const html = `<p>¿Borrar equipo <em>${this.json[id].equipo}</em>?</p><p><strong>¡SE BORRARÁN TODAS LAS RESERVAS Y PARTICIPACIONES ASOCIADAS DE TODOS LOS EVENTOS!</strong></p>`
        modalBox2 (['Borrar equipo','var(--color-red)'], html, [ {text:'Borrar', color: 'var(--color-red)', action:async()=> { 
            let data = await fetchJson(`/api/taquilla,equipos,delete`, {id:id})

            switch ( Number(data.status) ) {
                case 0x0000:
                    this.printList(true)
                return true
                case 0x0610:
                    alert('❌ El equipo ya existe' )
                return false
                default:
                    alert(`❌ RESPONSE: ${data}`)
                return false
            }
            
        }}])
    }

    async printList (refresh=false) {
        if (refresh) {
            await fetchJson('/api/taquilla,equipos,lista').then( (data)=> { this.json = data.data })
        }
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const titulo = el.titulo.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            const nombre = el.nombre.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            const adulto = el.adulto.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == ''
                || titulo.search( textFilter ) != -1 
                || nombre.search( textFilter ) != -1
                || adulto.search( textFilter ) != -1 
            ) {
                filtered.push(el)
            }
        }
        if (this.order) { sortJson(filtered, this.order.value) }
        
        let children = []
        if (filtered.length == 0) {
            contentBox( this.target.parentElement, 'info', 'No hay equipos' )
        } else {
            contentBox( this.target.parentElement, 'info', false )
            for (let item of filtered) {
                let el = document.createElement('li')
                el.dataset.id = item.id;
                el.style.borderColor = '#'+item.color
                el.innerHTML = Mustache.render( this.template.innerHTML, item )

                el.onclick = ()=> { this.modal(item.id) }
                children.push(el)
            }
        }
        this.target.replaceChildren(...children)
    }
}



/*
===========================================================================================
====================================  PARTICIPACIONES  ====================================
===========================================================================================
*/
class PARTICIPACIONES {
    constructor (targetElement, templateElement, searchElement, orderElement=false) {
        this.target = targetElement
        this.template = templateElement
        this.search = searchElement
        this.order = orderElement
        this.json = []
    }

    modal (id=false)  {
        if (!id) {
            let html = Mustache.render( $('entryModal').innerHTML, {} )
            modalBox2 (['Nueva participación','var(--color-yellow)'], html, [ {text:'Confirmar', color: 'var(--color-yellow)', action:()=> { return this.send() } } ])
        } else {
            let item = this.json[id]
            let html = Mustache.render( $('entryModal').innerHTML, item )
            modalBox2 (['Editar participación','var(--color-yellow)'], html, [
                {text:'Borrar', color: 'var(--color-red)', action:()=> { return this.delete(id) }},
                {text:'Editar', color: 'var(--color-yellow)', size:2, action:()=> { return this.send(id) }}
            ])


            let form = $('entryForm')
            form.elements["equipo"].value = item.equipoId
            form.elements["prueba"].value = item.pruebaId
        }

        new Choices( $('formEquipo'), { renderChoiceLimit:6} )
        new Choices( $('formPrueba'), { renderChoiceLimit:7} )
    }


    async send (id=false) {
        if ( !$('entryForm').checkValidity() ) { return false }

        const entryForm = new FormData($('entryForm'))
        if (id) { entryForm.append('id', id) }

        const resp = await fetchJson(`/api/taquilla,participaciones,edit`, Object.fromEntries(entryForm))
        switch ( Number(resp.status) ) {
            case 0x0000:
                this.printList(true)
            return true
            default:
                alert(`❌ RESPONSE: ${data}`)
            return false
        }
    }

    async delete (id) {
        const el = this.json[id]
        const html = `<p>¿Borrar participación de <em>${el.equipo}</em> en <em>${el.prueba}</em>?</p><p>Fecha: <em>${el.fecha}</em> | Resultado: <em>${el.resultadoISO}</em></p><p><strong>Esto no se puede deshacer</strong></p>`
        modalBox2 (['Borrar participación','var(--color-red)'], html, [ {text:'Borrar', color: 'var(--color-red)', action:async()=> { 
            let resp = await fetchJson(`/api/taquilla,participaciones,delete`, {id:id})
            switch ( Number(resp.status) ) {
                case 0x0000:
                    this.printList(true)
                return true
                default:
                    alert(`❌ RESPONSE: ${data.data}`)
                return false
            }
        }}])
    }

    async printList (refresh=false) {
        if (refresh) {
            let resp = await fetchJson('/api/taquilla,participaciones,lista')
            switch (resp._httpCode) {
                case 200:
                    this.json = resp.data
                break
                case 204:
                    contentBox( this.target.parentElement, 'info', 'No hay participaciones' )
                break
                default:
                    alert(`❌ HTTP_CODE: ${data._httpCode}<br>RESPONSE: ${data.data}`)
                break
            }
            
        }
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        let filtered = []
        for ( let el of Object.values(this.json) ) {
            switch (el.pruebaTipo) {
                case 'puntos':
                    el.resultadoISO = el.resultado
                    break
                case 'race':
                    if (el.resultado == -1) { el.resultadoISO = 'corriendo' }
                    else                    { el.resultadoISO = new Date(parseInt(el.resultado)).toISOString().slice(14,23) }
                break
                default:
                    el.resultadoISO = new Date(parseInt(el.resultado)).toISOString().slice(14,23)
                break;
            }

            const equipo = el.equipo.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            const prueba = el.prueba.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == ''
                || equipo.search( textFilter ) != -1
                || prueba.search( textFilter ) != -1 
            ) {
                filtered.push(el)
            }
        }
        if (this.order) { sortJson(filtered, this.order.value) }
        
        let children = []
        if (filtered.length == 0) {
            contentBox( this.target.parentElement, 'info', 'No hay participaciones' )
        } else {
            contentBox( this.target.parentElement, 'info', false )
            for (let item of filtered) {
                let el = document.createElement('li')
                el.dataset.id = item.id;
                el.innerHTML = Mustache.render( this.template.innerHTML, item )

                el.onclick = ()=> { this.modal(item.id) }
                children.push(el)
            }
        }
        this.target.replaceChildren(...children)
    }
}




class LOGIN {
    static init() {
        const form = $('loginForm')
        form.onsubmit = (e)=> {
            e.preventDefault()

            const data = { user: $('user').value, pass: $('pass').value }
            fetchJson('/api/admin,login', data).then( (data)=> {
                switch ( Number(data.status) ) {
                    case 0x0000:
                        location = '/taquilla'
                    break
                    case 0x0501:
                        alert('❌ Sin usuario')
                    break
                    case 0x0502:
                        alert('❌ Sin pass')
                    break
                    case 0x0503:
                        alert('❌ Credenciales inválidas')
                    break
                    default:
                        alert(`❌ HTTP_CODE: ${data._httpCode}<br>RESPONSE: ${data.data}`)
                    break
                }
            })
            return false
        }
   }
}


export {
    RESERVAS,
    ADULTOS,
    EQUIPOS,
    PARTICIPACIONES,
    LOGIN
}