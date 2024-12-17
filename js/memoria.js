class Memoria {
    constructor() {
        this.hasFlippedCard = false;
        this.lockBoard = false;
        this.firstCard = null;
        this.secondCard = null;
        this.elements = [
            { element: "alpine", source: "multimedia/imagenes/Alpine_F1_Team_2021_Logo.svg"},
            { element: "alpine", source: "multimedia/imagenes/Alpine_F1_Team_2021_Logo.svg"},
            { element: "aston martin", source: "multimedia/imagenes/Aston_Martin_Aramco_Cognizant_F1.svg"},
            { element: "aston martin", source: "multimedia/imagenes/Aston_Martin_Aramco_Cognizant_F1.svg"},
            { element: "mclaren", source: "multimedia/imagenes/McLaren_Racing_logo.svg"},
            { element: "mclaren", source: "multimedia/imagenes/McLaren_Racing_logo.svg"},
            { element: "mercedes", source: "multimedia/imagenes/Mercedes_AMG_Petronas_F1_Logo.svg"},
            { element: "mercedes", source: "multimedia/imagenes/Mercedes_AMG_Petronas_F1_Logo.svg"},
            { element: "ferrari", source: "multimedia/imagenes/Scuderia_Ferrari_Logo.svg"},
            { element: "ferrari", source: "multimedia/imagenes/Scuderia_Ferrari_Logo.svg"},
            { element: "red bull", source: "multimedia/imagenes/Red_Bull_Racing_logo.svg"},
            { element: "red bull", source: "multimedia/imagenes/Red_Bull_Racing_logo.svg"}
        ];

        this.shuffleElements();
    }

    shuffleElements() {
        this.lockBoard = true;
        this.elements.sort(() => Math.random() - 0.5);
        this.lockBoard = false;
    }

    flipCard(game) {
        if(game.lockBoard || this.dataset.state === "revealed" || (game.firstCard && game.firstCard === this)) return;
        this.dataset.state = "flip";
        this.lastChild.hidden = false;
        if(!game.hasFlippedCard) {
            game.hasFlippedCard = true;
            game.firstCard = this;
        } else {
            game.hasFlippedCard = false;
            game.secondCard = this;
            game.checkForMatch();
        }
    }

    checkForMatch() {
        const isMatch = this.firstCard.dataset.element === this.secondCard.dataset.element;
        if(isMatch) this.disableCards();
        else this.unflipCards();
    }

    disableCards() {
        this.firstCard.dataset.state = 'revealed';
        this.secondCard.dataset.state = 'revealed';
        this.resetBoard();
        this.checkForWin();
    }

    unflipCards() {
        this.lockBoard = true;
        setTimeout(() => {
            if (this.firstCard.dataset.state === 'flip')
                this.firstCard.dataset.state = '';
            if (this.secondCard.dataset.state === 'flip')
                this.secondCard.dataset.state = '';
            this.resetBoard();
        }, 2500);
    }

    resetBoard() {
        this.firstCard = null;
        this.secondCard = null;
        this.hasFlippedCard = false;
        this.lockBoard = false;
    }

    createElements() {
        const main = document.querySelector("main");
        const section = document.createElement("section");
        const h3 = document.createElement("h3");
        h3.textContent = "Juego de memoria";
        section.appendChild(h3);

        this.elements.forEach(e => {
            const card = document.createElement("article");
            const header = document.createElement("h4");
            const img = document.createElement("img");
            card.setAttribute("data-element", e.element);
            header.textContent = "Tarjeta de memoria";
            img.src = e.source;
            img.alt = e.element;
            card.appendChild(header);
            card.appendChild(img);
            section.appendChild(card);
        });
        main.appendChild(section);
    }

    addEventListeners() {
        const cards = document.querySelectorAll('article');  
        cards.forEach(card => {
            card.addEventListener('click', this.flipCard.bind(card, this));
        });
    }

    checkForWin() {
        const revealedCards = document.querySelectorAll('article[data-state="revealed"]');
        if (revealedCards.length === this.elements.length) {
            setTimeout(() => {
                alert('¡Enhorabuena! Has ganado el juego.');
           }, 500); 
        }
    }

    createHelp() {
        const main = document.querySelector("main");
        const section = document.createElement("section");
        const p = document.createElement("p");
        p.textContent = "El objetivo es encontrar y emparejar las cartas que muestran elementos iguales. " +
        "Cada carta oculta una imagen, que podrás ver haciendo click sobre la carta. Memoriza las posiciones de las cartas y descubre " +
        "las coincidencias para completar todos los pares. ¡Pon a prueba tu concentración y diviértete!";
        section.appendChild(p);
        main.appendChild(section);
    }
}