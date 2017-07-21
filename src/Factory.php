<?php
declare(strict_types=1);

namespace Cocoon;

use Cocoon\Exception\IoException;
use Cocoon\Exception\DuplicatePathException;

use InvalidArgumentException;

/**
 * View factory.
 *
 * Stores all the possible view directories that templates may be read from,
 * and uses them to create view objects.
 */
class Factory
{
    /**
     * List of all registered view directories.
     *
     * @var array
     */
    private $dirs = [];

    /**
     * Template file extension.
     *
     * @var string
     */
    private $extension = 'tpl.php';

    /**
     * @param array $dirs
     */
    public function __construct(array $dirs = [])
    {
        $this->setDirs($dirs);
    }

    /**
     * Add a list of view directories to the list of registered view directories.
     *
     * @param  array $dirs
     * @return self
     */
    public function setDirs(array $dirs): self
    {
        $this->dirs = [];
        foreach ($dirs as $dir) {
            if (is_string($dir) === false) {
                throw new InvalidArgumentException(sprintf('Directory path should be string, got %s.', gettype($dirs)));
            }
            $this->addDir($dir);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getDirs(): array
    {
        return $this->dirs;
    }

    /**
     * Add a list of view directories to the list of registered view directories.
     *
     * @param  array $dirs
     * @return self
     */
    public function addDir(string $dir): self
    {
        if (($dir = realpath($dir)) === false) {
            throw new IoException(sprintf('Path does not exist: %s.', $dir));
        }

        if (!is_dir($dir)) {
            throw new IoException(sprintf('Path is not a directory: %s.', $dir));
        }

        if (in_array($dir, $this->dirs)) {
            throw new DuplicatePathException(sprintf('Path already added: %s.', $dir));
        }

        $this->dirs[] = $dir;
        return $this;
    }

    /**
     * @param  string $extension
     * @return self
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * Creates a view and returns it.
     *
     * @param  string $tpl the path to the template file relative to one of the
     *     registered view directories it is contained in.
     * @return View
     * @throws IoException If no template file can be found.
     */
    public function createView(string $tpl)
    {
        foreach ($this->dirs as $dir) {
            $filePath = $dir . DIRECTORY_SEPARATOR . $tpl . '.' . $this->extension;
            if (file_exists($filePath)) {
                return new View($filePath);
            }
        }

        throw new IoException(sprintf('Template not found: %s.', $tpl));
    }
}
