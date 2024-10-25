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
    }

    shuffleElements() {
        this.lockBoard = true;
        this.elements.sort(() => Math.random - 0.5);
        this.lockBoard = false;
    }

    flipCard(card) {
        if(this.lockBoard || card === this.firstCard) return;
        this.lockBoard = true;
        if(!this.hasFlippedCard) {
            this.hasFlippedCard = true;
            this.firstCard = card;
        } else {
            this.secondCard = card;
            this.checkForMatch();
        }
        this.lockBoard = false;
    }

    checkForMatch() {
        const isMatch = this.firstCard.dataset.element === this.secondCard.dataset.element;
        if(!isMatch) this.unflip();
    }

    disableCards() {
        this.firstCard.setAttribute("data-state", "revealed");
        this.secondCard.setAttribute("data-state", "revealed");
        this.resetBoard();
    }

    unflipCards() {
        this.lockBoard = true;
        setTimeout(() => {
            this.firstCard.setAttribute("data-state", "");
            this.secondCard.setAttribute("data-state", "");
        }, 2000);
        this.resetBoard();
    }

    resetBoard() {
        this.firstCard = null;
        this.secondCard = null;
        this.hasFlippedCard = false;
        this.lockBoard = false;
    }
}