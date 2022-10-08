<h1>Sistema CRUD para empleados</h1>
    
</h2>Es una aplicación desarrollada con (Back) PHP 8 nativo y MySQL, ademas con un Front Javascript con llamadas tipo fetch() y Bootstrap.</h2>

<h3>Instalación :</h3>

<ul>
<li>Clonar el repositorio</li>

<li>Crear una base de datos MySQL</li>

<li>Ahora debe crear dentro de la carpeta db un archivo llamado config.php para guardar en un arreglo llamado <code>$config</code> la información  de la conexión a la DB, así:</li>
<code>
$config = ['DB_HOST' => 'localhost', 'DB_USERNAME' => 'alex', 'DB_PASSWORD' => 'pass', 'DB_DATABASE' => 'empleados',];
</code>

<li>Ahora debe ejecutar en la base de datos la migración que esta dentro de la carpeta db en el archivo migraciones.sql.</li>

<h3>Listo, la base de datos ya contendra información de prueba y puede ir al archivo index.html</h3>
