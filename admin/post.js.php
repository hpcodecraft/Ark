<?php
require_once( '../config/'.$_SERVER['SERVER_NAME'].'.php' );
header( 'Content-type: text/javascript; charset=utf-8' );
?>
function ArkBookmarklet( postURL, baseURL ) {
	this.postURL = postURL;
	this.stylesheet = baseURL + 'admin/templates/css/bookmarklet.css';
	this.baseURL = baseURL;
	this.visible = false;
	this.bookmarklet = null;
	this.dialog = null;
	this.images = null;
	this.iframe = null;
	this.checkSuccessInterval = 0;
	this.minImageSize = 32;

	var that = this;

	this.create = function() {
		var css = document.createElement('link');
		css.type = 'text/css';
		css.rel = 'stylesheet';
		css.href = this.stylesheet;

		document.querySelector('head').appendChild(css);

    css = document.createElement('link');
    css.type = 'text/css';
    css.rel = 'stylesheet';
    css.href = "https://fonts.googleapis.com/css?family=Cookie";

    document.querySelector('head').appendChild(css);


		//if( document.getElementsByTagName("head").item(0) ) {
		//	document.getElementsByTagName("head").item(0).appendChild( css );
		//}
		//else {
		//	document.getElementsByTagName("body").item(0).appendChild( css );
		//}

		var postActiveButton;

		metaData = document.getElementsByTagName("meta");
		url = getData(metaData,"og:url");
		title = getData(metaData,"og:title");
		type = getData(metaData,"og:type");
		image = getData(metaData,"og:image");
		site_name = getData(metaData,"og:site_name");
		description = getData(metaData,"og:description");
		video = getData(metaData,"og:video");
		width = getData(metaData,"og:width");
		height = getData(metaData,"og:height");
		video_type = getData(metaData,"og:video:type");
		video_width = getData(metaData,"og:video:width");
		video_height = getData(metaData,"og:video:height");

		var menuBar = document.createElement('div');
		menuBar.id = 'ark-bookmarklet-menu';

		var logo = document.createElement('h3');
		logo.className = 'logo';
    logo.onclick = function() {
      window.open(that.baseURL);
    };
		logo.appendChild(document.createTextNode("Ark"));

    // var thisPage = document.createElement('span');
    // thisPage.appendChild(document.createTextNode("This page"));

		var postSiteButton = document.createElement('a');
		postSiteButton.appendChild( document.createTextNode("save this page") );
		postSiteButton.onclick = function() {
			that.loadIFrame({
				'type': 'link',
				'title': document.title,
				'url': document.location.href,
				'xhrLocation': document.location.href.replace(/#.*$/,''),
				'description' : description || document.location.href,
				'image': image || null,
			});
			postActiveButton.style.color="#666";
			postActiveButton = postSiteButton;
			postActiveButton.style.color="#00adef";
		};

		postSiteButton.href = '#';
		postActiveButton = postSiteButton;

		var postQuoteButton = document.createElement('a');
		postQuoteButton.appendChild( document.createTextNode("save a quote") );
		postQuoteButton.onclick = function()
		{
			that.loadIFrame({
				'type': 'quote',
				'title': '',
				'quote' : window.getSelection().toString() || '',
				'speaker' : '',
				'description' : '',
				'source' : '',
				'xhrLocation': document.location.href.replace(/#.*$/,''),
			});
			postActiveButton.style.color="#666";
			postActiveButton = postQuoteButton;
			postActiveButton.style.color="#00adef";
		};
		postQuoteButton.href = '#';

    var yourArk = document.createElement('span');
    yourArk.appendChild(document.createTextNode("|"));

		var postStoryButton = document.createElement('a');
		postStoryButton.appendChild( document.createTextNode("post story") );
		postStoryButton.onclick = function() {
			that.loadIFrame({
				'type': 'story',
				'title': '',
				'description' : window.getSelection().toString() || '',
				'xhrLocation': document.location.href.replace(/#.*$/,'')
			});
			postActiveButton.style.color="#666";
			postActiveButton = postSiteButton;
			postActiveButton.style.color="#00adef";
		};
		postStoryButton.href = '#';

    var postUrlButton = document.createElement('a');
		postUrlButton.appendChild( document.createTextNode("post URL") );
		postUrlButton.onclick = function() {
      that.loadIFrame({
				'type': 'custom-link',
				'title': '',
				'url': '',
				'xhrLocation': document.location.href.replace(/#.*$/,''),
				'description' : '',
				'image': '',
			});
			postActiveButton.style.color = "#666";
			postActiveButton = postSiteButton;
			postActiveButton.style.color = "#00adef";
		};
		postUrlButton.href = '#';

		var uploadImageButton = document.createElement('a');
		uploadImageButton.appendChild( document.createTextNode("upload image") );
		uploadImageButton.onclick = function() {
			that.loadIFrame({
				'type': 'image',
				'title': '',
				'image' : 'upload',
				'description' : "",
				'source' : 'upload',
				'xhrLocation': document.location.href.replace(/#.*$/,'')
			});
			postActiveButton.style.color="#666";
			postActiveButton = postSiteButton;
			postActiveButton.style.color="#00adef";
		};
		uploadImageButton.href = '#';



		var closeButton = document.createElement('a');
		closeButton.appendChild( document.createTextNode("close") );
		closeButton.onclick = function() { return that.toggle(); };
		closeButton.href = '#';

		// var arkButton = document.createElement('a');
		// arkButton.appendChild( document.createTextNode("visit my Ark"));
		// arkButton.target = '_new';
		// arkButton.href = this.baseURL;

		menuBar.appendChild( logo );
    // menuBar.appendChild( thisPage );
		menuBar.appendChild( postSiteButton );
		menuBar.appendChild( postQuoteButton );
    menuBar.appendChild( yourArk );
		menuBar.appendChild( postStoryButton );
    menuBar.appendChild( postUrlButton );
		menuBar.appendChild( uploadImageButton );
		menuBar.appendChild( closeButton );
		// menuBar.appendChild( arkButton );

		this.bookmarklet = document.createElement('div');
		this.bookmarklet.id = 'ark-bookmarklet';
		this.bookmarklet.appendChild( menuBar );

		var closeDialog = document.createElement('button');
		closeDialog.appendChild( document.createTextNode("cancel") );
		closeDialog.id = 'ark-bookmarklet-close';
		closeDialog.onclick = function() {
			that.dialog.classList.remove('show');
			return false;
		};

		closeDialog.href = '#';

		this.dialog = document.createElement('div');
		this.dialog.id = 'ark-bookmarklet-dialog';

		this.images = document.createElement('div');
		this.images.id = 'ark-bookmarklet-images';

		this.iframe = document.createElement('iframe');
		this.iframe.src = 'about:blank';

		this.dialog.appendChild( closeDialog );
		this.dialog.appendChild( this.iframe );
		this.bookmarklet.appendChild( this.images );
		this.bookmarklet.appendChild( this.dialog );


		/*
		if(type=="instapp:photo")
		{
			this.loadIFrame({
				'title': title,
				'image' : image,
				'description' : description,
				'source' : url,
				'xhrLocation': document.location.href.replace(/#.*$/,''),
				'width' :width,
				'height':height
			});
			postActiveButton.style.color="#666";
			postActiveButton = postImageButton;
			postActiveButton.style.color="#00adef";
		}
		else if(type=="tumblr-feed:quote")
		{
			this.loadIFrame({
				'title': "",
				'quote' : title,
				'speaker' : description,
				'description' : "",
				'source' : url,
				'xhrLocation': document.location.href.replace(/#.*$/,''),
			});
			postActiveButton.style.color="#666";
			postActiveButton = postQuoteButton;
			postActiveButton.style.color="#00adef";
		}
		else if(type=="video")
		{

			this.loadIFrame({
				'title': title,
				'video' : video,
				'description' : description,
				'source' : url,
				'thumb' : image,
				'xhrLocation': document.location.href.replace(/#.*$/,''),
				'width' :video_width,
				'height':video_height,
				'video_type' : video_type
			});
			postActiveButton.style.color="#666";
		}
		*/

		document.body.appendChild( this.bookmarklet );
	};

	function getData(array,key) {
		for(i = 0;i<array.length;i++)
		{
			if(array[i].getAttribute("property")==key)
			{
				return array[i].getAttribute("content");
			}
		}
		return false;
	}

	this.loadIFrame = function( params ) {
		this.dialog.classList.add('show');
		var reqUrl = this.postURL + '?nocache=' + parseInt(Math.random()*10000);
		for( var p in params ) {
			reqUrl += '&' + p + '=' + encodeURIComponent( params[p] );
		}
		this.iframe.src = reqUrl;
	};

	this.checkSuccess = function() {
		if( document.location.href.match(/#post-success/) ) {
			document.location.href = document.location.href.replace(/#.*$/, '#');
			this.hide();
		}
		else if( document.location.href.match(/#image-success/) ) {
			document.location.href = document.location.href.replace(/#.*$/, '#');
			this.dialog.classList.remove('show');
		}
	};

	this.overflow = null;

	this.show = function() {

		this.overflow = document.body.style.overflow;
		document.body.style.overflow = 'hidden';

		var that = this;
		var images = document.getElementsByTagName('img');

    var imageLoadHandler = function() {
      var imgBox = document.createElement('div');
      imgBox.className = 'image-box';

      var imgSquare = document.createElement('div');
      imgSquare.className = 'image-square';
      imgSquare.appendChild(this);
      imgBox.appendChild(imgSquare);

      //var imgLabel = document.createElement('label');
      //imgLabel.className = 'image-label';

      //var title = img.getAttribute('title') || img.getAttribute('alt') || '';
      //imgLabel.appendChild(document.createTextNode(title));

      //imgBox.appendChild(imgLabel);
      imgBox.onclick = imageClickHandler(this, '');

      that.images.appendChild(imgBox);
    };

    var imageClickHandler = function(img, title) {
      return function() { that.selectImage(img, title); };
    };

		for( var i=0; i<images.length; i++ ) {
			var img = images[i];
			if( img && img.src && img.width > this.minImageSize && img.height > this.minImageSize) {
				var cloneImg = new Image();
				cloneImg.onload = imageLoadHandler;
				cloneImg.src = img.src;
			}
		}

		this.visible = true;
		this.checkSuccessInterval = setInterval( function() { that.checkSuccess(); }, 500 );
		this.bookmarklet.style.display = 'block';
	};

	this.selectImage = function(img, title) {
		that.loadIFrame({
			'type': 'image',
			'title': title,
			'image' : img.src,
			'description' : "",
			'source' : document.location.href,
			'xhrLocation': document.location.href.replace(/#.*$/,'')
		});
	};

	this.hide = function() {
		this.visible = false;
		this.dialog.classList.remove('show');

		clearInterval( this.checkSuccessInterval );
		this.bookmarklet.style.display = 'none';

		var el = this.images;
		while( el.hasChildNodes() ){
    		el.removeChild(el.lastChild);
		}

		document.body.style.overflow = this.overflow;
	};

	this.toggle = function() {
		if( !this.visible ) {
			this.show();
		} else {
			this.hide();
		}
		return false;
	};

	this.create();
}

if( typeof(ArkBookmarkletInstance) == 'undefined' )  {
	var ArkBookmarkletInstance = new ArkBookmarklet(
		'<?php echo ASAPH_POST_PHP; ?>',
		'<?php echo ASAPH_BASE_URL; ?>'
	);
}
ArkBookmarkletInstance.toggle();
