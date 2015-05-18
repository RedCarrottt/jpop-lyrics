<?php
/**
 * @package Jpop_Lyrics
 * @version 2.0
 */
/*
Plugin Name: J-pop Lyrics
Plugin URI: http://wordpress.org/extend/plugins/jpop-lyrics/
Description: Wordpress Assistant for the posts containing J-pop lyrics.
 
Author: Gyeonghwan Hong
Version: 1.2
Author URI: http://redcarottt.com/
*/

function jpop_lyrics_head_func() {
	$css = '<style type="text/css">
		.jpop-lyrics-upper-box {
			padding-left: 10px;
			background: #eeeeee;
		}
		.jpop-lyrics-button-floating {
			position: static;
			z-index: 999;
			top: 0px;
			left: 0px;
			width: 100%;
			display: none;
		}
		.jpop-lyrics-verse {
			margin-bottom: 10px;
		}
		.jpop-lyrics-line {
			width: 100%;
		}
		.jpop-lyrics-line-pron {
			color: #A4A4A4;
		}
		</style>';
	$script = '<script>
		var floatButton = function() {
			var buttonsFloating = jQuery(".jpop-lyrics-button-floating");
			buttonsFloating.css({"display": "block", "position": "fixed", "z-index": "999"});
		};
		var unfloatButton = function() {
			var buttonsFloating = jQuery(".jpop-lyrics-button-floating");
			buttonsFloating.css({"display": "none", "position": "static"});
		};

		var lineVisible = [true, true, true];
		var showHideLine = function(target, visible) {
			var duration_ms = 1000;
			var opacity_val;
			if(visible === true)
				opacity_val = "1";
			else
				opacity_val = "0";

			var target_str;
			switch(target) {
			case 1:
				target_str = ".jpop-lyrics-line-orig";
				break;
			case 2:
				target_str = ".jpop-lyrics-line-pron";
				break;
			case 3:
				target_str = ".jpop-lyrics-line-tran";
				break;
			};

			if(typeof target_str !== "undefined") {
				lineVisible[target] = visible;
				jQuery(target_str).animate({"opacity": opacity_val}, duration_ms);
			}
		};
		var flipLine = function(target) {
			showHideLine(target, !lineVisible[target]);
		};

		var onJpopMode = function(mode) {
			switch(mode) {
			case 1:
				showHideLine(1, true);
				showHideLine(2, true);
				showHideLine(3, true);
				break;
			case 2:
				showHideLine(1, true);
				showHideLine(2, false);
				showHideLine(3, true);
				break;
			case 3:
				showHideLine(1, true);
				showHideLine(2, false);
				showHideLine(3, false);
			break;
			}
		};
		var onLineMouseDown = function(target) {
			switch(target) {
			case 1:
				flipLine(1);
				break;
			case 2:
				flipLine(2);
				break;
			case 3:
				flipLine(3);
				break;
			}
			return false;
		};
</script>';
	echo $css . $script;
}

function jpop_lyrics_short_func($attrs, $content = null) {
	$output = '';
	$verses = explode('</p>', $content);
//	$content = str_replace(
//		array('<p>', '</p>'), 
//		array('<div class="jpop-lyrics-verse">', '</div>'), 
//		$content);
	foreach($verses as $verse) {
		$verse = str_replace('<p>','', $verse);
		$verse_inner = '';
		$lines = explode('<br />', $verse);
		$i = 0;
		foreach($lines as $line_idx => $line) {
			$nospace_line = preg_replace('/\s+/', '', $line);
			if(strlen($nospace_line) == 0) {
				unset($lines[$line_idx]);
			}
		}

		foreach($lines as $line) {
			$line_output = '';
			switch($i) {
			case 0: // Original language
				#$line_output = '<div class="jpop-lyrics-line-orig jpop-lyrics-line" onmousedown="onLineMouseDown(1);" onmouseup="onLineMouseUp();">' . $line . '</div>';
				$line_output = '<div class="jpop-lyrics-line-orig jpop-lyrics-line"">' . $line . '</div>';
				break;
			case 1: // Pronounciation or translated
				if(count($lines) == 2) {
					$line_output = '<div class="jpop-lyrics-line-tran jpop-lyrics-line" onclick="onLineMouseDown(3);">' . $line . '</div>';
				} else {
					$line_output = '<div class="jpop-lyrics-line-pron jpop-lyrics-line" onclick="onLineMouseDown(2);">' . $line . '</div>';
				}
				break;
			default: // Translated
				$line_output = '<div class="jpop-lyrics-line-tran jpop-lyrics-line" onclick="onLineMouseDown(3);">' . $line . '</div>';
				break;
			}
			$verse_inner = $verse_inner . $line_output;
			$i = $i + 1;
		}

		$verse_output = '<div class="jpop-lyrics-verse">' . $verse_inner . '</div>';
		$output = $output . $verse_output;
	}

	$upper_buttons = '<div class="jpop-lyrics-upper-box jpop-lyrics-button-static">
		<a onClick="onJpopMode(1);">원어-발음-번역</a> |
		<a onClick="onJpopMode(2);">원어-번역</a> |
		<a onClick="onJpopMode(3);">원어</a> |
	  <a onClick="floatButton();">고정</a> </div>';

	$upper_info = '<div class="jpop-lyrics-upper-box">
		* 발음 및 번역은 클릭하여 숨길 수 있습니다.</div>';
	
	//return '<div class="jpop-lyrics-block">' . $upper_buttons . $output .'</div>';
	return '<div class="jpop-lyrics-block">' . $upper_info . $output .'</div>';
}

function jpop_lyrics_footer_func() {
	$div_tag = '<div class="jpop-lyrics-upper-box jpop-lyrics-button-floating">
		<a onClick="onJpopMode(1);">원어-발음-번역</a> |
		<a onClick="onJpopMode(2);">원어-번역</a> |
		<a onClick="onJpopMode(3);">원어</a> |
	  <a onClick="unfloatButton();">닫기</a>	</div>';
	echo $div_tag;
}

add_action('wp_head', 'jpop_lyrics_head_func');
add_action('wp_footer', 'jpop_lyrics_footer_func');
add_shortcode('jpop-lyrics', 'jpop_lyrics_short_func');

?>
