<?php
    declare(strict_types = 1);
?>

<?php function drawManagement(array $clients, array $departments, array $agents) : void { ?>
    <main>
        <section id="management">
            <h2>Management</h2>
            <details class="management">
                <summary class="action">Upgrade a client</summary>
                <form action="../actions/action_upgrade_client.php" method="post" class="upgrade">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="client">Select a client</label>
                    <select id="client" name="client">
                        <option value="0">None</option>
                        <?php foreach ($clients as $client) { ?>
                        <option value="<?=$client->getId()?>"><?=htmlentities($client->getUsername())?></option>
                        <?php } ?>
                    </select>
                    <div id="upgrade-role">
                        <label for="agent">To agent</label>
                        <input id="agent" type="radio" name="role" value="agent" required>
                        <label for="admin">To admin</label>
                        <input id="admin" type="radio" name="role" value="admin" required>
                    </div>
                    <button type="submit">Upgrade</button>
                </form>
            </details>
            <details class="management">
                <summary class="action">Add a new entity</summary>
                <form action="../actions/action_add_entity.php" method="post" class="entity">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="entity">Select an entity</label>
                    <select id="entity" name="entity">
                        <option value="0">None</option>
                        <option value="department">Department</option>
                        <option value="status">Status</option>
                        <option value="priority">Priority</option>
                        <option value="tag">Tag</option>
                    </select>
                    <div class="field">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <button type="submit">Add</button>
                </form>
            </details>
            <details class="management">
                <summary class="action">Assign agent to department</summary>
                <form action="../actions/action_assign_agent.php" method="post" class="assign">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="department">Select a department</label>
                    <select id="department" name="department">
                        <option value="0">None</option>
                        <?php foreach ($departments as $department) { ?>
                        <option value="<?=$department->getId()?>"><?=htmlentities($department->getName())?></option>
                        <?php } ?>
                    </select>
                    <div class="field">
                        <label for="agent-department">Select an agent</label>
                        <select id="agent-department" name="agent">
                            <option value="0">None</option>
                            <?php foreach ($agents as $agent) { ?>
                            <option value="<?=$agent->getId()?>"><?=htmlentities($agent->getUsername())?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit">Assign</button>
                </form>
            </details>
            <details class="management">
                <summary class="action">Delete an entity</summary>
                <form action="../actions/action_delete_entity.php" method="post" class="delete-entity">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="entity-delete">Select an entity</label>
                    <select id="entity-delete" name="entity">
                        <option value="0">None</option>
                        <option value="department">Department</option>
                        <option value="status">Status</option>
                        <option value="priority">Priority</option>
                        <option value="tag">Tag</option>
                    </select>
                    <div class="field">
                        <label for="entity-name">Name</label>
                        <input type="text" id="entity-name" name="name" required>
                    </div>
                    <button type="submit">Delete</button>
                </form>
            </details>
            <details class="management">
                <summary class="action">Delete a user</summary>
                <form action="../actions/action_delete_user.php" method="post" class="delete-user">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="user">Select a user</label>
                    <select id="user" name="user">
                        <option value="0">None</option>
                        <?php foreach ($clients as $user) { ?>
                            <option value="<?=$user->getId()?>"><?=htmlentities($user->getUsername())?></option>
                        <?php } ?>
                    </select>
                    <button type="submit">Delete</button>
                </form>
            </details>
        </section>
    </main>
<?php } ?>
