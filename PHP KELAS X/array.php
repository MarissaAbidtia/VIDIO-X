<?php 

    // ARRAY DIMENSI

    // $nama = array("joni", "tejo",  "budi",  "siti", 100, 2.5);
    
    // var_dump($nama);

    // echo "<br>";

    // echo  $nama[5];

    // echo "<br>";

    // for ($i=0; $i < 6 ; $i++) { 
    //     // echo $i;
    //     echo $nama[$i].'<br>';
    // }

    // foreach ($nama as $key) {
    //     echo  $key.'<br>';
    // }

    // ARRAY ASOSIATIF

    $nama = array(
        "Joni" => "Surabaya",
        "Budi" => "Malang Raya",
        "Tejo" => "Jakarta",
        "Siti" => "Sidoarjo",
    );

    var_dump($nama);

    echo '<br>';

    // echo $nama['Budi'];

    foreach ($nama as $a => $b) {
    echo $a.' : '.$b.'<br>';
    }
?>