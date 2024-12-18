<?php
    class Liga {
        private $server;
        private $user;
        private $pass;
        private $dbname;
        private $db;
        
        public function __construct() {
            $this->server = "localhost";
            $this->user = "DBUSER2024";
            $this->pass = "DBPSWD2024";
            $this->dbname = "liga";
            $this->createDatabase();
        }

        public function createDatabase() {
            $this->createConection();
            $this->db->query("CREATE DATABASE IF NOT EXISTS " . $this->dbname . " COLLATE utf8_spanish_ci");
            $this->db->select_db($this->dbname);

            $this->createTableCoches();
            $this->createTablePilotos();
            $this->createTableEscuderias();
            $this->createTableCarreras();
            $this->createTableResultadosCarreras();
        }

        public function importDatabase($fileName) {
            $file = fopen($fileName, "r");
            if (!$file) {
                die("<p>Error al abrir el archivo CSV: $fileName</p>");
            }
            while(($line = fgetcsv($file))) {
                switch(strtolower($line[0])) {
                    case "pilotos":
                        $this->insertIntoPilotos(array_slice($line, 1));
                        break;
                    case "coches":
                        $this->insertIntoCoches(array_slice($line, 1));
                        break;
                    case "escuderias":
                        $this->insertIntoEscuderias(array_slice($line, 1));
                        break;
                    case "carreras":
                        $this->insertIntoCarreras(array_slice($line, 1));
                        break;
                    case "resultadoscarreras":
                        $this->insertIntoResultadosCarreras(array_slice($line, 1));
                        break;
                }
            }

            fclose($file);
            return "";
        }

        public function exportData() {
    
            $file = fopen("liga_exportada.csv", "w");
        
            $this->exportTable($file, "coches");
            
            $this->exportTable($file, "pilotos");
            
            $this->exportTable($file, "escuderias");
        
            $this->exportTable($file, "carreras");
        
            $this->exportTable($file, "resultadosCarreras");
        
            fclose($file);
        
        }

        public function getClassificationPilots() {
            $result = $this->db->query("SELECT * FROM Pilotos ORDER BY pPuntos DESC")->fetch_all(MYSQLI_ASSOC);
            $classification = "<table><tr><th id='posicionTablaPilotos'>Posición</th><th id='pilotoTablaPilotos' scope='col'>Piloto</th>" . "
            <th id='escuderiaTablaPilotos' scope='col'>Escudería</th><th id='nacionalidadTablaPilotos' scope='col'>Nacionalidad</th><th id='puntosTablaPilotos' scope='col'>Puntos</th></tr>";
            $i = 1;
            foreach($result as $pilot) {
                $escuderia = $this->getTeamForPilot($pilot['pId']);
                $line = "<tr><td headers='posicionTablaPilotos'>{$i}</td>";
                $line = $line . "<td headers='pilotoTablaPilotos'>{$pilot['pNombre']} {$pilot['pApellido']}</td>";
                $line = $line . "<td headers='escuderiaTablaPilotos'>{$escuderia}</td>";
                $line = $line . "<td headers='nacionalidadTablaPilotos'>{$pilot['pNacionalidad']}</td>";
                $line = $line . "<td headers='puntosTablaPilotos'>{$pilot['pPuntos']}</td></tr>";
                $classification = $classification . $line;
                $i++;
            }
            $classification = $classification . "</table>";
            return $classification;
        }

        public function getClassificationTeams() {
            $result = $this->db->query("SELECT * FROM Escuderias ORDER BY ePuntos DESC")->fetch_all(MYSQLI_ASSOC);
            $classification = "<table><tr><th id='posicionTablaEscuderias'>Posición</th><th id='escuderiaTablaEscuderias' scope='col'>Escudería</th>" . "
            <th id='piloto1TablaEscuderias' scope='col'>Primer piloto</th><th id='piloto2TablaEscuderias' scope='col'>Segundo piloto</th><th id='puntosTablaEscuderias' scope='col'>Puntos</th></tr>";
            $i = 1;
            foreach($result as $team) {
                $pilot1 = $this->getPilotName($team["ePrimerPiloto"]);
                $pilot2 = $this->getPilotName($team["eSegundoPiloto"]);
                $line = "<tr><td headers='posicionTablaEscuderias'>{$i}</td>";
                $line = $line . "<td headers='escuderiaTablaEscuderias'>{$team['eNombre']}</td>";
                $line = $line . "<td headers='piloto1TablaEscuderias'>{$pilot1}</td>";
                $line = $line . "<td headers='piloto2TablaEscuderias'>{$pilot2}</td>";
                $line = $line . "<td headers='puntosTablaEscuderias'>{$team['ePuntos']}</td></tr>";
                $classification = $classification . $line;
                $i++;
            }
            $classification = $classification . "</table>";
            return $classification;
        }

        private function exportTable($file, $nombreTabla) {
            $query = "SELECT * FROM $nombreTabla";
            $result = $this->db->query($query);
            if ($result) {
                $columnas = array();
                while ($columna = $result->fetch_field()) {
                    $columnas[] = $columna->name;
                }
        
                $result->data_seek(0);
        
                while ($fila = $result->fetch_assoc()) {
                    $datosFila = array($nombreTabla);
                    foreach ($fila as $valor) {
                        $datosFila[] = $valor;
                    }
                    fputcsv($file, $datosFila);
                }
            }
        }

        public function getTeamForPilot($pId) {
            $ps = $this->db->prepare("SELECT eNombre FROM Escuderias WHERE ePrimerPiloto = ? OR eSegundoPiloto = ?");
            $data = [$pId, $pId];
            $ps->execute($data);
            $result = $ps->get_result();
            $name = "Sin escudería";
            if($result->num_rows > 0 && $row = $result->fetch_assoc()) {
                $name = $row['eNombre'];
            }
            $ps->close();
            return $name;
        }

        public function getFreeCars() {
            $result = $this->db->query("SELECT * FROM Coches");
            $result2 = $this->db->query("SELECT coId FROM Pilotos");
            $occupiedCars = [];
            while($occupiedCar = $result2->fetch_assoc()) {
                $occupiedCars[] = $occupiedCar['coId'];
            }
            $freeCars = [];
            while($row = $result->fetch_assoc()) {
                $coId = $row['coId'];
                if(!in_array($coId, $occupiedCars)) {
                    $car = [$coId, $row['coMotor'], $row['coNeumaticos']];
                    $freeCars[] = $car;
                }
            }
            return $freeCars;
        }

        public function showFreeCars() {
            $freeCars = $this->getFreeCars();
            $options = "";
            foreach($freeCars as $car) {
                $options = $options . "<option value='{$car[0]}'>Motor: {$car[1]} - Neumáticos: {$car[2]}</option>";
            }
            return $options;
        }

        public function getFreePilots() {
            $result = $this->db->query("SELECT * FROM Pilotos");
            $freePilots = [];
            while($row = $result->fetch_assoc()) {
                $pId = $row['pId'];
                if($this->getTeamForPilot($pId) === "Sin escudería") {
                    $pilot = [$pId, $row['pNombre'], $row['pApellido']];
                    $freePilots[] = $pilot;
                }
            }
            return $freePilots;
        }

        public function showFreePilots() {
            $freePilots = $this->getFreePilots();
            $options = "";
            foreach($freePilots as $pilot) {
                $options = $options . "<option value='{$pilot[0]}'>{$pilot[1]} {$pilot[2]}</option>";
            }
            return $options;
        }

        public function showAllTeams() {
            $result = $this->db->query("SELECT eNombre FROM Escuderias");
            $options = "";
            while($row = $result->fetch_assoc()) {
                $name = $row["eNombre"];
                $options = $options . "<option value='{$name}'>{$name}</option>";
            }
            return $options;
        }

        public function getPilotName($pId) {
            $ps = $this->db->prepare("SELECT pNombre, pApellido FROM Pilotos WHERE pId = ?");
            $ps->bind_param("i", $pId);
            $ps->execute();
            $result = $ps->get_result();
            $name = "";
            if($result->num_rows > 0 && $row = $result->fetch_assoc()) {
                $name = $row['pNombre'];
                $name = $name . " " . $row["pApellido"];
            }
            $ps->close();
            return $name;
        }

        public function addPilot($name, $surname, $country, $car) {
            $id = $this->getLastPilotId() + 1;
            $ps = $this->db->prepare("INSERT INTO Pilotos (pId, pNombre, pApellido, pNacionalidad, coId, pPuntos) VALUES (?, ?, ?, ?, ?, 0)");
            $ps->bind_param("isssi", $id, $name, $surname, $country, $car);
            $ps->execute();
            $ps->close();
        }

        public function addCar($engine, $tires) {
            $id = $this->getLastCarId() + 1;
            $ps = $this->db->prepare("INSERT INTO Coches (coId, coMotor, coNeumaticos) VALUES (?, ?, ?)");
            $ps->bind_param("iss", $id, $engine, $tires);
            $ps->execute();
            $ps->close();
        }

        public function addTeam($name, $pilot1, $pilot2) {
            $points = $this->getPointsForPilot($pilot1) + $this->getPointsForPilot($pilot2);
            $ps = $this->db->prepare("INSERT IGNORE INTO Escuderias (eNombre, ePrimerPiloto, eSegundoPiloto, ePuntos) VALUES (?, ?, ?, ?)");
            $ps->bind_param("siii", $name, $pilot1, $pilot2, $points);
            $ps->execute();
            $ps->close();
        }

        public function addRace($name, $circuit, $country, $weather) {
            $id = $this->getLastRaceId() + 1;
            $ps = $this->db->prepare("INSERT INTO Carreras (caId, caNombre, caCircuito, caPais, caMeteorologia) VALUES (?, ?, ?, ?, ?)");
            $ps->bind_param("issss", $id, $name, $circuit, $country, $weather);
            $ps->execute();
            $ps->close();
        }

        public function removePilot($pId) {
            $ps = $this->db->prepare("DELETE FROM Pilotos WHERE pId = ?");
            $ps->bind_param("i", $pId);
            $ps->execute();
            $ps->close();
        }

        public function removeCar($coId) {
            $ps = $this->db->prepare("DELETE FROM Coches WHERE coId = ?");
            $ps->bind_param("i", $coId);
            $ps->execute();
            $ps->close();
        }

        public function removeTeam($name) {
            $ps = $this->db->prepare("DELETE FROM Escuderias WHERE eNombre = ?");
            $ps->bind_param("s", $name);
            $ps->execute();
            $ps->close();
        }

        public function getLastPilotId() {
            $result = $this->db->query("SELECT MAX(pId) AS max_pId FROM Pilotos");
            if($row = $result->fetch_assoc()){
                return $row["max_pId"];
            }
            return 0;
        }

        public function getLastCarId() {
            $result = $this->db->query("SELECT MAX(coId) AS max_coId FROM Coches");
            if($row = $result->fetch_assoc()){
                return $row["max_coId"];
            }
            return 0;
        }

        public function getLastRaceId() {
            $result = $this->db->query("SELECT MAX(caId) AS max_caId FROM Carreras");
            if($row = $result->fetch_assoc()){
                return $row["max_caId"];
            }
            return 0;
        }

        public function getPointsForPilot($pId) {
            $ps = $this->db->prepare("SELECT pPuntos FROM Pilotos WHERE pId = ?");
            $ps->bind_param("i", $pId);
            $ps->execute();
            $result = $ps->get_result();
            $points = 0;
            if($row = $result->fetch_assoc()) {
                $points = $row["pPuntos"];
            }
            $ps->close();
            return $points;
        }

        public function insertIntoPilotos($data) {
            $ps = $this->db->prepare("INSERT IGNORE INTO Pilotos (pId, pNombre, pApellido, pNacionalidad, coId, pPuntos) VALUES (?, ?, ?, ?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoCoches($data) {
            $ps = $this->db->prepare("INSERT IGNORE INTO Coches (coId, coMotor, coNeumaticos) VALUES (?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoEscuderias($data) {
            $ps = $this->db->prepare("INSERT IGNORE INTO Escuderias (eNombre, ePrimerPiloto, eSegundoPiloto, ePuntos) VALUES (?, ?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoCarreras($data) {
            $ps = $this->db->prepare("INSERT IGNORE INTO Carreras (caId, caNombre, caCircuito, caPais, caMeteorologia) VALUES (?, ?, ?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoResultadosCarreras($data) {
            $ps = $this->db->prepare("INSERT IGNORE INTO ResultadosCarreras (caId, pId, caPosicion, caPuntos) VALUES (?, ?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function createTablePilotos() {
            $this->db->query("CREATE TABLE IF NOT EXISTS Pilotos (
                pId INT NOT NULL PRIMARY KEY,
                pNombre VARCHAR(32) NOT NULL,
                pApellido VARCHAR(32) NOT NULL,
                pNacionalidad VARCHAR(32) NOT NULL,
                coId INT NOT NULL,
                pPuntos INT NOT NULL,
                FOREIGN KEY (coId) REFERENCES Coches(coId));");
        }
        public function createTableCoches() {
            $this->db->query("CREATE TABLE IF NOT EXISTS Coches (
                coId INT NOT NULL PRIMARY KEY,
                coMotor VARCHAR(32) NOT NULL,
                coNeumaticos VARCHAR(32) NOT NULL,
                CHECK (coMotor IN ('ferrari', 'mercedes', 'honda', 'renault')),
                CHECK (coNeumaticos IN ('blandos', 'medios', 'duros', 'intermedios', 'fullWet')));");
        }

        public function createTableEscuderias() {
            $this->db->query("CREATE TABLE IF NOT EXISTS Escuderias (
                eNombre VARCHAR(32) PRIMARY KEY,
                ePrimerPiloto INT NOT NULL,
                eSegundoPiloto INT NOT NULL,
                ePuntos INT NOT NULL,
                FOREIGN KEY (ePrimerPiloto) REFERENCES Pilotos(pId),
                FOREIGN KEY (eSegundoPiloto) REFERENCES Pilotos(pId),
                UNIQUE (ePrimerPiloto), 
                UNIQUE (eSegundoPiloto));");
        }

        public function createTableCarreras() {
            $this->db->query("CREATE TABLE IF NOT EXISTS Carreras (
                caId INT NOT NULL PRIMARY KEY,
                caNombre VARCHAR(32) NOT NULL,
                caCircuito VARCHAR(32) NOT NULL,
                caPais VARCHAR(32) NOT NULL,
                caMeteorologia ENUM('lluvia', 'calor', 'frio', 'lluvia intensa', 'ambiente'));");
        }

        public function createTableResultadosCarreras() {
            $this->db->query("CREATE TABLE IF NOT EXISTS ResultadosCarreras (
                caId INT NOT NULL,
                pId INT NOT NULL,
                caPosicion INT NOT NULL,
                caPuntos INT NOT NULL,
                FOREIGN KEY (caId) REFERENCES Carreras(caId),
                FOREIGN KEY (pId) REFERENCES Pilotos(pId),
                PRIMARY KEY (caId, pId),
                UNIQUE (caPosicion, caId));");
        }

        public function createConection() {
            $this->db = new mysqli($this->server, $this->user, $this->pass);
    
            // Verificar si hubo error en la conexión
            if ($this->db->connect_error) {
                die("Error de conexión a MySQL: " . $this->db->connect_error);
            }
        }
    }

    $liga = new Liga();
    $cars = $liga->showFreeCars();
    $freePilots = $liga->showFreePilots();
    $teams = $liga->showAllTeams();
    $pilotsTable = "";
    $teamsTable = "";
    if(count($_POST) > 0) {
        if(isset($_POST['importDb'])) {
            if(isset($_FILES["csvDb"])) {
                $fileName = $_FILES["csvDb"]["name"];
                $liga->importDatabase($fileName);
            }
        }
        if(isset($_POST['classificationPilots'])) {
            $pilotsTable = $liga->getClassificationPilots();
        }
        if(isset($_POST["classificationTeams"])) {
            $teamsTable = $liga->getClassificationTeams();
        }
        if(isset($_POST['exportDb'])) {
            $liga->exportData();
        }
        if(isset($_POST['addPilot'])) {
            $pilotName = $_POST['pilotName'];
            $pilotSurname = $_POST['pilotSurname'];
            $pilotCountry = $_POST['pilotCountry'];
            $pilotCar = $_POST['pilotCar'];
            $liga->addPilot($pilotName, $pilotSurname, $pilotCountry, $pilotCar);
            $cars = $liga->showFreeCars();
            $freePilots = $liga->showFreePilots();
        }
        if(isset($_POST['addCar'])) {
            $carEngine = $_POST['carEngine'];
            $carTires = $_POST['carTires'];
            $liga->addCar($carEngine, $carTires);
            $cars = $liga->showFreeCars();
        }
        if(isset($_POST['addTeam'])) {
            $teamName = $_POST["teamName"];
            $teamPilot1 = $_POST["teamPilot1"];
            $teamPilot2 = $_POST["teamPilot2"];
            if($teamPilot1 !== $teamPilot2) $liga->addTeam($teamName, $teamPilot1, $teamPilot2);
            $freePilots = $liga->showFreePilots();
        }
        if(isset($_POST['addRace'])) {
            $raceName = $_POST["raceName"];
            $circuit = $_POST["circuitName"];
            $raceCountry = $_POST["raceCountry"];
            $raceWeather = $_POST["raceWeather"];
            $liga->addRace($raceName, $circuit, $raceCountry, $raceWeather);
        }
        if(isset($_POST['removePilot'])) {
            $pId = $_POST['pilotsToRemove'];
            $liga->removePilot($pId);
            $freePilots = $liga->showFreePilots();
        }
        if(isset($_POST['removeCar'])) {
            $coId = $_POST['carsToRemove'];
            $liga->removeCar($coId);
            $cars = $liga->showFreeCars();
        }
        if(isset($_POST['removeTeam'])) {
            $name = $_POST['teamsToRemove'];
            $liga->removeTeam($name);
            $freePilots = $liga->showFreePilots();
        }
    }
    echo "<html lang='es'>
            <head>
            <!-- Datos que describen el documento -->
            <meta charset='UTF-8' />
            <meta name='author' content='Marcos Argüelles Rivera'/>
            <meta name='description' content='Liga de carreras de F1Desktop'/>
            <meta name='keywords' content='Liga, carrera, F1'/>
            <meta name='viewport' content='width=device-width,initial-scale=1.0'/>
            <title>F1 Desktop: Liga de Carreras</title>
            <link rel='icon' type='image/icon' href='../multimedia/imagenes/f1.png'>
            <link rel='stylesheet' type='text/css' href='../estilo/estilo.css' />
            <link rel='stylesheet' type='text/css' href='../estilo/layout.css'/>
        </head>
        <body>
            
        <!-- Datos con el contenidos que aparece en el navegador -->
        <header>
            <h1><a href='../index.html'>F1 Desktop</a></h1>
            <nav>
                <a href='../index.html'>Inicio</a>
                <a href='../calendario.html'>Calendario</a>
                <a href='../circuito.html'>Circuito</a>
                <a class='active' href='../juegos.html'>Juegos</a>
                <a href='../meteorologia.html'>Meteorología</a>
                <a href='../noticias.html'>Noticias</a>
                <a href='../piloto.html'>Piloto</a>
                <a href='../viajes.html'>Viajes</a>
                </nav>
            </header>
            <p><a href='../index.html'>F1 Desktop</a> >> <a href='../juegos.html'>Juegos</a> >> Liga de carreras</p>
            <main>
                <nav>
                    <a href='../memoria.html'>Memoria</a>
                    <a href='liga.php'>Liga</a>
                    <a href='../api.html'>API</a>
                    <a href='../semaforo.php'>Semáforo</a>
                </nav>
                <h2>Liga de carreras</h2>
                <section>
                    <h3>Clasificación pilotos</h3>
                    <form action='#' method='post' name='classificationPilots'>
                        <input type='submit' name='classificationPilots' value='Cargar clasificación'>
                    </form>
                    $pilotsTable
                </section>
                <section>
                    <h3>Clasificación escuderías</h3>
                    <form action='#' method='post' name='classificationTeams'>
                        <input type='submit' name='classificationTeams' value='Cargar clasificación'>
                    </form>
                    $teamsTable
                </section>
                <section>
                    <h3>Importar base de datos</h3>
                    <form action='#' method='post' name='import' enctype='multipart/form-data'>
                        <label for='csvInput'>Selecciona un archivo csv </label>
                        <input type='file' id='csvInput' name='csvDb' accept='.csv' required>
                        <input type='submit' name='importDb' value='Importar'>
                    </form>
                </section>
                <section>
                    <h3>Exportar base de datos</h3>
                    <form action='#' method='post' name='export'>
                        <input type='submit' name='exportDb' value='Exportar'>
                    </form>
                </section>
                <section>
                    <h3>Añadir piloto</h3>
                    <form method='post' action='#' name='addPilot' enctype='multipart/form-data'>
                        <label for='pilotName'>Nombre: </label>
                        <input type='text' id='pilotName' name='pilotName' required>
                        <label for='pilotSurname'>Apellido: </label>
                        <input type='text' id='pilotSurname' name='pilotSurname' required>
                        <label for='pilotCountry'>País: </label>
                        <input type='text' id='pilotCountry' name='pilotCountry' required>
                        <label for='pilotCar'>Coche: </label>
                        <select id='pilotCar' name='pilotCar' required>
                            <option value=''>Selecciona un coche</option>
                            $cars
                        </select>
                        <input type='submit' name='addPilot' value='Añadir'>
                    </form>
                </section>
                <section>
                    <h3>Añadir coche</h3>
                    <form method='post' action='#' name='addCar' enctype='multipart/form-data'>
                        <label for='carEngine'>Motor: </label>
                        <select id='carEngine' name='carEngine' required>
                            <option value=''>Selecciona un motor</option>
                            <option value='ferrari'>Ferrari</option>
                            <option value='mercedes'>Mercedes</option>
                            <option value='honda'>Honda</option>
                            <option value='renault'>Renault</option>
                        </select>
                        <label for='carTires'>Neumáticos: </label>
                        <select id='carTires' name='carTires' required>
                            <option value=''>Selecciona unos neumáticos</option>
                            <option value='blandos'>Pirelli blandos</option>
                            <option value='medios'>Pirelli medios</option>
                            <option value='duros'>Pirelli duros</option>
                            <option value='inter'>Pirelli intermedios</option>
                            <option value='fullWet'>Pirelli de lluvia intensa</option>
                        </select>
                        <input type='submit' name='addCar' value='Añadir'>
                    </form>
                </section>
                <section>
                    <h3>Añadir escudería</h3>
                    <form method='post' action='#' name='addTeam' enctype='multipart/form-data'>
                        <label for='teamName'>Nombre: </label>
                        <input type='text' id='teamName' name='teamName' required>
                        <label for='teamPilot1'>Primer piloto: </label>
                        <select id='teamPilot1' name='teamPilot1' required>
                            <option value=''>Selecciona un piloto</option>
                            $freePilots
                        </select>
                        <label for='teamPilot2'>Segundo piloto: </label>
                        <select id='teamPilot2' name='teamPilot2' required>
                            <option value=''>Selecciona un piloto</option>
                            $freePilots
                        </select>
                        <input type='submit' name='addTeam' value='Añadir'>
                    </form>
                </section>
                <section>
                    <h3>Añadir carrera</h3>
                    <form method='post' action='#' name='addRace' enctype='multipart/form-data'>
                        <label for='raceName'>Nombre: </label>
                        <input type='text' id='raceName' name='raceName' required>
                        <label for='circuitName'>Nombre del circuito: </label>
                        <input type='text' id='circuitName' name='circuitName' required>
                        <label for='raceCountry'>País: </label>
                        <input type='text' id='raceCountry' name='raceCountry' required>
                        <label for='raceWeather'>Tiempo meteorológico: </label>
                        <select id='raceWeather' name='raceWeather' required>
                            <option value=''>Selecciona un tiempo</option>
                            <option value='calor'>Calor</option>
                            <option value='frio'>Frío</option>
                            <option value='ambiente'>Temperatura ambiente</option>
                            <option value='lluvia'>Lluvia</option>
                            <option value='lluvia intensa'>Lluvia intensa</option>
                        </select>
                        <input type='submit' name='addRace' value='Añadir'>
                    </form>
                </section>
                <section>
                    <h3>Eliminar</h3>
                    <form method='post' action='#' name='removePilot' enctype='multipart/form-data'>
                        <label for='pilotsToRemove'>Piloto a eliminar: </label>
                        <select id='pilotsToRemove' name='pilotsToRemove' required>
                            <option value=''>Selecciona un piloto</option>
                            $freePilots
                        </select>
                        <input type='submit' name='removePilot' value='Eliminar'>
                    </form>
                    <form method='post' action='#' name='removeCar' enctype='multipart/form-data'>
                        <label for='carsToRemove'>Coche a eliminar: </label>
                        <select id='carsToRemove' name='carsToRemove' required>
                            <option value=''>Selecciona un coche</option>
                            $cars
                        </select>
                        <input type='submit' name='removeCar' value='Eliminar'>
                    </form>
                    <form method='post' action='#' name='removeTeam' enctype='multipart/form-data'>
                        <label for='teamsToRemove'>Escudería a eliminar: </label>
                        <select id='teamsToRemove' name='teamsToRemove' required>
                            <option value=''>Selecciona una escudería</option>
                            $teams
                        </select>
                        <input type='submit' name='removeTeam' value='Eliminar'>
                    </form>
                </section>
            </main>
        </body>
        </html>";
?>