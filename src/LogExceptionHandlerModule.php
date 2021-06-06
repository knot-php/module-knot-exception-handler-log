<?php
declare(strict_types=1);

namespace knotphp\module\knotexceptionhandler\log;

use Throwable;

use knotlib\exceptionhandler\text\TextDebugtraceRenderer;
use knotlib\exceptionhandler\log\LogExceptionHandler;
use knotlib\kernel\eventstream\Channels;
use knotlib\kernel\eventstream\Events;
use knotlib\kernel\exception\ModuleInstallationException;
use knotlib\kernel\kernel\ApplicationInterface;
use knotlib\kernel\module\ComponentTypes;
use knotlib\kernel\module\ModuleInterface;

use knotphp\module\knotexceptionhandler\adapter\KnotExceptionHandlerAdapter;

class LogExceptionHandlerModule implements ModuleInterface
{
    /**
     * Declare dependency on another modules
     *
     * @return array
     */
    public static function requiredModules() : array
    {
        return [];
    }

    /**
     * Declare dependent on components
     *
     * @return array
     */
    public static function requiredComponentTypes() : array
    {
        return [
            ComponentTypes::EVENTSTREAM,
            ComponentTypes::LOGGER,
        ];
    }

    /**
     * Declare component type of this module
     *
     * @return string
     */
    public static function declareComponentType() : string
    {
        return ComponentTypes::EX_HANDLER;
    }

    /**
     * Install module
     *
     * @param ApplicationInterface $app
     *
     * @throws ModuleInstallationException
     */
    public function install(ApplicationInterface $app)
    {
        try{
            $renderer = new TextDebugtraceRenderer();

            $ex_handler = new KnotExceptionHandlerAdapter(new LogExceptionHandler($app->logger(), $renderer));
            $app->addExceptionHandler($ex_handler);

            // fire event
            $app->eventstream()->channel(Channels::SYSTEM)->push(Events::EX_HANDLER_ADDED, $ex_handler);
        }
        catch(Throwable $ex)
        {
            throw new ModuleInstallationException(self::class, $ex->getMessage(), $ex);
        }
    }
}