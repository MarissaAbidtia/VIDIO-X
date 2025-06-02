<?php
// admin/manage_menu.php
require_once '../includes/header_admin.php'; // Sertakan header admin

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki izin untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

// --- Konfigurasi Upload Gambar ---
define('UPLOAD_DIR', '../assets/img/menu/'); // Direktori upload relatif terhadap file ini
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // Max 2MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

// --- Fungsi Helper ---
// Fungsi untuk mengupload gambar
function uploadImage($file_input_name, $current_image = null) {
    global $allowed_types;
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES[$file_input_name];

        // Validasi ukuran
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['error' => 'Ukuran file gambar terlalu besar (maks 2MB).'];
        }

        // Validasi tipe
        $file_type = mime_content_type($file['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            return ['error' => 'Tipe file gambar tidak valid (hanya JPG, PNG, GIF).'];
        }

        // Buat nama file unik
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('menu_', true) . '.' . strtolower($extension);
        $destination = UPLOAD_DIR . $new_filename;

        // Pindahkan file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Hapus gambar lama jika ada (saat update)
            if ($current_image && file_exists(UPLOAD_DIR . $current_image)) {
                unlink(UPLOAD_DIR . $current_image);
            }
            return ['success' => $new_filename]; // Kembalikan nama file baru
        } else {
            return ['error' => 'Gagal memindahkan file gambar yang diupload.'];
        }
    } elseif (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_NO_FILE) {
        // Error upload selain 'tidak ada file'
        return ['error' => 'Terjadi kesalahan saat upload gambar: Error code ' . $_FILES[$file_input_name]['error']];
    }
    // Tidak ada file baru yang diupload, kembalikan null atau gambar saat ini
    return ['success' => $current_image];
}

// Fungsi untuk menghapus gambar
function deleteImage($filename) {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        unlink(UPLOAD_DIR . $filename);
    }
}

// --- Logika CRUD ---
$action = $_GET['action'] ?? 'list'; // Default action is 'list'
$item_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$menu_item = null; // Untuk menyimpan data item saat edit

// Proses form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id_post = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $category = trim($_POST['category'] ?? '');
    $status = trim($_POST['status'] ?? 'unavailable'); // Default 'unavailable'
    $current_image_filename = $_POST['current_image'] ?? null; // Gambar saat ini (untuk update)

    // Validasi dasar
    if (empty($title) || $price === false || $price < 0 || empty($category)) {
        $_SESSION['admin_error_message'] = "Harap isi semua field yang wajib diisi (Judul, Harga, Kategori).";
    } else {
        // Proses upload gambar
        $upload_result = uploadImage('image_url', $current_image_filename);

        if (isset($upload_result['error'])) {
            $_SESSION['admin_error_message'] = $upload_result['error'];
        } else {
            $image_filename = $upload_result['success']; // Nama file baru atau yang lama

            // --- CREATE ---
            if (isset($_POST['add_item'])) {
                $sql = "INSERT INTO menu_items (title, description, price, category, image_url, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssdsss", $title, $description, $price, $category, $image_filename, $status);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['admin_success_message'] = "Item menu '$title' berhasil ditambahkan.";
                    } else {
                        $_SESSION['admin_error_message'] = "Gagal menambahkan item menu: " . mysqli_stmt_error($stmt);
                        deleteImage($image_filename); // Hapus gambar jika insert gagal
                    }
                    mysqli_stmt_close($stmt);
                } else {
                     $_SESSION['admin_error_message'] = "Gagal menyiapkan statement: " . mysqli_error($conn);
                     deleteImage($image_filename); // Hapus gambar jika prepare gagal
                }
                 header("Location: manage_menu.php"); // Redirect setelah proses
                 exit();
            }
            // --- UPDATE ---
            elseif (isset($_POST['update_item']) && $item_id_post) {
                 // Jika tidak ada gambar baru diupload, gunakan gambar lama
                 $image_to_save = $image_filename ?? $current_image_filename;

                 $sql = "UPDATE menu_items SET title=?, description=?, price=?, category=?, image_url=?, status=? WHERE id=?";
                 $stmt = mysqli_prepare($conn, $sql);
                 if ($stmt) {
                     mysqli_stmt_bind_param($stmt, "ssdsssi", $title, $description, $price, $category, $image_to_save, $status, $item_id_post);
                     if (mysqli_stmt_execute($stmt)) {
                         $_SESSION['admin_success_message'] = "Item menu '$title' berhasil diperbarui.";
                     } else {
                         $_SESSION['admin_error_message'] = "Gagal memperbarui item menu: " . mysqli_stmt_error($stmt);
                         // Jangan hapus gambar lama jika update gagal, mungkin masih dipakai
                     }
                     mysqli_stmt_close($stmt);
                 } else {
                      $_SESSION['admin_error_message'] = "Gagal menyiapkan statement update: " . mysqli_error($conn);
                 }
                 header("Location: manage_menu.php"); // Redirect setelah proses
                 exit();
            }
        }
    }
     // Jika ada error validasi atau upload, redirect kembali ke form (jika memungkinkan)
     // header("Location: manage_menu.php?action=" . ($item_id_post ? 'edit&id='.$item_id_post : 'add'));
     // exit();
}

// --- DELETE ---
if ($action == 'delete' && $item_id) {
    // Ambil nama file gambar sebelum menghapus record
    $sql_get_image = "SELECT image_url FROM menu_items WHERE id = ?";
    $stmt_get_image = mysqli_prepare($conn, $sql_get_image);
    $image_to_delete = null;
    if ($stmt_get_image) {
        mysqli_stmt_bind_param($stmt_get_image, "i", $item_id);
        mysqli_stmt_execute($stmt_get_image);
        $result_image = mysqli_stmt_get_result($stmt_get_image);
        if ($row = mysqli_fetch_assoc($result_image)) {
            $image_to_delete = $row['image_url'];
        }
        mysqli_stmt_close($stmt_get_image);
    }

    // Hapus record dari database
    $sql_delete = "DELETE FROM menu_items WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $item_id);
        if (mysqli_stmt_execute($stmt_delete)) {
            // Hapus gambar terkait jika record berhasil dihapus
            deleteImage($image_to_delete);
            $_SESSION['admin_success_message'] = "Item menu berhasil dihapus.";
        } else {
            $_SESSION['admin_error_message'] = "Gagal menghapus item menu: " . mysqli_stmt_error($stmt_delete);
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['admin_error_message'] = "Gagal menyiapkan statement delete: " . mysqli_error($conn);
    }
    header("Location: manage_menu.php"); // Redirect setelah proses
    exit();
}

// --- READ (untuk form edit) ---
if ($action == 'edit' && $item_id) {
    $sql_edit = "SELECT id, title, description, price, category, image_url, status FROM menu_items WHERE id = ?";
    $stmt_edit = mysqli_prepare($conn, $sql_edit);
    if ($stmt_edit) {
        mysqli_stmt_bind_param($stmt_edit, "i", $item_id);
        mysqli_stmt_execute($stmt_edit);
        $result_edit = mysqli_stmt_get_result($stmt_edit);
        $menu_item = mysqli_fetch_assoc($result_edit);
        mysqli_stmt_close($stmt_edit);
        if (!$menu_item) {
             $_SESSION['admin_error_message'] = "Item menu tidak ditemukan.";
             header("Location: manage_menu.php");
             exit();
        }
    } else {
         $_SESSION['admin_error_message'] = "Gagal mengambil data item untuk diedit: " . mysqli_error($conn);
         header("Location: manage_menu.php");
         exit();
    }
}

// --- READ (untuk list) ---
$menu_items = [];
if ($action == 'list' || $action == 'add' || $action == 'edit') { // Tetap tampilkan list di halaman add/edit
    $sql_list = "SELECT id, title, price, category, image_url, status FROM menu_items ORDER BY category, title";
    $result_list = mysqli_query($conn, $sql_list);
    if ($result_list) {
        while ($row = mysqli_fetch_assoc($result_list)) {
            $menu_items[] = $row;
        }
    } else {
        $_SESSION['admin_error_message'] = "Gagal mengambil daftar menu: " . mysqli_error($conn);
    }
}

// Ambil daftar kategori unik untuk dropdown
$categories = [];
$sql_cats = "SELECT DISTINCT category FROM menu_items ORDER BY category";
$result_cats = mysqli_query($conn, $sql_cats);
if($result_cats) {
    while($cat_row = mysqli_fetch_assoc($result_cats)) {
        $categories[] = $cat_row['category'];
    }
}

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kelola Menu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelola Menu</li>
    </ol>

    <!-- Tombol Tambah / Kembali -->
    <div class="mb-3">
        <?php if ($action == 'add' || $action == 'edit'): ?>
            <a href="manage_menu.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Menu</a>
        <?php else: ?>
            <a href="manage_menu.php?action=add" class="btn btn-success"><i class="fas fa-plus me-1"></i> Tambah Item Menu Baru</a>
        <?php endif; ?>
    </div>

    <!-- Form Tambah/Edit -->
    <?php if ($action == 'add' || ($action == 'edit' && $menu_item)): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas <?php echo ($action == 'edit') ? 'fa-edit' : 'fa-plus'; ?> me-1"></i>
            <?php echo ($action == 'edit') ? 'Edit Item Menu: ' . htmlspecialchars($menu_item['title']) : 'Tambah Item Menu Baru'; ?>
        </div>
        <div class="card-body">
            <form action="manage_menu.php" method="POST" enctype="multipart/form-data">
                <?php if ($action == 'edit'): ?>
                    <input type="hidden" name="item_id" value="<?php echo $menu_item['id']; ?>">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($menu_item['image_url'] ?? ''); ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Nama Item <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($menu_item['title'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($menu_item['description'] ?? ''); ?></textarea>
                        </div>
                         <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" step="500" min="0" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($menu_item['price'] ?? ''); ?>" required>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <input list="category-list" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($menu_item['category'] ?? ''); ?>" required>
                                <datalist id="category-list">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>">
                                    <?php endforeach; ?>
                                    <!-- Tambahkan opsi default jika perlu -->
                                    <option value="Makanan">
                                    <option value="Minuman Dingin">
                                    <option value="Minuman Panas">
                                    <option value="Snack">
                                </datalist>
                            </div>
                         </div>
                         <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="available" <?php echo (isset($menu_item['status']) && $menu_item['status'] == 'available') ? 'selected' : ''; ?>>Tersedia</option>
                                <option value="unavailable" <?php echo (!isset($menu_item['status']) || $menu_item['status'] == 'unavailable') ? 'selected' : ''; ?>>Tidak Tersedia</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Gambar Item</label>
                            <input class="form-control" type="file" id="image_url" name="image_url" accept="image/jpeg, image/png, image/gif">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar. Maks 2MB (JPG, PNG, GIF).</small>
                        </div>
                        <?php if ($action == 'edit' && !empty($menu_item['image_url'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Gambar Saat Ini:</label><br>
                                <img src="../assets/img/menu/<?php echo htmlspecialchars($menu_item['image_url']); ?>" alt="<?php echo htmlspecialchars($menu_item['title']); ?>" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>
                <button type="submit" name="<?php echo ($action == 'edit') ? 'update_item' : 'add_item'; ?>" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> <?php echo ($action == 'edit') ? 'Simpan Perubahan' : 'Tambah Item'; ?>
                </button>
                <a href="manage_menu.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
    <?php endif; ?>


    <!-- Daftar Menu -->
    <?php // Tampilkan daftar hanya jika action bukan add atau edit, atau jika form disembunyikan
    if ($action == 'list'):
    ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas fa-utensils me-1"></i>
            Daftar Item Menu
        </div>
        <div class="card-body">
            <?php if (empty($menu_items)): ?>
                <div class="alert alert-info">Belum ada item menu yang ditambahkan.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="menuTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Item</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menu_items as $item): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="../assets/img/menu/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="max-height: 50px; max-width: 70px;" class="img-thumbnail">
                                        <?php else: ?>
                                            <i class="fas fa-image text-muted fa-2x"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($item['status'] == 'available'): ?>
                                            <span class="badge bg-success">Tersedia</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak Tersedia</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="manage_menu.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_menu.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus item menu \'<?php echo htmlspecialchars(addslashes($item['title'])); ?>\'?');">
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
    <?php endif; ?>

</div>

<?php
require_once '../includes/footer_admin.php'; // Sertakan footer admin

// Tutup koneksi jika dibuka di header
if (isset($conn) && $conn instanceof mysqli) {
   mysqli_close($conn);
}
?>