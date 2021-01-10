<?php
include_once 'Usuarios.php';
include_once 'config.php';


class Conexion{
    private static $modelo = null;
    private $dbh = null;
    private $stmt_usuarios = null;
    private $stmt_usuario  = null;
    private $stmt_boruser  = null;
    private $stmt_moduser  = null;
    private $stmt_creauser = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new Conexion();
        }
        return self::$modelo;
    }
    public function __construct(){
        try{
            $dns="mysql:host=192.168.1.60;dbname=Usuarios;charset=utf8";
            $this->dbh=new PDO($dns, "root","root",);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }
        
        $this->stmt_usuarios  = $this->dbh->prepare("select * from Usuarios");
        $this->stmt_usuario   = $this->dbh->prepare("select * from Usuarios where id=:id");
        $this->stmt_boruser   = $this->dbh->prepare("delete from Usuarios where id =:id");
        $this->stmt_moduser   = $this->dbh->prepare("update Usuarios set  nombre=:nombre,correo=:correo, pass=:pass, plan=:plan, estado=:estado where id=:id ");
        $this->stmt_creauser  = $this->dbh->prepare("insert into Usuarios (id,pass,nombre,correo,plan,estado) Values(?,?,?,?,?,?)");
    }
      
    // Devuelvo la lista de Usuarios
    public function getUsuarios ():array {
        $tuser = [];
        $this->stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Usuarios');
        
        if ( $this->stmt_usuarios->execute() ){
            while ( $resut = $this->stmt_usuarios->fetch()){
                $user=$resut->id;              
                $datos[$user][]=$resut->pass;
                $datos[$user][]=$resut->nombre;
                $datos[$user][]=$resut->correo;
                $datos[$user][]=$resut->plan;
                $datos[$user][]=$resut->estado;
                
            }
        }
        return $datos;
    }
    // Devuelvo un usuario (array) o false
    public function getUsuario (String $login) {
        $user = false;
        
        $this->stmt_usuario->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        $this->stmt_usuario->bindParam(':id', $login);
        if ( $this->stmt_usuario->execute() ){
            if ( $obj = $this->stmt_usuario->fetch()){
                $user= $obj;
            }
        }
        return $user;
    }
    // UPDATE
    public function modUsuario($user):bool{        
        $this->stmt_moduser->bindValue(':id',$user->id);
        $this->stmt_moduser->bindValue(':pass',$user->pass);
        $this->stmt_moduser->bindValue(':correo',$user->correo);
        $this->stmt_moduser->bindValue(':nombre',$user->nombre);
        $this->stmt_moduser->bindValue(':plan',$user->plan);
        $this->stmt_moduser->bindValue(':estado',$user->estado);
        $this->stmt_moduser->execute();
        $resu = ($this->stmt_moduser->rowCount () == 1);
        return $resu;
    }
    
    //INSERT
    public function addUsuario($user):bool{
        $this->stmt_creauser->execute( [$user->id, $user->pass ,$user->nombre, $user->correo,$user->plan,$user->estado]);
        $resu = ($this->stmt_creauser->rowCount () == 1);
        return $resu;
    }
    
    //DELETE
    public function borrarUsuario(String $id):bool {
        $this->stmt_boruser->bindParam(':id', $id);
        $this->stmt_boruser->execute();
        $resu = ($this->stmt_boruser->rowCount () == 1);
        return $resu;
    }   
    
    // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    {
        trigger_error('La clonación no permitida', E_USER_ERROR);
    }
}





