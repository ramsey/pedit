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

/* Editable text field */
$text_area = &new GtkHBox();
$box->pack_start($text_area);

$default_font = Gdk::font_load('-*-courier-medium-r-normal--*-140-*-*-m-*-iso8859-1');
$textpad_style = &new GtkStyle();
$textpad_style->font = $default_font;

$textpad = &new GtkText;
$textpad->set_editable(true);
$textpad->set_style($textpad_style);
$textpad->set_line_wrap(false);
$textpad->set_word_wrap(false);
$textpad->connect('changed', 'text_changed');
$text_area->pack_start($textpad);

/* Vertical scrollbar */
$textpad_vadj = $textpad->vadj;
$textpad_vscrollbar = &new GtkVScrollbar($textpad_vadj);
$text_area->pack_end($textpad_vscrollbar, false);
?>
