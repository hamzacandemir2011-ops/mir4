<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de configuração
require_once 'config/config.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirmPassword = $_POST["confirmPassword"] ?? '';

    // Verifica o captcha no servidor (a validação no navegador pode ser ignorada)
    if (!verify_hcaptcha($hcaptchaSecret, $_POST['h-captcha-response'] ?? '')) {
        $_SESSION['captchaError'] = 'Captcha check failed, please try again.';
        header("Location: register");
        exit();
    }

    // Valida os dados no servidor
    if (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $username)) {
        $_SESSION['error'] = "Username must be 4-20 characters and contain only letters, numbers or underscores.";
        header("Location: register");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: register");
        exit();
    }
    if (strlen($password) < 6 || strlen($password) > 64) {
        $_SESSION['error'] = "Password must be between 6 and 64 characters.";
        header("Location: register");
        exit();
    }
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register");
        exit();
    }

    // Hash da senha usando SHA-256 (formato esperado pelo servidor do jogo)
    $hashed_password = strtoupper(hash('sha256', $password));

    try {
        // Inicia a transação
        $pdo_user->beginTransaction();

        // Prepara a consulta para verificar se o nome de usuário já existe no banco de dados
        $stmt = $pdo_user->prepare("SELECT * FROM user_tb WHERE Username = :username OR Email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $pdo_user->rollBack();
            // Nome de usuário ou email já existem, armazena a mensagem de erro na sessão
            $_SESSION['error'] = "Username or email already exists. Please try again.";
            header("Location: register");
            exit();
        } else {
            // Nome de usuário e email não existem, insere os dados no banco de dados

            // Obtém o maior AccountUID atual e adiciona 1 a ele.
            // FOR UPDATE bloqueia a leitura até o commit, evitando UIDs duplicados em registros simultâneos.
            $stmt = $pdo_user->prepare("SELECT MAX(AccountUID) AS maxAccountUID FROM user_tb FOR UPDATE");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $accountUID = $row['maxAccountUID'] + 1;

            $stmt = $pdo_user->prepare("INSERT INTO user_tb (AccountUID, Username, Email, PasswordHash) VALUES (:accountUID, :username, :email, :hashed_password)");
            $stmt->execute(['accountUID' => $accountUID, 'username' => $username, 'email' => $email, 'hashed_password' => $hashed_password]);
            $inserted = $stmt->rowCount() > 0;

            // Confirma a transação
            $pdo_user->commit();

            if ($inserted) {
                // Registro concluído com sucesso, redireciona para a página de sucesso
                header("Location: success");
                exit();
            } else {
                // Erro ao inserir dados, armazena a mensagem de erro na sessão
                $_SESSION['error'] = "There was an error registering. Please try again in a few moments.";
                header("Location: register");
                exit();
            }
        }
    } catch (Exception $e) {
        // Alguma coisa deu errado, reverte a transação
        if ($pdo_user->inTransaction()) {
            $pdo_user->rollBack();
        }
        error_log('Registration failed: ' . $e->getMessage());
        // Armazena a mensagem de erro na sessão
        $_SESSION['error'] = "There was an error registering. Please try again in a few moments.";
        header("Location: register");
        exit();
    }
} else {
    // Redireciona para a página de erro se o formulário não foi enviado
    header("Location: erro");
    exit();
}
