class Circuito {

    constructor() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {  
            this.supports = true;
        } else {
            this.supports = false;
        }
    }

    leerArchivoTexto(files) { 
        if(this.supports) {
            var archivo = files[0];
            var main = document.querySelector("main");
            var section = document.createElement("section");
            var nombre = document.createElement("h3");
            var tamano = document.createElement("p");
            var tipo = document.createElement("p");
            var ultima = document.createElement("p");
            var contenido = document.createElement("p");
            var areaVisualizacion = document.createElement("pre");
            var errorArchivo = document.createElement("p");
            nombre.innerText = archivo.name;
            tamano.innerText = "Tamaño del archivo: " + archivo.size + " bytes"; 
            tipo.innerText = "Tipo del archivo: " + archivo.type;
            ultima.innerText = "Fecha de la última modificación: " + archivo.lastModifiedDate;
            contenido.innerText="Contenido del archivo de texto:"
            //Solamente admite archivos de tipo texto
            var tipoTexto = /text.*/;
            if (archivo.type.match(tipoTexto)) {
                var lector = new FileReader();
                lector.onload = function (evento) {
                //El evento "onload" se lleva a cabo cada vez que se completa con éxito una operación de lectura
                //La propiedad "result" es donde se almacena el contenido del archivo
                //Esta propiedad solamente es válida cuando se termina la operación de lectura
                areaVisualizacion.innerText = lector.result;
                }      
                lector.readAsText(archivo);
                section.appendChild(nombre);
                section.appendChild(tamano);
                section.appendChild(tipo);
                section.appendChild(ultima);
                section.appendChild(contenido);
                section.appendChild(areaVisualizacion);
            } else {
                errorArchivo.innerText = "Error : ¡¡¡ Archivo no válido !!!";
                section.appendChild(errorArchivo);
            }       
            main.appendChild(section);
        }
    }
}