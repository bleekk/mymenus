<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id: skin_version.php 12944 2015-01-23 13:05:09Z beckmi $
 */

$skinversion['template'] = 'templates/template.tpl';

$skinversion['css'] = array(
    'css/superfish.css',
    'css/superfish-navbar.css'
);

$skinversion['js'] = array(
//        '../../assets/js/jquery-1.11.2.min.js',
'../../../../browse.php?Frameworks/jquery/jquery.js',
'../../assets/js/hoverIntent.js',
'../../assets/js/superfish.js'
);

$header = "\n" . '<script type="text/javascript">';
$header .= "\n" . '  var $sfnav = jQuery.noConflict()';
$header .= "\n" . '  $sfnav(function(){';
$header .= "\n" . '    $sfnav(\'ul.sf-menu\').superfish({';
$header .= "\n" . '       pathClass:  \'current\'';
$header .= "\n" . '    });';
$header .= "\n" . '  });';
$header .= "\n" . '</script>';

$skinversion['header'] = $header;
