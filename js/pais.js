class Pais {
    constructor(nombre, capital, circuito) {
        this.nombre = nombre;
        this.capital = capital;
        this.circuito = circuito;
        this.rellenarAtributos(331000000, 'República', '36.1097, -115.1765', 'Cristianismo');
    }

    rellenarAtributos(poblacion, gobierno, meta, religion) {
        this.poblacion = poblacion;
        this.gobierno = gobierno;
        this.meta = meta;
        this.religion =  religion;
    }

    getNombre() {
        return this.nombre + "";
    }

    getCapital() {
        return this.capital + "";
    }

    writeCoordenadasMeta() {
        document.write("<h3>Coordenadas de la meta: " + this.meta + "</h3>");
    }

    getInfoHtml() {
        return `
        <ul>
            <li>Nombre del circuito: ${this.circuito}</li>
            <li>Población: ${this.poblacion}</li>
            <li>Forma de gobierno: ${this.gobierno}</li>
            <li>Religión mayoritaria: ${this.religion}</li>
        </ul>`;
    }

    getTiempoMeta() {
        const [latitud, longitud] = this.meta.split(",").map(c => c.trim());
        var key = "ddb409e093594ac7c5a834e8e2035522";
        var api = `https://api.openweathermap.org/data/2.5/forecast?lat=${latitud}&lon=${longitud}&appid=${key}&mode=xml&units=metric&lang=es`
        $.ajax({
            url: api,
            type: 'GET',
            dataType: 'xml',
            success: function(data) {
                let diasPronostico = {};

                $(data).find("time").each(function() {
                    const fechaArray = $(this).attr("from").split("T")[0].split("-");
                    const fecha = `${fechaArray[2]}-${fechaArray[1]}-${fechaArray[0]}`;
                    const temp = parseFloat($(this).find("temperature").attr("value"));
                    const humedad = $(this).find("humidity").attr("value");
                    const icono = $(this).find("symbol").attr("var");
                    const descripcionIcono = $(this).find("symbol").attr("name");
                    const lluvia = parseFloat($(this).find("precipitation").attr("value") || -1);

                    if (!diasPronostico[fecha]) {
                        diasPronostico[fecha] = { temps: [], humedad, icono, descripcionIcono, lluvia };
                    }
                    diasPronostico[fecha].temps.push(temp);
                });

                let contador = 0;
                for (let fecha in diasPronostico) {
                    if (contador >= 5) break;
                    contador++;
                    const temps = diasPronostico[fecha].temps;
                    const humedad = diasPronostico[fecha].humedad;
                    const icono = diasPronostico[fecha].icono;
                    const descripcionIcono = diasPronostico[fecha].descripcionIcono;
                    var lluvia = diasPronostico[fecha].lluvia;
                    const tempMax = Math.max(...temps);
                    const tempMin = Math.min(...temps);
                    const descripcionClima = descripcionIcono || "Clima desconocido";
                    if(lluvia >= 0) lluvia += " mm";
                    else lluvia = "No hay datos";
                    $("main").append(`
                        <article>
                            <h3>${fecha}</h3>
                            <figure>
                                <img src="https://openweathermap.org/img/wn/${icono}@2x.png" alt="Icono del clima ${contador}">
                                <figcaption>${descripcionClima}</figcaption>
                            </figure>
                            <table>
                                <thead>
                                    <tr>
                                        <th id="tmax${contador}" scope="col">Temp. Máx:</th>
                                        <th id="tmin${contador}" scope="col">Temp. Mín:</th>
                                        <th id="humedad${contador}" scope="col">Humedad:</th>
                                        <th id="lluvia${contador}" scope="col">Lluvia:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td headers="tmax${contador}">${tempMax} °C</td>
                                        <td headers="tmin${contador}">${tempMin} °C</td>
                                        <td headers="humedad${contador}">${humedad}%</td>
                                        <td headers="lluvia${contador}">${lluvia}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </article>
                    `);
                }
            },
            error: function() {
                console.error("Error al obtener los datos meteorológicos en formato XML.");
            }
        });
    }
}