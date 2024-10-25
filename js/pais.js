class Pais {
    constructor(nombre, capital, circuito, poblacion, gobierno, meta, religion) {
        this.nombre = nombre;
        this.capital = capital;
        this.circuito = circuito;
        this.poblacion = poblacion;
        this.gobierno = gobierno;
        this.meta = meta;
        this.religion =  religion;
    }

    actualizarAtributos({nombre, capital, circuito, poblacion, gobierno, meta, religion}) {
        if(nombre) this.nombre = nombre;
        if(capital) this.capital = capital;
        if(circuito) this.circuito = circuito;
        if(poblacion) this.poblacion = poblacion;
        if(gobierno) this.gobierno = gobierno;
        if(meta) this.meta = meta;
        if(religion) this.religion = religion;
    }

    getNombre() {
        return `País: ${this.nombre}`;
    }

    getCapital() {
        return `Capital: ${this.capital}`;
    }

    writeCoordenadasMeta() {
        document.write(`Coordenadas de la meta: ${this.meta}`);
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