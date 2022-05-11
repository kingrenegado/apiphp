<?php

use Firebase\JWT\JWT;

    class PostController{

        //peticion nombre de Columnas

        static public function getColumnsData($table,$database){

            $response = PostModel::getColumnsData($table,$database);
            return $response;
        }
        
        // Peticion post para crear datos

        public function postData($table, $data){

            $response = PostModel::postData($table,$data);

            $return = new PostController();
            $return -> fncResponse($response,"post Data",null);

        }


        public function postRegister($table,$data){
            if(isset($data['password_user']) &&  $data['password_user'] != null){
                $crypt = crypt($data['password_user'],'$2a$07$azybxcags23425SSdcgneinogfndinsd$');

                $data['password_user'] = $crypt;

                $response = PostModel::postData($table,$data);

                $return = new PostController();
                $return -> fncResponse($response,"postRegisterData",null);
            }
        }

        public function postLogin($table,$data){
            $response = GetModel::getFilterData($table,"email_user",$data['email_user'],null,null,null,null);

            if(!empty($response)){
                // Encripta Contraseña
                $crypt = crypt($data['password_user'],'$2a$07$azybxcags23425SSdcgneinogfndinsd$');

                $data['password_user'] = $crypt;

                if($response['0']->password_user == $crypt){
                    // crear jwt

                    $time = time();
                    $key = "azvcdfmnjkom345unt9i45n9235p5";

                    $token = array(
                        "init" => $time,
                        "exp" => $time + (60 * 60 * 24 * 10),
                        'data' => [
                            'id' => $response['0']->id_user,
                            'email' => $response['0']->email_user
                        ]
                    );

                    $jwt = JWT::encode($token,$key);

                    // Actualizar en la bd el token del usuario

                    $data = array(
                        'token_user' => $jwt,
                        'token_exp_user' => $token['exp']
                    );

                    $update = PutModel::putData($table,$data,$response['0']->id_user, "id_user");

                    if($update == "ok"){
                        $response[0]->token_user = $jwt;
                        $response[0]->token_exp_user = $token['exp'];
                        $return = new PostController();
                        $return -> fncResponse($response,"postLoggin", null);
                    }

                }else{
                    $response = null;
                    $return = new PostController();
                    $return -> fncResponse($response,"postLoggin","Password Incorrecto");
                }
            }else{
                $response = null;
                $return = new PostController();
                $return -> fncResponse($response,"postLoggin","Email Incorrecto");
            }

        }

        public function fncResponse($response,$method,$error){

            if(!empty($response)){

                //se quita contraseña de la respuesta
                if(isset($response['0']->password_user)){
                    unset($response['0']->password_user);
                }

                $json = array(
                    'status' => 200,
                    "results" => $response
                );
            }else{

                if($error != null){
                    $json = array(
                        'status' => 400,
                        "results" => $error
                    );
                }else{
                    $json = array(
                        'status' => 404,
                        "results" => 'Not Found',
                        "method" => $method
                    );
                }
            }
    
            
            
            echo json_encode($json,http_response_code($json["status"]));
            return;
    
        }

    }
