class Pais{
    constructor(nombre, capital, circuito, poblacion, gobierno, meta){

    }
    obtenerInformacionSecundaria() {
        return `
            <ul>
                <li>Nombre del circuito: ${this.circuito}</li>
                <li>Población: ${this.poblacion}</li>
                <li>Forma de gobierno: ${this.formaGobierno}</li>
                <li>Religión mayoritaria: ${this.religionMayoritaria}</li>
            </ul>
        `;
    }
}