class Semaforo {
    constructor() {
        this.levels = [0.2, 0.5, 0.8];
        this.lights = 4;
        this.unload_moment = null;
        this.clic_moment = null;
        this.difficulty = this.levels[Math.floor(Math.random() * this.levels.length)];
        this.createStructure();
    }

    createStructure() {
        const main = document.querySelector("main");
        const h2 = document.createElement("h2");
        h2.textContent = "Semáforo";
        main.appendChild(h2);
        const section = document.createElement("section");
        section.classList.add("container");
        for(let i = 0; i < this.lights; i++) {
            var div = document.createElement("div");
            section.appendChild(div);
        }
        var buttonStart = document.createElement("button");
        buttonStart.textContent = "Arranque";
        buttonStart.classList.add("start");
        buttonStart.addEventListener("click", this.initSequence.bind(this));
        section.appendChild(buttonStart);
        var buttonStop = document.createElement("button");
        buttonStop.textContent = "Reacción";
        buttonStop.classList.add("stop");
        buttonStop.addEventListener("click", this.stopReaction.bind(this));
        buttonStop.disabled = true;
        section.appendChild(buttonStop);
        main.appendChild(section);
    }

    initSequence() {
        const section = document.querySelector(".container");
        section.classList.add("load");
        const button = document.querySelector(".start");
        button.disabled = true;
        setTimeout(() =>{
            this.unload_moment = new Date();
            this.endSequence();
        }, this.difficulty * 100 + 2000);
    }
    
    endSequence() {
        const section = document.querySelector(".container");
        section.classList.remove("load");
        section.classList.add("unload");
        const button = document.querySelector(".stop");
        button.disabled = false;
    }

    stopReaction() {
        this.clic_moment = new Date();
        const time = ((this.clic_moment - this.unload_moment) / 1000).toFixed(3);
        const p = document.createElement("p");
        p.textContent = time + "s";
        const main = document.querySelector("main");
        main.appendChild(p);
        const start = document.querySelector(".start");
        start.disabled = false;
        const stop = document.querySelector(".stop");
        stop.disabled = true;
        const section = document.querySelector(".container");
        section.classList.remove("unload");
    }
}