<?php

namespace spec\Cocoon;

use Cocoon\Factory;
use Cocoon\Exception\IoException;
use Cocoon\Exception\DuplicatePathException;

use PhpSpec\ObjectBehavior;
use InvalidArgumentException;

class FactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Factory::class);
    }

    function it_should_return_an_empty_list_of_view_directories()
    {
        $this->getDirs()->shouldReturn([]);
    }

    function it_should_add_a_directory_to_the_list()
    {
        $this->addDir(__DIR__ . '/../res/views/dir1');
        $this->getDirs()->shouldReturn([realpath(__DIR__ . '/../res/views/dir1')]);
    }

    function it_should_add_2_directories_to_the_list()
    {
        $this->addDir(__DIR__ . '/../res/views/dir1');
        $this->addDir(__DIR__ . '/../res/views/dir2');
        $this->getDirs()->shouldReturn([
            realpath(__DIR__ . '/../res/views/dir1'),
            realpath(__DIR__ . '/../res/views/dir2')
        ]);
    }

    function it_should_set_directories()
    {
        $this->setDirs([
            __DIR__ . '/../res/views/dir1',
            __DIR__ . '/../res/views/dir2',
            __DIR__ . '/../res/views/dir3'
        ]);

        $this->getDirs()->shouldReturn([
            realpath(__DIR__ . '/../res/views/dir1'),
            realpath(__DIR__ . '/../res/views/dir2'),
            realpath(__DIR__ . '/../res/views/dir3')
        ]);
    }

    function it_should_throw_an_exception_if_one_of_the_directories_set_is_not_a_string()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringSetDirs([
            __DIR__ . '/../res/views/dir1',
            123,
            __DIR__ . '/../res/views/dir3'
        ]);
    }

    function it_should_throw_an_exception_if_adding_a_directory_already_set()
    {
        $this->addDir(__DIR__ . '/../res/views/dir1');
        $this->shouldThrow(DuplicatePathException::class)->duringAddDir(__DIR__ . '/./../res/views/dir1');
    }

    function it_should_throw_an_exception_if_setting_duplicate_directories()
    {
        $this->shouldThrow(DuplicatePathException::class)->duringSetDirs([
            __DIR__ . '/../res/views/dir1',
            __DIR__ . '/../res/views/dir2',
            __DIR__ . '/./../res/views/dir1'
        ]);
    }

    function it_should_throw_an_exception_if_an_added_directory_is_not_a_directory()
    {
        $this->shouldThrow(IoException::class)->duringAddDir(__DIR__ . '/./../res/dir1/views/tpl1.tpl.php');
    }

    function it_should_throw_an_exception_if_one_of_the_directories_added_in_a_group_is_not_a_directory()
    {
        $this->shouldThrow(IoException::class)->duringSetDirs([
            __DIR__ . '/./../res/views/dir1',
            __DIR__ . '/./../res/views/dir1/tpl1.tpl.php'
        ]);
    }

    function it_should_throw_an_exception_if_an_added_directory_does_not_exist()
    {
        $this->shouldThrow(IoException::class)->duringAddDir(__DIR__ . '/./../res/views/dir4');
    }

    function it_should_throw_an_exception_if_one_of_the_directories_added_in_a_group_does_not_exist()
    {
        $this->shouldThrow(IoException::class)->duringSetDirs([
            __DIR__ . '/./../res/views/dir1',
            __DIR__ . '/./../res/views/dir4'
        ]);
    }

    function it_should_not_append_directories_when_they_are_set()
    {
        $this->setDirs([__DIR__ . '/./../res/views/dir1']);
        $this->setDirs([
            __DIR__ . '/./../res/views/dir2',
            __DIR__ . '/./../res/views/dir3'
        ]);

        $this->getDirs()->shouldReturn([
            realpath(__DIR__ . '/../res/views/dir2'),
            realpath(__DIR__ . '/../res/views/dir3')
        ]);
    }

    function it_should_set_an_array_of_directories_passed_in_through_the_constructor()
    {
        $this->beConstructedWith([
            __DIR__ . '/./../res/views/dir1',
            __DIR__ . '/./../res/views/dir2'
        ]);

        $this->getDirs()->shouldReturn([
            realpath(__DIR__ . '/../res/views/dir1'),
            realpath(__DIR__ . '/../res/views/dir2')
        ]);
    }

    function it_should_return_a_default_extension()
    {
        $this->getExtension()->shouldReturn('tpl.php');
    }

    function it_should_set_an_extension()
    {
        $this->setExtension('new.extension');
        $this->getExtension()->shouldReturn('new.extension');
    }

    function it_should_create_a_template_object()
    {
        $this->addDir(__DIR__ . '/./../res/views/dir1');

        $view = $this->createView('tpl1');
        $view->getPath()->shouldReturn(realpath(__DIR__ . '/./../res/views/dir1/tpl1.tpl.php'));
    }

    function it_should_prioritise_a_directory_earlier_in_the_list()
    {
        $this->setDirs([
            __DIR__ . '/./../res/views/dir1',
            __DIR__ . '/./../res/views/dir2',
            __DIR__ . '/./../res/views/dir3'
        ]);

        $view = $this->createView('tpl2');
        $view->getPath()->shouldReturn(realpath(__DIR__ . '/./../res/views/dir2/tpl2.tpl.php'));
    }

    function it_should_throw_an_exception_if_the_template_could_not_be_found()
    {
        $this->setDirs([
            __DIR__ . '/./../res/views/dir1',
            __DIR__ . '/./../res/views/dir2',
            __DIR__ . '/./../res/views/dir3'
        ]);

        $this->shouldThrow(IoException::class)->duringCreateView('tpl-1');
    }

    function it_should_create_a_view_with_a_different_extension()
    {
        $this->setExtension('extension');
        $this->setDirs([
            __DIR__ . '/./../res/views/dir1',
            __DIR__ . '/./../res/views/dir2',
            __DIR__ . '/./../res/views/dir3'
        ]);

        $view = $this->createView('tpl1');
        $view->getPath()->shouldReturn(realpath(__DIR__ . '/./../res/views/dir1/tpl1.extension'));
    }
}
