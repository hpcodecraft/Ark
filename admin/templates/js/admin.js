$(function() {

  /***********************************************************************
   * Post viewer
   ***********************************************************************/

   // Close and reset the viewer on background click
   $(document).on('click', '.ark-viewer', function(e) {
       $('.ark-viewer').hide();
       $('.ark-viewer-content').empty();
       $('.ark-viewer-type').empty();
       $('.ark-viewer-title').empty();
       $('.ark-viewer-created').empty();
       $('.ark-viewer-footer a').hide();
   });


  /***********************************************************************
   * Dialogs
   ***********************************************************************/

  // Dialog close button
  $(document).on('click', '.close-dialog', function(e) {
      $('#ark-dialog-frame').attr('src', 'dialog.php');
      $('#ark-dialog').removeClass('show');
  });

  function showDialog(dialog, e) {
    e.stopPropagation();
    e.preventDefault();

    $('#ark-dialog-frame').attr('src', 'dialog.php?dialog='+dialog);
    $('#ark-dialog').addClass('show');
  }

  // Main menu: Account button
  $(document).on('click', '.edit-account', showDialog.bind(this, 'edit-account'));

  // Main menu: Tools button
  $(document).on('click', '.tools', showDialog.bind(this, 'tools'));

  // Main menu: Settings button
  $(document).on('click', '.settings', showDialog.bind(this, 'settings'));


  /***********************************************************************
   * Collections
   ***********************************************************************/

  // Add Collection button
  $('#add-collection').click(function() {
      $('#ark-dialog-frame').attr('src', 'dialog.php?dialog=add-collection');
      $('#ark-dialog').addClass('show');
  });

  // Edit Collection button
  $(document).on('click', '.edit-collection', function(e) {
      e.stopPropagation();
      e.preventDefault();

      var id = $(this).data('id');

      $('#ark-dialog-frame').attr('src', 'dialog.php?dialog=edit-collection&id='+id);
      $('#ark-dialog').addClass('show');

      return false;
  });


  /***********************************************************************
   * Post filters
   ***********************************************************************/

  var filter = {
    type: 'all',
  };

  function filterPosts($posts) {
      $posts.each(function(i, el) {
          var post = $(el);
          if(filter.type == 'all') post.show();
          else {
              if(post.data('type') == filter.type) post.show();
              else post.hide();
          }
      });
  }

  // filter buttons
  $(document).on('click', '#filters button', function(e) {
      var value = $(this).data('value'),
          posts = $('#content').find('.post');

      filter.type = value;
      filterPosts(posts);

      $('#filters button').removeClass('active');
      $(this).addClass('active');
  });


  /***********************************************************************
   * Posts
   ***********************************************************************/

  // Post: edit post button
  $(document).on('click', '.post-actions .edit', function(e) {
      var id = $(this).parents('.post').data('id');
      $('#ark-dialog-frame').attr('src', 'dialog.php?dialog=edit-post&id='+id);
      $('#ark-dialog').addClass('show');
  });


  // Posts: setup infinite scrolling
  $('#content').infinitescroll({
      behavior: 'local',
      binder: $('#content'),
      navSelector  : "#pages",
      nextSelector : "#pages .next",
      itemSelector : "#content .post",
      loading: {
          finishedMsg: '<div class="scroll-message"><em>This is the end, my friend.</em></div>',
          msgText: '<div class="scroll-message"><em>Loading more posts...</em></div>',
          speed: 'fast',
          img: "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",
      }
  }, function(arrayOfNewElems) {
     filterPosts($(arrayOfNewElems));
     if(document.location.search.match(/collection/) === null) {
        $('#content').append(loadMorePosts);
     }
  });


  /***********************************************************************
   * Search
   ***********************************************************************/

  // Search: filter loaded posts on input
  $('#search').on('input', function(e) {
    $('#search-results').removeClass('show');
    $('#search').removeClass('active');

    var val = $(this).val();

    $('#content .post').show();

    if(val.length > 0) {
      var posts = $('#content .post');

      posts.each(function(i, el) {
        var str = $(el)[0].outerHTML.toString();
        if(str.toLowerCase().indexOf(val) == -1) {
          $(el).hide();
        }
      });
    }
  });

  // Search: search the entire database if return is pressed
  $('#search').on('keyup', function(e) {
    if(e.keyCode == 13) {
      var query = this.value;
      $.getJSON('search.php?q='+encodeURIComponent(query), renderPosts);
    }
  });

  var loadMorePosts = $('<a href="#" class="load-more">load more</a>');
  if(document.location.search.match(/collection/) === null) {
    $('#content').append(loadMorePosts);
  }

  $(document).on('click', '.load-more', function(e) {
    e.preventDefault();
    $('#content').infinitescroll('retrieve');
  });

  function renderPosts(json) {
    $('#search-results').empty();
    $('#search').addClass('active');

    json.posts.forEach(function(post) {
      var imageAttr = '',
  		    quoteAttr = '',
  		    urlLink 	= '';

      var title = post.title.length === 0 ? "untitled" : post.title;
      var title2 = title;

      if(post.type == 'quote') {
  			title = '';
  			title2 = '- ' + post.quote.speaker + ' -';
  			quoteAttr = 'data-quote="'+ post.quote.quote +'" data-speaker="'+ post.quote.speaker +'" ';
  		}

  		if(post.type == 'image') {
  			imageAttr = 'data-image="'+ post.image.image +'" ';
  		}

  		if(post.type == 'url') {
  			urlLink = '<a class="url-link" href="'+ post.source +'" target="_blank">';
  			if(post.image_url !== null) urlLink += '<img src="'+ post.image_url +'" />';
  			urlLink+= '</a>';
  		}

  		var tags = [];
      post.tags.forEach(function(t) {
        tags.push(t.tag);
      });

      var html = '<div class="post" ' +
          'data-id="' + post.id + '" ' +
          'data-type="' + post.type + '" ' +
          'data-title="' + title + '" ' +
          'data-created="' + post.created + '" ' +
          'data-source="' + post.source + '" ' +
          'data-tags="' + tags.join(',') + '" ' +
          imageAttr +
          quoteAttr + '>' +
            '<div class="post-image">' +
          urlLink;

          html +=
            '<div class="post-actions">' +
              '<span class="post-type">'+ post.type.toUpperCase() +'</span>' +
              '<button type="button" class="edit"></button>' +
          '</div>';

          if(post.type == 'image')
            html += '<img src="'+ post.image.thumb +'" alt="" />';
          else if(post.type == 'image')
            html += '<img src="'+ post.video.thumb +'" alt="" />';
          else if(post.type == 'quote')
            html += '<em>'+ post.quote.quote.split('\n').join('<br>') +'</em>';

        html +=
          '</div>' +
            '<div class="post-title">' +
            '<em>' + title2 + '</em>' +
          '</div>' +
        '</div>';

        $('#search-results').append($(html));
    });

    if(json.posts.length === 0) {
      var html = '<h2>Nothing found :(</h2>';
      $('#search-results').append($(html));
    }

    $('#search-results').addClass('show');
  }
});
