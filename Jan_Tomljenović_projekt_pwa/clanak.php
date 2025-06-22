<?php
include 'config.php';

$id = 0; 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id']; 
}

if ($id == 0) {
    header("Location: index.php"); 
    exit();
}

$sql = "SELECT naslov, sazetak, tekst, slika, kategorija, datum FROM vijesti WHERE id = ?";
$stmt = mysqli_stmt_init($dbc);

$row = null; 

if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id); 
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt); 

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result); 
    } else {
        header("Location: index.php");
        exit();
    }
    mysqli_stmt_close($stmt); 
} else {
    die("Greška u pripremi upita za članak: " . mysqli_error($dbc));
}

$formattedDate = date('d.m.Y.', strtotime($row['datum']));
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['naslov']); ?> - Sopitas.com </title>
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

    <main class="article-content">
        <article>
            <header class="article-header-info">
                <p class="category"><?php echo strtoupper(htmlspecialchars($row['kategorija'])); ?></p>
                <h1 class="title"><?php echo htmlspecialchars($row['naslov']); ?></h1>
                <p class="author-date">AUTOR: Administrator | OBJAVLJENO: <?php echo $formattedDate; ?></p>
            </header>

            <figure class="article-header-image">
                <img src="<?php echo UPLPATH . htmlspecialchars($row['slika']); ?>" alt="Slika za vijest: <?php echo htmlspecialchars($row['naslov']); ?>">
            </figure>

            <section class="about">
                <p>
                    <?php echo nl2br(htmlspecialchars($row['sazetak'])); ?>
                </p>
            </section>

            <section class="sadrzaj">
                <p>
                    <?php echo nl2br(htmlspecialchars($row['tekst'])); ?>
                </p>
            </section>
        </article>
    </main>

    <footer class="main-footer">
        <p>&copy; <?php echo date('Y'); ?> Jan Tomljenović - 0246118040</p>
    </footer>

</body>
</html>
<?php
mysqli_close($dbc);
?>