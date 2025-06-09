<?php
$profile = [
    'first_name' => '',
    'last_name' => '',
    'birthdate' => '',
    'about' => '',
    'photo' => ''
];

$profile_file = 'user_profile.php';

if (file_exists($profile_file)) {
    $profile = include $profile_file;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile['first_name'] = $_POST['first_name'] ?? '';
    $profile['last_name'] = $_POST['last_name'] ?? '';
    $profile['birthdate'] = $_POST['birthdate'] ?? '';
    $profile['about'] = $_POST['about'] ?? '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = 'uploads/photo_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoName);
        $profile['photo'] = $photoName;
    }

    file_put_contents($profile_file, '<?php return ' . var_export($profile, true) . ';');
}
?>

<h2>Профіль користувача</h2>

<form method="POST" enctype="multipart/form-data" class="profile-wrapper">
    <div class="profile-photo-block">
        <?php if ($profile['photo']): ?>
            <img src="<?= $profile['photo'] ?>" alt="Фото користувача" class="profile-photo-large">
        <?php else: ?>
            <div class="profile-photo-placeholder">Немає фото</div>
        <?php endif; ?>

        <div class="form-group">
            <label>Завантажити нове фото:</label>
            <input type="file" name="photo">
        </div>
    </div>

    <div class="profile-form-block">
        <div class="form-group">
            <label>Ім’я:</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Прізвище:</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Дата народження:</label>
            <input type="date" name="birthdate" value="<?= htmlspecialchars($profile['birthdate']) ?>" required>
        </div>
        <div class="form-group">
            <label>Інформація:</label>
            <textarea name="about" rows="4"><?= htmlspecialchars($profile['about']) ?></textarea>
        </div>

        <button type="submit" class="button">Зберегти</button>
    </div>
</form>