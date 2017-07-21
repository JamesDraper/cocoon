<?php
declare(strict_types=1);

/**
 * Class used to test the view helper functionality.
 */
class TestHelper
{
    /**
     * @var string
     */
    private $val;

    /**
     * @param string $val
     */
    public function __construct(string $val)
    {
        $this->val = $val;
    }

    /**
     * @param  string $val2
     * @return string
     */
    public function getVals(string $val2): string
    {
        return sprintf('%s %s', $this->val, $val2);
    }

    /**
     * @return string
     */
    private function getVals2(): string
    {
        return 'vals';
    }
}
