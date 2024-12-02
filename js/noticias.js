class Noticias {

    constructor() {
        //const main = document.querySelector("main");
        if (window.File && window.FileReader && window.FileList && window.Blob) {  
            /*const p = document.createElement("p");
            p.textContent = "Este navegador soporta el API File";
            main.appendChild(p);*/
            this.supports = true;
        } else {
            /*const p = document.createElement("p");
            p.textContent = "¡Este navegador NO soporta el API File!";
            main.appendChild(p);*/
            this.supports = false;
        }
    }

    readInputFile(files) {
        if(this.supports) {
            var archivo = files[0];
            var tipoTexto = /text.*/;
            if (archivo.type.match(tipoTexto)) {
                var lector = new FileReader();
                lector.onload = (evento) => {
                    const contenido = evento.target.result;
                    const lineas = contenido.split('\n');
                    lineas.forEach((linea) => {
                        if (!linea.trim()) return;
                        const [titular, cuerpo, autor] = linea.split('_');
                        this.crearNoticia(titular || `Titular no disponible`, cuerpo || "Contenido no disponible", autor || "Autor no disponible");
                    });
                };
                lector.readAsText(archivo);
            } else {
                alert("Archivo no válido");
            }
        }
    }

    crearNoticia(titular, contenido, autor) {
        const main = document.querySelector("main");
        const noticia = document.createElement("article");
        const titularElemento = document.createElement("h2");
        const contenidoElemento = document.createElement("p");
        const autorElemento = document.createElement("p");
        titularElemento.textContent = titular;
        contenidoElemento.textContent = contenido;
        autorElemento.textContent = "Autor: " + autor;
        noticia.appendChild(titularElemento);
        noticia.appendChild(contenidoElemento);
        noticia.appendChild(autorElemento);
        main.appendChild(noticia);
    }

    agregarNoticiaFormulario() {
        const inputs = document.querySelectorAll('section input[type="text"]');
        const textArea = document.querySelector("section textarea");
        const titular = inputs[0].value;
        const autor = inputs[1].value;
        const contenido = textArea.value;
        this.crearNoticia(titular, contenido, autor);
        inputs.forEach(input => {
            input.value = '';
        });
        textArea.value = "";
    }
}
