<?php

namespace spec\Cocoon;

use Cocoon\View;
use Cocoon\Exception\TemplateException;
use Cocoon\Exception\ReservedTermException;
use Cocoon\Exception\InvalidTermException;

use TestHelper;
use InvalidArgumentException;

use PhpSpec\ObjectBehavior;

class ViewSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__ . '/../res/views/dir1/tpl1.tpl.php');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(View::class);
    }

    function it_should_get_all_the_variables()
    {
        $this->getVars()->shouldReturn([]);
    }

    function it_should_escape_strings()
    {
        $this->setVar('name1', '&');
        $this->getVars()->shouldReturn(['name1' => '&amp;']);
    }

    function it_should_not_escape_strings_that_are_set_as_raw()
    {
        $this->setVarRaw('name1', '&');
        $this->getVars()->shouldReturn(['name1' => '&']);
    }

    function it_should_render_a_template()
    {
        $this->setVar('name1', '&');
        $this->setVarRaw('name2', '&');
        $this->render()->shouldReturn('|&amp;|&|');
    }

    function it_should_set_a_helper()
    {
        $this->beConstructedWith(__DIR__ . '/../res/views/dir1/tpl4.tpl.php');
        $this->setHelper((new TestHelper('ONE')));
        $this->setVar('name1', 'TWO');
        $this->render()->shouldReturn('ONE TWO');
    }

    function it_should_throw_an_exception_if_a_helper_is_not_set()
    {
        $this->beConstructedWith(__DIR__ . '/../res/views/dir1/tpl4.tpl.php');
        $this->setVar('name1', 'TWO');
        $this->shouldThrow(new TemplateException('No template helper object defined.'))->duringRender();
    }

    function it_should_throw_an_exception_if_a_template_calls_a_non_private_helper_method()
    {
        $this->beConstructedWith(__DIR__ . '/../res/views/dir1/tpl7.tpl.php');
        $this->setHelper((new TestHelper('ONE')));
        $this->shouldThrow(new TemplateException('Helper object does not contain public method: getVals2.'))->duringRender();
    }

    function it_should_throw_an_exception_if_a_template_variable_is_not_defined()
    {
        $this->beConstructedWith(__DIR__ . '/../res/views/dir1/tpl4.tpl.php');
        $this->shouldThrow(new TemplateException('Template variable not defined: name1.'))->duringRender();
    }

    function it_should_throw_an_exception_if_rendering_an_object_that_cannot_be_converted_to_a_string()
    {
        $this->shouldThrow(new InvalidArgumentException('TestHelper could not be converted to string.'))->duringSetVar('name1', new TestHelper('ONE'));
    }

    function it_should_throw_an_exception_if_rendering_a_raw_object_that_cannot_be_converted_to_a_string()
    {
        $this->shouldThrow(new InvalidArgumentException('TestHelper could not be converted to string.'))->duringSetVarRaw('name1', new TestHelper('ONE'));
    }

    function it_should_throw_an_exception_thrown_from_within_a_template()
    {
        $this->beConstructedWith(__DIR__ . '/../res/views/dir2/tpl5.tpl.php');
        $this->shouldThrow(new TemplateException('Error thrown from within template: syntax error, unexpected \'error\' (T_STRING).'))->duringRender();
    }

    function it_should_throw_an_exception_if_a_helper_object_is_not_an_object()
    {
        $this->shouldThrow(new InvalidArgumentException('Helper must be an object, got integer.'))->duringSetHelper(123);
    }

    function it_should_wrap_one_view_in_another(View $innerView)
    {
        $innerView->render()->willReturn('Rendered view');
        $this->beConstructedWith(__DIR__ . '/../res/views/dir3/tpl6.tpl.php');

        $this->wrap($innerView);
        $this->render()->shouldReturn('|Rendered view|');
    }

    function it_should_throw_an_exception_if_a_variable_is_set_to_content()
    {
        $this->shouldThrow(new ReservedTermException('Reserved word used as a variable name: content.'))->duringSetVar('content', 123);
    }

    function it_should_throw_an_exception_if_a_raw_variable_is_set_to_content()
    {
        $this->shouldThrow(new ReservedTermException('Reserved word used as a variable name: content.'))->duringSetVarRaw('content', 123);
    }

    function it_should_throw_an_exception_if_a_variable_name_is_empty()
    {
        $this->shouldThrow(new InvalidTermException('Variable name cannot be empty.'))->duringSetVar('', '');
    }

    function it_should_throw_an_exception_if_a_raw_variable_name_is_empty()
    {
        $this->shouldThrow(new InvalidTermException('Variable name cannot be empty.'))->duringSetVar('', '');
    }

    function it_should_throw_an_exception_if_a_variable_name_contains_spaces()
    {
        $this->shouldThrow(new InvalidTermException('Template variable names cannot contain tabs or spaces.'))->duringSetVar('one two', '');
    }

    function it_should_throw_an_exception_if_a_raw_variable_name_contains_spaces()
    {
        $this->shouldThrow(new InvalidTermException('Template variable names cannot contain tabs or spaces.'))->duringSetVarRaw('one two', '');
    }

    function it_should_throw_an_exception_if_a_variable_name_contains_tabs()
    {
        $this->shouldThrow(new InvalidTermException('Template variable names cannot contain tabs or spaces.'))->duringSetVar("one\ttwo", '');
    }

    function it_should_throw_an_exception_if_a_raw_variable_name_contains_tabs()
    {
        $this->shouldThrow(new InvalidTermException('Template variable names cannot contain tabs or spaces.'))->duringSetVarRaw("one\ttwo", '');
    }
}
