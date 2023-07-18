<?php
    declare(strict_types = 1);
?>

<?php function drawProfile(User $user) : void { ?>
    <main class="profile-page">
        <section id="profile">
            <h2>My Profile</h2>
            <a id="edit-profile" href="../pages/profile.php">Edit Profile</a>
            <a id="change-password" href="../pages/password.php">Change Password</a>
            <form action="../actions/action_edit_profile.php" method="post" class="profile">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <label for="first-name">First Name</label>
                <input id="first-name" type="text" name="first-name" value="<?=htmlentities($user->getFirstName())?>" required>
                <label for="last-name">Last Name</label>
                <input id="last-name" type="text" name="last-name" value="<?=htmlentities($user->getLastName())?>" required>
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="<?=htmlentities($user->getUsername())?>" required>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?=htmlentities($user->getEmail())?>" required>
                <button type="submit">Save</button>
            </form>
        </section>
        <section id="profile-photo">
            <h2><?=$user->getName()?></h2>
            <form action="../actions/action_upload_photo.php" method="post" enctype="multipart/form-data" class="upload-photo">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <img class="upload-photo" src="<?php echo htmlentities('../profile_photos/' . $user->getPhoto()) ?>" alt="Profile Photo">
                    <input type="file" name="photo-upload" id="photo-upload">
                    <button type="submit">Upload</button>
            </form>
        </section>
    </main>
<?php } ?>

<?php function drawPassword(User $user) : void { ?>
    <main class="profile-page">
        <section id="password">
            <h2>Change Password</h2>
            <a id="edit-profile" href="../pages/profile.php">Edit Profile</a>
            <a id="change-password" href="../pages/password.php">Change Password</a>
            <form action="../actions/action_edit_password.php" method="post" class="profile">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <label for="current">Current Password</label>
                <input id="current" type="password" name="current" required>
                <label for="new">New Password</label>
                <input id="new" type="password" name="new" required>
                <label for="confirm">Confirm Password</label>
                <input id="confirm" type="password" name="confirm" required>
                <button type="submit">Save</button>
            </form>
        </section>
        <section id="password-photo">
            <h2><?=$user->getName()?></h2>
            <img class="upload-photo" src="<?php echo ('../profile_photos/' . $user->getPhoto()) ?>" alt="Profile Photo">
        </section>
    </main>
<?php } ?>
