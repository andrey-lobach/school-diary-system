
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
</head>
<body>
<a href="/app.php/users">Users List</a>
<h1>Add user</h1>
<?php
    $form = $this->data['form'];
    if (!$form->isValid()) {
        foreach ($form->getViolations() as $key => $violation){
            echo $key;
            echo $violation.'<br>';
        }
    }
?>
<form method="post" action="/app.php/users/create">
    <!-- return field value -->
    <input type="text" name="login" placeholder="login" required value="<?php echo $form->getData()['login']; ?>">
    <input type="password" name="password" placeholder="password" required>
    <select name="roles" multiple required>
        <option value=""></option>
        <?php foreach (\Enum\RolesEnum::getAll() as $role) { ?>
            <!-- TODO if in array => selected-->
        <option value="<?php echo $role ?>"
            <?php if (in_array($form->getData()['roles'], $role)) echo 'selected'; ?>>
            <?php echo $role ?>
        </option>
        <?php } ?>
    </select>
    <button type="submit" name="submit">Create</button>

</form>
</body>
</html>