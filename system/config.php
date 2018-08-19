<?php 

/**
* @author Erik Mota Galindo
* @date_create: 17 de Julio del 2018
* @date_update: 17 de Julio del 2018
* @return: string
**/

/* Clase que contendra la configuracion de nuestra aplicacion */
class Config
{
	/* Funcion que se encargara de cargar los datos de configuracion de 
	nuestra aplicacion actualmente */
	public static function set( $config = array() ){
		foreach ($config as $key => $value)
			self::$config[$key] = $value;
	}

	/* Puedes obtener informacion de la configuracion actual de cada aplication */
	public static function get($key = null){
		if(isset(self::$config[$key]))
			return self::$config[$key];
		else
			return null;
	}

	/* -- Constructor de la clase --  */
	public function __construct(){

		self::$config['uri'] = $_SERVER['REQUEST_URI']; // host actual
		self::$config['section_uri'] = explode('/', $_SERVER['REQUEST_URI']); // array que secciona a la URL
		self::$config['folder'] = "../config";	// Carpeta actual de las clases de configuracion
	}

	private static $config;
}