<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

echo twig()->render('pages/chat_tab.html.twig', ['room_id' => $_GET['room_id'] ?? '']);
