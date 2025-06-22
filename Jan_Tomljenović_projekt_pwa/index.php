<?php
session_start(); 
include 'config.php'; 

function getTranslatedCategoryName($category_id) {
    if ($category_id === 'glazba') {
        return 'MÚSICA';
    } elseif ($category_id === 'sport') {
        return 'DEPORTES';
    } else {
        return strtoupper($category_id);
    }
}

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Početna - Sopitas.com</title>
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

    <main class="main-content">
        <?php
        $queryCategories = "SELECT DISTINCT kategorija FROM vijesti WHERE arhiva = 0 ORDER BY CASE kategorija WHEN 'glazba' THEN 1 WHEN 'sport' THEN 2 ELSE 3 END, kategorija ASC";
        $resultCategories = mysqli_query($dbc, $queryCategories);

        if ($resultCategories && mysqli_num_rows($resultCategories) > 0) {
            while ($categoryRow = mysqli_fetch_assoc($resultCategories)) {
                $current_category_id = $categoryRow['kategorija'];
                $display_category_title = getTranslatedCategoryName($current_category_id);

                $catClass = '';
                if ($current_category_id === 'glazba') {
                    $catClass = 'music-category';
                } elseif ($current_category_id === 'sport') {
                    $catClass = 'sport-category';
                }
                ?>
                <section class="category-section <?php echo $catClass; ?>">
                    <h2 class="section-title"><?php echo htmlspecialchars($display_category_title); ?></h2>
                    <div class="card-grid ">
                        <?php
                    
                        $queryNews = "SELECT id, naslov, sazetak, slika, datum
                                      FROM vijesti
                                      WHERE kategorija = ? AND arhiva = 0
                                      ORDER BY datum DESC
                                      LIMIT 4"; 

                        $stmtNews = mysqli_stmt_init($dbc);

                        if (mysqli_stmt_prepare($stmtNews, $queryNews)) {
                            mysqli_stmt_bind_param($stmtNews, 's', $current_category_id);
                            mysqli_stmt_execute($stmtNews);
                            $resultNews = mysqli_stmt_get_result($stmtNews);

                            if ($resultNews && mysqli_num_rows($resultNews) > 0):
                                while ($row = mysqli_fetch_assoc($resultNews)):
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
                            mysqli_stmt_close($stmtNews);
                        } else {
                            echo "<p>Greška u pripremi upita za vijesti: " . mysqli_error($dbc) . "</p>";
                        }
                        ?>
                    </div>
                    <p class="category-link"><a href="kategorija.php?id=<?php echo htmlspecialchars($current_category_id); ?>">Više vijesti iz kategorije <?php echo htmlspecialchars($display_category_title); ?> »</a></p>
                </section>
                <?php
            } 
        } else {
            echo "<p>Nema dostupnih kategorija s vijestima.</p>";
        }
        ?>
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