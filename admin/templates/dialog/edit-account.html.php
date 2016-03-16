<div class="ark-dialog-content">
  <h2>Edit your account</h2>
  <form action="<?php echo Asaph_Config::$absolutePath; ?>admin/dialog.php?<?php echo getGETString() ?>" method="post">

    <ul>
      <li>
        <label>Name</label>
        <input type="text" name="name" class="long" value="<?php echo $user['name']; ?>" />
      </li>
      <li>
        <label>Password</label>
        <input type="password" name="password" value="" />
        (leave empty if you don't want to change it)
      </li>
      <li>
        <label>Password (repeat)</label>
        <input type="password" name="password2" value="" />
      </li>
    </ul>

    <?php if( !empty($status) ) { ?>
      <div class="warn">
        <?php if( $status == 'passwords-not-equal' ) { ?>The passwords do not match<?php } ?>
        <?php if( $status == 'username-empty' ) { ?>The username was empty<?php } ?>
      </div>
    <?php } ?>

    <div class="ark-dialog-footer">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>"/>
        <input type="hidden" name="action" value="edit-account" />
        <button type="submit">save</button>
    </div>

  </form>
</div>