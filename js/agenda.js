class Agenda {
    constructor() {
        this.url = "https://ergast.com/api/f1/current.json";
    }

    getAgenda() {
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
                    var article = document.createElement("article");
                    var table = document.createElement("table");
                    table.innerHTML = `
                        <caption>${race.raceName}</caption>
                        <thead>
                            <tr>
                                <th id="circuit" scope="col">Circuito</th>
                                <th id="date" scope="col">Fecha y hora</th>
                                <th id="coords" scope="col">Coordenadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td headers="circuit">${race.Circuit.circuitName}</td>
                            <td headers="date">${race.date + " - " + (race.time || "No hay hora")}</td>
                            <td headers="coords">${race.Circuit.Location.lat + ", " + race.Circuit.Location.long}</td>
                            </tr>
                        </tbody>
                        `;
                    article.append(table);
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