<?php

    require_once "connection.php";

    class DeleteModel{

        // Peticion delete para eliminar datos

        static public function deleteData($table,$id,$nameId){

            $stmt = Connection::connect()->prepare("DELETE FROM $table WHERE $nameId =:$nameId ");

            $stmt -> bindParam(":".$nameId,$id,PDO::PARAM_STR);

            if($stmt->execute()){
                return "ok";
            }else{
                return Connection::connect()->errorInfo();
            }

        }

    }