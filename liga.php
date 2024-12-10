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
        $this->db->query("CREATE TABLE IF NOT EXISTS Pilotos (
            pId INT NOT NULL PRIMARY KEY,
            pNombre VARCHAR(32) NOT NULL,
            pApellido VARCHAR(32) NOT NULL,
            pEdad INT NOT NULL,
            cId INT NOT NULL,
            pPuntos INT NOT NULL,
            FOREIGN KEY (cId) REFERENCES Coches(cId));");
        $this->db->query("CREATE TABLE IF NOT EXISTS Coches (
            cId INT NOT NULL PRIMARY KEY,
            cMotor VARCHAR(32) NOT NULL);");
    }

    public function createConection() {
        $this->db = new mysqli($this->server, $this->user, $this->pass);
    }
}
echo "<html lang='es'>
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
    </head>
    <body>
        
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
        <p><a href='index.html'>F1 Desktop</a> >> <a href='juegos.html'>Juegos</a> >> Liga de carreras</p>
        <main>
            <nav>
                <a href='memoria.html'>Memoria</a>
                <a href='semaforo.php'>Semáforo</a>
                <a href='liga.php'>Liga</a>
            </nav>
            <h2>Liga de carreras</h2>
            <section>
                <h3>Clasificación</h3>
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
        </main>
    </body>
    </html>";
?>