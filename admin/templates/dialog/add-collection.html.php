<div class="ark-dialog-content">
  <h2>Add Collection</h2>
  <form action="<?php echo Asaph_Config::$absolutePath; ?>admin/dialog.php?<?php echo getGETString() ?>" method="post">

    <ul>
      <li>
        <label>Name</label>
        <input type="text" name="name" class="long" value="<?php echo !empty($_POST['name']) ? $_POST['name'] : '' ; ?>" autofocus />
      </li>
    </ul>

    <?php if( !empty($status) ) { ?>
      <div class="warn">
        <?php if( $status == 'collectionname-empty' ) { ?>The collection name was empty<?php } ?>
      </div>
    <?php } ?>

    <div class="ark-dialog-footer">
        <input type="hidden" name="action" value="add-collection" />
        <button type="submit">save</button>
    </div>

  </form>
</div>