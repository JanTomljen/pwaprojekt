<?php
session_start();
include 'config.php';


$uspjesnaPrijava = false;
$imeKorisnika = '';
$porukaPrijava = '';
$porukaAkcija = '';


if (isset($_POST['prijava'])) {
    $prijavaImeKorisnika = $_POST['username'];
    $prijavaLozinkaKorisnika = $_POST['lozinka'];

    $sql = "SELECT korisnicko_ime, lozinka FROM korisnik WHERE korisnicko_ime = ?";
    $stmt = mysqli_stmt_init($dbc);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $prijavaImeKorisnika);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
    }

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $imeKorisnika, $lozinkaKorisnika);
        mysqli_stmt_fetch($stmt);

        if (password_verify($prijavaLozinkaKorisnika, $lozinkaKorisnika)) {
            $uspjesnaPrijava = true;
            $_SESSION['username'] = $imeKorisnika;
            $_SESSION['prijavljen_kao_urednik'] = true; 
        } else {
            $uspjesnaPrijava = false;
            $porukaPrijava = 'Korisničko ime i/ili lozinka nisu ispravni. Molimo, <a href="registracija.php">registrirajte se</a> ako nemate račun.';
        }
    } else {
        $uspjesnaPrijava = false;
        $porukaPrijava = 'Korisničko ime i/ili lozinka nisu ispravni. Molimo, <a href="registracija.php">registrirajte se</a> ako nemate račun.';
    }
    mysqli_stmt_close($stmt);
}


$prijavljenZaUredjivanje = (isset($_SESSION['prijavljen_kao_urednik']) && $_SESSION['prijavljen_kao_urednik'] === true);


if ($prijavljenZaUredjivanje) { 
    if (isset($_GET['message'])) {
        $porukaAkcija = htmlspecialchars($_GET['message']);
    }

    $show_edit_form = false;
    if (isset($_GET['action']) && $_GET['action'] === 'izbrisi' && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $query = "DELETE FROM vijesti WHERE id=?";
        $stmt = mysqli_stmt_init($dbc);

        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (mysqli_stmt_execute($stmt)) {
                $porukaAkcija = "Vijest je uspješno izbrisana.";
            } else {
                $porukaAkcija = "Greška prilikom brisanja vijesti: " . mysqli_error($dbc);
            }
        } else {
            $porukaAkcija = "Greška u pripremi upita za brisanje: " . mysqli_error($dbc);
        }
        mysqli_stmt_close($stmt);
        header("Location: administracija.php?message=" . urlencode($porukaAkcija));
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = $_POST['id'];
        $naslov = mysqli_real_escape_string($dbc, $_POST['naslov']);
        $sazetak = mysqli_real_escape_string($dbc, $_POST['sazetak']);
        $tekst = mysqli_real_escape_string($dbc, $_POST['tekst']);
        $kategorija = mysqli_real_escape_string($dbc, $_POST['kategorija']);
        $arhiva = isset($_POST['arhiva']) ? 1 : 0;
        $slika = '';

        if (isset($_FILES['slika']) && $_FILES['slika']['error'] == UPLOAD_ERR_OK) {
            $file_name = $_FILES['slika']['name'];
            $temp_name = $_FILES['slika']['tmp_name'];
            $target_dir = UPLPATH;
            $target_file = $target_dir . basename($file_name);

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $valid_extensions = array("jpg", "jpeg", "png", "gif");
            $max_size = 2 * 1024 * 1024;

            if (!in_array($imageFileType, $valid_extensions)) {
                $porukaAkcija = "Greška: Dozvoljeni su samo JPG, JPEG, PNG & GIF fajlovi.";
            } elseif ($_FILES["slika"]["size"] > $max_size) {
                $porukaAkcija = "Greška: Datoteka je prevelika, maksimalno 2MB.";
            } else {
                if (move_uploaded_file($temp_name, $target_file)) {
                    $slika = $file_name;
                } else {
                    $porukaAkcija = "Greška prilikom upload-a slike.";
                }
            }
        } else {
            $query_old_image = "SELECT slika FROM vijesti WHERE id = ?";
            $stmt_old_image = mysqli_stmt_init($dbc);
            if (mysqli_stmt_prepare($stmt_old_image, $query_old_image)) {
                mysqli_stmt_bind_param($stmt_old_image, 'i', $id);
                mysqli_stmt_execute($stmt_old_image);
                mysqli_stmt_bind_result($stmt_old_image, $slika);
                mysqli_stmt_fetch($stmt_old_image);
                mysqli_stmt_close($stmt_old_image);
            }
        }

        if (empty($porukaAkcija)) {
            $query_update = "UPDATE vijesti SET naslov=?, sazetak=?, tekst=?, slika=?, kategorija=?, arhiva=?, datum=NOW() WHERE id=?";
            $stmt_update = mysqli_stmt_init($dbc);

            if (mysqli_stmt_prepare($stmt_update, $query_update)) {
                mysqli_stmt_bind_param($stmt_update, 'sssssii', $naslov, $sazetak, $tekst, $slika, $kategorija, $arhiva, $id);
                if (mysqli_stmt_execute($stmt_update)) {
                    $porukaAkcija = "Vijest je uspješno ažurirana.";
                } else {
                    $porukaAkcija = "Greška prilikom ažuriranja vijesti: " . mysqli_error($dbc);
                }
            } else {
                $porukaAkcija = "Greška u pripremi upita za ažuriranje: " . mysqli_error($dbc);
            }
            mysqli_stmt_close($stmt_update);
        }

        header("Location: administracija.php?message=" . urlencode($porukaAkcija));
        exit();
    }

    $edit_naslov = $edit_sazetak = $edit_tekst = $edit_kategorija = $edit_slika = '';
    $edit_arhiva = 0;
    $edit_current_image_path = '';

    if (isset($_GET['action']) && $_GET['action'] === 'uredi' && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $show_edit_form = true;
        $id_to_edit = $_GET['id'];

        $query_select_edit = "SELECT naslov, sazetak, tekst, slika, kategorija, arhiva FROM vijesti WHERE id=?";
        $stmt_select_edit = mysqli_stmt_init($dbc);

        if (mysqli_stmt_prepare($stmt_select_edit, $query_select_edit)) {
            mysqli_stmt_bind_param($stmt_select_edit, 'i', $id_to_edit);
            mysqli_stmt_execute($stmt_select_edit);
            $result_select_edit = mysqli_stmt_get_result($stmt_select_edit);

            if ($row_edit = mysqli_fetch_assoc($result_select_edit)) {
                $edit_naslov = htmlspecialchars($row_edit['naslov']);
                $edit_sazetak = htmlspecialchars($row_edit['sazetak']);
                $edit_tekst = htmlspecialchars($row_edit['tekst']);
                $edit_slika = htmlspecialchars($row_edit['slika']);
                $edit_kategorija = htmlspecialchars($row_edit['kategorija']);
                $edit_arhiva = $row_edit['arhiva'];
                $edit_current_image_path = UPLPATH . $edit_slika;
            } else {

                $show_edit_form = false;
                header("Location: administracija.php?message=" . urlencode("Vijest za uređivanje nije pronađena."));
                exit();
            }
        } else {
            die("Greška u pripremi upita za dohvaćanje vijesti za uređivanje: " . mysqli_error($dbc));
        }
        mysqli_stmt_close($stmt_select_edit);
    }
}


$vijesti = [];
if ($prijavljenZaUredjivanje && !$show_edit_form) { 
    $query_vijesti = "SELECT id, naslov, datum, kategorija, arhiva FROM vijesti ORDER BY datum DESC";
    $result_vijesti = mysqli_query($dbc, $query_vijesti);
    if ($result_vijesti) {
        while ($row = mysqli_fetch_assoc($result_vijesti)) {
            $vijesti[] = $row;
        }
    } else {
        $porukaAkcija = "Greška prilikom dohvaćanja vijesti iz baze: " . mysqli_error($dbc);
    }
}


?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administracija - Sopitas.com</title>
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

    <main class="main-prijava-admin">
        <section role="main">
            <?php

            if (!empty($porukaAkcija)) {
                $messageClass = (strpos($porukaAkcija, 'Greška') !== false) ? 'error-message' : 'success-message';
                echo '<p class="' . $messageClass . '">' . $porukaAkcija . '</p>';
            }

            if ($prijavljenZaUredjivanje) {
                if ($show_edit_form) {
            ?>
                    <h2 class="section-title">UREDI VIJEST</h2>
                    <form enctype="multipart/form-data" action="administracija.php" method="POST"> <div class="form-item">
                            <label for="naslov">Naslov vijesti:</label>
                            <div class="form-field">
                                <input type="text" name="naslov" id="naslov" class="form-field-textual" value="<?php echo $edit_naslov; ?>" required>
                            </div>
                        </div>
                        <div class="form-item">
                            <label for="sazetak">Kratak sadržaj vijesti (do 100 znakova):</label>
                            <div class="form-field">
                                <textarea name="sazetak" id="sazetak" cols="30" rows="5" class="form-field-textual" maxlength="100" required><?php echo $edit_sazetak; ?></textarea>
                            </div>
                        </div>
                        <div class="form-item">
                            <label for="tekst">Sadržaj vijesti:</label>
                            <div class="form-field">
                                <textarea name="tekst" id="tekst" cols="30" rows="10" class="form-field-textual" required><?php echo $edit_tekst; ?></textarea>
                            </div>
                        </div>
                        <div class="form-item">
                            <label for="slika">Slika:</label>
                            <div class="form-field">
                                <input type="file" accept="image/jpg,image/gif,image/png,image/jpeg" id="slika" name="slika">
                                <?php if (!empty($edit_slika)): ?>
                                    <br><img src="<?php echo $edit_current_image_path; ?>" width="100px" alt="Trenutna slika">
                                    <p>Trenutna slika: <?php echo htmlspecialchars($edit_slika); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-item">
                            <label for="kategorija">Kategorija vijesti:</label>
                            <div class="form-field">
                                <select name="kategorija" id="kategorija" class="form-field-textual" required>
                                    <option value="glazba" <?php if($edit_kategorija == 'glazba') echo 'selected'; ?>>Glazba</option>
                                    <option value="sport" <?php if($edit_kategorija == 'sport') echo 'selected'; ?>>Sport</option>
                                    <option value="ostalo" <?php if($edit_kategorija == 'ostalo') echo 'selected'; ?>>Ostalo</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-item">
                            <label>Spremiti u arhivu:
                                <div class="form-field">
                                    <input type="checkbox" name="arhiva" <?php if($edit_arhiva == 1) echo 'checked'; ?>>
                                </div>
                            </label>
                        </div>
                        <div class="form-item form-buttons">
                            <input type="hidden" name="id" value="<?php echo $id_to_edit; ?>"> <button type="submit" name="update" value="Ažuriraj">Ažuriraj</button>
                            <button type="reset" value="Poništi">Poništi</button>
                        </div>
                    </form>
            <?php
                } else {
            ?>
                    <h2 class="section-title">ADMINISTRACIJA VIJESTI</h2>
                    <p>Dobrodošli, <?php echo htmlspecialchars($_SESSION['username']); ?>! Ovo je administracijski panel.</p>
                    <div class="admin-buttons-wrapper">
                        <a href="logout.php" class="logout-button">Odjavi se</a>
                        <a href="html/unos.html" class="add-article-button">Unesi novu vijest</a>
                    </div>

                    <hr>
                    <h3>Postojeće vijesti:</h3>
                    <?php if (empty($vijesti)): ?>
                        <p class="no-news-message">Nema vijesti za administraciju.</p>
                    <?php else: ?>
                        <table class="tabla_sa_vijestima">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Naslov</th>
                                    <th>Datum</th>
                                    <th>Kategorija</th>
                                    <th>Arhiva</th>
                                    <th colspan="2">Akcije</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vijesti as $vijest): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($vijest['id']); ?></td>
                                        <td><?php echo htmlspecialchars($vijest['naslov']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d.m.Y.', strtotime($vijest['datum']))); ?></td>
                                        <td><?php echo htmlspecialchars($vijest['kategorija']); ?></td>
                                        <td><?php echo $vijest['arhiva'] == 1 ? 'Da' : 'Ne'; ?></td>
                                        <td><a href="administracija.php?action=uredi&id=<?php echo htmlspecialchars($vijest['id']); ?>" class="button uredi">Uredi</a></td>
                                        <td><a href="administracija.php?action=izbrisi&id=<?php echo htmlspecialchars($vijest['id']); ?>" class="button izbrisi" onclick="return confirm('Jeste li sigurni da želite izbrisati ovu vijest?');">Izbriši</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
            <?php
                }
            } else { 
            ?>
                <h2 class="section-title">PRIJAVA U ADMINISTRACIJU</h2>
                <form action="administracija.php" method="POST">
                    <div class="form-item">
                        <label for="username_login">Korisničko ime:</label>
                        <div class="form-field">
                            <input type="text" name="username" id="username_login" class="form-field-textual" required>
                        </div>
                    </div>
                    <div class="form-item">
                        <label for="lozinka_login">Lozinka:</label>
                        <div class="form-field">
                            <input type="password" name="lozinka" id="lozinka_login" class="form-field-textual" required>
                        </div>
                    </div>
                    <div class="form-item form-buttons">
                        <button type="submit" name="prijava" value="Prijava">Prijava</button>
                    </div>
                    <?php if (!empty($porukaPrijava)): ?>
                        <p class="error-message"><?php echo $porukaPrijava; ?></p>
                    <?php endif; ?>
                </form>
            <?php
            }
            ?>
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