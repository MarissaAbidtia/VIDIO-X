<?php
// Tangani submit rating (harus login)
if (isset($_POST['submit_rating'])) {
    if (!isset($_SESSION['user_id'])) {
        echo '<div class="alert alert-warning">Anda harus login untuk memberi rating.</div>';
    } else {
        $rating = (int)$_POST['rating'];
        $comment = htmlspecialchars(trim($_POST['comment']));
        $flower_id = (int)$_GET['id'];
        $user_id = $_SESSION['user_id'];

        // Cek apakah user sudah memberi rating untuk bunga ini
        $stmt = $koneksi->prepare("SELECT id FROM ratings WHERE flower_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $flower_id, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo '<div class="alert alert-info">Anda sudah memberi rating untuk bunga ini.</div>';
        } else {
            $stmt = $koneksi->prepare("INSERT INTO ratings (flower_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $flower_id, $user_id, $rating, $comment);
            if ($stmt->execute()) {
                echo '<div class="alert alert-success">Terima kasih sudah memberi rating!</div>';
            } else {
                echo '<div class="alert alert-danger">Terjadi kesalahan saat menyimpan rating.</div>';
            }
        }
    }
}

// Tampilkan rating bunga ini
$flower_id = (int)$_GET['id'];
$result_rating = $koneksi->query("SELECT r.rating, r.comment, u.username, r.created_at 
                                 FROM ratings r JOIN users u ON r.user_id = u.id 
                                 WHERE r.flower_id = $flower_id ORDER BY r.created_at DESC");

?>

<div class="mt-5">
  <h4 class="text-success">Beri Rating dan Komentar</h4>

  <?php if (isset($_SESSION['user_id'])): ?>
  <form method="post" class="mb-4">
    <label class="form-label">Rating:</label>
    <select name="rating" class="form-select mb-3" required>
      <option value="" disabled selected>Pilih rating</option>
      <?php for ($i=1; $i<=5; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?> <i class="bi bi-star-fill text-warning"></i></option>
      <?php endfor; ?>
    </select>
    <div class="mb-3">
      <textarea name="comment" class="form-control" placeholder="Tulis komentar..." rows="3"></textarea>
    </div>
    <button type="submit" name="submit_rating" class="btn btn-success"><i class="bi bi-hand-thumbs-up"></i> Kirim Rating</button>
  </form>
  <?php else: ?>
    <p><a href="login.php" class="text-success">Login</a> untuk memberi rating dan komentar.</p>
  <?php endif; ?>

  <h5 class="text-success">Komentar Pengguna:</h5>
  <?php if ($result_rating->num_rows > 0): ?>
    <ul class="list-group">
    <?php while($row = $result_rating->fetch_assoc()): ?>
      <li class="list-group-item">
        <strong><?= htmlspecialchars($row['username']) ?></strong> 
        <span class="text-warning">
          <?= str_repeat('<i class="bi bi-star-fill"></i>', $row['rating']) ?>
          <?= str_repeat('<i class="bi bi-star"></i>', 5 - $row['rating']) ?>
        </span>
        <br>
        <?= nl2br(htmlspecialchars($row['comment'])) ?>
        <br>
        <small class="text-muted"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></small>
      </li>
    <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>Belum ada komentar untuk bunga ini.</p>
  <?php endif; ?>
</div>
