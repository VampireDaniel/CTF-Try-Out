<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawPostData = file_get_contents('php://input');
    $xml = simplexml_load_string($rawPostData, null, LIBXML_NOENT);

    $username = (string) $xml->username ?? '';
    $password = (string) $xml->password ?? '';

    // Check the database for the user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    }

    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Council of Ricks</title>
    <link href="css/bootstrap-5.3.3.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-container">
            <div class="text-center">
                    <img style="height: 200px;" src="/logo.png">
                    <br>
                <h2 style="color: #c69250 !important;">Council of Ricks</h2>
                <p>Access granted only to certified dimension-hopping Ricks</p>
            </div>
            <form method="POST" class="mt-4" onsubmit="convertToXml(event, this)">
                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <input type="text" name="username" id="username" class="form-control" required placeholder="Username">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Password">
                </div>
                <button type="submit" style="background: #c69250;color:white;font-weight:600;" class="btn w-100">Sign in to the Council</button>
            </form>
        </div>
    </div>
    <script>
        function convertToXml(event, form) {
            event.preventDefault();

            const username = form.username.value;
            const password = form.password.value;

            const xml = `<credentials><username>${username}</username><password>${password}</password></credentials>`;

            console.log(xml);

            fetch(window.location.href, {
                method: "POST",
                headers: { "Content-Type": "application/xml" },
                body: xml,
            })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text();
                    }
                })
                .then(data => {
                    if (data) document.body.innerHTML = data;
                })
                .catch(error => console.error("Error:", error));
        }
    </script>
</body>
</html>
