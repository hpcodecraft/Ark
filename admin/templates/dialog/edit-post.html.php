<div class="ark-dialog-content">

  <form action="<?= Asaph_Config::$absolutePath; ?>admin/dialog.php?<?= getGETString() ?>" method="post">

    <?php if($post['type'] == 'url'): ?>
    <input type="hidden" name="url" value="<?= $post['url'] ?>" />

    <div class="post-image" style="background-image:url(<?= $post['image'] ?>);">
      <button type="button" class="remove-preview">
        remove preview?
        <div id="remove-preview-setting" class="safe">no</div>
        <input type="hidden" name="remove-preview" value="0" id="remove-preview-input" />
      </button>
    </div>
    <ul class="post-form">
      <li>
        <input type="text" placeholder="Title" class="long" name="title" value="<?= $post['title'] ?>" />
      </li>
      <li>
        <textarea type="text" name="description"><?= $post['description'] ?></textarea>
      </li>
    </ul>

    <?php elseif($post['type'] == 'image'): ?>
    <div class="post-image">
      <img src="<?= $post['image']['thumb'] ?>" />
    </div>
    <ul class="post-form">
      <li>
        <input type="text" placeholder="Title" class="long" name="title" value="<?= $post['title'] ?>" autofocus />
      </li>
      <li>
        <textarea type="text" name="description"><?= $post['description'] ?></textarea>
      </li>
    </ul>

    <?php elseif($post['type'] == 'quote'): ?>
    <input type="hidden" name="quote-id" value="<?= $post['quote']['id'] ?>"/>
    <input type="hidden" name="title" value="" />
    <input type="hidden" name="description" value="" />

    <ul class="post-form quote">
      <li>
        <textarea type="text" placeholder="A Deep and Meaningful Quote" name="quote" autofocus><?= $post['quote']['quote'] ?></textarea>
      </li>
      <li>
        <input type="text" name="speaker" class="long" placeholder="by an intelligent Person" value="<?= $post['quote']['speaker'] ?>"/>
      </li>
    </ul>

    <?php elseif($post['type'] == 'story'): ?>
    <ul class="post-form">
      <li>
        <input type="text" placeholder="Post title" class="long" name="title" value="<?= $post['title'] ?>" autofocus />
      </li>
      <li>
        <textarea type="text" name="description"><?= $post['description'] ?></textarea>
      </li>
    </ul>
    <?php endif; ?>

    <div class="post-tags">
      <?php
        $input_tags = [];
        foreach($post['tags'] as $t) {
          array_push($input_tags, $t['tag']);
        }
      ?>
      <input type="text" id="post-tags" name="tags" class="long" placeholder="Add a tag" value="<?= implode(', ', $input_tags ) ?>" />
    </div>

    <div class="post-collection">
      <select name="collection">
        <option value="0">Select a collection...</option>
        <?php foreach($collections as $c): ?>
        <option value="<?= $c['id'] ?>"<?php if($c['id'] == $post['collection']) echo ' selected' ?>><?= $c['name'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="ark-dialog-footer">
        <input type="hidden" name="id" value="<?= $post['id'] ?>"/>
        <input type="hidden" name="type" value="<?= $post['type'] ?>" />
        <input type="hidden" name="action" value="edit-post" />
        <div class="well">
          <label>
            <input type="checkbox" name="public" <?php if($post['public'] == 1) echo 'checked ' ?>/>
            public
          </label>

          <label>
            <input type="checkbox" name="nsfw" <?php if($post['nsfw'] == 1) echo 'checked ' ?>/>
            nsfw
          </label>

          <label class="danger">
            <input type="checkbox" name="delete" />
            delete this post
          </label>
        </div>
        <button type="submit">save</button>
    </div>
  </form>

</div>
