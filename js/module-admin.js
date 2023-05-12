function $(id)       { return document.getElementById(id)     } //Alias de 'getElementById'
function $$(sel)     { return document.querySelector(sel)     } //Alias de 'querySelector'
function $$$(sel)    { return document.querySelectorAll(sel)  } //Alias de 'querySelectorAll'

function may(f,...args){try{ return f(...args)}catch{}}

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

class toolTip {
    constructor(element) {
        if (typeof element.dataset.tt_text != 'undefined' || typeof element.dataset.tt_pos != 'undefined') {
            // Asignaciones
            this.element = element
            this.position = element.dataset.tt_pos
            this.text = element.dataset.tt_text

            // Creacion del tooltip
            this.div = document.createElement('div')
            this.div.className = `ttBox ${this.position}`
            this.div.textContent = this.text

            // Init
            this.element.onmouseenter = () =>   { this.show() }
            this.element.onmouseleave = () =>   { this.hide() }
        }
    }

    show() {
        let rectEl = this.element.getBoundingClientRect()
        document.body.appendChild(this.div)
        switch (this.position) {
            case 'right':
                this.div.style.left = `${rectEl.right + 10}px`
                this.div.style.top = (rectEl.top + (rectEl.height/2)) - (this.div.offsetHeight/2) + 'px'
            break
            case 'left':
                this.div.style.left = `${rectEl.left - this.div.offsetWidth - 10}px`
                this.div.style.top = (rectEl.top + (rectEl.height/2)) - (this.div.offsetHeight/2) + 'px'
            break
            case 'up':
                this.div.style.left = (rectEl.left + (rectEl.width/2)) - (this.div.offsetWidth/2) + 'px'
                this.div.style.top = `${rectEl.top - this.div.offsetHeight}px`
            break
            case 'down':
                this.div.style.left = (rectEl.left + (rectEl.width/2)) - (this.div.offsetWidth/2) + 'px'
                this.div.style.top = `${rectEl.bottom + 10}px`
            break
        }
    }

    hide() {
        document.body.removeChild(this.div)
    }
}

/**
 * Adjunta un cuadrito con un mensaje al elemento
 * @param {element} el Elemento al que adjuntar el cuadro
 * @param {string} type Tipo de mensaje (info, warning, error...)
 * @param {string} msg Mensaje a mostrar
 */
function contentBox(el, type, msg) {
    if ( msg ) {
        let div = document.createElement('div')
        div.className = `contentBox ${type}Box`; div.textContent = msg

        if ( !el.contains( $$(`.${type}Box`) ) ) { el.appendChild(div) }
    } else {
        try { $$(`.${type}Box`).remove() } catch (e) {}
    }
}

/**
 * Ejecuta una función si dicha función no haya sido llamada anteriormente en el tiempo establecido
 * @param {function} func Función a demorar
 * @param {int} wait Milisegundos a esperar
 */
function debounce(func, wait) {
    var timeout
    return ()=> {
        var context = this, args = arguments
        if (!timeout) { func.apply(context, args) } 
        clearTimeout(timeout)
        timeout = setTimeout(()=>{timeout = false}, wait)
    }
}

/**
 * Ordena un array de json respecto a un parametro
 * @param {array} json Array json para ordenar
 * @param {String} param Parámetro desde el cual se ordena
 * @param {bool} numeric Si el parámetro es numérico
 */
function sortJson(json, param, numeric=false) {
    if (numeric)    { json.sort((a, b) => b[param]-a[param]) } 
    else            { json.sort((a, b) => a[param].toString().localeCompare(b[param])) }
}

/**
 * Nuevos selects
 */
class selectModal {
    constructor(target, text, type='list', search=false, groups=false) {
        this.target = target
        this.select = $(target)
        this.multiple = this.select.multiple
        this.text = text
        this.search = search
        this.groups = groups
        this.opts = this.select.querySelectorAll('option:enabled')
        this.groupsMenuShown = false

        // Elements
        this.div = document.createElement('div')
        this.ul = document.createElement('ul')
        this.button = document.createElement('button');
        this.div.className = `selectModal-div ${type}` + (this.multiple? ' multiple' : '')
        this.div.id = `selectModal-${target}`
        this.groupsMenu = null
        this.groupsBtn = null


        // Dropdown creation
        this.opts.forEach(op => {
            let li = document.createElement('li')
            li.dataset.value = op.value
            li.onclick = (e) => { this.change(e.target.dataset.value) }
            let span = document.createElement('span')
            span.textContent = op.textContent

            li.appendChild(span)
            if (op.dataset.icon) {
                let img = document.createElement('img')
                img.src = op.dataset.icon
                li.appendChild(img)
            }
            this.ul.appendChild(li)
        })

        // Texto
        let header = document.createElement('label')
        header.textContent = this.text
        this.div.appendChild( header )

        // Cuadro de busqueda
        if (this.search) {
            let search = document.createElement('input')
            search.type = 'search'
            search.placeholder = 'Buscar'
            this.div.appendChild( search )
            search.onkeyup = (e)=> {
                this.filter(e.currentTarget.value)
            }
        }

        // ====== Groups menu construction ========
        if (this.multiple && this.groups !== false) {
            this.groupsBtn = document.createElement('button')
            this.groupsBtn.textContent = 'Grupos ↓'; this.groupsBtn.className = 'groupsBtn flat'
            this.groupsBtn.onclick = (e)=> {
                if (e.target == e.currentTarget) { this.toggleGroupsMenu() }
            }
            this.div.appendChild( this.groupsBtn )

            
            this.groupsMenu = document.createElement('ul')
            this.groupsMenu.className = 'groupsMenu'
            
            // Option todos
                let li = document.createElement('li')
                li.dataset.items = []
                let span = document.createElement('span')
                span.textContent = 'Todos'

                let addBtn = document.createElement('button'); addBtn.className = 'addBtn'; addBtn.textContent = '+'
                let delBtn = document.createElement('button'); delBtn.className = 'delBtn'; delBtn.textContent = '-'

                li.appendChild(span)
                li.appendChild(addBtn)
                li.appendChild(delBtn)
                this.groupsMenu.appendChild(li)
            
            // resto de grupos
            for ( let grp of Object.values( this.groups ) ) {
                let li = document.createElement('li')
                li.dataset.items = grp.devices
                let span = document.createElement('span')
                span.textContent = grp.name

                let addBtn = document.createElement('button'); addBtn.className = 'addBtn'; addBtn.textContent = '+'
                let delBtn = document.createElement('button'); delBtn.className = 'delBtn'; delBtn.textContent = '-'
                li.appendChild(span)
                li.appendChild(addBtn)
                li.appendChild(delBtn)
                this.groupsMenu.appendChild(li)
            }

            this.groupsMenu.querySelectorAll('.addBtn').forEach(btn => {
                btn.onclick = (e)=> { this.mark(true, e.currentTarget.parentElement.dataset.items) }
            })
            this.groupsMenu.querySelectorAll('.delBtn').forEach(btn => {
                btn.onclick = (e)=> { this.mark(false, e.currentTarget.parentElement.dataset.items) }
            })

            this.groupsBtn.appendChild( this.groupsMenu )
        }
        // ====== /Groups menu construction ========
        
        // Ul
        this.div.appendChild(this.ul)

        // Boton del select
        this.button.textContent = this.text
        this.button.className = 'selectModal empty'
        this.button.className += this.multiple? ' multiple':''
        this.select.parentElement.insertBefore(this.button, this.select)

        // Eventos
        this.button.onclick = (e)=> { e.preventDefault(); e.stopPropagation(); this.show() }
        this.button.onmousedown = (e)=> { e.preventDefault() }

        this.select.addEventListener('change', this.update.bind(this))
        this.update()
    }

    mark(enable, items) {
        if (items!='')
            items.split(',').forEach(it => { try { this.select.querySelector(`option[value="${it}"]`).selected = enable }catch(e){} })
        else { // Todos
            this.select.querySelectorAll('option').forEach(op => { op.selected = enable })
        }
        this.select.dispatchEvent( new Event( 'change', {'bubbles':true} ) )
    }

    toggleGroupsMenu() {
        if (this.multiple) {
            this.groupsBtn.classList.toggle('shown')
            this.groupsMenuShown = !this.groupsMenuShown
        }
    }

    filter(pattern) {
        pattern = pattern.toUpperCase()
        this.ul.childNodes.forEach(li => {
            if (!li.querySelector('span').textContent.toUpperCase().includes(pattern))    { li.style.display = 'none'}
            else                                                                          { li.style.display = '' }
        })
    }

    change(n) {
        if (!this.multiple) {
            this.select.value = n
            this.close()
        } else {
            this.select.querySelector(`option[value="${n}"]`).selected ^= true
        }
        this.select.dispatchEvent( new Event( 'change', {'bubbles':true} ) )
    }

    show() {
        if ( !document.body.contains($(`closeWrapper-${this.target}`)) ) {
            let closeArea = document.createElement('button')
            closeArea.id = `closeWrapper-${this.target}`; closeArea.className = 'selectModalCloseArea'
            closeArea.onclick = this.close.bind(this)
            document.body.appendChild(closeArea)

            const rect = this.button.getBoundingClientRect()
            this.div.style.top = `${rect.top}px`
            this.div.style.left = `${rect.left}px`

            this.ul.style.maxHeight = `${window.innerHeight - rect.top - 70}px`
    
            document.body.appendChild( this.div )
    
            if (this.search) {
                this.div.querySelector('input[type="search"]').focus()
            }
        }
    }

    close() {
        try { $(`closeWrapper-${this.target}`).remove() }catch(e){}
        this.div.remove()
        if (this.search) {
            this.div.querySelector('input[type="search"]').value = ''
            this.filter('')
        }
    }

    update() {
        this.ul.childNodes.forEach(el => { el.classList.remove('selected') })
        let selected = this.getSelectedOptions()
        let text = ''

        selected.forEach( el=> {
            text += el.textContent + ','
            try { this.ul.querySelector(`li[data-value="${el.value}"]`).classList.add('selected') }catch(e){}
        })
        text = text.slice(0,-1)

        if (selected.length > 0) { this.button.textContent = text; this.button.classList.remove('empty') }
        else                    { this.button.textContent = this.text; this.button.classList.add('empty') }
    }

    getSelectedOptions() {
        let sel = []
        this.opts.forEach( op=> { if (op.selected) { sel.push(op) } })
        return sel
    }
}


/**
 * Listas editables
 */
class editableList {
    constructor(target, data, options={}) {
        this.target = target
        this.container = $(target)
        this.ul = document.createElement('ul')
        this.data = data
        this.addButton = 'add' in options? options.add : true
        this.delButton = 'delete' in options? options.delete : true
        this.headers = 'header' in options? options.header : true

        this.init()
    }

    init () {
        this.container.classList.add('editableList')

        if (this.headers) {
            let header = document.createElement('div')
            header.className = 'header row'

            this.data.fields.forEach(field => {
                let span = document.createElement('span')
                span.textContent = field.name
                span.className = 'width' in field? `f${field.width}` : 'f1'

                header.appendChild(span)
            })

            this.container.appendChild(header)
        }

        this.data.items.forEach(item => {
           this.new(item)
        })

        this.container.appendChild(this.ul)

        if (this.addButton) {
            let button = document.createElement('button')
            button.className = 'flat addButton'
            button.textContent = 'Añadir'
            button.onclick = (e) => {
                e.preventDefault()
                this.new()
            }

            this.container.appendChild(button)
        }
    }

    new( item=false ) {
        let li = document.createElement('li')
        li.className = 'row input'

        for (let i=0; i < this.data.fields.length; i++) {
            let input = document.createElement('input')
            input.type = this.data.fields[i].type
            input.className = 'width' in this.data.fields[i]? `f${this.data.fields[i].width}` : 'f1'
            if ('name' in this.data.fields[i])    { input.dataset.name = this.data.fields[i].name }
            if (item) { input.value = item[i] }
            li.appendChild(input)
        }

        if (this.delButton) {
            let button = document.createElement('button')
            button.className = 'flat'
            let i = document.createElement('i'); i.className = 'icon-delete'; button.appendChild(i)
            button.onclick = ()=> { li.remove() }

            li.appendChild(button)
        }

        this.ul.appendChild(li)
    }

    getFields() {
        return this.data.fields
    }

    getData() {
        let data = []

        this.ul.querySelectorAll('li').forEach(li => {
            let itemData = []
            for (let i=0; i < this.data.fields.length; i++) {
                itemData.push( li.querySelectorAll('input')[i].value )
            }
            data.push(itemData)
        })

        return data
    }
}


/**
 * Genera un cuadro modal con el contenido recibido y los botones con sus acciones en un objeto. 
 * @param {string} html El HTML a mostrar en el cuadro. NO ejecuta scripts.
 * @param {Object} buttons Los botones del footer. Ej: [{text:'Aceptar', action:''}]
 * @param {Number} width Ancho del cuadro en porcentaje de pantalla
 */
  function modalBox2 ( title, content, buttons=[{text:'Aceptar', action:''}], width=70, maxHeight=70 ) {
    if (document.body.contains( $('modalBox') )) { $('modalBox').remove() } // Borra el elemento si ya existe

    const modalBox = document.createElement('div'); modalBox.id = 'modalBox';
    const modalInner = document.createElement('div'); modalInner.style.width = `${width}%`; modalInner.style.maxHeight = `${maxHeight}%`;
    const modalHeader = document.createElement('div'); modalHeader.id = 'modalHeader'; modalHeader.textContent = title[0]; modalHeader.style.color = title[1]
    const modalContent = document.createElement('div'); modalContent.id = 'modalContent'; modalContent.innerHTML = content
    const modalButtons = document.createElement('div'); modalButtons.id = 'modalButtons'

    buttons.forEach(el => {
        const button = document.createElement('button')
        button.textContent = el.text; button.style.backgroundColor = el.color
        button.style.flexGrow = !!(el.size)? el.size : 1
        button.onclick = async ()=> { if ( await el.action()) { $('modalBox').remove() } }

        modalButtons.appendChild(button)
    })

    modalBox.onclick = (e) => { 
        if (e.target.id == 'modalBox' || e.target.className == 'cancelBtn') {
            $('modalBox').remove() 
    }}

    modalInner.appendChild(modalHeader)
    modalInner.appendChild(modalContent)
    modalInner.appendChild(modalButtons)
    modalBox.appendChild(modalInner)
    document.body.appendChild(modalBox)
}

/**
 * Genera un cuadro modal de confirmación
 * @param {string} msg El contenido HTML a mostrar
 * @param {function} action Accion a realizar si el usuario acepta
 */
function modalConfirm(msg, action) {
    modalBox(`<h1>${msg}</h1>`, [{text:'No'}, {text:'Si', action:()=>{action(); return true}}], 30)
}



/**
 * Busca y borra un valor de un array
 * @param arr array
 * @param value valor a borrar
 * @returns Array con el elemento borrado
 */
function arrayRemove(arr, value) { 
    return arr.filter(function(ele){ return ele != value })
}


function toggleRowActions(ev) {
    let row = ev.currentTarget.parentElement.parentElement
    if (!row.classList.contains('actionsVisible')) {
        try {$$('.actionsVisible').classList.remove('actionsVisible')}catch(e){}
        row.classList.add('actionsVisible')
    } else {
        row.classList.remove('actionsVisible')
    }
}

/**
 * Listas ordenables
 */
class sortableList {
    /**
     * 
     * @param {String} target Nombre del elemento OL con el que trabajar
     * @param {Array} data Datos en un array. Los elementos debe de tener las siguientes propiedades: {content, color, [img], [...]}
     */
    constructor(target, data) {
        this.target = target
        this.ol = $(target)
        this.data = data

        this.ol.classList.add('sortableList')
        this.render()
    }

    delete(index) {
        this.data.splice(index,1)
        this.render()
    }

    /** Dibuja la lista en elemento objetivo */
    render() {
        while (this.ol.firstChild) { this.ol.removeChild(this.ol.lastChild) }

        if (this.data.length < 1) {
            let li = document.createElement('li')
            li.dataset.index = 0
            li.textContent = 'Listado vacío'
            li.addEventListener('dragover', this.dragover)
            li.addEventListener('drop', this.drop.bind(this))

            this.ol.appendChild(li)
        } else {
            this.data.forEach((el, index) => {
                let li = document.createElement('li')
                li.draggable = true
                li.dataset.index = index
                li.addEventListener('dragstart', this.dragstart)
                li.addEventListener('dragover', this.dragover)
                li.addEventListener('drop', this.drop.bind(this))
                if (typeof el.color !== 'undefined') { li.style = `border-color: ${el.color}` }
    
                // Pic
                if (typeof el.img !== 'undefined') {
                    let img = document.createElement('img')
                    img.src = el.img
                    img.draggable = false
                    img.className = 'thumb'
    
                    li.appendChild(img)
                }
    
                // Content
                let content = document.createElement('div')
                content.className = 'content'; content.innerHTML = el.content
                li.appendChild(content)
    
    
                // Actions
                let actions = document.createElement('div')
                actions.className = 'actions'

                // Up & down

                // Delete
                let delBtn = document.createElement('button')
                delBtn.className = 'flat delete'
                delBtn.addEventListener('click', this.delete.bind(this, li.dataset.index))

                let icon = document.createElement('i')
                icon.className = 'icon-delete'

                delBtn.appendChild(icon)
                actions.appendChild(delBtn)
                li.appendChild(actions)
    
                this.ol.appendChild(li)
            })
        }
        
        
    }

    dragstart(e) { e.dataTransfer.setData('index', e.target.dataset.index) }
    dragover(e) { e.preventDefault() }

    drop(e) {
        e.preventDefault()
        let targetIndex = e.currentTarget.dataset.index
        
        if ( e.dataTransfer.getData('copy') != '' ) { // Copiar
            let data = JSON.parse(e.dataTransfer.getData('copy'))
            this.data.splice(targetIndex, 0, data)
            this.render()
        } else {
            let originIndex = parseInt(e.dataTransfer.getData('index'))
            if (originIndex != targetIndex) {
                this.data.splice(targetIndex, 0, this.data[originIndex])
                delete this.data[originIndex+1]
                this.render()
            }
        }
    }

    setData(data) {
        this.data = data
        this.render()
    }

    /**
     * Devuelve un array con el orden actual de la propiedad especificada
     * @param {String} property Propiedad a la que se quiere acceder
     * @returns Array
     */
    getData(property) {
        return this.data.map( a => a[property])
    }
}

class sortableListCatalog {
    constructor(target, data) {
        this.target = target
        this.ol = $(target)
        this.data = data

        this.ol.classList.add('sortableListCatalog')
        this.render()
    }

    render() {
        while (this.ol.firstChild) { this.ol.removeChild(this.ol.lastChild) }
        
        this.data.forEach((el, index) => {
            let li = document.createElement('li')
            li.draggable = true
            li.dataset.index = index
            li.addEventListener('dragstart', this.dragstart.bind(this))
            if (typeof el.color !== 'undefined') { li.style = `border-color: ${el.color}` }

            // Pic
            if (typeof el.img !== 'undefined') {
                let img = document.createElement('img')
                img.src = el.img
                img.draggable = false
                img.className = 'thumb'

                li.appendChild(img)
            }

            // Content
            let content = document.createElement('div')
            content.className = 'content'; content.innerHTML = el.content
            li.appendChild(content)
            
            this.ol.appendChild(li)
        })
    }

    dragstart(e) { e.dataTransfer.setData('copy', JSON.stringify(this.data[e.target.dataset.index])) }
}


/** 
 * Listas seleccionables
 */

class selectableList {
    constructor(target) {
        this.target = target
        this.list = $(target)
        this.selected = []
        this.lastSelected = 0

        this.list.classList.add('selectable-list')

        Array.from(this.list.children).forEach((li, i) => {
            li.dataset.index = i
            li.onclick = (e)=> { 
                let index = parseInt( e.currentTarget.dataset.index )
                this.select(index, e.shiftKey, e.ctrlKey)
             }
        })
    }

    selectRange(from, to) {
        const min = from>to? to : from
        const max = from>to? from : to;

        this.all(false)
        for (let i=min;i<=max;i++) {
            this.selected.push(i)
            this.list.children[i].classList.add('selected')
        }
    }

    select(index, line=false, toggle=false) {
        const indexEl = this.list.children[index]

        if (line) { // Shift pulsado, seleccion por rango
            this.selectRange(this.lastSelected, index)
        } else if (toggle) { // Ctrl pulsado, toggle
            if (indexEl.classList.toggle('selected')) {
                this.selected.push( index )
            } else {
                this.selected = arrayRemove(this.selected, index)
            }
        } else { // Seleccion normal
            this.all(false)
            indexEl.classList.add('selected')
            this.selected.push( index )
            this.lastSelected = index
        }
        
    }

    all(selected) {
        if (selected) {
            this.selectRange(0, this.list.children.length)
        } else {
            Array.from(this.list.children).forEach( li=> { li.classList.remove('selected') } )
            this.selected = []
        }
    }

    getSelectedNodes() {
        let nodes = []
        this.selected.forEach(i => {
            nodes.push( this.list.children[i] )
        })

        return nodes
    }
}

/** 
 * Comprueba si el dispositivo es una pantalla táctil
 * Sujeto a mejoras, por ahora funciona bien
 */
var isTouchScreen = (()=> { return window.matchMedia("(pointer: coarse)").matches })()


/** 
 * Teclado virtual
 */

 class virtualKeyboard {
    constructor(target) {
        this.keyboard = $(target)
        this.input = $(this.keyboard.dataset.input)
        this.keys = this.keyboard.querySelectorAll('button')

        for (let but of this.keys) {
            but.onclick = ()=>{ this.putKey(but.dataset.key) }
        }
    }

    putKey(char) {
        switch(char) {
            case '-b':
                this.input.value = this.input.value.slice(0, -1)
                break
            case '-x':
                this.input.value = ''
                break
            default:
                if ( this.input.value.length < this.input.dataset.vkmaxlength ) {
                    this.input.value += char
                    this.input.dispatchEvent( new Event('vkPut') )
                    if ( this.input.value.length == this.input.dataset.vkmaxlength ) { this.input.dispatchEvent( new Event('vkMaxReached') ) }
                }
                break
        }
    }
}


function letraDNI(dni) {
    let letters = ['T', 'R', 'W', 'A', 'G', 'M', 'Y', 'F', 'P', 'D', 'X', 'B', 'N', 'J', 'Z', 'S', 'Q', 'V', 'H', 'L', 'C', 'K', 'E', 'T'];
    return letters[dni%23];
}




export {
    $, $$, $$$,
    may,
    fetchJson,
    toolTip,
    contentBox,
    debounce,
    sortJson,
    selectModal,
    modalBox2,
    modalConfirm,
    toggleRowActions,
    editableList,
    sortableList,
    sortableListCatalog,
    arrayRemove,
    selectableList,
    virtualKeyboard,
    isTouchScreen,
    letraDNI
}