<?php
    declare(strict_types = 1);
?>

<?php function drawTickets(?array $tickets, int $limit, int $offset, string $after, string $before, ?Status $status, ?Priority $priority, ?Department $department, ?User $agent, ?Tag $tag, array $statuses, array $priorities, array $departments, array $agents, array $tags) : void { ?>
    <main id="tickets-page">
        <section id="tickets">
            <h2>Tickets</h2>
            <?php for ($i = 0; $i < $limit && $i + $offset < count($tickets); $i++) { $ticket = $tickets[$i + $offset]; ?>
                <article class="ticket">
                    <a href="../pages/ticket.php?id=<?=$ticket->getId()?>">
                        <header class="author">
                            <img src="<?php echo ('../profile_photos/' . $ticket->getAuthor()->getPhoto()) ?>" alt="Profile Photo">
                            <h3><?=htmlentities($ticket->getAuthor()->getName())?></h3>
                        </header>
                        <h4><?=htmlentities($ticket->getTitle())?></h4>
                        <?php if ($ticket->getStatus()) { ?>
                        <p class="status <?=htmlentities(strtolower($ticket->getStatus()->getName()))?>"><?=htmlentities($ticket->getStatus()->getName())?></p>
                        <?php } else { ?>
                        <p class="status none">None</p>
                        <?php } ?>
                        <p class="date-opened"><?=$ticket->getDateOpened()?></p>
                        <?php if ($ticket->getPriority()) { ?>
                        <p class="priority <?=htmlentities(strtolower($ticket->getPriority()->getName()))?>"><?=htmlentities($ticket->getPriority()->getName())?></p>
                        <?php } else { ?>
                        <p class="priority none">None</p>
                        <?php } ?>
                    </a>
                </article>
            <?php } ?>
        </section>
        <div id="paging">
            <?php if ($offset > 0) { ?>
            <form action="../pages/tickets.php" method="post" class="previous">
                <input type="hidden" name="after" <?php if ($after !== '') echo "value=$after"; ?>>
                <input type="hidden" name="before" <?php if ($before !== '') echo "value=$before"; ?>>
                <input type="hidden" name="status" <?php if ($status) echo 'value=' . $status->getId(); ?>>
                <input type="hidden" name="priority" <?php if ($priority) echo 'value=' . $priority->getId(); ?>>
                <input type="hidden" name="department" <?php if ($department) echo 'value=' . $department->getId(); ?>>
                <input type="hidden" name="agent" <?php if ($agent) echo 'value=' . $agent->getId(); ?>>
                <input type="hidden" name="tag" <?php if ($tag) echo 'value=' . $tag->getId(); ?>>
                <input type="hidden" name="offset" value="<?=$offset - $limit?>">
                <button id="previous" type="submit">Previous</button>
            </form>
            <?php } ?>
            <?php if ($offset + $limit < count($tickets)) { ?>
            <form action="../pages/tickets.php" method="post" class="next">
                <input type="hidden" name="after" <?php if ($after !== '') echo "value=$after"; ?>>
                <input type="hidden" name="before" <?php if ($before !== '') echo "value=$before"; ?>>
                <input type="hidden" name="status" <?php if ($status) echo 'value=' . $status->getId(); ?>>
                <input type="hidden" name="priority" <?php if ($priority) echo 'value=' . $priority->getId(); ?>>
                <input type="hidden" name="department" <?php if ($department) echo 'value=' . $department->getId(); ?>>
                <input type="hidden" name="agent" <?php if ($agent) echo 'value=' . $agent->getId(); ?>>
                <input type="hidden" name="tag" <?php if ($tag) echo 'value=' . $tag->getId(); ?>>
                <input type="hidden" name="offset" value="<?=$offset + $limit?>">
                <button id="next" type="submit">Next</button>
            </form>
            <?php } ?>
        </div>
        <img id="controller" src="../assets/filters.png" alt="Filters Icon">
        <form action="../pages/tickets.php" method="post" class="filters">
            <h3>Filters</h3>
            <label for="after">After</label>
            <input id="after" type="date" name="after" <?php if ($after !== '') echo "value=$after"; ?>>
            <label for="before">Before</label>
            <input id="before" type="date" name="before" <?php if ($before !== '') echo "value=$before"; ?>>
            <?php drawFilter('Status', $status, $statuses); ?>
            <?php drawFilter('Priority', $priority, $priorities); ?>
            <?php drawFilter('Department', $department, $departments); ?>
            <?php drawFilter('Agent', $agent, $agents); ?>
            <?php drawFilter('Tag', $tag, $tags); ?>
            <button id="filter" type="submit">Filter</button>
        </form>
    </main>
<?php } ?>

<?php function drawFilter(string $name, $entity, array $entities) : void { ?>
    <label for="<?=strtolower($name)?>"><?=$name?></label>
    <select class="select-options" id="<?=strtolower($name)?>" name="<?=strtolower($name)?>">
        <?php if (!$entity) { ?>
            <option value="0">All</option>
            <?php foreach ($entities as $e) { ?>
                <option value="<?=$e->getId()?>"><?=htmlentities($e->getName())?></option>
            <?php } ?>
        <?php } else { ?>
            <option value="<?=$entity->getId()?>"><?=htmlentities($entity->getName())?></option>
            <option value="0">All</option>
            <?php foreach ($entities as $e) { ?>
                <?php if ($e->getId() !== $entity->getId()) { ?>
                    <option value="<?=$e->getId()?>"><?=htmlentities($e->getName())?></option>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </select>
<?php } ?>
