<?php

class Model
{
	public function __construct(){
		
		if(empty(self::$connect))
			self::$connect = DB::getConnect();

		$this->table = get_class($this);
	}

	# devuelve todos los registros de una table
	public function all(){
		$query = "select * from {$this->table}";
		$result = self::$connect->query($query);

		return $this->getRecord($result);
	}

	# esta funcion te permite buscar datos de un elemento dentro de la 
	# tabla que se este consultando puede hacer 
	public function find($key = array()){


		# depurando el resultado de la db
		$result = null;

		# buscando un registro por identidad
		if(is_int($key)){
			$query  = "select * from {$this->table} where id='{$key}'";
			$result = self::$connect->query($query);
		}

		# buscando una serie de registros
		if(is_array($key)){

			$params = "";
			$i = 0;

			foreach ($key as $id => $value) {

				if($i > 0)
					$params = "{$params} and";

				$params = "{$params} {$id} = '{$value}'";
				$i++;
			}

			$query = "select * from {$this->table} where ({$params})";
			$result = self::$connect->query($query);
		}

		return $this->last_record = $this->getRecord($result);
	}

	# funcion que actualiza el registro actual que se este mostrando
	# para actualizar sus datos
	public function update($params = array()){
		
		$values_fields = array(); 
		$fields = $this->getFields();

		foreach ($fields as $key => $value) {

			# solo si existe un cierto campo dentro del registro
			# este podra ser agregado a la lista de parametros a enviar

			if(isset($params[$value]))
				$values_fields[ $value ] = $params[ $value ];
		}

		$i = 0;
		if(!empty($values_fields))
		{
			$params_new = "";

			foreach ($values_fields as $key => $value) {

				if($i > 0)
					$params_new = "{$params_new},";

				if($key != "id"){
					$this->last_record[$key] = $value;
					$params_new = "{$params_new} $key='$value'";
				}

			}

			if($this->last_record)
			{
				$query = "update {$this->table} set {$params_new} where id='{$this->last_record['id']}'";

				$result = self::$connect->query($query);

				return $this->last_record;
			}
		}
		else
			return null;
	}

	# funcion encargada de insertar un nuevo registro dentro de la tabla
	# de nuestra base de datos que usemos
	public function insert($record = array()){

		$values_fields = array(); 
		$fields = $this->getFields();

		foreach ($fields as $key => $value) {

			# solo si existe un cierto campo dentro del registro
			# este podra ser agregado a la lista de parametros a enviar

			if(isset($record[$value]))
				$values_fields[ $value ] = $record[ $value ];
		}

		# solo si hay parametros a enviar este puede crear un 
		# registro hacia su tabla
		if(!empty($values_fields)){

			$text_fields = "";
			$values_text_fields = "";
			$i = 0;

			foreach ($values_fields as $key => $value) {

				if($i > 0 && $key != "id" ){
					$text_fields = "{$text_fields},";
					$values_text_fields = "{$values_text_fields},";
				}

				if($key != "id"){
					$text_fields = "{$text_fields} {$key}";
					$values_text_fields = "{$values_text_fields} '{$value}'";
					$i++;
				}
			}

			# consulta a realizar para la insercion de un registro
			$query = "insert into {$this->table} ({$text_fields}) values ({$values_text_fields})";

			$result = self::$connect->query($query);
			return $this->find(self::$connect->insert_id);
			
		}
		else
			return null;
	}


	public function delete(){
	}

	public function destroy(){
	}

	# mostrar el registro actual
	public function __get($key){
		
		if(isset($this->last_record[$key]))
			return $this->last_record[$key];
		else
			return null;
	}

	# cargara todos los que pueda conseguir en la carpeta de modelos
	public function load_models(){

		$path_app = Config::get("path_application");
		$path_models = Config::get("path_models");

		$dir = "{$path_app}/{$path_models}";

		# verifica la existencia de carpeta en donde encontrara los modelos
		if(is_dir($dir)){

			if($dh = opendir($dir)){

				while($models = readdir($dh)){
					if($models != "." && $models != ".." && $model = strstr($models,".php",true))
						require_once "{$dir}/{$model}.php";
				}

			}

			# cierra el directorio de los modelos
			closedir($dh);
		}
	}

	private function getRecord($result){

		# esto permite saber si hubo una buena consulta 
		if($result)
		{
			switch ($result->num_rows) {
				case 1:
					return $result->fetch_assoc();
				break;
				case 0:
					return null;
				break;
				default:

					if($result->num_rows > 1)
					{
						# registro vacio primero
						$record = array();

						while($reg = $result->fetch_assoc()){
							$record[] = $reg;
						}

						$this->last_record = $record;

						return $record;
					}

				break;
			}
		}
	}

	# esta funcion privada consigue obtener todos los campos que tiene 
	# una tabla dentro de nuestra base de datos
	private function getFields(){
		$query  = "select * from {$this->table}";
		$result = self::$connect->query($query);

		# visita cada uno de los campos de nuestra tabla
		while($field = $result->fetch_field())
			$fields[] = $field->name; # devolviendo el nombre de cada uno de los campos

		return $fields;
	}

	#contedra la tabla que se va ha examinar
	private $table;
	private $last_record;
	protected static $connect;
}