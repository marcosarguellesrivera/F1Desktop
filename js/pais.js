class Pais {
    constructor(nombre, capital, circuito) {
        this.nombre = nombre;
        this.capital = capital;
        this.circuito = circuito;
        this.rellenarAtributos(331000000, 'República', '36.1097º N, 115.1765º W', 'Cristianismo');
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
}