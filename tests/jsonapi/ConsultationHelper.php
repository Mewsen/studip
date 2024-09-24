<?php
require_once __DIR__ . '/JSONAPIHelperTrait.php';

trait ConsultationHelper
{
    use JSONAPIHelperTrait;

    protected static $BLOCK_DATA = [
        'room'              => 'Testraum',
        'calendar_events'   => false,
        'show_participants' => false,
        'require_reason'    => 'no',
        'confirmation_text' => null,
        'note'              => 'Testnotiz für Block',
        'size'              => 1,
    ];

    protected static $SLOT_DATA = [
        'note' => 'Testnotiz für Slot',
    ];

    protected static $BOOKING_DATA = [
        'reason' => 'Test reason',
    ];

    protected function getUserForCredentials(array $credentials): User
    {
        return User::find($credentials['id']);
    }

    protected function createBlockWithSlotsForRange(Range $range, bool $lock_blocks = false): ConsultationBlock
    {
        $slot_length_in_hours = 2;

        // Generate start and end time. Assures that the day is not a holiday.
        $now = time();

        do {
            $begin = strtotime('next monday 8:00:00', $now);
            $end = strtotime("+{$slot_length_in_hours} hours", $begin);

            $now = strtotime('+1 week', $now);

            $temp = holiday($begin);
        } while (is_array($temp) && $temp['col'] === 3);

        // Lock blocks?
        $additional_data = [];
        if ($lock_blocks) {
            $additional_data['lock_time'] = ceil(($begin - time()) / 3600) + $slot_length_in_hours;
        }

        // Generate blocks
        $blocks = ConsultationBlock::generateBlocks(
            $range,
            $begin,
            $end,
            date('w', $begin),
            1
        );
        $blocks = iterator_to_array($blocks);

        $block = reset($blocks);
        $block->setData(array_merge(self::$BLOCK_DATA, $additional_data));

        $block->slots->exchangeArray($block->createSlots(15));
        foreach ($block->slots as $slot) {
            $slot->setData(self::$SLOT_DATA['note']);
        }

        $block->store();

        return ConsultationBlock::find($block->id);
    }

    protected function getSlotFromBlock(ConsultationBlock $block): ConsultationSlot
    {
        return $block->slots->first();
    }

    protected function createBookingForSlot(array $credentials, ConsultationSlot $slot, User $user): ConsultationBooking
    {
        return $this->withStudipEnv(
            $credentials,
            function () use ($slot, $user): ConsultationBooking {
                $booking = new ConsultationBooking();
                $booking->slot_id = $slot->id;
                $booking->user_id = $user->id;

                $booking->setData(self::$BOOKING_DATA);

                $booking->store();

                return $booking;
            }
        );
    }
}
