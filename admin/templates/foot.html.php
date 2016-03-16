    <div class="ark-viewer">
        <div class="ark-viewer-header">
          <span class="ark-viewer-type"></span>
          <span class="ark-viewer-title"></span>
          <span class="ark-viewer-created"></span>
        </div>
        <div class="ark-viewer-content"></div>
        <div class="ark-viewer-footer">
          <span>Links:</span>
          <a href="#" target="_blank" class="ark-viewer-post">Ark Post</a>
          <a href="#" target="_blank" class="ark-viewer-fullsize">Image only</a>
          <a href="#" target="_blank" class="ark-viewer-source">Source</a>
          <em>(right-click and copy URL to share)</em>
        </div>
    </div>

    <footer>
      <button id="add-collection" title="New collection">✚</button>
    </footer>

    <script src="templates/js/jquery-1.10.2.min.js"></script>
    <script src="templates/js/jquery.infinitescroll.min.js"></script>

    <?php
        $jsonCollections = array();
        foreach( $collections as $c ) {
            $jsonCollections[$c['id']] = $c;
        }
    ?>
    <script>
        var collections = <?php echo json_encode($jsonCollections); ?>;
    </script>


    <?php if(isset($posts)):
        $jsonPosts = array();
        foreach( $posts as $p ) {
            $jsonPosts[$p['id']] = $p;
        }
    ?>
    <script>
        var options = {
            //weekday: 'long',
            month: 'short',
            year: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            //second: 'numeric'
        },
        intlDate = new Intl.DateTimeFormat( undefined, options );


        var posts = <?php echo json_encode($jsonPosts); ?>;

        $(function() {

            var inspectorVisible = false;



            $(document).on('click', '.post', function(e) {
                if(e.target.className == 'edit') return true;

                var id      = $(this).data('id'),
                    type    = $(this).data('type'),
                    title   = $(this).data('title'),
                    created = 'posted at '+intlDate.format(new Date($(this).data('created')*1000)),
                    source  = $(this).data('source'),
                    image   = $(this).data('image'),
                    quote   = $(this).data('quote'),
                    speaker = $(this).data('speaker'),
                    show    = false;

                if(type == 'url') return true;

                switch(type) {
                    case 'image':
                        var viewerImg = new Image();
                        viewerImg.onload = function() {
                          $('.ark-viewer-content').empty().append(viewerImg);
                        }

                        $('.ark-viewer-content').html('<div class="loading">loading, please wait</div>');

                        viewerImg.src = image;

                        $('.ark-viewer-post').show();
                        $('.ark-viewer-fullsize').show();
                        $('.ark-viewer-source').show();
                        break;

                    case 'quote':
                        var quote = $('<div><pre class="quote">'+quote+'</pre><div class="speaker">- '+speaker+' -</div></div>');
                        $('.ark-viewer-content').append(quote);
                        $('.ark-viewer-post').show();
                        break;
                }

                $('.ark-viewer-type').html(type);
                $('.ark-viewer-title').html(title);
                $('.ark-viewer-created').html(created);

                $('.ark-viewer-post').attr('href', '<?php echo ASAPH_BASE_URL.'?post/'; ?>'+id);
                $('.ark-viewer-fullsize').attr('href', image);
                $('.ark-viewer-source').attr('href', source);

                $('.ark-viewer').show();
            });


        });
    </script>
    <?php endif; ?>


    <script src="templates/js/admin.js"></script>
  </body>
</html>
