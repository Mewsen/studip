<?php
namespace JsonApi\Routes\Plugins;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\NonJsonApiController;
use Plugin;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PluginUpdateInfos extends NonJsonApiController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $user = $this->getUser($request);
        if (!$user || $user->perms !== 'root') {
            throw new AuthorizationFailedException();
        }

        $plugins = Plugin::findAndMapBySQL(
            function (Plugin $plugin): array {
                return [
                    'id' => $plugin->id,
                    'path' => $plugin->pluginpath,
                    'depends' => (bool) $plugin->dependentonid,
                ];
            },
            '1'
        );

        $plugin_administration = new \PluginAdministration();
        $update_info = array_filter(
            $plugin_administration->getUpdateInfo($plugins),
            function (array $info, $id) use ($plugins): bool {
                return isset($info['update']) && !$plugins[$id]['depends'];
            },
            ARRAY_FILTER_USE_BOTH
        );
        $update_info = array_map(
            function (array $info): array {
                return $info['update'];
            },
            $update_info
        );

        $response->getBody()->write(json_encode($update_info));
        return $response->withAddedHeader('Content-Type', 'application/json');
    }
}
