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
        this.p = document.createElement("p");
        this.p.textContent = "Tiempo de reacción: -";
        section.appendChild(buttonStop);
        main.appendChild(section);
        main.appendChild(this.p);
    }

    initSequence() {
        const main = document.querySelector("main");
        main.classList.add("load");
        const button = document.querySelector(".start");
        button.disabled = true;
        setTimeout(() =>{
            this.unload_moment = new Date();
            this.endSequence();
        }, this.difficulty * 100 + 2000);
    }
    
    endSequence() {
        const main = document.querySelector("main");
        main.classList.remove("load");
        main.classList.add("unload");
        const button = document.querySelector(".stop");
        button.disabled = false;
    }

    stopReaction() {
        this.clic_moment = new Date();
        const time = ((this.clic_moment - this.unload_moment) / 1000).toFixed(3);
        this.p.textContent = "Tiempo de reacción: " + time + "s";
        const main = document.querySelector("main");
        const start = document.querySelector(".start");
        start.disabled = false;
        const stop = document.querySelector(".stop");
        stop.disabled = true;
        main.classList.remove("unload");
        this.createRecordForm(time);
    }

    createRecordForm(time) {
        const main = document.querySelector("main");
        var section = document.querySelector("section");
        if(section) main.removeChild(section);
        section = document.createElement("section");
        const h3 = document.createElement("h3");
        h3.textContent = "Guardar resultados";
        const form = document.createElement("form");
        form.action = "#";
        form.method = "post";
        const nameLabel = document.createElement("label");
        nameLabel.textContent = "Nombre: ";
        form.appendChild(nameLabel);
        const nameInput = document.createElement("input");
        nameInput.type = "text";
        nameInput.name =  "nombre";
        form.appendChild(nameInput);
        const surnameLabel = document.createElement("label");
        surnameLabel.textContent = "Apellidos: ";
        form.appendChild(surnameLabel);
        const surnameInput = document.createElement("input");
        surnameInput.type = "text";
        surnameInput.name = "apellido";
        form.appendChild(surnameInput);
        const levelLabel = document.createElement("label");
        levelLabel.textContent = "Nivel: ";
        form.appendChild(levelLabel);
        const levelInput = document.createElement("input");
        levelInput.type = "text";
        levelInput.name = "nivel";
        levelInput.value = this.difficulty;
        levelInput.readOnly = true;
        form.appendChild(levelInput);
        const timeLabel = document.createElement("label");
        timeLabel.textContent = "Tiempo de reacción: ";
        form.appendChild(timeLabel);
        const timeInput = document.createElement("input");
        timeInput.type = "text";
        timeInput.name = "nivel";
        timeInput.value = time + " s";
        timeInput.readOnly = true;
        form.appendChild(timeInput);
        section.appendChild(h3);
        section.appendChild(form);
        main.appendChild(section);
    }
}