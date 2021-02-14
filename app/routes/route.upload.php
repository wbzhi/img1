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
		if (!$handler::getCond('upload_allowed')) {
			if (CHV\Login::getUser()) {
				G\set_status_header(403);
				$handler->template = 'request-denied';
				return;
			} else {
				G\redirect('login');
			}
		}
		$logged_user = CHV\Login::getUser();
		// User status override redirect
		CHV\User::statusRedirect($logged_user['status']);
		$album = null;
		if ($_GET['toAlbum']) {
			$toAlbumId = CHV\decodeID($_GET['toAlbum']);
			$album = CHV\Album::getSingle($toAlbumId, false, true, $logged_user);
			$is_owner = $album['user']['id'] && $album['user']['id'] == $logged_user['id'];
			if (!$is_owner) {
				$album = null;
			}
		}
		$handler::setVar('album', $album);
		$handler::setVar('pre_doctitle', _s('Upload'));
	} catch (Exception $e) {
		G\exception_to_error($e);
	}
};
