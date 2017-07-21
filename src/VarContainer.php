<?php
declare(strict_types=1);

namespace Cocoon;

use Cocoon\Exception\TemplateException;

use ReflectionMethod;

/**
 * Variable container used to isolate the magic methods used within a template
 * and seperate them from the rest of the source.
 */
class VarContainer
{
    /**
     * Key-value array of template variables.
     *
     * @var array
     */
    private $vars;

    /**
     * Helper object, either a plain old php object,
     *     or null if no helper object is set.
     * @var mixed
     */
    private $helper;

    /**
     * @param array $vars
     * @param mixed $helper
     */
    public function __construct(array $vars, $helper)
    {
        $this->vars   = $vars;
        $this->helper =  $helper;
    }

    /**
     * Return a varaible assigned to the view.
     *
     * @param string $name
     * @return string
     * @throws TemplateException if no such variable is assigned to the template.
     */
    public function __get(string $name): string
    {
        if (isset($this->vars[$name]) === false) {
            throw new TemplateException(sprintf('Template variable not defined: %s.', $name));
        }

        return (string)$this->vars[$name];
    }

    /**
     * Calls a method in the helper object.
     *
     * @param  string $name
     * @param  array  $args
     * @return string
     */
    public function __call(string $name, array $args): string
    {
        if ($this->helper === null) {
            throw new TemplateException('No template helper object defined.');
        }

        if ($this->helperHasPublicMethod($name) === false) {
            throw new TemplateException(sprintf('Helper object does not contain public method: %s.', $name));
        }

        return (string)call_user_func_array([$this->helper, $name], $args);
    }

    /**
     * Returns true if the helper object contains the specified public method,
     * otherwise returns false.
     *
     * @param  string $method
     * @return bool
     */
    private function helperHasPublicMethod(string $method): bool
    {
        if (method_exists($this->helper, $method) === false) {
            return false;
        }

        return (new ReflectionMethod($this->helper, $method))->isPublic();
    }
}
