<?php

class View
{

	# renderizacion de la plantilla que se mostrara
	public function render(){
		$this->setParams(Config::get("params"));

		# solo si existe la vista renderizara
		if(class_exists( Config::get("controller")."Controller" ) )
		{
			# datos de donde se renderizaran la plantilla
			$path_app = Config::get("path_application");
			$path_views = Config::get("path_views");
			$controller = Config::get("controller");
			$action = Config::get("action");

			# archivo que se buscara para mostrar
			$file_to_show = "{$path_app}/{$path_views}/{$controller}/{$action}.phtml";

			if(is_file($file_to_show))
			{
				$this->getContentTemplate($file_to_show);
				echo $this->template;
			}
		}

	}

	# obtendra el contenido del archivo
	public function getContentTemplate($file){

		#establecemos variables obtenidas pasadas a nuestros parametros
		if(!empty($this->params))
			extract($this->params);

		ob_start();
		require $file;
		$this->template = ob_get_contents();
		ob_end_clean();
	}

	# introduce los parametros que mostraremos
	public function setParams($param = array()){
		
		if(!empty($param))
		{
			foreach ($param as $key => $value) {
				$this->params[$key] = $value;
			}
		}
	}

	# plantilla phtml que se va ha mostrar
	protected $template;
	protected $params;
}