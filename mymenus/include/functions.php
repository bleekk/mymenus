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
 * @version         $Id: functions.php 0 2010-07-21 18:47:04Z trabis $
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
include_once __DIR__ . '/common.php';

/**
 * Checks if a user is admin of Mymenus
 *
 * @return boolean
 */
function mymenus_userIsAdmin()
{
    global $xoopsUser;
    $mymenus = MymenusMymenus::getInstance();

    static $mymenus_isAdmin;
    if (isset($mymenus_isAdmin)) {
        return $mymenus_isAdmin;
    }

    $mymenus_isAdmin = (!is_object($xoopsUser)) ? false : $xoopsUser->isAdmin($mymenus->getModule()->getVar('mid'));
    return $mymenus_isAdmin;
}

/**
 * @param string $module_skin
 * @param boolean $use_theme_skin
 * @param string $theme_skin
 *
 * @return array
 */
function mymenus_getSkinInfo($module_skin = 'default', $use_theme_skin = false, $theme_skin = '')
{
    include_once __DIR__ . '/common.php';
    $mymenus = MymenusMymenus::getInstance();
    $error = false;
    if ($use_theme_skin) {
        $path = "themes/" . $GLOBALS['xoopsConfig']['theme_set'] . "/menu";
        if (!file_exists($GLOBALS['xoops']->path("{$path}/skin_version.php"))) {
            $path = "themes/" . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/{$mymenus->dirname}/skins/{$theme_skin}";
            if (!file_exists($GLOBALS['xoops']->path("{$path}/skin_version.php"))) {
                $error = true;
            }
        }
    }

    if ($error || !$use_theme_skin) {
        $path = "modules/{$mymenus->dirname}/skins/{$module_skin}";
    }

    $file = $GLOBALS['xoops']->path("{$path}/skin_version.php");
    $info = array();

    if (file_exists($file)) {
        include $file;
        $info =& $skinversion;
    }

    $info['path'] = $GLOBALS['xoops']->path($path);
    $info['url']  = $GLOBALS['xoops']->url($path);

    if (!isset($info['template'])) {
        $info['template'] = $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/templates/static/blocks/mymenus_block.tpl");
    } else {
        $info['template'] = $GLOBALS['xoops']->path("{$path}/" . $info['template']);
    }

    if (!isset($info['prefix'])) {
        $info['prefix'] = $module_skin;
    }

    if (isset($info['css'])) {
        $info['css'] = (array)$info['css'];
        foreach ($info['css'] as $key => $value) {
            $info['css'][$key] = $GLOBALS['xoops']->url("{$path}/{$value}");
        }
    }

    if (isset($info['js'])) {
        $info['js'] = (array)$info['js'];
        foreach ($info['js'] as $key => $value) {
            $info['js'][$key] = $GLOBALS['xoops']->url("{$path}/{$value}");
        }
    }

    if (!isset($info['config'])) {
        $info['config'] = array();
    }

    return $info;
}
