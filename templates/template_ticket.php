<?php
    declare(strict_types = 1);
?>

<?php function drawTicket(Session $session, Ticket $ticket, array $statuses, array $priorities, array $departments, array $agents, array $tags, array $changes, array $messages, array $faqs) : void { ?>
    <main id="ticket-page">
        <section id="ticket-main">
            <article id="ticket-info">
                <?php $paragraphs = explode('\n', $ticket->getDescription()); ?>
                <?php if ($session->getId() === $ticket->getAuthor()->getId()) { ?>
                    <form id="edit-ticket-form" method="post" class="edit-ticket">
                        <input type="hidden" name="id" value="<?=$ticket->getId()?>">
                        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                        <header id="ticket-header">
                            <img src="../assets/message.png" alt="Ticket Icon">
                            <h2><input type="text" id="edit-header" name="title" required value="<?=htmlentities($ticket->getTitle())?>"></h2>
                        </header>
                        <div id="author-edit">
                            <div id="author">
                                <img class="upload-photo-ticket" src="<?php echo ('../profile_photos/' . $ticket->getAuthor()->getPhoto()) ?>" alt="Profile Photo">
                                <h3><?=htmlentities($ticket->getAuthor()->getName())?></h3>
                            </div>
                            <button formaction="../actions/action_edit_ticket.php" id="author-edit-button">Edit</button>
                        </div>
                        <textarea id="description" name="description"><?php foreach ($paragraphs as $paragraph) echo htmlentities($paragraph); ?></textarea>
                        <?php if (trim($ticket->getFilename() ?? '') !== '') { ?>
                            <a href="<?=$ticket->getFilename()?>" download>Download the file here</a>
                        <?php } ?>
                    </form>
                <?php } else { ?>
                    <form id="delete-ticket-form" method="post" class="delete-ticket">
                        <input type="hidden" name="id" value="<?=$ticket->getId()?>">
                        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                        <header id="ticket-header">
                            <img src="../assets/message.png" alt="Ticket Icon">
                            <h2><?=htmlentities($ticket->getTitle())?></h2>
                        </header>
                        <div id="author-delete">
                            <div id="author">
                                <img class="upload-photo" src="<?php echo ('../profile_photos/' . $ticket->getAuthor()->getPhoto()) ?>" alt="Profile Photo">
                                <h3><?=htmlentities($ticket->getAuthor()->getName())?></h3>
                            </div>
                            <button formaction="../actions/action_delete_ticket.php" id="author-delete-button">Delete</button>
                        </div>
                    </form>
                    <?php foreach ($paragraphs as $paragraph) { ?>
                    <p><?=htmlentities($paragraph)?></p>
                    <?php } ?>
                    <?php if (trim($ticket->getFilename() ?? '') !== '') { ?>
                        <a href="<?=$ticket->getFilename()?>" download>Download the file here</a>
                    <?php } ?>
                <?php } ?>
            </article>
            <details id="messageBoard" >
                <summary>Message Board</summary>
                <hr>
                <div id="all-messages">
                <?php foreach ($messages as $message) { ?>
                <article class="<?php if ($message->getAuthor()->getId() === $session->getId()) echo 'self'; else echo 'other'; ?>">
                    <header>
                        <img class="message-photo" src="<?php echo ('../profile_photos/' . $message->getAuthor()->getPhoto()) ?>" alt="Profile Photo">
                        <p><?=$message->getAuthor()->getName()?></p>
                        <p class="message-date"> <?=$message->getDate()?> </p>
                    </header>
                    <p class="message-content"><?=$message->getContent()?></p>
                    <?php if ($session->isAdmin()) { ?>
                        <form action="../actions/action_delete_messsage.php" method="post">
                            <input type="hidden" name="id" value="<?=$message->id?>">
                            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                            <button type="submit" class="delete-message">Delete</button>
                        </form>
                    <?php } ?>
                </article>
                <?php } ?>
                <form action="../actions/action_add_message.php" method="post" class="messageBoard-form">
                    <input type="hidden" name="id" value="<?=$ticket->getId()?>">
                    <input id="message-author" type="hidden" value="<?=$session->getId()?>">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <?php if ($session->isAgent()) { ?>
                    <select id="faq-reply" name="faq-reply">
                        <option value="0">Reply with FAQ: </option>
                        <?php foreach ($faqs as $faq) { ?>
                        <option value="<?=$faq->getId()?>"><?=$faq->getQuestion()?></option>
                        <?php } ?>
                    </select>
                    <?php } ?>
                    <textarea id="new-message" name="content" placeholder="Type a New Message"></textarea>
                    <button id="send" type="submit">Send</button>
                </form>
                </div>
            </details>
        </section>
        <img id="tools" src="../assets/tools.png" alt="Tools Icon">
        <aside id="information">
            <section id="date-opened" class="date">
                <h3>Opened</h3>
                <p><?=$ticket->getDateOpened()?></p>
            </section>
            <?php if ($ticket->getStatus() && $ticket->getStatus()->getName() === 'Closed') { ?>
            <section id="date-closed" class="date">
                <h3>Closed</h3>
                <p><?=$ticket->getDateClosed()?></p>
            </section>
            <?php } ?>
            <form action="../actions/action_edit_ticket_properties.php" method="post" class="properties" novalidate>
                <input id="id" type="hidden" name="id" value="<?=$ticket->getId()?>">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <details>
                    <summary>Properties</summary>
                    <?php drawProperty($session->isAdmin() || ($session->isAgent() && $session->getId() !== $ticket->getAuthor()->getId()), 'Status', $ticket->getStatus(), $statuses); ?>
                    <?php drawProperty($session->isAdmin() || ($session->isAgent() && $session->getId() !== $ticket->getAuthor()->getId()), 'Priority', $ticket->getPriority(), $priorities); ?>
                    <?php drawProperty($session->isAdmin() || ($session->isAgent() && $session->getId() !== $ticket->getAuthor()->getId()), 'Department', $ticket->getDepartment(), $departments); ?>
                    <?php drawProperty($session->isAdmin() || ($session->isAgent() && $session->getId() !== $ticket->getAuthor()->getId()), 'Agent', $ticket->getAgent(), $agents); ?>
                    <section id="property-tag">
                        <h4>Tags</h4>
                        <?php foreach ($tags as $tag) { ?>
                            <?php if ($session->isAdmin() || ($session->isAgent() && $session->getId() !== $ticket->getAuthor()->getId())) { ?>
                            <button formaction="../actions/action_delete_ticket_tag.php" formmethod="post" class="all-tags" value="<?=$tag->getId()?>" name="tag" id="<?=$tag->name?>"><?=htmlentities($tag->getName())?></button>
                            <?php } else { ?>
                            <p><?=htmlentities($tag->getName())?></p>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($session->isAdmin() || ($session->isAgent() && $session->getId() !== $ticket->getAuthor()->getId())) { ?>
                        <input type="text" id="tags" name="tags">
                        <?php } ?>
                    </section>
                    <?php if ($session->isAdmin() || ($session->isAgent() && $session->getId() !== $ticket->getAuthor()->getId())) { ?>
                    <button type="submit" id="apply">Apply</button>
                    <?php } ?>
                </details>
            </form>
            <details id="changes">
                <summary>Changes</summary>
                <?php foreach ($changes as $change) { ?>
                    <h5><?=$change->getDate()?></h5>
                    <p><?=$change->getDescription()?></p>
                <?php } ?>
            </details>
        </aside>
    </main>
<?php } ?>

<?php function drawProperty(bool $canEdit, string $name, $entity, array $entities) : void { ?>
    <label for="<?=strtolower($name)?>"><?=$name?></label>
    <select id="<?=strtolower($name)?>" name="<?=strtolower($name)?>">
        <?php if ($entity) { ?>
        <option value="<?=$entity->getId()?>"><?=htmlentities($entity->getName())?></option>
        <?php } else { ?>
        <option value="0">None</option>
        <?php } ?>
        <?php if ($canEdit) {
            foreach ($entities as $e) {
                if (!$entity || $e->getId() !== $entity->getId()) { ?>
                <option value="<?=$e->getId()?>"><?=htmlentities($e->getname())?></option>
            <?php }
            }
        } ?>
    </select>
<?php } ?>
