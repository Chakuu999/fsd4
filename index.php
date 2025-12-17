<?php
// Handle form submission and store data in user.json
$errors = [];
$success = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['conform-password']) ? $_POST['conform-password'] : '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $storageFile = __DIR__ . '/user.json';
        $contents = file_exists($storageFile) ? file_get_contents($storageFile) : '[]';
        $data = json_decode($contents, true);
        if (!is_array($data)) {
            $data = [];
        }

        $data[] = [
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('c'),
        ];

        file_put_contents($storageFile, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
        $success = 'Login details stored.';
        // Clear fields after success
        $username = '';
        $email = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USER LOGIN</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
    <form action="" method="POST">
        <h2>USER LOGIN</h2>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" ><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" ><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" ><br><br>

        <label for="password"> Conform-Password:</label>
        <input type="password" id="conform-password" name="conform-password" ><br><br>
        
        <input type="submit" value="Login">
    </form>
</body>
</html>