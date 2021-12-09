<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Modules\Web3\Components\Web3\Validators;

interface IValidator
{
    /**
     * validate
     *
     * @param mixed $value
     * @return bool
     */
     public static function validate($value);
}