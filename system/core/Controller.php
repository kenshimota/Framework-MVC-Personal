<?php

/* funcion que se encargara en los controladores de los de la aplicacion que 
nos permitira escribir el codigo de envio de parametros a la vista y hacia la
base de datos, nos ayudara a comunicar ha estos */
abstract class Controller{

	public function __construct(){
		$this->view = new View();
	}

	public function show(){
		$this->render();
	}

	protected function render(){

		# le envio los datos obtenidos a la vista
		$this->view->render();
	}

	# funcion por defecto de cada controllador
	abstract public function indexAction();

	protected $view;
}