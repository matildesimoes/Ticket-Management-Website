<?php
    declare(strict_types = 1);
?>

<?php function drawFAQsClient(array $faqs) : void { ?>
    <main>
        <section id="faqs">
            <h2>FAQ</h2>
            <?php foreach($faqs as $faq) { ?>
            <details class="faq">
                <summary class="question"><?=htmlentities($faq->getQuestion())?></summary>
                <p class="answer"><?=htmlentities($faq->getAnswer())?></p>
            </details>
            <?php } ?>
        </section>
    </main>
<?php } ?>

<?php function drawFAQsAgent(array $faqs) : void { ?>
    <main>
        <section id="faqs">
            <h2>FAQ</h2>
            <form action="../actions/action_add_faq.php" method="post" class="add-faq">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <details class="faq">
                    <summary class="question">Add a new FAQ</summary>
                    <label id="add-question" for="question">Question</label>
                    <input id="question" type="text" name="question" placeholder="question" required>
                    <label id="add-answer" for="answer">Answer</label>
                    <textarea id="answer" class="answer" name="answer" placeholder="answer" required></textarea>
                    <button type="submit">Add</button>
                </details>
            </form>
            <?php foreach($faqs as $faq) { ?>
            <form method="post" class="faq-questions-agent">
                <input type="hidden" name="id" value="<?=$faq->getId()?>">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <details class="faq">
                    <summary class="question faq-arrow">
                        <textarea name="question" required><?=htmlentities($faq->getQuestion())?></textarea>
                    </summary>
                    <textarea class="answer" name="answer" required><?=htmlentities($faq->getAnswer())?></textarea>
                    <div class="faq-buttons">
                        <button formaction="../actions/action_edit_faq.php">Edit</button>
                        <button formaction="../actions/action_delete_faq.php">Delete</button>
                    </div>
                </details>
            </form>
            <?php } ?>
        </section>
    </main>
<?php } ?>
