<?php
$credentials = include 'credential.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($credentials[$login]) && $credentials[$login] === $password) {
        $_SESSION['login'] = $login;
        header('Location: main.php?page=profile');
        exit;
    } else {
        $error = "Невірний логін або пароль.";
    }
}
?>

<h2>Вхід</h2>
<?php if ($error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>
<form method="POST">
    <label>Логін: <input type="text" name="login" required></label><br><br>
    <label>Пароль: <input type="password" name="password" required></label><br><br>
    <button type="submit" class="button">Увійти</button>
</form>
