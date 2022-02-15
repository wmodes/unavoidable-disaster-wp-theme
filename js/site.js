// site.js
// Theme Name:     Unavoidable Disaster
// Theme URI:      https://UnavoidableDisaster.com
// Author:         Wes Modes (wmodes@gmail.com)
// Author URI:     https://modes.io
// Template:       understrap
// Version:        0.1.0
// Dependencies:   jQuery4

var metaGutter = 10;
var fontOptions = ["font1", "font2", "font3", "font4"];

function setMetaPosition() {
	// position metadata relative to thing
	$(".thing-content").each(function(){
		// find elements
		var img = $(this).find(".content");
		var meta = $(this).find(".metadata");
		if (meta.length) {
			var tail = meta.find(".tail");
			// get size and position of thing
			var imgWidth = img.outerWidth();
			var imgHeight = img.height();
			// if img is 0x0, prob empty
			if (imgWidth && imgHeight) {
				var imgLeft = img.offset().left;
				var imgRight = imgLeft + imgWidth;
				var imgTop = img.offset().top;
				var imgBottom = imgTop + imgHeight;
				var docWidth = $(document).outerWidth();
				var horzCenter = Math.floor(docWidth / 2);
				var metaHeight = meta.outerHeight();
				var metaWidth = meta.outerWidth();
				var tailHeight = tail.outerHeight();
				var tailWidth = tail.outerWidth();
				// console.log("docWidth:", docWidth);
				// console.log("imgWidth:", imgWidth, "imgHeight:", imgHeight);
				// console.log("imgLeft:", imgLeft, "imgRight:", imgRight);
				// console.log("imgTop:", imgTop, "imgBottom:", imgBottom);
				// console.log("metaWidth:", metaWidth, "metaHeight:", metaHeight);

				// fudge
				var fudge = 24;
				// var fudge = 0;

				// make sure tail is visible
				tail.removeClass("hidden");

				// vertically adjust tail
				var tailVert = Math.min(imgHeight / 2, metaHeight / 2);
				tail.offset({top: imgTop + Math.floor(tailVert) -
					Math.floor(tailHeight / 2)})

				// Possibilities (in order of preference):
				//  0) If bigmeta, metadata at margin to fill width, nontail
				if ($(this).parents(".bigmeta").length) {
					meta.addClass("full");
					tail.addClass("hidden");
					var page = $(this).parents(".elementor-container");
					meta.offset({left: page.offset().left + fudge})
				}
				// 	1) If sufficient space between right edge of thing and margin,
				// 		 then metadata positioned to right of thing (class toright),
				// 		 arrow points left (class toleft).
				//	   ┄┐
				//      ┆ ┌┄┄┄┄┄┐
				//	    ┆<      ┆
				//      ┆ ┆     ┆
				//      ┆ └┄┄┄┄┄┘
				else if (imgRight + metaWidth < docWidth) {
					meta.addClass("toright");
					tail.addClass("toleft");
				}
				// 	2) Else if sufficient space between left edge of thing and margin,
				// 	   then metadata positioned to left of thing (class toleft),
				// 	   arrow points right (class toright).
				//    	       ┌┄
				//     ┌┄┄┄┄┄┐ ┆
				//	   ┆      >┆
				//     ┆     ┆ ┆
				//     └┄┄┄┄┄┘ ┆
				else if (imgLeft - metaWidth > 0) {
					meta.addClass("toleft");
					tail.addClass("toright");
				}
				// 	3) Else metadata positioned at left edge of doc (class tomargin),
				// 	   arrow points left (class toleft).
				//		 ┄┄┄┄┄┄┄┄┄┐
				// 			 ┌┄┄┄┄┄┐┆
				//		  <      ┆┆
				//  	   ┆     ┆┆
				//   		 └┄┄┄┄┄┘┆
				else {
					meta.addClass("tomargin");
					tail.addClass("toleft");
				}
			}
		}
	})
}

// take a string and return a hashed checksum of the string
// from https://stackoverflow.com/questions/26057572/
function checksum(s) {
	var hash = 0, strlen = s.length, i, c;
	if ( strlen === 0 ) {
		return hash;
	}
	for (var i = 0; i < strlen; i++) {
		c = s.charCodeAt( i );
		hash = ((hash << 5) - hash) + c;
		hash = hash & hash; // Convert to 32bit integer
	}
	return Math.abs(hash);
};


// suggest turning your mobile device horizontally
//
function showTurnModelIfNeeded() {
	var screenWidth = $(window).width();
	var screenHeight = $(window).height();
	// if looking at a cover with a vertical screen
	if ($('.tabloid-cover').length &&
			screenWidth < 600 &&
			screenHeight > screenWidth) {
		$(".screen-turn-modal").addClass("show");
	}
	else {
		$(".screen-turn-modal").removeClass("show");
	}
}

// do this after everything has loaded, including images
$(window).on("load", function(){

	// remove loading screen
	$(".loading-modal").addClass("hidden");

});

// wait for everything to be loaded
$(document).ready(function() {

	// STUFF WE DO TO A LAYOUT PAGE
	//
	if ($("body.home").length || $("body.single").length) {

		// suggest turning your mobile device horizontally
		//
		// we create the modal and then display or hide it as needed
		var modalHTML = `
			<div class="screen-turn-modal">
				<div class="close">x</div>
				<div class="text">
					For <strong>best results,</strong> turn your mobile device horizontally.
				</div>
			</div>`
		var modal = $(modalHTML);
		$("#page").append(modal);
		// attach event to close
		var close = modal.find(".close");
		close.click(function(){
			$(".screen-turn-modal").removeClass("show");
		});

		showTurnModelIfNeeded()
		setMetaPosition();

		// recalculate positions when screen is resized
		//
		// $(window).on('resize', function(){
		// 	console.log("Resizing and resetting meta positions");
		// 	// setMetaPosition();
		// 	showTurnModelIfNeeded()
		// });

		// show metadata when thing is clicked
		//
		$(".thing-content.clickable .content").click(function(){
			// console.log("thing opened");
			var meta = $(this).siblings(".metadata");
			var alreadyShown = meta.hasClass("show");
			// first hide all metadata from any other things
			$(".thing-content .metadata").removeClass("show");
			if (! alreadyShown) {
				// add "show" class which shows metadata
				meta.addClass("show");
			}
		});

		// hide metadata when close is clicked
		//
		$(".thing-content .metadata .close").click(function(){
			// console.log("thing closed");
			$(this).parent().removeClass("show");
		});

		// show comments when link is clicked
		//
		$(".thing-content.clickable.comments .metadata .comment-link a").click(function(event){
			event.preventDefault();
			// console.log("Comments opened");
			var commentEl = $(this).closest(".thing-content").find(".entry-comment-wrapper");
			commentEl.addClass("show");
			setTimeout(function(){
				commentEl.find(".entry-comment").addClass("show");
			}, 100);
		});

		// hide comments when close or modal are clicked
		//
		$(".thing-content .entry-comment .close").click(function(){
			// console.log("Close clicked: Comment box closed");
			var commentEl = $(this).closest(".thing-content").find(".entry-comment-wrapper");
			commentEl.find(".entry-comment").removeClass("show");
			setTimeout(function(){
				commentEl.removeClass('show');
			}, 500);
		});
		$(".thing-content .entry-comment-wrapper").click(function(el){
			// console.log("Modal clicked: Comment box closed");
			if (el.target == this) {
				$(this).find(".entry-comment").removeClass("show");
				setTimeout(function(){
					$(this).removeClass('show');
				}.bind(this), 500);
			}
		});

		// show hover for animated things
		//
		$(".thing-content.animated .image img").removeAttr("srcset");
		$(".thing-content.animated").hover(
			function(){
				var image = $(this).find(".image img");
	    		var hover_img = image.data("hover");
	    		hover_img = hover_img.split("?")[0];
	    		// console.log("switch to image: ", hover_img);
	    		image.attr("src", hover_img);
	    	},
	    	// turn off hover image
	    	function(){
				var image = $(this).find(".image img");
	    		var plain_img = image.data("plain");
	    		// console.log("switch back to image: ", plain_img);
	    		image.attr("src", plain_img);
	    	}
    	);

		// find most liked thing
		//
		// init maxLikes and maxThing
		// TODO: Maybe this shouldn't cover individual thing pages
		if ($("body").hasClass("single-post") | $("body").hasClass("home")) {
			var maxLikes = -1;
			var mostLikedThings = [];
			var maxComments = -1;
			var mostCommentedThings = [];
			// loop through all clickable things on page
			$("article .thing-content.clickable").each(function(){
				// get number of likes
				var thisLikes = parseInt($(this).data("likes"));
				// if greater than max likes so far, save thing and likes
				if (thisLikes > maxLikes) {
					maxLikes = thisLikes;
					mostLikedThings = [$(this)];
				} else if (thisLikes == maxLikes) {
					mostLikedThings.push($(this));
				}
				//
				// get number of comments
				var thisComments = parseInt($(this).data("comments"));
				// if greater than max likes so far, save thing and likes
				if (thisComments > maxComments) {
					maxComments = thisComments;
					mostCommentedThings = [$(this)];
				} else if (thisComments == maxComments) {
					mostCommentedThings.push($(this));
				}
			});
			//
			// add 'mostliked' class to thing
			if (maxLikes > 2) {
				$.each(mostLikedThings, function() {
					$(this).addClass('mostliked');
					var winnerText = '(This thing is among the most liked in this issue)';
					$(this).find(".upvote").after('<div class="most-liked-text">' + winnerText + '</div>');
				});
			}
			//
			// add 'mostcommented' class to thing
			if (maxComments > 2) {
				$.each(mostCommentedThings, function() {
					$(this).addClass('mostcommented');
					var winnerText = '(This thing has hella comments)';
					$(this).find(".comment-link").after('<div class="most-commented-text">' + winnerText + '</div>');
				});
			}
		}

		// asign font classes to metadata
		//
		$(".thing-content .metadata").each(function(){
			var titleEl = $(this).find(".title");
			if (titleEl) {
				titleHTML = titleEl.html().trim();
				if (!titleHTML) titleHTML = "";
				var hash = checksum(titleHTML);
				var fontClass = fontOptions[hash % fontOptions.length];
				$(this).addClass(fontClass);
			}
		});

	}

	// STUFF WE DO TO PROFILE PAGES
	//
	if ($("body.um-page-user").length) {
		// for each um_field in profile
		$(".um-field").each(function() {
			// the following bootstrap classes are implemented in our site.css
			// add row class to um-field
			// $(this).addClass("row");
			// add col-sm-2 class to um-field-label
			// $(this).find(".um-field-label").addClass("col-sm-2");
			// add col-sm-4 class to um-field-area
			// $(this).find(".um-field-area").addClass("col-sm-4");
			// Twitter
			var linkEl = $(this).find("[title|='Twitter']");
			if (linkEl.length) {
				var newText = linkEl[0].href.replace(/\?.*$/, "").replace(/\/$/, "").split('/').pop();
				linkEl.html("@" + newText);
			}
			// Instagram
			var linkEl = $(this).find("[title|='Instagram']");
			if (linkEl.length) {
				var newText = linkEl[0].href.replace(/\?.*$/, "").replace(/\/$/, "").split('/').pop();
				linkEl.html(newText);
			}
			// YouTube
			var linkEl = $(this).find("[title|='YouTube']");
			if (linkEl.length) {
				var newText = linkEl[0].href.replace(/\?.*$/, "").replace(/\/$/, "").split('/').pop();
				linkEl.html(newText);
			}
			// Soundcloud
			var linkEl = $(this).find("[title|='SoundCloud']");
			if (linkEl.length) {
				var newText = linkEl[0].href.replace(/\?.*$/, "").replace(/\/$/, "").split('/').pop();
				linkEl.html(newText);
			}
		});
		// Replace "points" with "coin" image
		var replace = $(".um-meta>span:first-child").html().replace('points', '<span class="coin"></span>');
		$(".um-meta>span:first-child").html(replace);
	}

	// STUFF WE DO TO COVER SUBMISSION FORM
	//
	if ($("body.page-id-847").length) {
		// remove everything but "Cover Image" checkbox
		$(".wpcf7-form .field-tags .wpcf7-list-item input:not([value='Cover Image'])").parent().hide();
		// set remaining checkbox set
		$(".wpcf7-form .field-tags .wpcf7-list-item input[value='Cover Image']").prop('checked', true);
	}

	// STUFF WE DO TO SUBMISSION FORM
	//
	if ($("body.page-id-251").length) {
		if ($('.deadline').length) {
			// solution from https://stackoverflow.com/questions/30611586/get-date-of-the-next-15th-in-javascript
			var date = new Date();
			//if 25th of current month is over move to next month
			//need to check whether to use >= or just > ie on 25th Jun
			//if you want 15 Jun then use > else if you want 25 Jul use >=
			var dt = date.getDate();
			date.setDate(25);
			if (dt >= 25) {
			  date.setMonth(date.getMonth() + 1);
			}
			date.setHours(23, 59, 59, 0);
			var nextDeadline = date.toDateString();
			$('.deadline').each(function(){
			    $(this).html($(this).html().replace("##submission-deadline##", nextDeadline));
			})
		}
	}

	//
	// Active screens
	//
	//set the overflow to hidden to make scrollbars disappear
	$(".noscroll").hover(function() {
	    $("body").css("overflow","hidden");
	}, function() {
	     $("body").css("overflow","auto");
	});

}) // $(document).ready
