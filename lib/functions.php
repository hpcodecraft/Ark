<?php

  function renderPagination($mode, $pages, $collection) {
    if($mode === 'post') return '';

  	$html = '';

    $collection_fragment = '';
    if($mode === 'collection') $collection_fragment = 'collection/'.$collection.'/';

  	if(isset($pages)) {
  		$html.= '<div class="navigation">';

      $page_fragment = 'page/'.$pages['prev'];
      if($pages['current'] == 2) $page_fragment = '';

  		if( $pages['prev']) {
  			$html.= '<a href="'.ASAPH_LINK_PREFIX.$collection_fragment.$page_fragment.'" class="pageleft">«</a>';
  		}
  		else {
  			$html.= '<a href="" style="visibility:hidden" class="pageleft hidden">«</a>';
  		}

  		$html.= '<div class="all-pages">';

  		for($i=1; $i<=$pages['total']; $i++) {
        $page_fragment = 'page/'.$i;
        if($i == 1) $page_fragment = '';

  			if($i == $pages['current']) {
  				$html.= '<a class="active" href="'.ASAPH_LINK_PREFIX.$collection_fragment.$page_fragment.'">'.$i.'</a> ';
  			}
  			else {
  				$html.= '<a href="'.ASAPH_LINK_PREFIX.$collection_fragment.$page_fragment.'">'.$i.'</a> ';
  			}
  		}

  		$html.= '</div>
  		<a href="#" class="jump-to-page">jump</a>';

  		if( $pages['next']) {
  				$html.= '<a href="'.ASAPH_LINK_PREFIX.$collection_fragment.'page/'.$pages['next'].'" class="pageright">»</a>';
  		}
  		else {
  				$html.= '<a href="" style="visibility:hidden" class="pageright hidden">»</a>';
  		}

  		$html.= '</div>';
  	}

  	return $html;
  }

  function extractText($node) {
    return $node->textContent;

    if($node->nodeType == XML_TEXT_NODE)
      return $node->textContent;
    else if($node->nodeType == XML_ELEMENT_NODE) {
      $text = "";
      foreach($node->childNodes as $n) {
        $text=$text.extractText($n);
      }

      if($node->nodeName == "p") {
        $text = $text." \n";
      }
      if($node->nodeName == "br") {
        $text = $text." \n";
      }
      return $text;
    }
    else
      return "";
  }
?>
