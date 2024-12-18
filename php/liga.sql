-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS liga COLLATE utf8_spanish_ci;

-- Uso de la base de datos
USE liga;

-- Creación de la tabla Coches
CREATE TABLE IF NOT EXISTS Coches (
    coId INT NOT NULL PRIMARY KEY,
    coMotor VARCHAR(32) NOT NULL,
    coNeumaticos VARCHAR(32) NOT NULL,
    CHECK (coMotor IN ('ferrari', 'mercedes', 'honda', 'renault')),
    CHECK (coNeumaticos IN ('blandos', 'medios', 'duros', 'intermedios', 'fullWet'))
);

-- Creación de la tabla Pilotos
CREATE TABLE IF NOT EXISTS Pilotos (
    pId INT NOT NULL PRIMARY KEY,
    pNombre VARCHAR(32) NOT NULL,
    pApellido VARCHAR(32) NOT NULL,
    pNacionalidad VARCHAR(32) NOT NULL,
    coId INT NOT NULL,
    pPuntos INT NOT NULL,
    FOREIGN KEY (coId) REFERENCES Coches(coId)
);

-- Creación de la tabla Escuderias
CREATE TABLE IF NOT EXISTS Escuderias (
    eNombre VARCHAR(32) PRIMARY KEY,
    ePrimerPiloto INT NOT NULL,
    eSegundoPiloto INT NOT NULL,
    ePuntos INT NOT NULL,
    FOREIGN KEY (ePrimerPiloto) REFERENCES Pilotos(pId),
    FOREIGN KEY (eSegundoPiloto) REFERENCES Pilotos(pId),
    UNIQUE (ePrimerPiloto), 
    UNIQUE (eSegundoPiloto)
);

-- Creación de la tabla Carreras
CREATE TABLE IF NOT EXISTS Carreras (
    caId INT NOT NULL PRIMARY KEY,
    caNombre VARCHAR(32) NOT NULL,
    caCircuito VARCHAR(32) NOT NULL,
    caPais VARCHAR(32) NOT NULL,
    caMeteorologia ENUM('Lluvia', 'Calor', 'Frio', 'Diluvio', 'Ambiente')
);

-- Creación de la tabla ResultadosCarreras
CREATE TABLE IF NOT EXISTS ResultadosCarreras (
    caId INT NOT NULL,
    pId INT NOT NULL,
    caPosicion INT NOT NULL,
    caPuntos INT NOT NULL,
    FOREIGN KEY (caId) REFERENCES Carreras(caId),
    FOREIGN KEY (pId) REFERENCES Pilotos(pId),
    PRIMARY KEY (caId, pId),
    UNIQUE (caPosicion, caId)
);
