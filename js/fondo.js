class Fondo {
    constructor(pais, capital, circuito) {
        this.pais = pais;
        this.capital = capital;
        this.circuito = circuito;
    }

    consultaFlickr() {
        var flickrAPI = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        var photo = 1;
        $.getJSON(flickrAPI, {
            tags: this.circuito + ", F1",
            tagmode: "all",
            format: "json"
        }).done(function(data) {
            if (data.items.length > photo) {
                $("body").css("background-image", `url("${data.items[photo].media.m}")`.replace("_m", "_b"));
                $("body").css("background-size", "cover");
                $("body").css("background-repeat", "no-repeat");
            } else {
                console.error("No se encontraron suficientes imágenes en el feed de Flickr.");
            }
        });
    }
}