<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\Application;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Throwable;

class ApplicationTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws Throwable
     */
    public function testDoRunWillFailWithoutLockFile(): void
    {
        $fileLocker = $this->createMock(FileLockerInterface::class);

        $exception = new Exception();
        $fileLocker->expects($this->once())->method('lock')->willThrowException($exception);

        $input  = new StringInput('list');
        $output = new BufferedOutput();

        $application = new Application($fileLocker, $this->getConfig()['dot_cli']);
        $result      = $application->doRun($input, $output);
        $this->assertSame($result, Command::FAILURE);
    }

    /**
     * @throws Throwable
     */
    public function testDoRunWillSucceed(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(
            $config[FileLockerInterface::class]['enabled'],
            $config[FileLockerInterface::class]['dirPath']
        );
        $input      = new StringInput('list');
        $output     = new BufferedOutput();

        $application = new Application($fileLocker, $this->getConfig()['dot_cli']);
        $result      = $application->doRun($input, $output);
        $this->assertSame($result, Command::SUCCESS);
    }
}
