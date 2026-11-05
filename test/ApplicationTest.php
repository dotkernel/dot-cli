<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\Application;
use Dot\Cli\Exception\RuntimeException;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use DotTest\Cli\Fixture\AliasedCommand;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Throwable;

use function fopen;
use function rewind;
use function stream_get_contents;

class ApplicationTest extends TestCase
{
    use CommonTrait;
    use TempDirectoryTrait;

    /**
     * @throws Throwable
     */
    public function testDoRunWillFailWhenLockThrows(): void
    {
        $fileLocker = $this->createMock(FileLockerInterface::class);
        $fileLocker->expects($this->once())->method('lock')->willThrowException(new Exception('locked'));

        $output = new BufferedOutput();

        $application = new Application($fileLocker, $this->dotCliConfig());
        $result      = $application->doRun(new StringInput('list'), $output);

        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString('locked', $output->fetch());
    }

    /**
     * @throws Throwable
     */
    public function testDoRunWillSucceed(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir);

        $application = new Application($fileLocker, $this->dotCliConfig());
        $result      = $application->doRun(new StringInput('list'), new BufferedOutput());

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame('list', $fileLocker->getCommandName());
    }

    public function testNameAndVersionFallBackToUnknown(): void
    {
        $application = new Application(new FileLocker(), ['name' => ['not a string']]);

        $this->assertSame('UNKNOWN', $application->getName());
        $this->assertSame('UNKNOWN', $application->getVersion());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function commandSpellingProvider(): iterable
    {
        yield 'full name' => ['fixture:command'];
        yield 'abbreviation' => ['fix:com'];
        yield 'alias' => ['fc'];
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('commandSpellingProvider')]
    public function testEverySpellingOfACommandSharesTheLock(string $input): void
    {
        $holder = new FileLocker(true, $this->tempDir, 'fixture:command');
        $holder->lock();

        $application = $this->createApplication(new FileLocker(true, $this->tempDir));
        $output      = new BufferedOutput();
        $result      = $application->doRun(new StringInput($input), $output);

        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString(RuntimeException::MESSAGE_LOCK_HELD, $output->fetch());
        $holder->unlock();
    }

    /**
     * @throws Throwable
     */
    public function testAliasIsLockedUnderTheResolvedName(): void
    {
        $fileLocker  = new FileLocker(true, $this->tempDir);
        $application = $this->createApplication($fileLocker);

        $output = new BufferedOutput();
        $result = $application->doRun(new StringInput('fc'), $output);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame('fixture:command', $fileLocker->getCommandName());
        $this->assertFileExists($this->tempDir . '/command-fixture-command.lock');
        $this->assertFileDoesNotExist($this->tempDir . '/command-fc.lock');
        $this->assertStringContainsString(AliasedCommand::OUTPUT, $output->fetch());
    }

    /**
     * @throws Throwable
     */
    public function testUnknownCommandIsNotLocked(): void
    {
        $fileLocker  = new FileLocker(true, $this->tempDir);
        $application = $this->createApplication($fileLocker);
        $application->setCatchExceptions(false);

        try {
            $application->doRun(new StringInput('unknown'), new BufferedOutput());
            $this->fail('Expected CommandNotFoundException');
        } catch (CommandNotFoundException) {
        }

        $this->assertNull($fileLocker->getCommandName());
        $this->assertFileDoesNotExist($this->tempDir . '/command-unknown.lock');
    }

    /**
     * @throws Throwable
     */
    public function testDoRunFailsCleanlyWhenLockDirectoryIsMissing(): void
    {
        $fileLocker  = new FileLocker(true, $this->tempDir . '/missing');
        $application = $this->createApplication($fileLocker);

        $output = new BufferedOutput();
        $result = $application->doRun(new StringInput('fixture:command'), $output);

        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString('does not exist or is not writable', $output->fetch());

        $application->__destruct();
        $this->assertNull($fileLocker->getLockFile());
    }

    /**
     * @throws Throwable
     */
    public function testLockErrorIsWrittenToStderrWhenQuiet(): void
    {
        $holder = new FileLocker(true, $this->tempDir, 'fixture:command');
        $holder->lock();

        $stderr = fopen('php://memory', 'w+');
        $this->assertIsResource($stderr);

        $output = new ConsoleOutput(OutputInterface::VERBOSITY_QUIET);
        $output->setErrorOutput(new StreamOutput($stderr, OutputInterface::VERBOSITY_QUIET));
        $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $application = $this->createApplication(new FileLocker(true, $this->tempDir));
        $result      = $application->doRun(new StringInput('fixture:command -q'), $output);

        rewind($stderr);
        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString(RuntimeException::MESSAGE_LOCK_HELD, (string) stream_get_contents($stderr));
        $holder->unlock();
    }

    public function testDestructorReleasesTheLock(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'fixture:command');
        $fileLocker->lock();
        $this->assertIsResource($fileLocker->getLockFile());

        $application = new Application($fileLocker, $this->dotCliConfig());
        $application->__destruct();

        $this->assertNull($fileLocker->getLockFile());
    }

    private function createApplication(FileLockerInterface $fileLocker): Application
    {
        $application = new Application($fileLocker, $this->dotCliConfig());
        $application->setAutoExit(false);
        $application->addCommand(new AliasedCommand());

        return $application;
    }

    /**
     * @return array<string, mixed>
     */
    private function dotCliConfig(): array
    {
        $config = $this->getConfig()['dot_cli'];
        $this->assertIsArray($config);

        return $config;
    }
}
