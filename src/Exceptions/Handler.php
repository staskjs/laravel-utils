<?php

namespace Staskjs\LaravelUtils\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function render($request, Exception $e)
    {
        if ($request->isXmlHttpRequest()) {
            if (config('app.debug') == false) {
                if ($e instanceof \PDOException) {
                   return response()->json(['error' => 'Error in database connection or query'], 500);
                }
                if ($e instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {
                   return response()->json(['error' => 'Whoops! Something went wrong.'], 500);
                }
            }

            if ($this->isHttpException($e)) {
                return response()->json($this->exceptionToArray($e), $e->getStatusCode());
            }
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json($this->exceptionToArray($e), 403);
            }
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return parent::render($request, $e);
            }

            $statusCode = 500;
            if (method_exists($e, 'getStatusCode')) {
                $statusCode = $e->getStatusCode();
            }

            return response()->json($this->exceptionToArray($e), $statusCode);
        }
        return parent::render($request, $e);
    }

    public function exceptionToArray(Exception $e) {
        $data = ['error' => $e->getMessage()];

        if (config('app.debug')) {
            $data = array_merge($data, [
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(function($item) {
                    if (!isset($item['file'])) {
                        return null;
                    }
                    return "{$item['file']}:{$item['line']}";
                })->filter(function($item) { return !empty($item); })->values(),
            ]);
        }

        return $data;
    }
}
