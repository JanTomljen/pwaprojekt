<?php
session_start();
include 'config.php';

$message = '';

if (isset($_POST['registracija'])) {
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $username = $_POST['username'];
    $lozinka = $_POST['lozinka'];
    $ponovljenaLozinka = $_POST['ponovljena_lozinka'];
   

    if ($lozinka !== $ponovljenaLozinka) {
        $message = '<p class="error-message">Lozinke se ne podudaraju!</p>';
    } else {
        $sql_check_username = "SELECT korisnicko_ime FROM korisnik WHERE korisnicko_ime = ?";
        $stmt_check = mysqli_stmt_init($dbc);
        if (mysqli_stmt_prepare($stmt_check, $sql_check_username)) {
            mysqli_stmt_bind_param($stmt_check, 's', $username);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
        }

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $message = '<p class="error-message">Korisničko ime već postoji!</p>';
        } else {
            $hashed_password = password_hash($lozinka, PASSWORD_DEFAULT);

            $sql_insert = "INSERT INTO korisnik (ime, prezime, korisnicko_ime, lozinka) VALUES (?, ?, ?, ?)";
            $stmt_insert = mysqli_stmt_init($dbc);

            if (mysqli_stmt_prepare($stmt_insert, $sql_insert)) {
                mysqli_stmt_bind_param($stmt_insert, 'ssss', $ime, $prezime, $username, $hashed_password);
                mysqli_stmt_execute($stmt_insert);

                if (mysqli_stmt_affected_rows($stmt_insert) > 0) {
                    $message = '<p class="success-message">Uspješna registracija! Sada se možete <a href="administracija.php">prijaviti</a>.</p>';
                } else {
                    $message = '<p class="error-message">Greška prilikom registracije: ' . mysqli_error($dbc) . '</p>';
                }
            } else {
                $message = '<p class="error-message">Greška u pripremi SQL upita za registraciju.</p>';
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija | Sopitas.com (Klon)</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stil.css">
</head>
<body>

    <header class="main-header">
        <p class="logo">
            <img src="img/logo.png" alt="Sopitas.com Logo">
        </p>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="kategorija.php?id=glazba">MÚSICA</a></li>
                <li><a href="kategorija.php?id=sport">DEPORTES</a></li>
                <li><a href="administracija.php">PRIJAVA/ADMIN</a></li>
                <li><a href="registracija.php">REGISTRACIJA</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-regisracija">
        <section class="registration-form-container">
            <h2 class="section-title">REGISTRACIJA</h2>
            <?php echo $message;  ?>
            <form action="registracija.php" method="POST" class="input-form">

                <div class="form-group-section">
                    <h3 class="group-title">Osobni podaci</h3>
                    <div class="form-item">
                        <label for="ime_reg">Ime:</label>
                        <div class="form-field">
                            <input type="text" name="ime" id="ime_reg" class="form-field-textual" required>
                        </div>
                    </div>
                    <div class="form-item">
                        <label for="prezime_reg">Prezime:</label>
                        <div class="form-field">
                            <input type="text" name="prezime" id="prezime_reg" class="form-field-textual" required>
                        </div>
                    </div>
                </div>

                <div class="form-group-section">
                    <h3 class="group-title">Podaci za prijavu</h3>
                    <div class="form-item">
                        <label for="username_reg">Korisničko ime:</label>
                        <div class="form-field">
                            <input type="text" name="username" id="username_reg" class="form-field-textual" required>
                        </div>
                    </div>
                    <div class="form-item">
                        <label for="lozinka_reg">Lozinka:</label>
                        <div class="form-field">
                            <input type="password" name="lozinka" id="lozinka_reg" class="form-field-textual" required>
                        </div>
                    </div>
                    <div class="form-item">
                        <label for="ponovljena_lozinka_reg">Ponovite lozinku:</label>
                        <div class="form-field">
                            <input type="password" name="ponovljena_lozinka" id="ponovljena_lozinka_reg" class="form-field-textual" required>
                        </div>
                    </div>
                </div>

                <div class="form-item form-buttons">
                    <button type="submit" name="registracija" value="Registracija">Registriraj se</button>
                </div>
            </form>
        </section>
    </main>

    <footer class="main-footer">
        <p>&copy; <?php echo date('Y'); ?> Jan Tomljenović - 0246118040</p>
    </footer>

    <?php
    if (isset($dbc)) {
        mysqli_close($dbc);
    }
    ?>
</body>
</html>