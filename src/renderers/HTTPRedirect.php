<?php

namespace Colore\Renderers;

use Colore\Logger;
use Colore\Interfaces\RequestHelper;
use Colore\Interfaces\RenderHelper;

class HTTPRedirect implements RenderHelper {
    public function dispatch(RequestHelper &$cro) {
        // Get the redirect path
        $redirectPath = $cro->getRenderPath();
        Logger::debug('redirectPath: [%s]', $redirectPath);

        // If not fully qualified URL, then prepend baseURL
        if (!filter_var($redirectPath, FILTER_VALIDATE_URL)) {
            // If the baseURL ends in a slash, strip the preceding '/' from the redirect path
            if (substr($cro->getRenderProperty('baseURL'), -1, 1) == '/') {
                $redirectPath = str_replace('/', '', $redirectPath);
            }

            Logger::debug('redirectPath: [%s]', $redirectPath);

            // Combine the baseURL and redirect path into the redirect URL
            $redirectURL = sprintf('%s%s', $cro->getRenderProperty('baseURL'), $redirectPath);

            Logger::debug('baseURL: [%s]', $cro->getRenderProperty('baseURL'));
            Logger::debug('redirectURL: [%s]', $redirectURL);
        } else {
            // If we have a full URL, then set redirectURL from redirectPath
            $redirectURL = $redirectPath;
        }

        // Construct header line
        $headerLine = sprintf('Location: %s', $redirectURL);

        // Log header line
        Logger::debug('Redirecting to: [%s]', $headerLine);

        // Send http header
        header($headerLine);
    }
}
