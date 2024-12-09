

<html lang='es'>
    <head>
        <!-- Datos que describen el documento -->
        <meta charset='UTF-8' />
        <meta name='author' content='Marcos Argüelles Rivera'/>
        <meta name='description' content=''/>
        <meta name='keywords' content=','/>
        <meta name='viewport' content='width=device-width,initial-scale=1.0'/>
        <title>F1 Desktop: Semáforo</title>
        <link rel='icon' type='image/icon' href='multimedia/imagenes/f1.png'>
        <link rel='stylesheet' type='text/css' href='estilo/estilo.css' />
        <link rel='stylesheet' type='text/css' href='estilo/layout.css'/>
        <link rel='stylesheet' type='text/css' href='estilo/semaforo_grid.css'/>
        <script src='js/semaforo.js'></script>
    </head>
    <body>
        <?php
        class Record {
            private $server;
            private $user;
            private $pass;
            private $dbname;
            private $db;
            
            public function __construct() {
                $this->server = "localhost";
                $this->user = "DBUSER2024";
                $this->pass = "DBPSWD2024";
                $this->dbname = "records";
                $this->createDatabase();
            }

            public function createDatabase() {
                $this->createConection();
                $this->db->query("CREATE DATABASE IF NOT EXISTS " . $this->dbname . " COLLATE utf8_spanish_ci");
                $this->db->select_db($this->dbname);
                $this->db->query("CREATE TABLE IF NOT EXISTS Registro (
                    nombre VARCHAR(32) NOT NULL,
                    apellidos VARCHAR(32) NOT NULL,
                    nivel VARCHAR(32) NOT NULL,
                    tiempo FLOAT(10) NOT NULL);");
            }

            public function insertRecord($name, $surnames, $level, $time) {
                $this->createConection();
                $this->db->select_db("records");
                $ps = $this->db->prepare("INSERT INTO Registro(nombre, apellidos, nivel, tiempo) VALUES ('$name', '$surnames', '$level', '$time')");
                $ps->execute();
                $ps->close();
            }

            public function getTopRecords($level) {
                $this->db->select_db($this->dbname);    
                $ps = $this->db->prepare("SELECT nombre, apellidos, tiempo FROM Registro WHERE nivel = ? ORDER BY tiempo ASC LIMIT 10");
                $ps->bind_param("s", $level);
                $ps->execute();
                $result = $ps->get_result();
            
                $list = "<p>No hay récords para este nivel</p>";
                if ($result->num_rows > 0) {
                    $list = "<ol>";
                    while ($row = $result->fetch_assoc()) {
                        $list .= "<li>";
                        $list .= $row["nombre"] . " " . $row["apellidos"] . " - " . $row["tiempo"] . "s";
                        $list .= "</li>";
                    }
                    $list .= "</ol>";
                }
                echo '<h3>Mejores tiempos - Nivel ' . $level . '</h3>';
                echo $list;
                $ps->close();
            }

            public function createConection() {
                $this->db = new mysqli($this->server, $this->user, $this->pass);
            }

            public function closeConection() {
                $this->db->close();
            }
        }
        ?>
        <!-- Datos con el contenidos que aparece en el navegador -->
        <header>
            <h1><a href='index.html'>F1 Desktop</a></h1>
            <nav>
                <a href='calendario.html'>Calendario</a>
                <a href='circuito.html'>Circuito</a>
                <a class='active' href='juegos.html'>Juegos</a>
                <a href='meteorologia.html'>Meteorología</a>
                <a href='noticias.html'>Noticias</a>
                <a href='piloto.html'>Piloto</a>
                <a href='viajes.html'>Viajes</a>
            </nav>
        </header>
        <p><a href='index.html'>F1 Desktop</a> >> <a href='juegos.html'>Juegos</a> >> Semáforo</p>
        <main>
            <nav>
                <a href='memoria.html'>Memoria</a>
                <a href='semaforo.php'>Semáforo</a>
            </nav>
            <script>
                semaforo = new Semaforo();
            </script>
            <?php
                $db = new Record();
                if (count($_POST) > 0) {
                    if (isset($_POST['enviar'])) {
                        $name = $_POST['nombre'];
                        $surnames = $_POST['apellidos'];
                        $level = $_POST['nivel'];
                        $time = (float) $_POST['tiempo'];
                        $db->insertRecord($name, $surnames, $level, $time);
                        $db->getTopRecords($level);
                    }
                }
            ?>
        </main>
    </body>
    </html>
