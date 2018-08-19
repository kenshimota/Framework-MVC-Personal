<?php

/**
* Clase que se encargara de realizar todas las transacciones
* Que deban ocurrir en la base de datos
*/
class DB
{
	public function __construct(){
		
		$data_connect = get_class_vars('DataBases');

		self::$connect = new Mysqli( $data_connect['host'], $data_connect['user'], $data_connect['password']);

		if(mysqli_connect_error())
			Report::setError("Error de conexion (". mysqli_connect_errno() ."): ". mysqli_connect_error());
		else
			Report::setInfo("La conexion fue realizada con exito:");

		/* Busca la existencia de la base de datos */
		$exist_db = !mysqli_connect_error() ? $this->find_db($data_connect['dbname']) : true;

		# si la base de datos no existe este permetira crearla
		if(!$exist_db){
			$success = $this->create_db($data_connect['dbname']);
			if(!$success)
				exit(1);
		}
		else
			self::$db_application = $data_connect['dbname'];
	}

	/* Funcion que busca la existencia de una base de datos si esta existe
	retorna 1 de lo contrario devuelve 0 */
	public function find_db($db){
		if(!mysqli_query(self::$connect, "use {$db}"))
			return 0;
		return 1;
	}

	/* Funcion que se encarga de crear la base de datos y devuelve una serie de noticias
	que puede recibir el servidor */
	public function create_db($db_save){
		$query = mysqli_query(self::$connect, "create database {$db_save}");
		if(!$query)
			Report::setError("Ocurrio un error al crear la base de datos {$db_save}");
		else
		{
			self::$db_application = $db_save;
			Report::setInfo("La base de datos {} fue creada con exito");
			return true;
		}
	}

	# funcion que se encarga de eliminar la base de datos
	public function destroy_db(){
		$query = mysqli_query(self::$connect, "drop database ".self::$db_application);
		if(!$query)
			Report::setError("Ocurrio un error al eliminar la base de datos");
		else
		{
			Report::setInfo("La base de datos ".self::$db_application." fue eliminada con exito");
			self::$db_application = null;
			return true;
		}
	}

	# te permite devolver la conexion que obtuviste
	# nota: solo puede devolverte una conexion si ya ha podido conectarse anteriormente
	public static function getConnect(){
		
		$data_connect = get_class_vars('DataBases');

		return new Mysqli( $data_connect['host'], $data_connect['user'], $data_connect['password'], $data_connect['dbname']);
	}

	# cierra la conexion hacia la base de datos de la pagina
	public function __destruct(){
		if(!mysqli_connect_error())
			self::$connect->close();
	}

	private static $db_application;
	private static $connect;
}