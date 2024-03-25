<?= $actor->getFullName() ?> hat einen Termin im Kalender gelöscht.

<?= $this->render_partial(__DIR__ . '/_date_information', [
    'date' => $date,
    'receiver' => $receiver,
]) ?>
