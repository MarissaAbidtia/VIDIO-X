<form action="nav.php" method="post">
    Email :
    <input type="email" name="email" value="">
    Password :
    <input type="password" name="password" value="">
    <input type="submit" name="kirim" value="Login">
</form>

<?php 

    if (isset($_POST['kirim'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        echo  $email;
        echo '<br>';
        echo $password;
    };