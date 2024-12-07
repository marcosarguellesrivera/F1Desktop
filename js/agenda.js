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
                var contador = 1;
                races.forEach(function(race) {
                    var article = document.createElement("article");
                    var h4 = document.createElement("h4");
                    h4.textContent = race.raceName;
                    article.appendChild(h4);
                    var table = document.createElement("table");
                    table.innerHTML = `
                        <thead>
                            <tr>
                                <th id="circuit${contador}" scope="col">Circuito</th>
                                <th id="date${contador}" scope="col">Fecha y hora</th>
                                <th id="coords${contador}" scope="col">Coordenadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td headers="circuit${contador}">${race.Circuit.circuitName}</td>
                            <td headers="date${contador}">${race.date + " - " + (race.time || "No hay hora")}</td>
                            <td headers="coords${contador}">${race.Circuit.Location.lat + ", " + race.Circuit.Location.long}</td>
                            </tr>
                        </tbody>
                        `;
                    article.append(table);
                    section.append(article);
                    contador++;
                });
                
                main.append(section);
                }.bind(this),
                    error:function() {
                        const main = document.querySelector("main");
                        const p = document.createElement("p");
                        p.textContent = "No se pudo obtener";
                        main.appendChild(p);
                    }
                });
    }
}