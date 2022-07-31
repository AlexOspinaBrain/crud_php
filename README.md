<h1>Sistema CRUD para empleados</h1>
    
</h2>Es una aplicación desarrollada con PHP 8 nativo y Javascript con llamadas tipo fetch().</h2>

<h3>Instalación :</h3>

<ul>
<li>Clonar el repositorio</li>

<li>Crear una base de datos</li>

<li>Ahora debe crear dentro de la carpeta db un archivo llamado config.php para guardar en un array llamado <code>$config</code> la información importante de la conexión a la DB, así:</li>
<code>
$config = ['DB_HOST' => 'localhost', 'DB_USERNAME' => 'alex', 'DB_PASSWORD' => 'pass', 'DB_DATABASE' => 'empleados',];
</code>

<li>Ahora debe ejecutar en la base de datos la migración que esta dentro de la carpeta db.</li>

<h3>Listo, la base de datos ya contendra información de prueba y puede ir al archivo index.html</h3>

<br>
