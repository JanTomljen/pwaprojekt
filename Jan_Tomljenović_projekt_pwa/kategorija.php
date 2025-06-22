<?php
include 'config.php'; 

$category_id = isset($_GET['id'])
    ? strtolower(mysqli_real_escape_string($dbc, $_GET['id']))
    : '';

$valid_categories = ['glazba', 'sport', 'ostalo']; 
if (!in_array($category_id, $valid_categories)) {
    header("Location: index.php");
    exit();
}

$display_category_title = '';
if ($category_id === 'glazba') {
    $display_category_title = 'MÚSICA';
} elseif ($category_id === 'sport') {
    $display_category_title = 'DEPORTES';
} else {
    $display_category_title = strtoupper($category_id);
}

$catClass = '';
if ($category_id === 'glazba') {
    $catClass = 'music-category';
} elseif ($category_id === 'sport') {
    $catClass = 'sport-category';
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $display_category_title; ?> - Sopitas.com </title>
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
                <li><a href="administracija.php">PRIJAVA/ADMIN </a></li>
                 <li><a href="registracija.php">REGISTRACIJA</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <section class="category-section <?php echo $catClass; ?>">
            <h2 class="section-title"><?php echo $display_category_title; ?></h2>
            <div class="card-grid">
                <?php
                $query = "SELECT id, naslov, sazetak, slika, datum
                          FROM vijesti
                          WHERE kategorija = ? AND arhiva = 0
                          ORDER BY datum DESC";

                $stmt = mysqli_stmt_init($dbc);

                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, 's', $category_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if ($result && mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)): 
                ?>
                <article class="card">
                    <a href="clanak.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                        <figure class="card-image-wrapper">
                            <img src="<?php echo UPLPATH . htmlspecialchars($row['slika']); ?>" alt="Slika za vijest: <?php echo htmlspecialchars($row['naslov']); ?>">
                        </figure>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($row['naslov']); ?></h3>
                            <time datetime="<?php echo date('Y-m-d', strtotime($row['datum'])); ?>">
                                <?php echo date('d.m.Y.', strtotime($row['datum'])); ?>
                            </time>
                        </div>
                    </a>
                </article>
                <?php
                        endwhile;
                    else:
                        echo "<p>Nema objavljenih vijesti u ovoj kategoriji.</p>";
                    endif;
                    mysqli_stmt_close($stmt); 
                } else {
                    echo "<p>Greška u pripremi upita: " . mysqli_error($dbc) . "</p>";
                }
                ?>
            </div>
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