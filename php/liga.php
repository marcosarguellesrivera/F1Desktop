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
                    default:
                        return "<p>Fallo al importar</p>";
                }
            }

            fclose($file);
            return "";
        }

        public function exportarDatos() {
    
            $file = fopen("liga_exportada.csv", "w");
        
            $this->exportarTabla($file, "pilotos");
        
            $this->exportarTabla($file, "coches");
            
            $this->exportarTabla($file, "escuderias");
        
            $this->exportarTabla($file, "carreras");
        
            $this->exportarTabla($file, "resultadosCarreras");
        
            fclose($file);
        
        }

        public function getClassificationPilotos() {
            $result = $this->db->query("SELECT * FROM Pilotos ORDER BY pPuntos DESC")->fetch_all(MYSQLI_ASSOC);
            $classification = "<table><tr><th id='pilotoTablaPilotos' scope='col'>Piloto</th><th id='escuderiaTablaPilotos' scope='col'>Escudería</th>" . "
            <th id='nacionalidadTablaPilotos' scope='col'>Nacionalidad</th><th id='puntosTablaPilotos' scope='col'>Puntos</th></tr>";
            foreach($result as $pilot) {
                $line = "<tr><td headers='pilotoTablaPilotos'>{$pilot['pNombre']} {$pilot['pApellido']}</td>";
                $line = $line . "<td headers='escuderiaTablaPilotos>'Por hacer'</td>";
                $line = $line . "<td headers='nacionalidadTablaPilotos>{$pilot['pNacionalidad']}</td>";
                $line = $line . "<td headers='puntosTablaPilotos'>{$pilot['pPuntos']}</td></tr>";
                $classification = $classification . $line;
            }
            $classification = $classification . "</table>";
            return $classification;
        }

        private function exportarTabla($file, $nombreTabla) {
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

        public function insertIntoPilotos($data) {
            $ps = $this->db->prepare("INSERT INTO Pilotos (pId, pNombre, pApellido, pNacionalidad, coId, pPuntos) VALUES (?, ?, ?, ?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoCoches($data) {
            $ps = $this->db->prepare("INSERT INTO Coches (coId, coMotor, coNeumaticos) VALUES (?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoEscuderias($data) {
            $ps = $this->db->prepare("INSERT INTO Escuderias (eNombre, ePrimerPiloto, eSegundoPiloto, ePuntos) VALUES (?, ?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoCarreras($data) {
            $ps = $this->db->prepare("INSERT INTO Carreras (caId,caNombre, caCircuito, caPais, caMeteorologia) VALUES (?, ?, ?, ?)");
            $ps->execute($data);
            $ps->close();
        }

        public function insertIntoResultadosCarreras($data) {
            $ps = $this->db->prepare("INSERT INTO ResultadosCarreras (caId, pId, caPosicion, caPuntos) VALUES (?, ?, ?, ?)");
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
                FOREIGN KEY (eSegundoPiloto) REFERENCES Pilotos(pId));");
        }

        public function createTableCarreras() {
            $this->db->query("CREATE TABLE IF NOT EXISTS Carreras (
                caId INT NOT NULL PRIMARY KEY,
                caNombre VARCHAR(32) NOT NULL,
                caCircuito VARCHAR(32) NOT NULL,
                caPais VARCHAR(32) NOT NULL,
                caMeteorologia ENUM('Lluvia', 'Calor', 'Frio', 'Diluvio', 'Ambiente'));");
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
    $table = "";
    $mensaje = "";
    if(count($_POST) > 0) {
        if(isset($_POST['importDb'])) {            
            if(isset($_FILES["csvDb"])) {
                $fileName = $_FILES["csvDb"]["name"];
                $mensaje = $liga->importDatabase($fileName);
            } else {
                $mensaje = "<p>no entra</p>";
            }
        }
        if(isset($_POST['classification'])) {
            $table = $liga->getClassificationPilotos();
        }
        if(isset($_POST['exportDb'])) {
            $liga->exportarDatos();
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
                    <h3>Clasificación</h3>
                    <form action='#' method='post' name='table'>
                        <input type='submit' name='classification' value='Cargar clasificación'>
                    </form>
                    $table
                </section>
                <section>
                    <h3>Importar base de datos</h3>
                    <form action='#' method='post' name='import'>
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
                $mensaje
            </main>
        </body>
        </html>";
?>