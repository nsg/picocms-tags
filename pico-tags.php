<?php

/**
 * Pico CMS - Tags plugin
 *
 * Access /tag/foo or /tags/foo to list all pages 
 * with that tag. Specify tags with Tags: foo, bar
 * in the markdown file. Like this:
 *
 * Tags: foo, bar
 *
 * You have a list of the current pages tags in
 * {{ meta.tags }} in the template. Use it like
 * this:
 * 
 * {% for tag in meta.tags %}
 *   <a href="/tag/{{ tag.url }}">{{ tag.name }}</a>
 *   {% if not tag.last %}, {% endif %}
 * {% endfor %}
 *
 * The tag page (/tag) uses a template called
 * tags.html.
 *
 * @author Stefan Berggren
 * @license http://opensource.org/licenses/MIT
 */

class Pico_Tags {

	private $base_url;
	private $tag;
	private $all_tags;

	public function __construct() {
		$this->all_tags = array();
	}

	public function request_url(&$url) {
		if (preg_match("/^tags?\/(.*)/", $url, $m)) {
			$this->tag = strtolower($m[1]);
		}
	}

	public function before_render(&$twig_vars, &$twig, &$template) {
		if ($this->is_tag_page()) {
			header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
			$template = 'tags';
			$page_tags = array();

			# Scan all pages and collect tags
			foreach($twig_vars['pages'] as $page) {
				$path = "content/" . substr($page['url'], strlen($this->base_url));
				$path = file_exists("${path}.md") ? "${path}.md" : "${path}/index.md";
				$content = file_get_contents("$path");
				if (preg_match("/Tags: (.*)/", $content, $m)) {
					$tags = preg_split("/, +/U", strtolower($m[1]));
					if (in_array($this->tag, $tags)) {
						$page_tags[] = array(
							"url" => $page['url'],
							"name" => $page['title']
						);
					}
				}
			}

			$twig_vars["meta"]["tags"] = $page_tags;
			$twig_vars["meta"]["title"] = $this->tag;
		}
	}

	public function before_read_file_meta(&$headers) {
		$headers['tags'] = 'Tags';
	}

	public function config_loaded(&$settings) {
		$this->base_url = $settings['base_url'];
	}

	public function file_meta(&$meta) {
		if (isset($meta['tags']) && !is_array($meta['tags']) && $meta['tags'] !== '') {
			$meta['tags'] = preg_split("/, +/U", $meta['tags']);
			foreach($meta['tags'] as $k => $tag) {
				$meta['tags'][$k] = array(
					"url" => strtolower($tag),
					"name" => $tag,
					"last" => ($k == count($meta['tags']) - 1)
				);
			}
		}
	}

	private function is_tag_page() {
		return isset($this->tag) && !empty($this->tag);
	}
}

?>
