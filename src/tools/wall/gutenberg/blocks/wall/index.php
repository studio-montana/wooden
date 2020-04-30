<?php

class WKG_Module_Block_wall extends WKG_Module_Block {

	private $id;
	private $align;
	private $items;
	private $items_source;
	private $items_query;
	private $display;
	private $thumbsize;
	private $format;
	private $filter;
	private $link_item_id;
	private $link_item_type;
	private $link_title;
	private $columns;
	private $maxwidth;
	private $maxwidth_custom;
	private $maxheight;
	private $maxheight_custom;
	private $margin_horizontal;
	private $margin_vertical;

	private $wall_items;

	function __construct() {
		parent::__construct('wall', array(
				'uri' => wooden_get_tools_directory_uri().'wall/gutenberg/blocks/wall/', // must be explicitly defined to support symbolic link context
		));
		add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
	}

	public function wp_enqueue_scripts () {
		wp_enqueue_script('woodkit-js-masonry');
	}

	public function render (array $attributes, $content) {
		// https://github.com/WordPress/gutenberg/issues/16319
		// pour utiliser les InnerBlocks dans un bloc dynamic (rendu en PHP),
		// il faut utiliser le paramètre $content passé en second
		// Cela vient du bloc React qui renvoit un save ()
		ob_start ();
		// print_r($attributes);
		$this->id = isset($attributes['id']) ? str_replace('-', '', $attributes['id']) : uniqid('wkg');
		$this->align = isset($attributes['align']) ? $attributes['align'] : '';
		$this->items = isset($attributes['items']) ? json_decode($attributes['items'], true) : array();
		$this->items_source = isset($attributes['items_source']) ? $attributes['items_source'] : 'post';
		$this->items_query = isset($attributes['items_query']) ? json_decode($attributes['items_query'], true) : array();
		$this->display = isset($attributes['display']) ? $attributes['display'] : 'grid';
		$this->thumbsize = isset($attributes['thumbsize']) ? $attributes['thumbsize'] : 'large';
		$this->format = isset($attributes['format']) ? $attributes['format'] : 'square';
		$this->filter = isset($attributes['filter']) ? $attributes['filter'] : 'none';
		$this->filter_multiple = isset($attributes['filter_multiple']) ? $attributes['filter_multiple'] : false;
		$this->link_item_id = isset($attributes['link_item_id']) ? $attributes['link_item_id'] : 'none';
		$this->link_item_type = isset($attributes['link_item_type']) ? $attributes['link_item_type'] : 'none';
		$this->link_title = isset($attributes['link_title']) ? $attributes['link_title'] : '';
		$this->columns = isset($attributes['columns']) ? intval($attributes['columns']) : 3;
		$this->maxwidth = isset($attributes['maxwidth']) ? $attributes['maxwidth'] : '25%';
		$this->maxwidth_custom = isset($attributes['maxwidth_custom']) ? intval($attributes['maxwidth_custom']) : 250;
		$this->maxheight = isset($attributes['maxheight']) ? $attributes['maxheight'] : 'auto';
		$this->maxheight_custom = isset($attributes['maxheight_custom']) ? intval($attributes['maxheight_custom']) : 250;
		$this->margin_horizontal = isset($attributes['margin_horizontal']) ? intval($attributes['margin_horizontal']) : 3;
		$this->margin_vertical = isset($attributes['margin_vertical']) ? intval($attributes['margin_vertical']) : 3;
		$this->space_before = isset($attributes['space_before']) ? intval($attributes['space_before']) : 0;
		$this->space_after = isset($attributes['space_after']) ? intval($attributes['space_after']) : 0;

		// parse items_query params
		$this->items_query = wp_parse_args($this->items_query, array(
			'post_type' => 'post', // sera surchargé par items_source
			'orderby' => 'date',
			'order' => 'desc',
			'numberposts' => '-1',
		));

		// error_log('wall items_query : ' . var_export($this->items_query, true));

		$this->wall_items = array();
		if ($this->items_source && $this->items_source !== '_none' && $this->items_source !== 'attachment' && $this->items_source !== '_custom') {
			// wall dynamic, on requête...
			// important : quand la source est un type de contenu, on effectue une requête pour récupérer les éléments de façon dynamique
			// le block wall avait sauvegardé les éléments au moment de sa création ('data' dans le tableau d'items) mais on les écrase
			// avec le résultat de notre requête. Ce principe n'est pas une erreur en soi, wall pourrait très bien demander à s'afficher
			// dans l'état où il était au moment de sa création (qui peut le plus, peut le moins)
			$query_items = wkg_wall_get_items(array_merge($this->items_query, array('post_type' => $this->items_source)), $this->thumbsize);
			if (!empty($query_items)) {
				for ($i = 0; $i < count($query_items); $i++) {
					$wall_item = isset($this->items[$i]) ? $this->items[$i] : array(); // récuparation de l'item au moment de la création du wall
					$wall_item['data'] = $query_items[$i]; // on écrase les data mais on garde le reste correspondant au spécificités graphiques de l'item (colonnes/lignes/titres personnalisé/etc.)
					$this->wall_items[] = $wall_item;
				}
			}
		} else if ($this->items_source && $this->items_source === 'attachment') {
			// wall static, on se contente de récupérer les items
			$this->wall_items = $this->items;
		} else if ($this->items_source && $this->items_source === '_custom') {
			// wall static, on se contente de récupérer les items
			$this->wall_items = $this->items;
		}

		$style_container = array(
			'margin-top' => $this->space_before . 'px',
			'margin-bottom' => $this->space_after . 'px',
		);
		$class_container = array();

		if ($this->align) {
			$class_container[] = 'align' . $this->align;
		}

		// wall classes
		$wall_classes = array('wall', $this->display);
		if ($this->display === 'masonry') {
			$wall_classes[] = $this->maxwidth !== 'custom' && strpos($this->maxwidth, '%') !== false ? 'columns-' . floor(100 / floatval(str_replace('%', '', $this->maxwidth))) : '';
		} else {
			$wall_classes[] = 'columns-' . $this->columns;
		}

		// wall style
		$wall_style = array();
		$wall_style['opacity'] = '0';
		$wall_style['width'] = 'calc(100% + ' . $this->margin_horizontal . 'px)';
		$wall_style['margin-left'] = '-' . $this->margin_horizontal . 'px';
		$wall_style['margin-top'] = '-' . $this->margin_vertical . 'px';

		// masonry
		$masonry_grid_li = null;
		$masonry_options = array();
		if ($this->display === 'masonry') {
			if ($this->maxwidth === 'custom') {
				$masonry_options['columnWidth'] = $this->maxwidth_custom;
			} else {
				// ce li sert à Masonry à déterminer la largeur de base pour l'affichage en pourcentage
				// https://masonry.desandro.com/options.html#element-sizing
				$masonry_grid_li = '<li class="masonry-grid-li" style="width: ' . $this->maxwidth . '"></li>';
				$masonry_options['percentPosition'] = true;
			}
		} else {
			// ce li sert à Masonry à déterminer la largeur de base pour l'affichage en pourcentage
			// https://masonry.desandro.com/options.html#element-sizing
			$masonry_grid_li = '<li class="masonry-grid-li" style="width: ' . (100 / $this->columns) . '%"></li>';
			$masonry_options['percentPosition'] = true;
		}

		?>
		<div id="<?php echo $this->id; ?>" class="<?php echo esc_attr($this->getFrontClasses($class_container)); ?>" style="<?php echo $this->implode_styles($style_container); ?>">
			<div class="content">
				<?php $this->render_filter(); ?>
				<ul id="wall-<?php echo $this->id; ?>" class="<?php echo implode(' ', $wall_classes); ?>" style="<?php echo $this->implode_styles($wall_style); ?>">
					<?php echo $masonry_grid_li; ?>
					<?php if (!empty($this->wall_items)) {
						foreach ($this->wall_items as $wall_item) {
							$this->render_item($wall_item);
						}
					} ?>
				</ul>
			</div>
		</div>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function () {
					$('#wall-<?php echo $this->id; ?>').masonry(<?php echo json_encode($masonry_options); ?>);
					$('#wall-<?php echo $this->id; ?>').css('opacity', 1);
					$('#wall-filters-<?php echo $this->id; ?> *[data-filter]').on('click', function () {
						if ($(this).hasClass('active')) {
							$(this).removeClass('active');
						} else {
							if (!<?php echo $this->filter_multiple ? 'true' : 'false'; ?>) {
								$('#wall-filters-<?php echo $this->id; ?> *[data-filter]').removeClass('active');
							}
							$(this).addClass('active');
						}
						let filters = []
						$('#wall-filters-<?php echo $this->id; ?> .active[data-filter]').each(function (i){
							filters.push($(this).data('filter'));
						});
						$('#wall-<?php echo $this->id; ?> .wall-item').each(function (i) {
							let wall_item = $(this);
							let valid = true;
							if (filters.length > 0) {
								valid = filters.every(function (filter) {
									let item_filter_terms = wall_item.data('filter');
									return item_filter_terms && item_filter_terms.includes(filter);
								});
							}
							if (valid) {
								$(this).css('display', 'block');
							} else {
								$(this).css('display', 'none');
							}
						});
						// mise à jour de Masonry une que les items sont filtrés
						// NOTE : on n'utilise pas Isotope par principe de légèreté et pour coller au BO (le block Wall utilise Masonry
						// car Isotope n'est pas implémenté en React), on utilise Masonry et ça fait bien le taf.
						// La contrainte est qu'il faut nous-même implémenter les filtres - mais on en est capable ;)
						$('#wall-<?php echo $this->id; ?>').masonry(<?php echo json_encode($masonry_options); ?>);
					});
				});
			})(jQuery);
		</script>
		<?php return ob_get_clean();
	}

	private function render_filter () {
		if ($this->filter === 'taxonomy') {
			$filter_terms = array();
			$taxonomies = get_object_taxonomies($this->items_source);
			if (!empty($taxonomies)) {
				foreach ($taxonomies as $taxonomy) {
					$args = array('taxonomy' => $taxonomy, 'hide_empty' => true);
					if (is_taxonomy_hierarchical($taxonomy)) {
						$args['parent'] = 0;
					}
					$terms = get_terms($args);
					if (!empty($terms)) {
						$filter_terms = array_merge($filter_terms, $terms);
					}
				}
			}
			if (!empty($filter_terms)) { ?>
				<ul id="wall-filters-<?php echo $this->id; ?>" class="wall-filters taxonomy">
					<?php foreach ($filter_terms as $term) { ?>
						<li class="filter-item term term-<?php echo $term->term_id; ?>" data-filter="<?php echo $term->slug; ?>"><?php echo $term->name; ?></li>
					<?php } ?>
				</ul>
			<?php }
		} else if ($this->filter === 'search') { ?>
			<div id="wall-filters-<?php echo $this->id; ?>" class="wall-filters search"><input type="search" placeholder="Filtre..." name="wall-filter" /></div>
		<?php }
	}

	private function render_item (array $item) {
		// --- item data
		$item_data = isset($item['data']) ? $item['data'] : array();
		if (isset($item_data['id'])) {
			$post = get_post($item_data['id']);
			if ($post) {
				// --- item filter terms
				$itemFilterTerms = $this->get_item_filter_terms($item, $post);

				// --- item styles
				$itemStyle = array();
				$itemContentStyle = array('width' => '100%');
				$itemInnerStyle = array(
					'position' => 'relative',
					'margin-left' => $this->margin_horizontal . 'px',
					'margin-top' => $this->margin_vertical . 'px'
				);

				$custom_columns = isset($item['custom_columns']) ? intval($item['custom_columns']) : 1;
				$custom_lines = isset($item['custom_lines']) ? intval($item['custom_lines']) : 1;

				$maxwidth = $this->maxwidth !== 'custom' ? $this->maxwidth : $this->maxwidth_custom . 'px';
				$maxheight = $this->maxheight !== 'custom' ? $this->maxheight : $this->maxheight_custom . 'px';

				if ($this->display === 'masonry') {
					$itemStyle = array_merge($itemStyle, array(
						'width' => '100%',
						'height' => 'auto',
						'max-width' => $maxwidth,
						'max-height' => $maxheight !== 'auto' ? $maxheight : 'none'
					));
				} else {
					$paddingBottom = 100;
					if ($this->format === 'portrait') {
						$paddingBottom *= 1.618;
					} else if ($this->format === 'landscape') {
						$paddingBottom /= 1.618;
					}
					$paddingBottom = ($paddingBottom * $custom_lines) / $custom_columns;
					$width = (100 / $this->columns) * $custom_columns;
					$itemStyle = array_merge($itemStyle, array('width' =>  $width . '%'));
					$itemContentStyle = array_merge($itemContentStyle, array(
						'width' => '100%',
						'height' => 0,
						'padding-bottom' => $paddingBottom . '%',
					));
					$itemInnerStyle = array_merge($itemInnerStyle, array(
						'position' => 'absolute',
						'left' => '0px',
						'right' => '0px',
						'top' => '0px',
						'bottom' => '0px',
						'overflow' => 'hidden',
					));
				} ?>
				<li class="wall-item custom-columns-<?php echo $custom_columns; ?>
					custom-lines-<?php echo $custom_lines; ?>"
					style="<?php echo $this->implode_styles($itemStyle); ?>"
					data-filter="<?php echo implode(' ', $itemFilterTerms); ?>"
				>
					<div class="wall-item-content" style="<?php echo $this->implode_styles($itemContentStyle); ?>">
						<div class="wall-item-inner" style="<?php echo $this->implode_styles($itemInnerStyle); ?>">
							<?php $this->render_item_content($item, $post); ?>
						</div>
					</div>
				</li>
			<?php }
		}
	}

	private function render_item_content (array $item, object $post) {
		$render = '';
		$item_data = isset($item['data']) ? $item['data'] : array();
		$item_title = isset($item['custom_title']) ? $item['custom_title'] : get_the_title($post);
		$item_link = isset($item['custom_link']) ? $item['custom_link'] : '';
		$item_link_target = isset($item['custom_link_target']) ? $item['custom_link_target'] : '_self';
		$item_link_class = array('wall-item-link');
		$item_link_rel = '';
		$item_link_add_attrs = array();
		if (get_post_type($post) === 'attachment') {
			/***************************************
			 * Attachment item
			 ***************************************/
			if (empty($item_link)) {
				$item_link_add_attrs['data-fancybox'] = 'wall-group-' . $this->id;
				$attachment = wp_get_attachment_image_src($item_data['id'], 'full');
				if ($attachment) {
					list($item_link) = $attachment;
				}
			}
			/* render */
			$render = wp_get_attachment_image($item_data['id'], $this->thumbsize);
			$render = apply_filters('wall_render_item_attachment', $render, $item, $post, $this);
			/* mask */
			$mask = '<div class="mask"><span class="title title-plus">+</span></div>';
			$mask = apply_filters('wall_render_item_attachment_mask', $mask, $item, $post, $this);
		} else {
			/***************************************
			 * Any post item
			 ***************************************/
			if (empty($item_link)) {
				$item_link = get_the_permalink($post);
			}
			$custom_content = isset($item['custom_content']) ? $item['custom_content'] : 'thumb';
			if ($custom_content === 'thumb' && has_post_thumbnail($post)) {
				/******************
				 * Content thumb
				 *****************/
			 	/* render */
				$render = get_the_post_thumbnail($post, $this->thumbsize);
				$render = apply_filters('wall_render_item_post_thumb', $render, $item, $post, $this);
				/* mask */
				$mask = '<div class="mask"><span class="title">' . $item_title . '</span></div>';
				$mask = apply_filters('wall_render_item_post_mask_thumb', $mask, $item, $post, $this);
			} else {
				/******************
				 * Content default
				 *****************/
				/* render */
				ob_start(); ?>
				<h4 className="title"><?php echo $item_title; ?></h4>
				<div className="excerpt"><?php echo get_the_excerpt($post); ?></div>
				<?php $render = ob_get_clean();
				$render = apply_filters('wall_render_item_post_content', $render, $item, $post, $this);
				/* mask */
				$mask = apply_filters('wall_render_item_post_mask_content', '', $item, $post, $this);
			}
			/* render */
			$render = apply_filters('wall_render_item_post', $render, $item, $post, $this);
			/* mask */
			$mask = apply_filters('wall_render_item_post_mask', $mask, $item, $post, $this);
		}
		/* render */
		ob_start(); ?>
		<a href="<?php echo $item_link; ?>" target="<?php echo $item_link_target; ?>" <?php echo $this->implode_tag_attrs($item_link_add_attrs); ?> class="<?php echo implode(' ', $item_link_class); ?>" rel="<?php echo $item_link_rel; ?>" title="<?php echo esc_attr($item_title); ?>">
			<?php echo $render; ?>
			<?php echo $mask; ?>
		</a>
		<?php
		$render = ob_get_clean();

		/** Apply filters for wall item overriding */
		echo apply_filters('wall_render_item', $render, $item, $post, $this);
	}

	private function get_item_filter_terms (array $item, object $post) {
		$filter_terms = array();
		if ($post) {
			$taxes = array();
			foreach (get_object_taxonomies($post) as $taxonomy) {
				$terms = get_object_term_cache($post->ID, $taxonomy );
				if ( false === $terms ) {
					$terms = wp_get_object_terms($post->ID, $taxonomy );
				}
				$terms_str = array();
				foreach ($terms as $term) {
					$terms_str[] = $term->slug;
				}
				if ($terms_str) {
					$taxes[$taxonomy] = $terms_str;
				}
			}
			if (!empty($taxes)){
				foreach ($taxes as $tax => $terms) {
					$filter_terms = array_merge($filter_terms, $terms);
				}
			}
		}
		return $filter_terms;
	}
}
new WKG_Module_Block_wall();
