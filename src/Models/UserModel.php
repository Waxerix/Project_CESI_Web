<?php

class user_{
    private $pdo;

   
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function getAuth($Email){
        $res = $this->pdo->prepare("SELECT * FROM User_ WHERE Email=$Email");
        if($res!=false){
        $res->execute();
        return $res->fetch(PDO::FETCH_ASSOC);
    }
    else{
        echo"adresse mail non existante";
        exit;
    }
}
}