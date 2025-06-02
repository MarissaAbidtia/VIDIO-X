<?php
// admin/manage_banners.php
require_once '../includes/header_admin.php'; // Sertakan header admin

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki izin untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

// --- Konfigurasi Upload Gambar Banner ---
define('BANNER_UPLOAD_DIR', '../assets/img/banners/'); // Direktori upload banner
define('BANNER_MAX_FILE_SIZE', 3 * 1024 * 1024); // Max 3MB untuk banner
$banner_allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// --- Fungsi Helper (Adaptasi dari manage_menu.php) ---
// Fungsi untuk mengupload gambar banner
function uploadBannerImage($file_input_name, $current_image = null) {
    global $banner_allowed_types;
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES[$file_input_name];

        // Validasi ukuran
        if ($file['size'] > BANNER_MAX_FILE_SIZE) {
            return ['error' => 'Ukuran file banner terlalu besar (maks 3MB).'];
        }

        // Validasi tipe
        $file_type = mime_content_type($file['tmp_name']);
        if (!in_array($file_type, $banner_allowed_types)) {
            return ['error' => 'Tipe file banner tidak valid (hanya JPG, PNG, GIF, WEBP).'];
        }

        // Buat nama file unik
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('banner_', true) . '.' . strtolower($extension);
        $destination = BANNER_UPLOAD_DIR . $new_filename;

        // Pastikan direktori ada
        if (!is_dir(BANNER_UPLOAD_DIR)) {
            mkdir(BANNER_UPLOAD_DIR, 0775, true); // Buat direktori jika belum ada
        }

        // Pindahkan file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Hapus gambar lama jika ada (saat update)
            if ($current_image && file_exists(BANNER_UPLOAD_DIR . $current_image)) {
                @unlink(BANNER_UPLOAD_DIR . $current_image); // Gunakan @ untuk menekan error jika file tidak ada
            }
            return ['success' => $new_filename]; // Kembalikan nama file baru
        } else {
            return ['error' => 'Gagal memindahkan file banner yang diupload. Periksa izin folder.'];
        }
    } elseif (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_NO_FILE) {
        // Error upload selain 'tidak ada file'
        return ['error' => 'Terjadi kesalahan saat upload banner: Error code ' . $_FILES[$file_input_name]['error']];
    }
    // Tidak ada file baru yang diupload, kembalikan gambar saat ini (jika update)
    return ['success' => $current_image];
}

// Fungsi untuk menghapus gambar banner
function deleteBannerImage($filename) {
    if ($filename && file_exists(BANNER_UPLOAD_DIR . $filename)) {
        @unlink(BANNER_UPLOAD_DIR . $filename);
    }
}

// --- Logika CRUD Banner ---
$action = $_GET['action'] ?? 'list'; // Default action
$banner_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$banner_data = null; // Untuk menyimpan data banner saat edit

// Proses form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $banner_id_post = filter_input(INPUT_POST, 'banner_id', FILTER_VALIDATE_INT);
    $title = trim($_POST['title'] ?? ''); // Judul/Alt text
    $link_url = filter_input(INPUT_POST, 'link_url', FILTER_VALIDATE_URL) ?: null; // Link tujuan (opsional)
    $is_active = isset($_POST['is_active']) ? 1 : 0; // Status aktif
    $current_image_filename = $_POST['current_image'] ?? null;

    // Proses upload gambar
    $upload_result = uploadBannerImage('image_file', $current_image_filename);

    if (isset($upload_result['error'])) {
        $_SESSION['admin_error_message'] = $upload_result['error'];
    } else {
        $image_filename = $upload_result['success']; // Nama file baru atau yang lama

        // Validasi: Gambar wajib ada saat menambah banner baru
        if (isset($_POST['add_banner']) && empty($image_filename)) {
             $_SESSION['admin_error_message'] = "File gambar banner wajib diisi.";
        } else {
            // --- CREATE ---
            if (isset($_POST['add_banner'])) {
                $sql = "INSERT INTO banners (image_filename, title, link_url, is_active, uploaded_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssi", $image_filename, $title, $link_url, $is_active);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['admin_success_message'] = "Banner '$title' berhasil ditambahkan.";
                    } else {
                        $_SESSION['admin_error_message'] = "Gagal menambahkan banner: " . mysqli_stmt_error($stmt);
                        deleteBannerImage($image_filename); // Hapus gambar jika insert gagal
                    }
                    mysqli_stmt_close($stmt);
                } else {
                     $_SESSION['admin_error_message'] = "Gagal menyiapkan statement: " . mysqli_error($conn);
                     deleteBannerImage($image_filename); // Hapus gambar jika prepare gagal
                }
                 header("Location: manage_banners.php");
                 exit();
            }
            // --- UPDATE ---
            elseif (isset($_POST['update_banner']) && $banner_id_post) {
                 // Jika tidak ada gambar baru diupload, gunakan gambar lama
                 $image_to_save = $image_filename ?? $current_image_filename;

                 // Pastikan gambar ada untuk disimpan
                 if (empty($image_to_save)) {
                     $_SESSION['admin_error_message'] = "Terjadi kesalahan: Nama file gambar tidak ditemukan untuk update.";
                 } else {
                     $sql = "UPDATE banners SET title=?, link_url=?, is_active=?, image_filename=? WHERE id=?";
                     $stmt = mysqli_prepare($conn, $sql);
                     if ($stmt) {
                         mysqli_stmt_bind_param($stmt, "ssisi", $title, $link_url, $is_active, $image_to_save, $banner_id_post);
                         if (mysqli_stmt_execute($stmt)) {
                             $_SESSION['admin_success_message'] = "Banner '$title' berhasil diperbarui.";
                         } else {
                             $_SESSION['admin_error_message'] = "Gagal memperbarui banner: " . mysqli_stmt_error($stmt);
                         }
                         mysqli_stmt_close($stmt);
                     } else {
                          $_SESSION['admin_error_message'] = "Gagal menyiapkan statement update: " . mysqli_error($conn);
                     }
                 }
                 header("Location: manage_banners.php");
                 exit();
            }
        }
    }
    // Redirect jika ada error validasi/upload (opsional, bisa juga tampilkan error di form)
    // header("Location: manage_banners.php?action=" . ($banner_id_post ? 'edit&id='.$banner_id_post : 'add'));
    // exit();
}

// --- DELETE ---
if ($action == 'delete' && $banner_id) {
    // Ambil nama file gambar sebelum menghapus record
    $sql_get_image = "SELECT image_filename FROM banners WHERE id = ?";
    $stmt_get_image = mysqli_prepare($conn, $sql_get_image);
    $image_to_delete = null;
    if ($stmt_get_image) {
        mysqli_stmt_bind_param($stmt_get_image, "i", $banner_id);
        mysqli_stmt_execute($stmt_get_image);
        $result_image = mysqli_stmt_get_result($stmt_get_image);
        if ($row = mysqli_fetch_assoc($result_image)) {
            $image_to_delete = $row['image_filename'];
        }
        mysqli_stmt_close($stmt_get_image);
    }

    // Hapus record dari database
    $sql_delete = "DELETE FROM banners WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $banner_id);
        if (mysqli_stmt_execute($stmt_delete)) {
            // Hapus gambar terkait jika record berhasil dihapus
            deleteBannerImage($image_to_delete);
            $_SESSION['admin_success_message'] = "Banner berhasil dihapus.";
        } else {
            $_SESSION['admin_error_message'] = "Gagal menghapus banner: " . mysqli_stmt_error($stmt_delete);
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['admin_error_message'] = "Gagal menyiapkan statement delete: " . mysqli_error($conn);
    }
    header("Location: manage_banners.php");
    exit();
}

// --- READ (untuk form edit) ---
if ($action == 'edit' && $banner_id) {
    $sql_edit = "SELECT id, image_filename, title, link_url, is_active FROM banners WHERE id = ?";
    $stmt_edit = mysqli_prepare($conn, $sql_edit);
    if ($stmt_edit) {
        mysqli_stmt_bind_param($stmt_edit, "i", $banner_id);
        mysqli_stmt_execute($stmt_edit);
        $result_edit = mysqli_stmt_get_result($stmt_edit);
        $banner_data = mysqli_fetch_assoc($result_edit);
        mysqli_stmt_close($stmt_edit);
        if (!$banner_data) {
             $_SESSION['admin_error_message'] = "Banner tidak ditemukan.";
             header("Location: manage_banners.php");
             exit();
        }
    } else {
         $_SESSION['admin_error_message'] = "Gagal mengambil data banner untuk diedit: " . mysqli_error($conn);
         header("Location: manage_banners.php");
         exit();
    }
}

// --- READ (untuk list) ---
$banners = [];
// Selalu ambil list untuk ditampilkan di bawah form add/edit
$sql_list = "SELECT id, image_filename, title, link_url, is_active, uploaded_at FROM banners ORDER BY uploaded_at DESC";
$result_list = mysqli_query($conn, $sql_list);
if ($result_list) {
    while ($row = mysqli_fetch_assoc($result_list)) {
        $banners[] = $row;
    }
} else {
    // Tampilkan error jika query gagal, tapi jangan hentikan script
    $_SESSION['admin_error_message'] = $_SESSION['admin_error_message'] ?? '' . " Gagal mengambil daftar banner: " . mysqli_error($conn);
}

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kelola Banner</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelola Banner</li>
    </ol>

    <!-- Tombol Tambah / Kembali -->
    <div class="mb-3">
        <?php if ($action == 'add' || $action == 'edit'): ?>
            <a href="manage_banners.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Banner</a>
        <?php else: ?>
            <a href="manage_banners.php?action=add" class="btn btn-success"><i class="fas fa-plus me-1"></i> Tambah Banner Baru</a>
        <?php endif; ?>
    </div>

    <!-- Form Tambah/Edit -->
    <?php if ($action == 'add' || ($action == 'edit' && $banner_data)): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas <?php echo ($action == 'edit') ? 'fa-edit' : 'fa-plus'; ?> me-1"></i>
            <?php echo ($action == 'edit') ? 'Edit Banner: ' . htmlspecialchars($banner_data['title'] ?: 'ID #'.$banner_data['id']) : 'Tambah Banner Baru'; ?>
        </div>
        <div class="card-body">
            <form action="manage_banners.php" method="POST" enctype="multipart/form-data">
                <?php if ($action == 'edit'): ?>
                    <input type="hidden" name="banner_id" value="<?php echo $banner_data['id']; ?>">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($banner_data['image_filename'] ?? ''); ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="image_file" class="form-label">File Gambar Banner <span class="text-danger"><?php echo ($action == 'add') ? '*' : ''; ?></span></label>
                    <input class="form-control" type="file" id="image_file" name="image_file" accept="<?php echo implode(',', $banner_allowed_types); ?>" <?php echo ($action == 'add') ? 'required' : ''; ?>>
                    <small class="form-text text-muted">
                        <?php echo ($action == 'edit') ? 'Kosongkan jika tidak ingin mengubah gambar. ' : ''; ?>
                        Maks <?php echo BANNER_MAX_FILE_SIZE / 1024 / 1024; ?>MB (JPG, PNG, GIF, WEBP).
                    </small>
                </div>

                <?php if ($action == 'edit' && !empty($banner_data['image_filename'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Gambar Saat Ini:</label><br>
                        <img src="../assets/img/banners/<?php echo htmlspecialchars($banner_data['image_filename']); ?>" alt="<?php echo htmlspecialchars($banner_data['title']); ?>" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="title" class="form-label">Judul / Alt Text</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($banner_data['title'] ?? ''); ?>" placeholder="Deskripsi singkat banner (untuk SEO/aksesibilitas)">
                </div>

                <div class="mb-3">
                    <label for="link_url" class="form-label">Link Tujuan (Opsional)</label>
                    <input type="url" class="form-control" id="link_url" name="link_url" value="<?php echo htmlspecialchars($banner_data['link_url'] ?? ''); ?>" placeholder="Contoh: https://domain.com/promo atau /menu/kopi-spesial">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" <?php echo (isset($banner_data['is_active']) && $banner_data['is_active'] == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">
                        Aktifkan Banner (Tampilkan di halaman depan)
                    </label>
                </div>

                <hr>
                <button type="submit" name="<?php echo ($action == 'edit') ? 'update_banner' : 'add_banner'; ?>" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> <?php echo ($action == 'edit') ? 'Simpan Perubahan' : 'Tambah Banner'; ?>
                </button>
                <a href="manage_banners.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
    <?php endif; ?>


    <!-- Daftar Banner -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas fa-images me-1"></i>
            Daftar Banner Tersimpan
        </div>
        <div class="card-body">
            <?php if (empty($banners)): ?>
                <div class="alert alert-info">Belum ada banner yang ditambahkan.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="bannersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Judul/Alt</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($banners as $banner): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if (!empty($banner['image_filename'])): ?>
                                            <img src="../assets/img/banners/<?php echo htmlspecialchars($banner['image_filename']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" style="max-height: 60px; max-width: 120px;" class="img-thumbnail">
                                        <?php else: ?>
                                            <i class="fas fa-image text-muted fa-2x"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($banner['title'] ?: '-'); ?></td>
                                    <td>
                                        <?php if (!empty($banner['link_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($banner['link_url']); ?>" target="_blank" title="<?php echo htmlspecialchars($banner['link_url']); ?>">
                                                <?php echo htmlspecialchars(substr($banner['link_url'], 0, 30)) . (strlen($banner['link_url']) > 30 ? '...' : ''); ?> <i class="fas fa-external-link-alt fa-xs"></i>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($banner['is_active'] == 1): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                     <td><?php echo date('d M Y, H:i', strtotime($banner['uploaded_at'])); ?></td>
                                    <td>
                                        <a href="manage_banners.php?action=edit&id=<?php echo $banner['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_banners.php?action=delete&id=<?php echo $banner['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus banner ini?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
require_once '../includes/footer_admin.php'; // Sertakan footer admin

// Tutup koneksi jika dibuka di header
if (isset($conn) && $conn instanceof mysqli) {
   mysqli_close($conn);
}
?>