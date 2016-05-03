<div class="ark-dialog-content">
  <h2>Edit Collection</h2>
  <form action="<?php echo Asaph_Config::$absolutePath; ?>admin/dialog.php?<?php echo getGETString() ?>" method="post">

    <ul>
      <li>
        <label>Name</label>
        <input type="text" name="name" class="long" value="<?=$collection['name']?>" autofocus />
      </li>
      <li class="well">
        <label>
          <input type="checkbox" name="featured" <?php if($collection['featured'] == 1) echo 'checked ' ?>/>
          featured
        </label>
        <label>
          <input type="checkbox" name="nsfw" <?php if($collection['nsfw'] == 1) echo 'checked ' ?>/>
          nsfw
        </label>
        <label>
          <input type="checkbox" name="delete" />
          <span class="danger">delete this collection.</span><br>
          <em>Items in the collection will not be deleted</em>
        </label>
      </li>
    </ul>

    <?php if( !empty($status) ) { ?>
      <div class="warn">
        <?php if( $status == 'collectionname-empty' ) { ?>The collection name was empty<?php } ?>
      </div>
    <?php } ?>

    <div class="ark-dialog-footer">
        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
        <input type="hidden" name="action" value="edit-collection" />
        <button type="submit">save</button>
    </div>
  </form>
</div>
