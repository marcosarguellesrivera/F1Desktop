class Juego {

    constructor() {
        this.fotos = [
            {
                element: "Max Verstappen",
                source: "multimedia/imagenes/max_verstappen.png"
            },
            {
                element: "Checo Perez",
                source: "multimedia/imagenes/checo_perez.png"
            },
            {
                element: "Charles Leclerc",
                source: "multimedia/imagenes/charles_leclerc.png"
            },
            {
                element: "Carlos Sainz Jr",
                source: "multimedia/imagenes/carlos_sainz.png"
            },
            {
                element: "Fernando Alonso",
                source: "multimedia/imagenes/fernando_alonso.png"
            },
            {
                element: "Lance Stroll",
                source: "multimedia/imagenes/lance_stroll_tarjeta.png"
            },
        ];
        this.nombres = [
            {
                element: "Max Verstappen"
            },
            {
                element: "Checo Perez"
            },
            {
                element: "Charles Leclerc"
            },
            {
                element: "Carlos Sainz Jr"
            },
            {
                element: "Fernando Alonso"
            },
            {
                element: "Lance Stroll"
            },
        ];
        this.crearEstructura();
    }

    crearEstructura() {
        const main = document.querySelector("main");
        const section = document.createElement("section");
        const h3 = document.createElement("h3");
        h3.textContent = "Relaciona";
        const button = document.createElement("button");
        button.textContent = "Expandir";
        section.appendChild(h3);
        this.crearTarjetas(section);
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
        section.appendChild(button);
        main.appendChild(section);
    }

    crearTarjetas(section) {
        this.fotos.forEach(e => {
            const card = document.createElement("article");
            const header = document.createElement("h4");
            const img = document.createElement("img");
            card.setAttribute("data-element", e.element);
            header.textContent = "Piloto";
            img.src = e.source;
            img.alt = e.element;
            img.classList.add("card-image");
            card.appendChild(header);
            card.appendChild(img);
            section.appendChild(card);
        });
    }
}