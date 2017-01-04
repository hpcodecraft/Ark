<div class="ark-dialog-content">
  <h2>Settings</h2>

  <form action="<?php echo Asaph_Config::$absolutePath; ?>admin/dialog.php?<?php echo getGETString() ?>" method="post">
    <ul>
      <li>
        <label>
          Site title
          <input type="text" name="site_title" value="<?= $settings['site_title'] ?>" />
        </label>
      </li>
      <li>
        <label>
          Site slogan
          <input type="text" name="site_slogan" value="<?= $settings['site_slogan'] ?>" />
        </label>
      </li>
      <li>
        <label>
          Show NSFW content in admin
          <input type="checkbox" name="admin_show_nsfw_content" <?=$settings['admin_show_nsfw_content'] == 1 ? ' checked' : '' ?> />
        </label>
      </li>
      <li>
        <label>
          Show NSFW content on public page
          <input type="checkbox" name="public_page_show_nsfw_content"<?=$settings['public_page_show_nsfw_content'] == 1 ? ' checked' : '' ?> />
        </label>
      </li>
    </ul>

    <div class="ark-dialog-footer">
        <input type="hidden" name="action" value="settings" />
        <button type="submit">save</button>
    </div>
  </form>
</div>
