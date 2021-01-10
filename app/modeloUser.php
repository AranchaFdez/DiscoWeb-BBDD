<?php 
include_once 'config.php';
include_once 'Usuarios.php';
include_once 'Conexion.php';
/* DATOS DE USUARIO
• Identificador ( 5 a 10 caracteres, no debe existir previamente, solo letras y números)

• Contraseña ( 8 a 15 caracteres, debe ser segura)

• Nombre ( Nombre y apellidos del usuario

• Correo electrónico ( Valor válido de dirección correo, no debe existir previamente)

• Tipo de Plan (0-Básico |1-Profesional |2- Premium| 3- Máster)
• Estado: (A-Activo | B-Bloqueado |I-Inactivo )
*/
// Inicializo el modelo 
// Cargo los datos del fichero a la session

function isId($user){
    foreach ($_SESSION['tusuarios'] as $id => $datos){
        if($id ==$user){
            return true;
            break;
        }
    }
    return false;
}
function comprobarId($user) {
     if(strlen($user)>=5 && strlen($user)<=10){
         if(preg_match('/[aA-zZ]/',$user) &&preg_match('/[0-10]/',$user)){
             return true;
            }
        }
    return false;
}
function comprobarPass($pass){
    if(strlen($pass)>=8 && strlen($pass)<=15){
        return true;
    }
    return false;
}
function isMail($correo,$user){
    foreach ($_SESSION['tusuarios'] as $id => $datos){
        if( $datos[2]==$correo){
            return true;
            break;
        }    
    }
    return false;
}
function comprobarMail($correo){
    if(filter_var($correo, FILTER_VALIDATE_EMAIL)==true){
        return true;
    }
    return false ;
}
function limpiarEntrada(string $entrada):string{
    $salida = trim($entrada); // Elimina espacios antes y despuÃ©s de los datos
    $salida = stripslashes($salida); // Elimina backslashes \
    $salida = htmlspecialchars($salida); // Traduce caracteres especiales en entidades HTML
    return $salida;
}
function limpiarArrayEntrada(array &$entrada){
    
    foreach ($entrada as $key => $value ) {
        $entrada[$key] = limpiarEntrada($value);
    }
}

function modeloUserInit(){
    $db=Conexion::getModelo();
    if($tuser=$db->getUsuarios()){
        $_SESSION['tusuarios'] =$tuser;
        return true;
    }
    return false;
}

// Comprueba usuario y contraseña (boolean)
function modeloOkUser($user,$clave){
    $correcto=false;
    if(isset($_SESSION['tusuarios'][$user])){
        $usedat=$_SESSION['tusuarios'][$user];
        $passUser=$usedat[0];
        $correcto=($clave==$passUser);
       
    }
    return $correcto ;
}

// Devuelve el plan de usuario (String)
function modeloObtenerTipo($user){
    $tipouser=$_SESSION['tusuarios'][$user][3];
    return PLANES[$tipouser]; // Máster
}
/**
 * funcion llamada en controleuser ctlUserBorrar
 * borra el user pasado por parametro y refresca la pag redirigiendo a verUsuarios
 */
// Borrar un usuario (boolean)
function modeloUserDel($user){
   $db=Conexion::getModelo();
   if( $db->borrarUsuario($user)){
       header("Refresh:0; url=index.php?orden=VerUsuarios");
       return true;
   }
   return false;
}
// Añadir un nuevo usuario (boolean)
function modeloUserAdd($userid,$userdat){
    $user1 = new Usuarios();
    $user1->id  = $userid;
    $user1->nombre  = $userdat[1];
    $user1->pass  =  $userdat[0];
    $user1->correo   = $userdat[2] ;
    $user1->plan   = $userdat[3] ;
    $user1->estado   = $userdat[4] ;
    
    $db = Conexion::getModelo();
    if($db-> addUsuario($user1)){
        return true;
    }
         return false;
}


// Tabla de todos los usuarios para visualizar
function modeloUserGetAll (){
    // Genero lo datos para la vista que no muestra la contraseña ni los códigos de estado o plan
    // sino su traducción a texto
    $tuservista=[];
    foreach ($_SESSION['tusuarios'] as $clave => $datosusuario){
        $tuservista[$clave] = [$datosusuario[1],
                               $datosusuario[2],
                            
                               PLANES[$datosusuario[3]],
                               ESTADOS[$datosusuario[4]]
                               ];
    }
    return $tuservista;
}


