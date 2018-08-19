<?php

/* Lista de clase que se encargara en cada parte del sistema */
require 'config.php';
require 'db.php';
require 'report.php';
require '/core/router.php';

/* Clase principal de nuestra aplicacion */
class ApplicationMVC
{
	/* Constructor de nuestra aplicacion */
	public function __construct(){
		
		/* Se encargara de agregar lo datos de la
		configuracion */
		self::$config = new Config();
		$this->addConfig();

		self::$db = new Databases();

		new Router();
		
		new Model();
	}


	/* Es funcion te permite agregar librearias y clase de la configuracion 
	de forma automatica */
	public function addClass($class = null, $folder_class = null){

		if(!empty($class)){
			$folder = !empty($folder_class) ? $folder_class : self::$config->get('folder');
			require "{$folder}/{$class}.php";
			return 1;
		}
		else
			return (-1);
	}

	/* Esta funcion se encargara de agregar las configuracion cargadas en la carpeta
	de config */
	private function addConfig(){

		$class_config = [ 
			'routes', // contiene las rutas de ubicaciones de la aplicacion
			'databases' // esta se encargara de los atributos hacia la base de datos
		];

		/* descompondra todos los archivos de configuracion que
		se incluyan en nuevas versiones del sistema */
		foreach ($class_config as $key) {
			if(!class_exists($key))
				$this->addClass($key);
		}

		# segundo cargaremos las rutas actuales de la aplicacion para que no ocurra errores
		$routes = get_class_vars("Routes");
		Config::set($routes);
	}

	protected static $config;
	protected static $db;
}