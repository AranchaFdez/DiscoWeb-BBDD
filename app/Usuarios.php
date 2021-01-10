<?php
[ "id","pass","nombre","correo","plan","estado"];

class Usuarios
{
 private $id;
    private $pass;
    private $nombre;
    private $correo;
    private $plan;
    private $estado;
    

    public function __get($atributo){
        if(property_exists($this, $atributo)) {
            return $this->$atributo;
        }
    }
    // Setter con método mágico
    public function __set($atributo,$valor){
        if(property_exists($this, $atributo)) {
            $this->$atributo = $valor;
        }
    }
  
    
}