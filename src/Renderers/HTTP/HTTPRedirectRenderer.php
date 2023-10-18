<?php

namespace Colore\Renderers\HTTP;

use Colore\Logger;
use Colore\Interfaces\Adapters\IRequestAdapter;
use Colore\Interfaces\Providers\IRenderProvider;

class HTTPRedirectRenderer implements IRenderProvider {
    /**
     * @return void
     */
    public function dispatch(IRequestAdapter &$cro) {
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

        // Log header line
        Logger::debug('Redirecting to: [%s]', $redirectURL);

        // Send http header
        $cro->output('', ['Location' => $redirectURL], 308);
    }
}
