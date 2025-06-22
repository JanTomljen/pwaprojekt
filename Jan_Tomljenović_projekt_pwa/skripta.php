<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $category = isset($_POST['category']) ? $_POST['category'] : 'Nepoznata kategorija';
    $title = isset($_POST['title']) ? $_POST['title'] : 'Nema naslova';
    $about = isset($_POST['about']) ? $_POST['about'] : 'Nema kratkog sadržaja';
    $content = isset($_POST['content']) ? $_POST['content'] : 'Nema sadržaja vijesti';
    $archive_status = isset($_POST['archive']) ? 1 : 0; 
    $datum = date('Y-m-d H:i:s'); 

    $image_name_for_db = 'placeholder.jpg';

    if (isset($_FILES['pphoto']) && $_FILES['pphoto']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES['pphoto']['name']); 
        $targetFilePath = UPLPATH . $fileName; 
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION)); 

     
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

        if (in_array($fileType, $allowTypes)) {
          
            if (move_uploaded_file($_FILES['pphoto']['tmp_name'], $targetFilePath)) {
                $image_name_for_db = $fileName; 
            } else {
                echo "<p class='error-message'>Greška pri premještanju datoteke. Provjerite dozvole za '" . UPLPATH . "' direktorij.</p>";
            }
        } else {
            echo "<p class='error-message'>Dozvoljeni su samo JPG, JPEG, PNG i GIF formati za sliku.</p>";
        }
    } else if (isset($_FILES['pphoto']) && $_FILES['pphoto']['error'] !== UPLOAD_ERR_NO_FILE) {
       
        echo "<p class='error-message'>Došlo je do greške prilikom uploada slike. Kod greške: " . $_FILES['pphoto']['error'] . "</p>";
    }
    
    $query = "INSERT INTO vijesti (datum, naslov, sazetak, tekst, slika, kategorija, arhiva) VALUES (?, ?, ?, ?, ?, ?, ?)";

   
    $stmt = mysqli_stmt_init($dbc);

    if (mysqli_stmt_prepare($stmt, $query)) {
       
        mysqli_stmt_bind_param($stmt, 'ssssssi', $datum, $title, $about, $content, $image_name_for_db, $category, $archive_status);

     
        if (mysqli_stmt_execute($stmt)) {
            ?>
            <!DOCTYPE html>
            <html lang="hr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Potvrda unosa</title>
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
                            <li><a href="administracija.php">PRIJAVA</a></li>
                             <li><a href="registracija.php">PRIJAVA/ADMIN</a></li>
                        </ul>
                    </nav>
                </header>

                <main class="main-content">
                    <section class="category-section">
                        <h2 class="section-title">VIJEST USPJEŠNO SPREMLJENA!</h2>
                        <div class="card-grid single-column-grid">
                            <article class="card">
                                <a href="clanak.php?id=<?php echo mysqli_insert_id($dbc); ?>">
                                    <figure class="card-image-wrapper">
                                        <img src="<?php echo UPLPATH . htmlspecialchars($image_name_for_db); ?>" alt="Slika vijesti">
                                    </figure>
                                    <div class="card-content">
                                        <h3><?php echo htmlspecialchars($title); ?></h3>
                                        <p><?php echo htmlspecialchars($about); ?></p>
                                        <p>Kategorija: <?php echo htmlspecialchars($category); ?></p>
                                        <p>Arhivirano: <?php echo $archive_status == 1 ? 'DA' : 'NE'; ?></p>
                                        <p><a href="index.php">Pogledajte sve vijesti</a></p>
                                    </div>
                                </a>
                            </article>
                        </div>
                    </section>
                </main>
                <footer class="main-footer">
                    <p>&copy; <?php echo date('Y'); ?> Jan Tomljenović - 0246118040</p>
                </footer>
            </body>
            </html>
            <?php
        } else {
            echo "<p class='error-message'>Greška pri spremanju vijesti: " . mysqli_error($dbc) . "</p>";
        }
    } else {
        echo "<p class='error-message'>Greška u pripremi SQL upita: " . mysqli_error($dbc) . "</p>";
    }

    mysqli_stmt_close($stmt); 

} else {
    echo "<p class='error-message'>Nije primljen POST zahtjev. Molimo ispunite formu na <a href='html/unos.html'>unos.html</a>.</p>";
}


mysqli_close($dbc);
?>