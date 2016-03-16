<div class="collections">

  <h2>Your Ark</h2>
  <a class="collection<?php if(!isset($_GET['collection'])) echo ' selected'; ?>" href="<?php echo Asaph_Config::$absolutePath.'admin/' ?>">All items</a>

  <br>

  <h2>Collections</h2>
  <?php foreach( $collections as $i => $c ) { ?>
    <a class="collection<?php if(isset($_GET['collection']) && $_GET['collection'] == $c['id']) echo ' selected'; ?>" href="?collection=<?php echo $c['id']; ?>">
      <?php echo $c['name']; ?>
      <button class="edit edit-collection<?php if(isset($_GET['collection']) && $_GET['collection'] == $c['id']) echo ' inverted'; ?>" type="button" data-id="<?php echo $c['id']; ?>" title="Edit this collection"></button>
    </a>
  <?php } ?>
</div>
