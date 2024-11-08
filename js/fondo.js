class Fondo {
    constructor(pais, capital, circuito) {
        this.pais = pais;
        this.capital = capital;
        this.circuito = circuito;
    }

    consultaFlickr() {
        var flickrAPI = "http://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        var photo = 7;
        $.getJSON(flickrAPI, {
            tags: "f1, car, race",
            tagmode: "all",
            format: "json"
        }).done(function(data) {
            if (data.items && data.items.length > photo) {
                $("body").css("background-image", `url("${data.items[photo].media.m}")`.replace("_m", "_b"));
                $("body").css("background-size", "cover");
                $("body").css("background-repeat", "no-repeat");
            } else {
                console.error("No se encontraron suficientes imágenes en el feed de Flickr.");
            }
        });
    }
}