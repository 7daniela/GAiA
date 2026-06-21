<?php

class ControladorUsuarios{

    // ************************************
    // LOGIN DE USUARIO 
    // ************************************
    public function ctrIngresarUsuario(){
        if (isset($_POST["ingDocumento"])){
            if (
                preg_match('/^[0-9]+$/', $_POST["ingDocumento"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])
            ){
                $documento = $_POST["ingDocumento"];
                $respuesta = ModeloUsuarios::mdlIngresarUsuario($documento);

                // $tempo=crypt("admin123",'$2a$07$asdfsdvafdsgf04sdfsadfGAiADeveloper$');
                // var_dump($tempo);
                // exit;

                $passEncriptado=crypt($_POST["ingPassword"],'$2a$07$asdfsdvafdsgf04sdfsadfGAiADeveloper$');

                if (is_array($respuesta)){
                    //preguntar si el usuario esta activo
                    if ($respuesta["estado"]== "activo"){
                        if ($respuesta["password"] == $passEncriptado && $respuesta["documento_id"]== $documento){
                            $_SESSION["iniciarSesion"] = "ok";
                            $_SESSION["id"] = $respuesta["id"];
                            $_SESSION["nombres"] = $respuesta["nombres"];
                            $_SESSION["apellidos"] = $respuesta["apellidos"];
                            $_SESSION["rol"] = $respuesta["rol"];
                            echo "<script>window.location = 'inicio';</script>";
                        } else{
                        // var_dump($respuesta);
                        echo  "<br><div class='alert alert-danger'>Usuario o contraseña incorrecto</div>";
                        return;
                        }   
                    } else {
                        echo  "<br><div class='alert alert-warning'>El usuario esta inactivo</div>";
                        return;
                    }                   
                }

            }    
            
        }
    } //fin del metodo de ingresar usuario

    
     // ************************************
    // LISA DE DE USUARIOS EN LA VENTANA PRINCIPAL
    // ************************************   
    static public function ctrListarUsuarios(){
        $respuesta= ModeloUsuarios::mdlListarUsuarios();
        return $respuesta;
    } //fin del metodo ctrListarUsuarios

    // ************************************
    // LISTA DE FICHAS
    // ************************************   
    static public function ctrListarFichas(){
        $respuesta= ModeloUsuarios::mdlListarFichas();
        return $respuesta;
    } //fin del metodo ctrListarFichas

    // ************************************
    // AGREGAR USUARIO A LA BD
    // ************************************
    public function ctrAgregarUsuario(){

        
        if (isset($_POST["nuevoTipoDocumento"])  && 
            isset($_POST["nuevoDocumento"])  && 
            isset($_POST["nuevoNombre"])  && 
            isset($_POST["nuevoApellido"])  && 
            isset($_POST["nuevoCorreo"])  && 
            isset($_POST["nuevoFechaNacimiento"])  && 
            isset($_POST["nuevoRol"]))

            {
                // echo "entrando a agregar usuario";
                // exit;
                if (
                preg_match('/^[0-9]+$/', $_POST["nuevoDocumento"]) &&
                preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚÑñ ]+$/', $_POST["nuevoNombre"]) &&
                preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚÑñ ]+$/', $_POST["nuevoApellido"])
              ) {

                $tabla="usuarios";
                // $passEncriptado=$_POST["nuevoDocumento"];

                $passEncriptado=crypt($_POST["nuevoDocumento"],'$2a$07$asdfsdvafdsgf04sdfsadfGAiADeveloper$');

                $fichaId = null;
                if ($_POST["nuevoRol"] == "Aprendiz" && isset($_POST["nuevaFicha"])) {
                    $fichaId = $_POST["nuevaFicha"];
                }

                $datos = array(
                  "tipoDocumento" => $_POST["nuevoTipoDocumento"],
                  "documentoId" => $_POST["nuevoDocumento"],
                  "nombres" => $_POST["nuevoNombre"],
                  "apellidos" => $_POST["nuevoApellido"],
                  "correo" => $_POST["nuevoCorreo"],
                  "fechaNacimiento" => $_POST["nuevoFechaNacimiento"],
                  "rol" => $_POST["nuevoRol"],
                  "password"=> $passEncriptado,
                  "ficha_id" => $fichaId
                );
                $respuesta= ModeloUsuarios::mdlAgregarUsuario($tabla, $datos);

                if($respuesta == "ok"){
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'El usuario ha sido registrado correctamente',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'Usuarios';
                            }
                        });
                                
                        
                    </script>";
                    // echo "<br><div class='alert alert-success'>El usuario ha sido registrado correctamente</div>";
                }else{
                    echo "<br><div class='alert alert-danger'>Error al agregar el usuario</div>";
                }

        


              }
        }  // fin del isset
    }

    // ************************************
    // TRAER UN USUARIO ESPECIFICO DE LA BD
    // ************************************
    static public function ctrMostrarUsuarios($item, $valor){
        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        return $respuesta;
    }

    // ************************************
    // EDITAR USUARIO
    // ************************************
    public function ctrEditarUsuario(){
        if (isset($_POST["editarDocumento"]) && isset($_POST["idUsuarioEditar"])) {
            if (
                preg_match('/^[0-9]+$/', $_POST["editarDocumento"]) &&
                preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚÑñ ]+$/', $_POST["editarNombre"]) &&
                preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚÑñ ]+$/', $_POST["editarApellido"])
            ) {
                $tabla = "usuarios";

                if ($_POST["editarPassword"] != "") {
                    if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])) {
                        $passEncriptado = crypt($_POST["editarPassword"], '$2a$07$asdfsdvafdsgf04sdfsadfGAiADeveloper$');
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: '¡La contraseña no puede ir vacía o llevar caracteres especiales!',
                                showConfirmButton: true,
                                confirmButtonText: 'Cerrar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location = 'Usuarios';
                                }
                            });
                        </script>";
                        return;
                    }
                } else {
                    $passEncriptado = $_POST["passwordActual"];
                }

                $fichaId = null;
                if (strtoupper($_POST["editarRol"]) == "APRENDIZ" && isset($_POST["editarFicha"]) && $_POST["editarFicha"] != "") {
                    $fichaId = $_POST["editarFicha"];
                }

                $datos = array(
                    "id" => $_POST["idUsuarioEditar"],
                    "tipoDocumento" => $_POST["editarTipoDocumento"],
                    "documentoId" => $_POST["editarDocumento"],
                    "nombres" => $_POST["editarNombre"],
                    "apellidos" => $_POST["editarApellido"],
                    "correo" => $_POST["editarCorreo"],
                    "fechaNacimiento" => $_POST["editarFechaNacimiento"],
                    "rol" => $_POST["editarRol"],
                    "password" => $passEncriptado,
                    "ficha_id" => $fichaId
                );

                $respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

                if ($respuesta == "ok") {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'El usuario ha sido editado correctamente',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'Usuarios';
                            }
                        });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error al editar el usuario!',
                            showConfirmButton: true,
                            confirmButtonText: 'Cerrar'
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: '¡El nombre o apellidos no pueden ir vacíos o llevar caracteres especiales!',
                        showConfirmButton: true,
                        confirmButtonText: 'Cerrar'
                    });
                </script>";
            }
        }
    }


    // ************************************
    // ACTUALIZAR ESTADO DE UN USUARIO
    // ************************************
    static public function ctrCambiarEstadoUsuario($idUsuario, $estado){
        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlCambiarEstadoUsuario($tabla, $idUsuario, $estado);
        return $respuesta;
    }   

}//fin de la clase ControladorUsuarios
