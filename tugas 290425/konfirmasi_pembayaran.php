<?php
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesanan_id = $_POST['pesanan_id'] ?? 0;
    $file = $_FILES['bukti_transfer'] ?? null;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/bukti_transfer/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = 'bukti_' . time() . '_' . $pesanan_id . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Update status pesanan
            $stmt = $pdo->prepare("
                UPDATE pesanan 
                SET status = 'selesai', 
                    bukti_transfer = ? 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$file_path, $pesanan_id, $_SESSION['user_id']]);

            header('Location: detail_pesanan.php?id=' . $pesanan_id . '&status=success');
            exit;
        }
    }

    header('Location: detail_pesanan.php?id=' . $pesanan_id . '&status=error');
    exit;
}

header('Location: history.php');
exit;