<?php


/**
 * Convert a JSON string coming from a Wordpress REST API Posts Response into Markdown
 *
 * @param string $json_data
 *
 * @return string
 */
function convertJsonToMarkdown($json_data)
{

    // $wp_post = json_decode($json_data, true);

    $wp_post = $json_data;

    $wp_post_id = $wp_post['id'];

    // check if post id in blacklist
    $blacklist = [1508, 1509, 1510, 1511, 1512, 1513, 1514, 1515];

    // if (in_array($wp_post_id, $blacklist)) {
    //     return;
    // }

    if ($wp_post_id == 1508) {
        return;
    }

    $wp_date = new DateTime($wp_post['date']);

    $title = $wp_post['title']['rendered'];

    $description = $wp_post['excerpt']['rendered'];

    $published_date = $wp_date->format('Y-m-d H:i:s');

    $modified_date = (new DateTime($wp_post['modified']))->format('Y-m-d H:i:s');

    $filename = $wp_date->format('Y-m-d') . '-' . $wp_post['slug'] . '.md';

    echo "🗳️ \t Writing file: " . $filename . PHP_EOL;

    // wordpress featured media image from rest api
    $wp_featured_media = $wp_post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? 'https://doy.tech/wp-content/uploads/default_stunning_gaming_wallpaper_3_b8302f81-478c-4c59-9b62-b9608edbbec0_1_upscale_smooth-scaled-e1694067879165.jpg';

    // render the content
    $content = $wp_post['content']['rendered'];
    $content = str_replace("\n", "\n\n", $content);



    $md_output = "---
layout: post
title: $title
description: $description
image: $wp_featured_media
date: $published_date
modified: $modified_date
tags: [\"$wp_post[slug]\"]
wpId: $wp_post_id
---
$content

";

    $md_output .= "Inspired by $wp_post[author]\n\n";

    $md_output .= "[$wp_post[link]]($wp_post[link])\n";

    file_put_contents(__DIR__ . '/../_drafts/' . $filename, $md_output);


    // return $markdown_output;

    return $filename;

}

// $post_json = file_get_contents(__DIR__ . '/single-post.json');

// convertJsonToMarkdown($post_json);

$posts_json = file_get_contents(__DIR__ . '/wp-posts.json');

$posts = json_decode($posts_json, true);

echo "📂 \t Converting " . count($posts) . " posts..." . PHP_EOL;

foreach ($posts as $post) {

    convertJsonToMarkdown($post);

}

echo "🎉 \t Done!" . PHP_EOL;