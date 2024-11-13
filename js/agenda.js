class Agenda {
    constructor() {
        this.url = "https://ergast.com/api/f1/current.json";
    }

    getAgenda() {
        alert("Llamado");
        $.ajax({
            url: this.url,
            method: 'GET',
            dataType: "json",
            success: function(data) {
                const races = data.MRData.RaceTable.Races;
                const season = data.MRData.RaceTable.season;
                const section = document.createElement("section");
                const h3 = document.createElement("h3");
                h3.textContent = "Temporada " + season;
                section.appendChild(h3);
                const main = document.querySelector("main");
                const firstSection = document.querySelector("section");
                if (firstSection) main.removeChild(firstSection);
                races.forEach(function(race) {
                    var h4 = document.createElement("h4");
                    h4.textContent = race.raceName;
                    var ul = document.createElement("ul");
                    var circuit = document.createElement("li");
                    circuit.textContent = "Circuito: " + race.Circuit.circuitName;
                    var date = document.createElement("li");
                    date.textContent = "Fecha y hora: " + race.date + " - " + (race.time || "No hay hora");
                    var coords = document.createElement("li");
                    coords.textContent = "Coordenadas: " + race.Circuit.Location.lat + ", " + race.Circuit.Location.long;
                    ul.append(circuit);
                    ul.append(date);
                    ul.append(coords);
                    var article = document.createElement("article");
                    article.append(h4);
                    article.append(ul);
                    section.append(article);
                });
                
                main.append(section);
                }.bind(this),
                    error:function() {
                        $("h2").html("¡Tenemos problemas! No se pudo obtener JSON"); 
                    }
                });
    }
}