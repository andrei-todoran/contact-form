
<?php
    session_start();

    require_once('conf/config.php');
    require_once('db.class.php');
    require_once('user.class.php');
    require_once('email.class.php');

    // CSRF token
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['token'];

    $db = new DB(DB_HOST, DB_DATABASE, DB_USER, DB_PASSWORD);

    if ($_SERVER["REQUEST_METHOD"] == 'POST') {
        $token = isset($_SESSION['token']) ? $_SESSION['token'] : "";
        if ($token && $_POST['token'] === $token) {
            unset($_SESSION['token']);
        } else {
            // invalid token - do not process the form
            $referer = $_SERVER['HTTP_REFERER'];
            header("Location: $referer");
        }

        $emailSent = false;
        $errors = [];
        $name = $email = $phone = $message = '';

        // validate name
        if (isset($_POST['name'])) {
            $name = trim(htmlspecialchars($_POST['name']));
        }

        if (empty($name)) {
            $errors[] = 'Please enter your name';
        }

        // validate email
        if (isset($_POST['email'])) {
            $email = trim(htmlspecialchars($_POST['email']));
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        }

        // validate phone
        if (isset($_POST['phone'])) {
            $phone = trim(htmlspecialchars($_POST['phone']));
        }

        if (empty($phone)) {
            $errors[] = 'Please enter your phone number';
        }

        // validate message
        if (isset($_POST['message'])) {
            $message = trim(htmlspecialchars($_POST['message']));
        }

        if (strlen($message) < 26) {
            $errors[] = 'Please enter a longer message';
        }

        $newsletter = 0;
        if (isset($_POST['newsletter']) && $_POST['newsletter'] == 'on') {
            $newsletter = 1;
        }

        if (count($errors) > 0) {
            // save the form data and error messages
            $_SESSION['contact_form']['errors'] = implode('<br>', $errors);
            $_SESSION['contact_form']['name'] = $name;
            $_SESSION['contact_form']['email'] = $email;
            $_SESSION['contact_form']['phone'] = $phone;
            $_SESSION['contact_form']['newsletter'] = $newsletter;
        } else {
            $user = new User();
            $user->setDb($db);

            if ($user->exists($email)) {
                $updated = $user->update($name, $email, $phone, $newsletter);
            } else {
                $userID = $user->create($name, $email, $phone, $newsletter);
            }

            // send email to admin
            $emailClass = new Email();
            $emailClass->setToEmail(CONTACT_FORM_EMAIL);
            $emailClass->setToName(CONTACT_FORM_NAME);
            $emailClass->setFromName($name);
            $emailClass->setFromEmail($email);
            $emailClass->setSubject('Message from a customer');
            $emailClass->setMessage([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'ip_address' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''),
                'date' => date('l d F Y H:i'),
                'message' => $message
            ]);

            $_SESSION['contact_form']['emailSent'] = $emailClass->sendMail();
            if (! $emailSent) {
                $errors[] = 'There was an error sending your message. Please try again later';
            }
        }

        // redirect back to the form after processing
        $referer = $_SERVER['HTTP_REFERER'];
        header("Location: $referer");
    }
    else {
        if (isset($_SESSION['contact_form'])) {
            $formData = $_SESSION['contact_form'];
        }

        unset($_SESSION['contact_form']);
    }

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/styles.css?v=1.0">
</head>

<body>

<div class="container">
    <h2>Contact Us</h2>
    <?php
    if (isset($formData['errors'])) {
        echo '<div class="error">' . $formData['errors'] . '</div>';
    }
    ?>
    <?php
    if (isset($formData['emailSent']) && $formData['emailSent']) {
        echo '<div class="message">Thank you for your message. We will be in touch soon</div>';
    }
    ?>
    <form method="post">
        <input type="hidden" name="token" value="<?php echo $token ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo (isset($formData['name']) ? $formData['name'] : '') ?>" required="required" placeholder="">
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo (isset($formData['phone']) ? $formData['phone'] : '') ?>" required="required"  placeholder="">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?php echo (isset($formData['email']) ? $formData['email'] : '') ?>" required="required"  placeholder="">
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" required="required" minlength="26" rows="10"><?php echo (isset($formData['message']) ? $formData['message'] : '') ?></textarea>
        </div>

        <div class="form-group">
            <label class="inline" for="message">Subscribe to newsletter</label>
            <input type="checkbox" id="newsletter" name="newsletter">
        </div>

        <div class="form-group">
            <input type="submit" value="Send">
        </div>
    </form>
</div>
<script src="js/contact.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
</body>
</html>
