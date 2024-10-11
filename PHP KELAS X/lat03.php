<?php 

    function belajar(){
        echo "Saya sedang belajar PHP";
    }

    function Luaspersegi($p = 5,$l = 3){
        $luas = $p * $l;
        echo $luas;
    }

    function Luas($p = 5,$l = 3){
        $luas = $p * $l;
        return $luas;
    }

    function output(){
        return "Belajar Function";
    }

    echo Luas(100,3) * 5;
?>