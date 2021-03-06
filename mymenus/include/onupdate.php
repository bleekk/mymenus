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
 * Mymenus module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         mymenus
 * @since           1.5
 * @author          Xoops Development Team
 * @version         svn:$id$
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
//$moduleDirname = basename(dirname(__DIR__));
//include_once(XOOPS_ROOT_PATH . "/modules/$moduleDirname/include/common.php");
include_once __DIR__ . '/common.php';
$mymenus = MymenusMymenus::getInstance($debug);

xoops_loadLanguage('admin', $mymenus->dirname);

/**
 * @param object $xoopsModule
 * @param int $previousVersion
 * @return bool             FALSE if failed
 */
function xoops_module_update_mymenus(&$xoopsModule, $previousVersion)
{
    if ($previousVersion < 151) {
        //if (!check_infotemplates($xoopsModule)) return false;
        if (!check_infotable($xoopsModule)) {
            return false;
        }
        //update_tables_to_150($xoopsModule);
    }
    return true;
}

// =========================================================================================
// This function updates any existing table of a < 1.50 version to the format used
// in the release of Mymenus 1.51
// =========================================================================================

/**
 * @param $module
 *
 * @return bool
 */
function check_infotemplates($module)
{
    $err = true;
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/" . $module->getInfo("dirname") . "/templates/blocks/" . $module->getInfo("dirname") . "_block.tpl")) {
        $module->setErrors("Template " . $module->getInfo("dirname") . "_block.tpl not exists!");
        $err = false;
    }
    return $err;
}

/**
 * @param $module
 *
 * @return bool
 */
function check_infotable($module)
{
    global $xoopsDB;
    $err = true;

    $tables_menus = array(
        "id"    => "int(5) NOT NULL auto_increment",
        "title" => "varchar(255) NOT NULL default ''",
        "css"   => "varchar(255) NOT NULL default ''"
    );

    $tables_links = array(
        "id"        => "int(5) NOT NULL auto_increment",
        "pid"       => "int(5) NOT NULL default '0'",
        "mid"       => "int(5) NOT NULL default '0'",
        "title"     => "varchar(150) NOT NULL default ''",
        "alt_title" => "varchar(255) NOT NULL default ''",
        "visible"   => "tinyint(1) NOT NULL default '0'",
        "link"      => "varchar(255) default NULL",
        "weight"    => "tinyint(4) NOT NULL default '0'",
        "target"    => "varchar(10) default NULL",
        "groups"    => "text default NULL",
        "hooks"     => "text default NULL",
        "image"     => "varchar(255) default NULL",
        "css"       => "varchar(255) default NULL"
    );

    // CREATE or ALTER 'mymenus_menus' table
    if (!InfoTableExists($xoopsDB->prefix($module->getInfo("dirname")) . '_menus')) {
        $sql = "CREATE TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus (";
        foreach ($tables_menus as $s => $w) {
            $sql .= " " . $s . " " . $w . ",";
        }
        $sql .= " PRIMARY KEY (id)); ";
        echo $sql;
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $module->setErrors("Can't create Table " . $xoopsDB->prefix($module->getInfo("dirname")) . '_menus');
            return false;
        } else {
            $sql    = "INSERT INTO " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus (id,title) VALUES (1,'Default')";
            $result = $xoopsDB->queryF($sql);
        }
    } else {
        foreach ($tables_menus as $s => $w) {
            if (!InfoColumnExists($xoopsDB->prefix($module->getInfo("dirname")) . '_menus', $s)) {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus ADD " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            } else {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus CHANGE " . $s . " " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            }
        }
    }

    // RENAME TABLE 'mymenus_menu' TO 'mymenus_links'
    if (!InfoTableExists($xoopsDB->prefix($module->getInfo("dirname")) . "_links")) {
        if (InfoTableExists($xoopsDB->prefix($module->getInfo("dirname")) . "_menu")) {
            $sql    = "RENAME TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menu TO " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links;";
            $result = $xoopsDB->queryF($sql);
            if (!$result) {
                $module->setErrors("Can't rename Table " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menu");
                return false;
            }
        }
    }

    // CREATE or ALTER 'mymenus_links' table
    if (!InfoTableExists($xoopsDB->prefix($module->getInfo("dirname")) . "_links")) {
        $sql = "CREATE TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links ( ";
        foreach ($tables_links as $c => $w) {
            $sql .= " " . $c . " " . $w . ",";
        }
        $sql .= "  PRIMARY KEY  (storyid) ) ;";
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $module->setErrors("Can't create Table " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links");
            $sql    = 'DROP TABLE ' . $xoopsDB->prefix($module->getInfo("dirname")) . '_menus';
            $result = $xoopsDB->queryF($sql);
            return false;
        }
    } else {
        foreach ($tables_links as $s => $w) {
            if (!InfoColumnExists($xoopsDB->prefix($module->getInfo("dirname")) . '_links', $s)) {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links ADD " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            } else {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links CHANGE " . $s . " " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            }
        }
    }
    return true;
}

if (!function_exists("InfoColumnExists")) {
    /**
     * @param $tablename
     * @param $spalte
     *
     * @return bool
     */
    function InfoColumnExists($tablename, $spalte)
    {
        global $xoopsDB;
        if ($tablename == "" || $spalte == "") {
            return true;
        } // Fehler!!
        $result = $xoopsDB->queryF("SHOW COLUMNS FROM " . $tablename . " LIKE '" . $spalte . "'");
        $ret    = ($xoopsDB->getRowsNum($result) > 0) ? true : false;
        return $ret;
    }
}

if (!function_exists("InfoTableExists")) {
    /**
     * @param $tablename
     *
     * @return bool
     */
    function InfoTableExists($tablename)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW TABLES LIKE '$tablename'");
        $ret    = ($xoopsDB->getRowsNum($result) > 0) ? true : false;
        return $ret;
    }
}
