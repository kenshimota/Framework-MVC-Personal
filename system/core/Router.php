<?php

/* lista de class que se necesitan para manejar la pagina */
require 'Controller.php';
require 'View.php';
require 'Model.php';

/* Clase que de encargara de la redireccionamiento de la pagina 
los render */
class Router{

	/* Constructor de nuestra clase */
	public function __construct(){

		# Extraigos todos los modelos conectados a la base de datos
		$models = new Model();
		$models->load_models();

		# agregando el controlador y su accion a realizar
		$config = array(
			"controller" => ( empty( Config::get("section_uri")[1] ) ? "home" : Config::get("section_uri")[1] ),
			"action" =>  ( empty( Config::get("section_uri")[2] ) ? "index" : Config::get("section_uri")[2] )
		);

		# ciclo que tendra que dar los valores de los parametros
		for($i = 0;  $i < (count(Config::get("section_uri")) - 3); $i = $i + 2 )
		{
			if(isset( Config::get("section_uri")[$i + 4] ))
				$config["params"][ Config::get("section_uri")[$i + 3] ] = htmlentities(Config::get("section_uri")[$i + 4]);
		}

		Config::set($config);

		$this->load();
	}

	/* funcion que permite cargar la configuracion */
	public function load(){

		# se obtiene los datos del controlador y la action
		$controller = Config::get("controller");
		$action = Config::get("action");

		# ubicacion de los controladores
		$path_controller = Config::get("path_controller");
		$path_app = Config::get("path_application");

		# carga el archivo del controllador
		if($controller != "img" && $controller != "script" && $controller != "style"){

			$file_controller = "{$path_app}/{$path_controller}/{$controller}_controller.php";

			# si no consigue el controlador
			if(!is_file($file_controller)){

				# reportando el fallo de pagina
				Report::setError("No existe un controlador con la siguente direccion ".Config::get("controller"));

				Config::set(["controller" => "Error"]);
				Config::set(["action" => "index"]);
				require_once "{$path_app}/{$path_controller}/error_controller.php";
			}
			else
				require_once "{$path_app}/{$path_controller}/{$controller}_controller.php";

			# los vuelvo a solicitar debido por si ocurrio un cambio osea no 
			# hallo un controlador
			$controller = Config::get("controller");
			$action = Config::get("action");
			$class = "{$controller}Controller";

			# si llegase a ocurrir algo y no puede obtener el metodo
			if(!method_exists(new $class(), "{$action}Action")){
				
				# reportando el fallo de pagina
				Report::setError("El metodo ". Config::get("action") ." introducido no se consigue en el controllador ");

				Config::set(["controller" => "Error"]);
				Config::set(["action" => "index"]);
			}

			# los vuelvo a solicitar debido por si ocurrio un cambio osea no 
			# hallo un controlador
			$controller = Config::get("controller");
			$action = Config::get("action");

			# Establece la clases y el metodo a ejecutar
			$class = "{$controller}Controller";
			$method = "{$action}Action";

			require_once "{$path_app}/{$path_controller}/{$controller}_controller.php";

			# despues de incluirlo hare una instancia de el mismo
			$run = new $class;
			$run->$method();
			$run->show();
		}
	}
}