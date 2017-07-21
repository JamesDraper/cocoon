<?php
declare(strict_types=1);

namespace Cocoon;

use Cocoon\Exception\TemplateException;
use Cocoon\Exception\ReservedTermException;
use Cocoon\Exception\InvalidTermException;

use InvalidArgumentException;
use Throwable;
use Closure;

/**
 * Wraps a template file and allows the setting of variables,
 * helper objects, and the rendering of templates.
 */
class View
{
    /**
     * The full path to the template file.
     *
     * @var string
     */
    private $path;

    /**
     * Helper object, either a plain old php object,
     *     or null if no helper object is set.
     * @var mixed
     */
    private $helper = null;

    /**
     * Key-value array of template variables.
     *
     * @var array
     */
    private $vars = [];

    /**
     * Inner view, either an instance of self, or null if no inner view was set.
     *
     * @var null|self
     */
    private $innerView = null;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Render the template and return the result as a string.
     *
     * @return string
     * @throws TemplateException if an error occurs while rendering the template.
     */
    public function render(): string
    {
        $vars = $this->vars;
        if ($this->innerView !== null) {
            $vars['content'] = $this->innerView->render();
        }

        return call_user_func_array(
            Closure::bind(
                (function ($viewPath) {
                    ob_start();
                    try {
                        require($viewPath);
                        $output = ob_get_clean();
                        return $output;
                    } catch (TemplateException $e) {
                        throw $e;
                    } catch (Throwable $e) {
                        ob_clean();
                        throw new TemplateException(
                            sprintf('Error thrown from within template: %s.', $e->getMessage()),
                            0,
                            $e
                        );
                    }
                }),
                new VarContainer($vars, $this->helper)
            ),
            [$this->path]
        );
    }

    /**
     * Wrap another view inside this view.
     *
     * @param  self $innerView
     * @return self
     */
    public function wrap(self $innerView): self
    {
        $this->innerView = $innerView;
        return $this;
    }

    /**
     * Set a template variable.
     *
     * @param  string $name
     * @param  mixed $val
     * @return self
     */
    public function setVar(string $name, $val): self
    {
        $this->vars[$name] = htmlspecialchars($this->checkAndStringify($name, $val));
        return $this;
    }

    /**
     * Set a template variable, but do not escape any html contained within it.
     *
     * @param  string $name
     * @param  mixed $val
     * @return self
     */
    public function setVarRaw(string $name, $val): self
    {
        $this->vars[$name] = $this->checkAndStringify($name, $val);
        return $this;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param  mixed $helper
     * @throws InvalidArgumentException
     */
    public function setHelper($helper): self
    {
        if (!is_object($helper)) {
            throw new InvalidArgumentException(sprintf('Helper must be an object, got %s.', gettype($helper)));
        }

        $this->helper = $helper;
        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed $val
     * @return string
     */
    private function checkAndStringify(string $name, $val): string
    {
        return $this
            ->checkName($name)
            ->stringify($val);
    }

    /**
     * Checks that variable name, and throws InvalidTermException if it is empty or contains spaces.
     *
     * @param  string $name
     * @return self
     * @throws InvalidTermException
     */
    private function checkName(string $name): self
    {
        if (strpos($name, ' ') !== false || strpos($name, "\t") !== false) {
            throw new InvalidTermException('Template variable names cannot contain tabs or spaces.');
        }

        if ($name === 'content') {
            throw new ReservedTermException('Reserved word used as a variable name: content.');
        }

        if ($name === '') {
            throw new InvalidTermException('Variable name cannot be empty.');
        }

        return $this;
    }

    /**
     * Converts a value into a string.
     * Throws an invalid argument exception if the value cannot be converted into a string for any reason.
     *
     * @param  mixed $val
     * @return string
     * @throws InvalidArgumentException
     */
    private function stringify($val): string
    {
        try {
            return (string)$val;
        } catch (Throwable $e) {
            $type = gettype($val);
            if ($type === 'object') {
                $type = get_class($val);
            }
            throw new InvalidArgumentException(sprintf('%s could not be converted to string.', $type));
        }
    }
}
