<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        header("Location: erro.php");
        exit();
    }

    // Hash da senha
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Verifica se o nome de usuário já existe no banco de dados
    $servername = "localhost";
    $db_username = "xxxxxx";
    $db_password = "xxxxxx";
    $dbname = "xxxxxx";

    // Conexão com o banco de dados
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    // Verifica a conexão
    if ($conn->connect_error) {
        error_log("Conexão falhou: " . $conn->connect_error);
        die("Conexão falhou.");
    }

    // Consulta para verificar se o nome de usuário já existe (consultas preparadas evitam SQL injection)
    $check = $conn->prepare("SELECT 1 FROM db_user_tb WHERE DBUserName = ? OR DBUserEmail = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        // Nome de usuário ou e-mail já existem, exibe mensagem de erro
        header("Location: erro.php");
        exit();
    } else {
        // Nome de usuário e e-mail não existem, insere os dados no banco de dados
        $insert = $conn->prepare("INSERT INTO db_user_tb (DBUserName, DBUserEmail, DBUserPassword) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $email, $hashed_password);
        if ($insert->execute()) {
            // Cadastro realizado com sucesso, redireciona para página de sucesso
            header("Location: sucesso.php");
            exit();
        } else {
            // Erro ao inserir dados, exibe mensagem de erro
            header("Location: erro.php");
            exit();
        }
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
} else {
    // Redireciona para página de erro se o formulário não foi enviado
    header("Location: erro.php");
    exit();
}
?>
