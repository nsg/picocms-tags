# picocms-tags

A Pico CMS tags plugin

## Install

1. Place `pico-tags.php` in `plugins/`.
2. Create `tags.html` in your theme in `themes/` (or use the provided example).
3. All done, now edit a few markdown files and add tags, for example:

```
/*
 Title: Pebble
 Tags: Foo, Bar, Baz
*/
```

## List all pages tagged with foo

Now, go to `http://example.com/tag/foo` to list the page `Pebble`. Note, case is not important and "tags" 
is also supported, for example `http://example.com/tags/Foo`.

## List all tags for a page

You have a list of the current page tags in {{ meta.tags }} in the template. Use it like this:

```
{% for tag in meta.tags %}
  <a href="/tag/{{ tag.url }}">{{ tag.name }}</a>
  {% if not tag.last %}, {% endif %}
{% endfor %}
```
