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

function new_file()
{
    global $text_changed, $loaded_file, $textpad, $w;
    if ($text_changed === true) {
        save_close_dialog(true);
    } else {
        $loaded_file = null;
        $textpad->freeze();
        $textpad->set_point(0);
        $textpad->forward_delete($textpad->get_length());
        $textpad->thaw();
        $w->set_title('Untitled');
    }
}

function file_open_dialog() 
{
    global $text_changed, $loaded_file;
    if ($text_changed === true) {
        save_close_dialog();
    } else {
        $fs =& new GtkFileSelection('Select a File');
        if (!is_null($loaded_file)) {
            $directory = dirname($loaded_file);
            /* We must provide a trailing slash, so provide the appropriate 
                one for the current system */
            $directory .= 
                    (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) ? '\\' : '/';
            $fs->set_filename($directory);
        }
        $ok = $fs->ok_button;
        $ok->connect('clicked', 'open_file', &$fs);
        $ok->connect_object('clicked', array(&$fs, 'destroy'));
        $cancel = $fs->cancel_button;
        $cancel->connect_object('clicked', array(&$fs, 'destroy'));
        $fs->show();
    }
}

function save_file()
{
    global $text_changed, $loaded_file, $textpad, $w;
    if (!is_null($loaded_file)) {
        $bytes = file_put_contents($loaded_file, $textpad->get_chars(0, -1));
        if ($bytes === false) {
            save_error_dialog();
        } else {
            $text_changed = false;
            $w->set_title($loaded_file);
        }
    }
    else
    {
        file_save_dialog();
    }
    return true;
}

function cut_text()
{
    global $textpad;
    $textpad->cut_clipboard();
}

function copy_text()
{
    global $textpad;
    $textpad->copy_clipboard();
}

function paste_text()
{
    global $textpad;
    $textpad->paste_clipboard();
}

function select_all_text()
{
    global $textpad;
    $textpad->select_region(0, -1);
}

function open_file($button, &$fs) 
{
    global $textpad, $w, $loaded_file;
    $textpad->freeze();
    $textpad->set_point(0);
    $textpad->forward_delete($textpad->get_length());
    $textpad->insert(null, null, null, file_get_contents($fs->get_filename()));
    $textpad->thaw();
    $w->set_title($fs->get_filename());
    $loaded_file = $fs->get_filename();
    return true;
}

function file_save_dialog()
{
    $fs =& new GtkFileSelection('Save File');
    $ok = $fs->ok_button;
    $ok->connect('clicked', 'file_save_from_dialog', &$fs);
    $ok->connect_object('clicked', array(&$fs, 'destroy'));
    $cancel = $fs->cancel_button;
    $cancel->connect_object('clicked', array(&$fs, 'destroy'));
    $fs->show();
}

function file_save_from_dialog($button, &$fs)
{
    global $w, $loaded_file;
    $w->set_title($fs->get_filename());
    $loaded_file = $fs->get_filename();
    save_file();
    return true;
}

function text_changed()
{
    global $w, $text_changed, $loaded_file;
    $text_changed = true;
    if (!is_null($loaded_file)) {
        $w->set_title('(*) ' . $loaded_file);
    } else {
        $w->set_title('(*) Untitled');
    }
}

function save_close_dialog($new_file = false)
{
    $dialog =& new GtkDialog();
    $dialog->set_title('Save before closing?');
    $dialog->set_usize(300, 125);
    $dialog->set_position(GTK_WIN_POS_CENTER);
    $dialog->connect('delete-event', create_function('', 'return false;'));
    
    $dialog_vbox = $dialog->vbox; 
    $dialog_action_area = $dialog->action_area; 
    
    $label =& new GtkLabel('Would you like to save the current file first?');
    $dialog_vbox->pack_start($label);
    $label->show();
    
    $ok =& new GtkButton('Ok');
    if ($new_file) {
        $ok->connect('clicked', create_function('', 'save_file(); 
                new_file();'));
    } else {
        $ok->connect('clicked', create_function('', 'save_file(); 
                file_open_dialog();'));
    }
    $ok->connect_object('clicked', array(&$dialog, 'destroy'));
    $dialog_action_area->pack_start($ok);
    $ok->show();
    
    $no =& new GtkButton('No');
    if ($new_file) {
        $no->connect('clicked', create_function('', 'global $text_changed; 
                $text_changed = false; new_file();'));
    } else {
        $no->connect('clicked', create_function('', 'global $text_changed; 
                $text_changed = false; file_open_dialog();'));
    }
    $no->connect_object('clicked', array(&$dialog, 'destroy'));
    $dialog_action_area->pack_start($no);
    $no->show();

    $cancel =& new GtkButton('Cancel');
    $cancel->connect_object('clicked', array(&$dialog, 'destroy'));
    $dialog_action_area->pack_start($cancel);
    $cancel->show();
    
    $dialog->show();
}

function save_error_dialog()
{
    $dialog =& new GtkDialog();
    $dialog->set_title('Error saving');
    $dialog->set_usize(300, 125);
    $dialog->connect('delete-event', create_function('', 'return false;'));

    $dialog_vbox = $dialog->vbox; 
    $dialog_action_area = $dialog->action_area; 
    
    $label =& new GtkLabel('Unable to open file for saving.');
    $dialog_vbox->pack_start($label);
    $label->show();
    
    $ok =& new GtkButton('Ok');
    $ok->connect_object('clicked', array(&$dialog, 'destroy'));
    $dialog_action_area->pack_start($ok);
    $ok->show();
    
    $dialog->show();
}

/* This function exists in PHP 5 */
if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data)
    {
        if (($h = @fopen($filename, 'w')) === false) {
            return false;
        }
        if (($bytes = @fwrite($h, $data)) === false) {
            return false;
        }
        fclose($h);
        return $bytes;
    }
} 
?>
