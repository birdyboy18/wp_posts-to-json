<?php
/*
Plugin Name: WP Posts-to-json
Plugin URI: http://www.github.com/birdyboy18/wp_posts-to-json
Description: A plugin that converts all posts into a json document, including the categories, tags and content attached to them.
Author: Paul Bird
Author URI: http://www.github.com/birdyboy18
Version: 0.0.1
*/

add_action('init', 'ifExists');

//check to see if the file exists first
function ifExists() {
  //Where do you want to write the file to
  $file = plugin_dir_path(__FILE__) . '/posts.json';

  if (file_exists($file)) {
    add_action('post_publish', 'generate_json');
    add_action('post_updated', 'generate_json');
    add_action('delete_post', 'generate_json');
  } else {
    touch('posts.json');
    generate_json();
  }
}

//generate the json file with all the posts in it.
function generate_json() {
  $file = plugin_dir_path(__FILE__) . '/posts.json';
  //set up an array for the posts
  $posts = array();

  //set up the arguments for the query
  $post_args = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post_status' => array('publish')
  );

  $the_query = new WP_Query($post_args);

  //loop through the posts and generate a giant array with it inside

  while ($the_query->have_posts()) {
    $the_query->the_post();

    //strip stuff out of the content, we jsut want the text
    $content = get_the_content();
    $content = wp_strip_all_tags($content);
    $content = preg_replace('/[\n-\r]/', '', $content);
    $content = preg_replace('(\[[^\]]*\])', '',$content);
    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

    $title = get_the_title();
    $title = html_entity_decode($title);

    //get the categories

    $categories = get_the_category();
    $cats = array();

    foreach ($categories as $cat) {
      $cats[] = array(
        'name' => $cat->cat_name,
        'link' => get_category_link($cat->term_id)
      );
    }

    //get the tags
    $post_tags = get_the_tags();
    $tags = array();
    if (is_array($post_tags)) {
      foreach($post_tags as $tag) {
        $tags[] = array(
          'name' => $tag->name,
          'link' => get_tag_link($tag->term_id)
        );
      }
    }

    $data = array(
      'categories' => $cats,
      'tags' => $tags,
      'url' => get_permalink(),
      'date' => get_the_date('jS F Y'),
      'title' => $title,
      'content' => $content,
      'excerpt' => get_the_excerpt(),
      'author' => array(
        'name' => get_the_author(),
        'link' => get_author_posts_url(get_the_author_meta('ID')),
        'avatar' => get_avatar(get_the_author_meta('ID'))
      )
    );
    array_push($posts, $data);
  }

  $json = json_encode($posts);

  file_put_contents($file, $json);
}


?>
