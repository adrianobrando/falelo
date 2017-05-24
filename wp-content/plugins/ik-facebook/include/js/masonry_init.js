	//init masonry galleries
	var galleries = document.querySelectorAll('.wpsp_masonry_gallery');
	for ( var i=0, len = galleries.length; i < len; i++ ) {
		var gallery = galleries[i];
		initMasonry( gallery );
	}
	
	function initMasonry( container ) {
		var imgLoad = imagesLoaded( container, function() {
			new Masonry( container, {
				itemSelector: '.ik_fb_gallery_item',
				columnWidth: '.ik_fb_gallery_item',
				isFitWidth: true
			});
		});
	}