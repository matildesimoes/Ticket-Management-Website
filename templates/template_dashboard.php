<?php
    declare(strict_types = 1);
?>

<?php function drawDashboard(array $statuses, array $count) : void { ?>
    <main>
        <section id="dashboard">
            <h2>Dashboard</h2>
            <?php for ($i = 0; $i < count($statuses) && $i < count($count); $i++) { $status = $statuses[$i]; ?>
            <article class="card">
                <h3><?=htmlentities($status->getName())?></h3>
                <p>You have <?=$count[$i]?> <?=htmlentities(strtolower($status->getName()))?> tickets!</p>
            </article>
            <?php } ?>
        </section>
    </main>
<?php } ?>
