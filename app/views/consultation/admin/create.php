<?php
/**
 * @var Consultation_AdminController $controller
 * @var Trails\Flash $flash
 * @var string|null $room
 * @var array $responsible
 * @var Range $range
 * @var int $slot_count_threshold
 */

$convertResponsibilities = function ($input) {
    if ($input === false) {
        return json_encode(false);
    }

    foreach ($input as $key => $values) {
        $input[$key] = array_map(
            fn($item) => ['id' => $item->id, 'label' => $item instanceof Statusgruppen ? $item->getName() : $item->getFullName()],
            $values
        );
    }

    return json_encode($input);
}

?>
<div data-vue-app="<?= htmlReady(json_encode(['components' => ['ConsultationCreator']])) ?>"
     is="ConsultationCreator"
     cancel-url="<?= $controller->indexURL() ?>"
     store-url="<?= $controller->storeURL() ?>"
     :with-responsible="<?= htmlReady($convertResponsibilities($responsible)) ?>"
     range-type="<?= get_class($range) ?>"
     default-room="<?= htmlReady($room) ?>"
     :slot-count-threshold="<?= htmlReady($slot_count_threshold) ?>"
     :as-dialog="<?= json_encode(Request::isXhr()) ?>"
></div>
