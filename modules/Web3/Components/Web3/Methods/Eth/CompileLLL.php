<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Modules\Web3\Components\Web3\Methods\Eth;

use InvalidArgumentException;
use Modules\Web3\Components\Web3\Methods\EthMethod;
use Modules\Web3\Components\Web3\Validators\StringValidator;
use Modules\Web3\Components\Web3\Formatters\StringFormatter;

class CompileLLL extends EthMethod
{
    /**
     * validators
     *
     * @var array
     */
    protected $validators = [
        StringValidator::class
    ];

    /**
     * inputFormatters
     *
     * @var array
     */
    protected $inputFormatters = [
        StringFormatter::class
    ];

    /**
     * outputFormatters
     *
     * @var array
     */
    protected $outputFormatters = [];

    /**
     * defaultValues
     *
     * @var array
     */
    protected $defaultValues = [];

    /**
     * construct
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    // public function __construct($method='', $arguments=[])
    // {
    //     parent::__construct($method, $arguments);
    // }
}