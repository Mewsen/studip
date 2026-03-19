<?php
use Courseware\StructuralElement;
use Courseware\Block;
use Courseware\Container;
use Courseware\Unit;

class Courseware_LocalSearchController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
    }

    public function index_action()
    {
        global $user;

        $filters = json_decode(Request::get('filters'));
        $range_id = $filters->rangeId;
        $search = Request::get('search');
        $payload_search = substr(str_replace('\\u00', '\\\\u00', json_encode($search)), 1, -1);

        if (str_starts_with($payload_search, '\\\\u00')) {
            $payload_search = substr($payload_search, 1, strlen($payload_search));
        }
        if (str_starts_with($payload_search, '\"')) {
            $payload_search = '"' . substr($payload_search, 2, strlen($payload_search));
        }
        if (str_ends_with($payload_search, '\"')) {
            $payload_search = substr($payload_search, 0, -2) . '\\\\"';
        }

        $query = DBManager::get()->quote("%{$search}%");
        $payload_query = DBManager::get()->quote("%{$payload_search}%");

        $sql = "(SELECT `cw_structural_elements` . `id` AS id, CONCAT('', 'cw_structural_elements') AS type
            FROM `cw_structural_elements`
            WHERE (`title` LIKE {$query} OR `payload` LIKE {$payload_query})
                AND `range_id` = '{$range_id}'
            ORDER BY `cw_structural_elements`.`mkdate` DESC)
            UNION (
                SELECT se . `id` AS id, CONCAT('', 'cw_containers') AS type
                FROM `cw_containers` c
                JOIN cw_structural_elements se
                ON se . `id` = c . `structural_element_id`
                WHERE c. `payload` LIKE {$payload_query}
                    AND `container_type` != 'list'
                    AND se . `range_id` = '{$range_id}'
                ORDER BY c . `mkdate` DESC)
            UNION (
                SELECT se . `id` AS id, CONCAT('', 'cw_blocks') AS type
                FROM `cw_blocks` b
                JOIN cw_containers c
                ON c.id = b.container_id
                JOIN cw_structural_elements se
                ON se . `id` = c . `structural_element_id`
                WHERE b.payload LIKE {$payload_query}
                    AND se . `range_id` = '{$range_id}'
                ORDER BY b . `mkdate` DESC
            )";

            $results = DBManager::get()->fetchAll($sql);
            $data = [];
            foreach ($results as $object) {
                $structural_element = StructuralElement::find($object['id']);
                $unit = $structural_element->findUnit();
                if ($unit && $unit->id === $filters->unitId && $structural_element->canRead($user)) {
                    $description = '';
                    if ($object['type'] === 'cw_structural_elements') {
                        $description = GlobalSearchModule::mark($structural_element->payload['description'], $search, true);
                    }
                    if ($object['type'] === 'cw_containers') {
                        $description = _('Suchbegriff wurde in einem Abschnitt gefunden');
                    }
                    if ($object['type'] === 'cw_blocks') {
                        $description = _('Suchbegriff wurde in einem Block gefunden');
                    }
                    $pageData = GlobalSearchCourseware::getPageData($structural_element, $unit);
                    $date = new DateTime();
                    $date->setTimestamp($structural_element->chdate);

                    $name = $unit->structural_element->id === $structural_element->id
                        ? $structural_element->title
                        : $unit->structural_element->title . ': ' . $structural_element->title;

                    array_push($data, [
                        'name' => GlobalSearchModule::mark($name, $search, true),
                        'description' => $description,
                        'url' => $pageData['url'],
                        'img' => $structural_element->image ? $structural_element->getImageUrl() : Icon::create('courseware')->asImagePath(),
                        'additional' => '<a href="' . $pageData['originUrl'] . '" title="' . $pageData['originName'] . '">' . $pageData['originName'] . '</a>',
                        'date' => $date->format('d.m.Y H:i'),
                        'structural-element-id' => $structural_element->id,
                        'expand' => null
                    ]);
                }
            }
            $this->render_json($data);
    }
}
