<?php

/**
* Clase que se encarga de hacer reporte de lo que sucede dentro del sistema
***/
class Report{

	/* Agrega la informacion de errores ocurrido al escribir datos 
	o fallo en la conexiones, entre otros */
	public static function setError($messague = null){
		self::$errors[] = $messague;
	}

	/* Agrega las notificaciones al sistema que se necesiten */
	public static function setInfo($messague = null){
		self::$notice[] = $messague;
	}

	/* Funcion que se encarga de devolver los errores acumulados */
	public static function getErrors(){
		return self::$errors;
	}

	private static $notice;
	private static $errors;

}