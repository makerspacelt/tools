<?php

namespace App\Command;

use App\Image\PathManager;
use App\Image\SizeManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ResizedImagesTidyUpCommand extends Command
{
    protected static $defaultName = 'tools:image:tidy-up';

    private const IMAGE_EXTS = ['png', 'jpg', 'jpeg', 'bmp'];

    private PathManager $pathManager;
    private SizeManager $sizeManager;

    public function __construct(PathManager $pathManager, SizeManager $sizeManager)
    {
        parent::__construct();

        $this->pathManager = $pathManager;
        $this->sizeManager = $sizeManager;
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', 'd', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = (bool)$input->getOption('dry-run');

        $files = scandir($this->pathManager->getOriginalImagesDir());
        $images = array_filter($files, function(string $filename): bool {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            return in_array($ext, self::IMAGE_EXTS)
                && (
                    !file_exists($this->pathManager->getPathToPreviewImage($filename)) 
                    || !file_exists($this->pathManager->getPathToThumbnail($filename))
                );
        });

        if (empty($images)) {
            $output->writeln("Nothing to tidy up");
            return Command::SUCCESS;
        }
        
        if ($dryRun) {
            $output->writeln("These images would be updated (missing sizes created):");
        }
        foreach ($images as $image) {
            $output->write($image);

            if (!$dryRun) {
                $output->write("... ");
                $this->sizeManager->ensureSizes($image);
                $output->writeln("done");
            } else {
                $output->writeln("");
            }
        }
        return Command::SUCCESS;
    }
}
