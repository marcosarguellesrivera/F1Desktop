class Circuito {

    constructor() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {  
            this.supports = true;
        } else {
            this.supports = false;
        }
    }

    leerArchivoTexto(archivos) { 
        if(this.supports) {
            var archivo = archivos[0];
            var main = document.querySelector("main");
            var section = document.querySelector("section");
            if(section) main.removeChild(section);
            section = document.createElement("section");
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

    crearMapaDinamico(archivos) {
        if(this.supports) {
            var archivo = archivos[0];
            if (archivo && archivo.name.endsWith(".kml")) {
                const lector = new FileReader();
                lector.onload = (e) => {
                    const div = document.createElement("div");
                    const kml = new DOMParser().parseFromString(e.target.result, "application/xml");
                    console.log(new XMLSerializer().serializeToString(kml));
                    const namespace = "http://www.opengis.net/kml/2.2";
                    const coordenadas = kml.getElementsByTagNameNS(namespace, "coordinates")[0].textContent.trim().split("\n").map(c => {
                        var [longitud, latitud] = c.split(",").map(Number);
                        return {lng: longitud, lat: latitud};
                    })
                    const opciones = {
                        center: coordenadas[0],
                        zoom: 13,
                    };
                    const mapa = new google.maps.Map(div, opciones);
                    const linea = new google.maps.Polyline({
                        strokeColor: "#ff0000",
                        strokeWeight: 5,
                        strokeOpacity: 0.7,
                        path: coordenadas,
                    });
                    linea.setMap(mapa);
                    const main = document.querySelector("main");
                    main.appendChild(div);
                };
                lector.readAsText(archivo);
            }
        }
    }

    mostrarSvg(archivos) {
        if (this.supports) {
            var archivo = archivos[0];
            if (archivo && archivo.name.endsWith(".svg")) { 
                const lector = new FileReader();
                lector.onload = (e) => {
                    const main = document.querySelector("main"); 
                    const section = document.createElement("section");
                    const img = document.createElement("img");
                    img.src = e.target.result; 
                    img.alt = "Vista previa del archivo SVG";
                    img.style.maxWidth = "100%";
                    img.style.height = "auto";
                    section.appendChild(img);
                    main.appendChild(section);
                };
                lector.readAsDataURL(archivo);
            }
        }
    }
}