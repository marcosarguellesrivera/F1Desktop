class Juego {

    constructor(){
        this.crearEstructura();
    }

    crearEstructura() {
        const main = document.querySelector("main");
        const section = document.createElement("section");
        const h3 = document.createElement("h3");
        h3.textContent = "Relaciona";
        const button = document.createElement("button");
        button.textContent = "Expandir";
        button.addEventListener("click", function() {
            var sectionToExpand = document.querySelector("section");
            if(document.webkitFullscreenElement) {
                document.webkitCancelFullScreen();
                this.textContent = "Expandir";
            }
            else {
                sectionToExpand.webkitRequestFullScreen();
                this.textContent = "Comprimir";
            };
        });
        section.appendChild(h3);
        section.appendChild(button);
        main.appendChild(section);
    }
}