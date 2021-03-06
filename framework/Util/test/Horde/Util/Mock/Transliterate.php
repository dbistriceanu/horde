<?php
/**
 * Copyright 2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Util
 */

/**
 * Wrapper to test individual transliteration backends.
 *
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Util
 * @since     2.4.0
 */
class Horde_Util_Mock_Transliterate extends Horde_String_Transliterate
{
    static public function testIntl($str)
    {
        return self::_intlToAscii($str);
    }

    static public function testIconv($str)
    {
        return self::_iconvToAscii($str);
    }

    static public function testFallback($str)
    {
        return self::_fallbackToAscii($str);
    }
}
