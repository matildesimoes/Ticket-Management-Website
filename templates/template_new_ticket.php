<?php
    declare(strict_types = 1);
?>

<?php function drawNewTicket(array $departments, array $tags) : void { ?>
    <main>
        <section id="new-ticket">
            <h2>How can we help you?</h2>
            <form action="../actions/action_add_ticket.php" method="post" enctype="multipart/form-data" class="new-ticket" novalidate>
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <label for="title">Title</label>
                <input id="title" type="text" name="title" placeholder="title" required>
                <label for="new-ticket-department">Department</label>
                <select name="department" id="new-ticket-department">
                    <option value="0">None</option>
                    <?php foreach ($departments as $department) { ?>
                    <option value=<?=$department->getId()?>><?=htmlentities($department->getName())?></option>
                    <?php } ?>
                </select>
                <label for="tags">Tags</label>
                <input type="text" id="tags" name="tags">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Describe your issue" required></textarea>
                <input type="file" name="file-upload" id="file-upload">
                <button type="submit">Submit</button>
            </form>
        </section>
    </main>
<?php } ?>
