<?php
    declare(strict_types = 1);
?>

<?php function drawHeader(Session $session, string $title) : void { ?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <title>Tickets Management</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/utils.css">
        <link rel="stylesheet" href="../css/authentication.css">
        <link rel="stylesheet" href="../css/common.css">
        <link rel="stylesheet" href="../css/dashboard.css">
        <link rel="stylesheet" href="../css/faqs_management.css">
        <link rel="stylesheet" href="../css/new_ticket.css">
        <link rel="stylesheet" href="../css/profile.css">
        <link rel="stylesheet" href="../css/tickets.css">   
        <link rel="stylesheet" href="../css/ticket.css">
        <script src="../javascript/sidebar.js" defer></script>
        <script src="../javascript/common.js" defer></script>
        <script src="../javascript/filters.js" defer></script>
        <script src="../javascript/ticket.js" defer></script>
        <script src="../javascript/tags.js" defer></script>
    </head>
    <body <?php if (!$session->isLoggedIn()) echo "id=\"authentication\""?>>
        <?php if ($session->isLoggedIn()) { ?>
        <header id="main-header">
            <h1><?=$title?></h1>
            <form action="../actions/action_logout.php" method="post" class="logout">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <a href="../pages/profile.php"><?php
                    echo htmlentities($session->getName());
                    if ($session->isAdmin()) echo ' (Admin)';
                    else if ($session->isAgent()) echo ' (Agent)';
                    else echo ' (Client)';
                ?></a>
                <button type="submit">Logout</button>
            </form>
        </header>
        <nav id="menu">
            <ul>
                <li class="text-menu"><a href="../pages/dashboard.php">Dashboard</a></li>
                <li class="img-menu"><a href="../pages/dashboard.php"><img src="../assets/dashboard.png" alt="Dashboard Icon"></a></li>
                <li class="text-menu"><a href="../pages/new_ticket.php">New Ticket</a></li>
                <li class="img-menu"><a href="../pages/new_ticket.php"><img src="../assets/new_ticket.png" alt="New Ticket Icon"></a></li>
                <li class="text-menu"><a href="../pages/tickets.php">Tickets</a></li>
                <li class="img-menu"><a href="../pages/tickets.php"><img src="../assets/tickets.png" alt="Ticket Icon"></a></li>
                <li class="text-menu"><a href="../pages/faqs.php">FAQ</a></li>
                <li class="img-menu"><a href="../pages/faqs.php"><img src="../assets/faq.png" alt="Faq Icon"></a></li>
                <?php if ($session->isAdmin()) { ?>
                <li class="text-menu"><a href="../pages/management.php">Management</a></li>
                <li class="img-menu"><a href="../pages/management.php"><img src="../assets/management.png" alt="Management Icon"></a></li>
                <?php } ?>
            </ul>
        </nav>
        <?php } else { ?>
        <header id="authentication-header">
            <h1><?=$title?></h1>
        </header>
        <?php } ?>
        <?php if (count($session->getMessages()) > 0) { ?>
        <section id="messages">
            <?php foreach ($session->getMessages() as $message) { ?>
            <article class="<?php if ($message['type']) echo 'success'; else echo 'error' ?>"><?=$message['text']?></article>
            <?php } ?>
        </section>
        <?php } ?>
<?php } ?>

<?php function drawFooter() : void { ?>
    </body>
</html>
<?php } ?>
