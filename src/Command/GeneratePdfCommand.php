<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ChromePdfGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:generate-pdf',
    description: 'Generate a PDF from an HTML file using Google Chrome headless',
)]
final class GeneratePdfCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly ChromePdfGenerator $generator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('input', InputArgument::REQUIRED, 'Path to the HTML file')
            ->addArgument('output', InputArgument::REQUIRED, 'Path to the output PDF file')
            ->addOption('chrome-bin', null, InputOption::VALUE_REQUIRED, 'Chrome binary path', 'google-chrome-stable')
            ->addOption('timeout', null, InputOption::VALUE_REQUIRED, 'Process timeout in seconds', '60')
            ->addOption('no-sandbox', null, InputOption::VALUE_NONE, 'Disable Chrome sandbox (often required in containers)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io         = new SymfonyStyle($input, $output);
        $projectDir = $this->kernel->getProjectDir();

        $inputPath = (string) $input->getArgument('input');
        if (!$this->isAbsolutePath($inputPath)) {
            $inputPath = $projectDir . '/' . \ltrim($inputPath, '/');
        }
        $inputRealPath = \realpath($inputPath);
        if (false === $inputRealPath || !\is_file($inputRealPath)) {
            $io->error(\sprintf('Input file not found: %s', $inputPath));

            return Command::FAILURE;
        }

        $outputPath = (string) $input->getArgument('output');
        if (!$this->isAbsolutePath($outputPath)) {
            $outputPath = $projectDir . '/' . \ltrim($outputPath, '/');
        }

        $outputDir = \dirname($outputPath);
        if (!\is_dir($outputDir) && !\mkdir($outputDir, 0775, true) && !\is_dir($outputDir)) {
            $io->error(\sprintf('Unable to create output directory: %s', $outputDir));

            return Command::FAILURE;
        }

        $chromeBin = (string) $input->getOption('chrome-bin');
        $timeout   = (int) $input->getOption('timeout');

        try {
            $this->generator->generate(
                inputPath: $inputRealPath,
                outputPath: $outputPath,
                chromeBin: $chromeBin,
                timeout: $timeout,
                noSandbox: (bool) $input->getOption('no-sandbox'),
            );
        } catch (\RuntimeException $exception) {
            $io->error('Chrome failed to generate the PDF.');
            $message = \trim($exception->getMessage());
            if ('' !== $message) {
                $io->writeln($message);
            }

            return Command::FAILURE;
        }

        $io->success(\sprintf('PDF created at %s', $outputPath));

        return Command::SUCCESS;
    }

    private function isAbsolutePath(string $path): bool
    {
        if ('' === $path) {
            return false;
        }

        if ('/' === $path[0] || '\\' === $path[0]) {
            return true;
        }

        return (bool) \preg_match('/^[A-Za-z]:\\\\/', $path);
    }
}
