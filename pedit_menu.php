<?php
/*
   +----------------------------------------------------------------------+
   | pedit - A PHP-Powered Text Editor                                    |
   +----------------------------------------------------------------------+
   | Copyright (c) 2004 Ben Ramsey                                        |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.0 of the PHP license,       |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_0.txt.                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: Ben Ramsey <benramsey@users.sourceforge.net>                |
   +----------------------------------------------------------------------+
   $Id$
*/

/* Application menu bar */
$menu = &new GtkMenuBar();
$box->pack_start($menu, false, false, 0);

/* File menu */
$file = &new GtkMenuItem('File');
$menu->append($file);

$file_menu = &new GtkMenu();

$new = &new GtkMenuItem('New');
$new->connect('activate', 'new_file');
$file_menu->append($new);

$open = &new GtkMenuItem('Open');
$open->connect('activate', 'file_open_dialog');
$file_menu->append($open);

$separator = &new GtkMenuItem();
$separator->set_sensitive(false);
$file_menu->append($separator);
 
$save = &new GtkMenuItem('Save');
$save->connect('activate', 'save_file');
$file_menu->append($save);

$separator = &new GtkMenuItem();
$separator->set_sensitive(false);
$file_menu->append($separator);

$close = &new GtkMenuItem('Close & Quit');
$close->connect_object('activate', array('Gtk', 'main_quit'));
$file_menu->append($close);

$file->set_submenu($file_menu);

/* Edit menu */
$edit = &new GtkMenuItem('Edit');
$menu->append($edit);

$edit_menu = &new GtkMenu();

$cut = &new GtkMenuItem('Cut');
$cut->connect('activate', 'cut_text');
$edit_menu->append($cut);

$copy = &new GtkMenuItem('Copy');
$copy->connect('activate', 'copy_text');
$edit_menu->append($copy);

$paste = &new GtkMenuItem('Paste');
$paste->connect('activate', 'paste_text');
$edit_menu->append($paste);

$separator = &new GtkMenuItem();
$separator->set_sensitive(false);
$edit_menu->append($separator);

$select_all = &new GtkMenuItem('Select All');
$select_all->connect('activate', 'select_all_text');
$edit_menu->append($select_all);

$edit->set_submenu($edit_menu);
?>
