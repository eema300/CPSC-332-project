<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = /*htmlspecialchars(*/$_POST["email"]/*)*/;     // uncomment if info should be sanitized (probably don't need to
    $pwd = /*htmlspecialchars(*/$_POST["pwd"]/*)*/;         // worry about that)

    try
    {
        require_once "dbh.inc.php";
        require_once "login_model.inc.php";
        require_once "login_contr.inc.php";

        // Error handling
        $errors = [];

        if (is_input_empty($email, $pwd))
        {
            $errors["no_input"] = "EMPTY FIELD! Fill all fields please!";
        }

        $result = get_user($pdo, $email);

        if (is_email_wrong($result))
        {
            $errors["login_incorrect"] = "Incorrect login info!";
        }
        if (!is_email_wrong($result) && is_password_wrong($pwd, $result["pwd"]))
        {
            $errors["login_incorrect"] = "Incorrect login info!";
        }

        require_once "session_config.inc.php";

        if ($errors)
        {
            $_SESSION["errors_signup"] = $errors;

            header("Location: ../index.php");
            die();
        }

        $newSessionId = session_create_id();
        $sessionId = $newSessionId . "_" . $result["id"];
        session_id($sessionId);

        $_SESSION["user_id"] = $result["id"];
        //$user_id ???
        $_SESSION["user_name"] = $result["name"];
        $_SESSION["user_email"] = htmlspecialchars($result["email"]);

        $_SESSION["last_regeneration"] = time();

        //header("Location: ../index.php?login=success");

        //$name = $result["name"];
        header("Location: homepage.inc.php");

        $pdo = null;
        $stmt = null;

        die();
    }
    catch (PDOException $e) 
    {
        die("Query failed: " . $e->getMessage());
    }
}
else
{
    header("Location: ../index.php");
    die();
}