/**
 * Message reporting
 *
 * @author      Viktoria Wiebe
 * @author      Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @version     1.0
 * @since       Stud.IP 4.5
 * @license     GLP2 or any later version
 * @copyright   2019 Stud.IP Core Group
 */
import eventBus from "./event-bus";

export default class Report
{
    // Info message
    static info (title: string, content: string) {
        Report.#reportMessage('info', title, content);
    }

    // Success message
    static success (title: string, content: string) {
        Report.#reportMessage('success', title, content);
    }

    // Warning message
    static warning (title: string, content: string) {
        Report.#reportMessage('warning', title, content);
    }

    // Error message
    static error (title: string, content: string) {
        Report.#reportMessage('error', title, content);
    }

    static #reportMessage(type: string, title: string, content: string) {
        eventBus.emit(
            'push-system-notification',
            {
                type: type,
                message: title,
                details: content
            }
        );
    }
}
