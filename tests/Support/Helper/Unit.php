<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Assert;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    /**
     * Rudimentary html comparison function. Currently just normalized excess whitespace.
     *
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @return void
     */
    public static function assertStringEqualsHtmlFile(string $actualString, string $expectedFile, string $message = ''): void
    {
        $actualStringNoWhitespace = preg_replace("/\s+/", " ", $actualString);

        Assert::assertFileExists($expectedFile, $message);
        /** @var string */
        $fileContents = file_get_contents($expectedFile);
        $fileContentsNoWhitespace = preg_replace("/\s+/", " ", $fileContents);
        $constraint = new IsEqual($fileContentsNoWhitespace);

        Assert::assertThat($actualStringNoWhitespace, $constraint, $message);
    }
}
