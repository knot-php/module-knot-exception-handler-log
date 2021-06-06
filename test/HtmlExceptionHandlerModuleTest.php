<?php
declare(strict_types=1);

namespace knotphp\module\knotexceptionhandler\log\test;

use PHPUnit\Framework\TestCase;

use knotlib\kernel\exception\ModuleInstallationException;
use knotphp\module\knotexceptionhandler\adapter\KnotExceptionHandlerAdapter;
use knotphp\module\knotexceptionhandler\log\LogExceptionHandlerModule;

final class HtmlExceptionHandlerModuleTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testInstall()
    {
        $module = new LogExceptionHandlerModule();

        $app = new TestSimpleApplication();

        try{
            $module->install($app);
        }
        catch(ModuleInstallationException $e){
            $this->fail($e->getMessage());
        }

        $ref_class = new \ReflectionClass($app);
        $prop = $ref_class->getParentClass()->getParentClass()->getProperty('ex_handlers');
        $prop->setAccessible(true);
        $ex_handlers = $prop->getValue($app);

        $this->assertCount(1, $ex_handlers);
        $this->assertInstanceOf(KnotExceptionHandlerAdapter::class, $ex_handlers[0]);
    }

}