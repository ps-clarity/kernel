<?php
namespace Clarity\Exceptions;

use Exception;
use ErrorException;
use Symfony\Component\Debug\ExceptionHandler;
use Monolog\ErrorHandler as MonologErrorHandler;
use Symfony\Component\Debug\Exception\FlattenException;

class Handler extends Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Handles fatal error, based on the lists
     */
    public function handleFatalError()
    {
        $error = error_get_last();

        if ($error && $error['type'] &= E_PARSE | E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR) {
            $this->handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    /**
     * Creates an error exception
     *
     * @param  [type] $num     error type e.g(E_PARSE | E_ERROR ...)
     * @param  [type] $str     error message
     * @param  [type] $file    the file affected by the error
     * @param  [type] $line    on what line affects
     * @param  [type] $context the contenxt
     */
    public function handleError($num, $str, $file, $line, $context = null)
    {
        $e = new ErrorException($str, 0, $num, $file, $line);

        $this->handleExceptionError(
            FlattenException::create($e)
        );
    }

    /**
     * Print outs a simple but useful debugging ui
     *
     * @param $e
     */
    public function handleExceptionError($e)
    {
        $this->render($e);
    }

    public function render($e)
    {
        if ( php_sapi_name() == 'cli' ) {
            dd($e) . "\n";

            return;
        }

        $content = (new ExceptionHandler($this->getDebugMode()))->getHtml($e);

        $response = di()->get('response');
        $response->setContent($content);

        if ( method_exists($e, 'getStatusCode') ) {
            $response->setStatusCode($e->getStatusCode());
        }

        return $response->send();
    }

    /**
     * Processes the error, fatal and exceptions
     */
    protected function report()
    {
        # - let monolog handle the logging in the errors,
        # unless you want it to, you can refer to method
        # handleExceptionError()

        if ( di()->has('log') ) {
            MonologErrorHandler::register(di()->get('log'));
        }


        # - register all the the loggers we have

        register_shutdown_function([$this, 'handleFatalError']);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleExceptionError']);
    }

    /**
     * Get the environment debug mode value
     */
    protected function getDebugMode()
    {
        $ret = false;
        $debug = config()->app->debug == 'true';

        if ( $debug || $debug === true) {
            $ret = true;
        }

        return $ret;
    }
}
