<?php

use App\models\User;

include_once '../header.php';
require_once "Usuario.php";

$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
if (!empty($formData['btnEntrar'])) {
    extract($formData);

    $user = new User();
    $isLoggedIn = $user->getUserByEmail($email);

    if (!isset($isLoggedIn['errorMsg'])) {
        extract($isLoggedIn);
        if (password_verify($senhaInput, $senha)) {
            $_SESSION['id'] = $id;
            $_SESSION['pic'] = $foto_perfil;
            $_SESSION['username'] = $nome_usuario;

            header('Location: ../index.php');
            echo "yes....";
            exit;
        } else {
            // print_error('Usuario ou senha invalidos', 'danger');
            header('refresh:1; url = login.php');
        }
    } else {
        // print_error($isLoggedIn['errorMsg'], 'danger');
        header('refresh:1.5; url = login.php');
    }
}
?>
<script src="../lib/fontawesome-free-6.4.2-web/js/all.min.js">
</script>
<script src="../lib/bootstrap-5.3.2-dist/js/bootstrap.js">
</script>
<?php
include_once '../footer.php';
?>