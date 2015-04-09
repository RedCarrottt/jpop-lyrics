<?php
/**
 * @package Jpop_Lyrics
 * @version 1.1
 */
/*
Plugin Name: J-pop Lyrics
Plugin URI: http://wordpress.org/extend/plugins/jpop-lyrics/
Description: Wordpress Assistant for the posts containing J-pop lyrics.
 
Author: Gyeonghwan Hong
Version: 1.1
Author URI: http://redcarottt.com/
*/

function jpop_lyrics_head_func() {
	$css = '<style type="text/css">
		.jpop-lyrics-buttons {
			padding-left: 10px;
			background: #eeeeee;
		}
		.jpop-lyrics-verse {
			margin-bottom: 10px;
		}
		</style>';
	$script = '<script>
		var onJpopMode = function(mode) {
			switch(mode) {
			case 1:
				jQuery(".jpop-lyrics-line-orig").animate({"opacity": "1"}, 500);
				jQuery(".jpop-lyrics-line-pron").animate({"opacity": "1"}, 500);
				jQuery(".jpop-lyrics-line-tran").animate({"opacity": "1"}, 500);
				break;
			case 2:
				jQuery(".jpop-lyrics-line-orig").animate({"opacity": "1"}, 500);
				jQuery(".jpop-lyrics-line-pron").animate({"opacity": "0"}, 500);
				jQuery(".jpop-lyrics-line-tran").animate({"opacity": "1"}, 500);
				break;
			case 3:
				jQuery(".jpop-lyrics-line-orig").animate({"opacity": "1"}, 500);
				jQuery(".jpop-lyrics-line-pron").animate({"opacity": "0"}, 500);
				jQuery(".jpop-lyrics-line-tran").animate({"opacity": "0"}, 500);
			break;
			}
		}
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
				$line_output = '<div class="jpop-lyrics-line-orig">' . $line . '</div>';
				break;
			case 1: // Pronounciation or translated
				if(count($lines) == 2) {
					$line_output = '<div class="jpop-lyrics-line-tran">' . $line . '</div>';
				} else {
					$line_output = '<div class="jpop-lyrics-line-pron">' . $line . '</div>';
				}
				break;
			default: // Translated
				$line_output = '<div class="jpop-lyrics-line-tran">' . $line . '</div>';
				break;
			}
			$verse_inner = $verse_inner . $line_output;
			$i = $i + 1;
		}

		$verse_output = '<div class="jpop-lyrics-verse">' . $verse_inner . '</div>';
		$output = $output . $verse_output;
	}

	$upper_buttons = '<div class="jpop-lyrics-buttons">
		<b>J-pop Viewer</b>:
		<a onClick="onJpopMode(1);">원어-발음-번역</a> |
		<a onClick="onJpopMode(2);">원어-번역</a> |
		<a onClick="onJpopMode(3);">원어</a> </div>';
	
	return '<div class="jpop-lyrics-block">' . $upper_buttons . $output .'</div>';
}

add_action('wp_head', 'jpop_lyrics_head_func');
add_shortcode('jpop-lyrics', 'jpop_lyrics_short_func');

?>
