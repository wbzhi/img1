<?php

/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @author	Rodolfo Berrios A. <http://rodolfoberrios.com/>
            <inbox@rodolfoberrios.com>

  Copyright (C) Rodolfo Berrios A. All rights reserved.

  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  http://chevereto.com/license

  --------------------------------------------------------------------- */

$route = function ($handler) {
    try {
        if ($handler->isRequestLevel(3)) {
            return $handler->issue404();
        } // Allow only 2 levels

        if (is_null($handler->request[0])) {
            return $handler->issue404();
        }

        $logged_user = CHV\Login::getUser();

        // User status override redirect
        CHV\User::statusRedirect($logged_user['status']);
        $id = CHV\getIdFromURL($handler->request[0]);

        if ($id == false) {
            return $handler->issue404();
        }
        // Trail this view
        $_SESSION['last_viewed_image'] = CHV\encodeId($id);

        // Session stock viewed images
        if (!$_SESSION['image_view_stock']) {
            $_SESSION['image_view_stock'] = [];
        }

        // Get image DB
        $image = CHV\Image::getSingle($id, !in_array($id, $_SESSION['image_view_stock']), true, $logged_user);

        if ($image && $image['url_viewer'] != G\get_current_url()) {
            G\redirect($image['url_viewer']);
        }

        $handler::setVar('canonical', $image['url_viewer']);

        // No image or belongs to a banned user if exists?
        if (!$image || (!$handler::getCond('content_manager') && $image['user']['status'] == 'banned')) {
            return $handler->issue404();
        }

        // Test local images
        if ($image['file_resource']['type'] == 'path') {
            if (!$image['file_resource']['chain']['image'] || !file_exists($image['file_resource']['chain']['image'])) {
                // return $handler->issue404();
            }
            // Update is_animated flag
            if (!$image['is_animated'] && G\is_animated_image($image['file_resource']['chain']['image'])) {
                CHV\Image::update($id, ['is_animated' => 1]);
                $image['is_animated'] = 1;
            }
        }

        /*
            Note: Remote image testing was removed because of the HUGE number of websites running external containers unaccesible via HTTP.
            Remote image test works only if the website can fetch the image URI headers.
            Check Chevereto < 3.8.4 for the old remote image tester code
        */

        $is_owner = $image['user']['id'] !== null ? ($image['user']['id'] == $logged_user['id']) : false;

        // Privacy
        if (CHV\getSetting('website_privacy_mode') == 'private') {
            if ($handler::getCond('forced_private_mode')) {
                $image['album']['privacy'] = CHV\getSetting('website_content_privacy_mode');
            }
            if (!CHV\Login::getUser() && $image['album']['privacy'] != 'private_but_link') {
                G\redirect('login');
            }
        }

        // Password protected content
        if (!($handler::getCond('content_manager') || $is_owner) && $image['album']['privacy'] == 'password' && !CHV\Album::checkSessionPassword($image['album'])) {
            G\redirect($image['album']['url']);
        }

        // Private profile
        if ($image['user']['is_private'] && !$handler::getCond('content_manager') && $image['user']['id'] !== $logged_user['id']) {
            unset($image['user']);
            $image['user'] = CHV\User::getPrivate();
        }

        if (!$handler::getCond('content_manager') and in_array($image['album']['privacy'], array('private', 'custom')) and !$is_owner) {
            return $handler->issue404();
        }

        $db = CHV\DB::getInstance();

        // User found
        if ($image['user']['id'] !== null) {
            // Get user albums
            $name_array = explode(' ', $image['user']['name']);
            $user_name_short = $name_array[0];

            $image['user']['albums'] = [];

            // Lets fake the stream as an album
            $image['user']['albums']['stream'] = CHV\User::getStreamAlbum($image['user']);

            // Get user album list
            $image['user']['albums'] += CHV\DB::get('albums', ['user_id' => $image['user']['id']], 'AND', ['field' => 'name', 'order' => 'asc']);

            foreach ($image['user']['albums'] as $k => $v) {
                $image['user']['albums'][$k] = CHV\DB::formatRow($v, 'album');
                CHV\Album::fill($image['user']['albums'][$k]);
            }
        }

        // Get the album slice
        if ($image['album']['id'] !== null) {
            $get_album_slice = CHV\Image::getAlbumSlice($image['id'], $image['album']['id'], 2);
            $image_album_slice = array_merge($image['album'], $get_album_slice);
        }

        $image_safe_html = G\safe_html($image);

        $image['alt'] = $image_safe_html['description'] ?: ($image_safe_html['title'] ?: $image_safe_html['name']);

        $pre_doctitle = $image_safe_html['title'] ?: ($image_safe_html['name'] . '.' . $image_safe_html['extension']) . ' hosted at ' . CHV\getSetting('website_name');

        $tabs = [
            [
                'label' => _s('About'),
                'id' => 'tab-about',
            ],
        ];
        if (CHV\isShowEmbedContent()) {
            $tabs[] = [
                'label' => _s('Embed codes'),
                'id' => 'tab-codes',
                'current' => true,
            ];
        }

        if ($handler::getCond('content_manager')) {
            if ($handler::getCond('admin')) {
                $tabs[] = [
                    'label' => _s('Full info'),
                    'id' => 'tab-full-info',
                ];
            }
            // Banned uploader IP?
            $banned_ip = CHV\Ip_ban::getSingle(['ip' => $image['uploader_ip']]);

            // Admin list values
            $image_admin_list_values = [
                [
                    'label' => _s('Image ID'),
                    'content' => $image['id'] . ' (' . $image['id_encoded'] . ')',
                ],
                [
                    'label' => _s('Uploader IP'),
                    'content' => sprintf(str_replace('%IP', '%1$s', '<a href="' . CHV\getSetting('ip_whois_url') . '" target="_blank">%IP</a> · <a href="' . G\get_base_url('search/images/?q=ip:%IP') . '">' . _s('search content') . '</a>  ·  ' . (!$banned_ip ? ('<a data-modal="form" data-args="%IP" data-target="modal-add-ip_ban" data-options=\'{"forced": true}\' data-content="ban_ip">' . _s('Ban IP') . '</a>') : null) . '<span class="' . ($banned_ip ? null : 'soft-hidden') . '" data-content="banned_ip">' . _s('IP already banned') . '</span>'), $image['uploader_ip']),
                ],
                [
                    'label' => _s('Upload date'),
                    'content' => $image['date'],
                ],
                [
                    'label' => '',
                    'content' => $image['date_gmt'] . ' (GMT)',
                ],
            ];

            $handler::setVar('content_ip', $image['uploader_ip']);
            $handler::setVar('image_admin_list_values', $image_admin_list_values);
            $handler::setCond('banned_ip', (bool) $banned_ip);
        }

        foreach ($tabs as $tab) {
            if ($tab['current'] === true) {
                $handler::setVar('current_tab', G\str_replace_first('tab-', null, $tab['id']));
                break;
            }
        }

        $handler::setCond('owner', $is_owner);
        $handler::setVar('pre_doctitle', $pre_doctitle);
        $handler::setVar('image', $image);
        $handler::setVar('image_safe_html', $image_safe_html);
        $handler::setVar('image_album_slice', G\safe_html($image_album_slice));
        $handler::setVar('tabs', $tabs);
        $handler::setVar('owner', $image['user']);

        // Populate the image meta description
        if ($image['description']) {
            $meta_description = $image['description'];
        } else {
            $image_tr = [
                '%i' => $image[is_null($image['title']) ? 'filename' : 'title'],
                '%a' => $image['album']['name'],
                '%w' => CHV\getSetting('website_name'),
                '%c' => $image['category']['name'],
            ];
            if ($image['album']['id'] || (!$image['user']['is_private'] && $image['album']['name'])) {
                $meta_description = _s('Image %i in %a album', $image_tr);
            } elseif ($image['category']['id']) {
                $meta_description = _s('Image %i in %c category', $image_tr);
            } else {
                $meta_description = _s('Image %i hosted in %w', $image_tr);
            }
        }
        $handler::setVar('meta_description', htmlspecialchars($meta_description));

        if ($handler::getCond('content_manager') or $is_owner) {
            $handler::setVar('user_items_editor', [
                'user_albums' => $image['user']['albums'],
                'type' => 'image',
                'album' => $image['album'],
                'category_id' => $image['category_id'],
            ]);
        }

        // Share thing
        $share_element = [
            'referer' => G\get_base_url(),
            'url' => $image['url_short'],
            'image' => $image['url'],
            'title' => $handler::getVar('pre_doctitle'),
        ];
        $share_element['HTML'] = '<a href="' . $share_element['url'] . '" title="' . $share_element['title'] . '"><img src="' . $share_element['image'] . '" /></a>';
        $share_links_array = CHV\render\get_share_links($share_element);
        $handler::setVar('share_links_array', $share_links_array);

        // Share modal
        $handler::setVar('share_modal', [
            'type' => 'image',
            'url' => $image['url_short'],
            'links_array' => $share_links_array,
            'privacy' => $image['album']['privacy'],
            'privacy_notes' => $image['album']['privacy_notes'],
        ]);

// Embed codes
        $embed = [];
       
        $image_full = [
            'html'        => '<img src="'.$image['url'].'" alt="'.$image['filename'].'" border="0" />',
            'markdown'    => '!['.$image['filename'].']('.$image['url'].')'
        ];
        $image_full['bbcode'] = G\html_to_bbcode($image_full['html']);
       
           
        $embed_full_linked['html'] = '<a href="'.$image['url_viewer'].'">'.$image_full['html'].'</a>';
        $embed_full_linked['bbcode'] = G\html_to_bbcode($embed_full_linked['html']);
        $embed_full_linked['markdown'] = '[!['.$image['filename'].']('.$image['url'].')]('.$image['url_viewer'].')';
       
        $embed['full-linked'] = [
            'label' => _s('图片引用'),
            'entries' => [
                [
                    'label' => 'HTML 代码',
                    'value' => htmlentities($embed_full_linked['html'])
                ],
                [
                    'label' => '论坛引用代码',
                    'value' => $embed_full_linked['bbcode']
                ],
                [
                    'label' => 'Markdown 代码',
                    'value' => $embed_full_linked['markdown']
                ],
            ]
        ];
       

       
        $embed['full-image'] = [
            'label' => _s('完整图片'),
            'entries' => [
                [
                    'label' => _s('图片页'),
                    'value' => $image['url_viewer']
                ],
                [
                    'label' => _s('原图链接'),
                    'value' => $image['url']
                ],
                [
                    'label' => 'HTML',
                    'value' => htmlentities($image_full['html'])
                ],
                [
                    'label' => 'BBCode',
                    'value' => $image_full['bbcode']
                ],
                [
                    'label' => 'Markdown',
                    'value' => $image_full['markdown']
                ],
            ]
        ];

        // Insert an embed id for each entry (for the cliboard.js bind)
        $embed_id = 1;
        foreach ($embed as &$v) {
            foreach ($v['entries'] as &$entry) {
                $entry['id'] = 'embed-code-' . $embed_id;
                ++$embed_id;
            }
        }

        $handler::setVar('embed', $embed);

        // Stock this image view
        $_SESSION['image_view_stock'][] = $id;
    } catch (Exception $e) {
        G\exception_to_error($e);
    }
};
