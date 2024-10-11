<?php 

    // $hari = 4;

    // switch ($hari) {
    //     case 1:
    //         echo 'Minggu';
    //         break;
    //     case 2:
    //         echo 'Senin';
    //         break;
    //     case 3:
    //         echo 'Selasa';
    //         break;
        
    //     default:
    //         echo 'Hari belum dikenal';
    //         break;
    // }

    $pilihan = 'tambah';

    switch ($pilihan) {
        case 'tambah':
            echo 'Anda meilih tambah';
            break;
        case 'ubah':
            echo 'Anda meilih ubah';
            break;
        case 'hapus':
            echo 'Anda meilih hapus';
            break;
        
        default:
            echo 'Pilihan tidak ada';
            break;
    }

?>