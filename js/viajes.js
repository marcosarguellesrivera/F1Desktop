class Viajes {

    constructor () {
        navigator.geolocation.getCurrentPosition(this.getPosicion.bind(this), this.verErrores.bind(this));
    }

    getPosicion(posicion) {
        this.mensaje = "Se ha realizado correctamente la peticion de geolocalizacion";
        this.longitud         = posicion.coords.longitude; 
        this.latitud          = posicion.coords.latitude;  
        this.getMapaEstatico();
        this.getMapaDinamico();
    }

    verErrores(error) {
        switch(error.code) {
        case error.PERMISSION_DENIED:
            this.mensaje = "El usuario no permite la peticion de geolocalizacion"
            break;
        case error.POSITION_UNAVAILABLE:
            this.mensaje = "Informacion de geolocalizacion no disponible"
            break;
        case error.TIMEOUT:
            this.mensaje = "La peticion de geolocalizacion ha caducado"
            break;
        case error.UNKNOWN_ERROR:
            this.mensaje = "Se ha producido un error desconocido"
            break;
        }
        console.error(this.mensaje);
    }

    getMapaEstatico() {
        if (typeof this.latitud === "undefined" || typeof this.longitud === "undefined") {
            console.error("Las coordenadas no están definidas");
            return;
        }
        var section = document.querySelector("section");
        var h3 = document.createElement("h3");
        h3.textContent = "Mapa estático - Posición actual";
        section.appendChild(h3);
        var article = document.createElement("article");
        var apiKey = "&key=AIzaSyDGGEsBmw72XKrpCtXAzTrtAqEKpKzd2kI";
        var url = "https://maps.googleapis.com/maps/api/staticmap?";
        var centro = "center=" + this.latitud + "," + this.longitud;
        var zoom ="&zoom=15";
        var size = "&size=800x600";
        var marcador = "&markers=color:red%7Clabel:S%7C" + this.latitud + "," + this.longitud;
        var sensor = "&sensor=false"; 
        this.imagenMapa = url + centro + zoom + size + marcador + sensor + apiKey;
        article.innerHTML = "<img src='"+ this.imagenMapa + "' alt='mapa estatico google' />";
        section.appendChild(article);
    }

    getMapaDinamico() {
        if (typeof this.latitud === "undefined" || typeof this.longitud === "undefined") {
            console.error("Las coordenadas no están definidas");
            return;
        }
        const opcionesMapa = {
            center: { lat: this.latitud, lng: this.longitud },
            zoom: 15,
        };
        const section = document.querySelector("section");
        var h3 = document.createElement("h3");
        h3.textContent = "Mapa dinámico - Posición actual";
        section.appendChild(h3);
        const div = document.createElement("div");
        const mapa = new google.maps.Map(div, opcionesMapa);
        new google.maps.Marker({
            position: { lat: this.latitud, lng: this.longitud },
            map: mapa,
            title: "Tu ubicación",
        });
        section.appendChild(div);
    }
}

function initMap() {
    const viajes = new Viajes();
}