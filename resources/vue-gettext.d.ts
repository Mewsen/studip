declare module "vue-gettext" {
    import GettextPlugin from 'vue-gettext';

    interface StringDict {
        [key: string]: string;
    }

    type TranslationDict = StringDict;

    interface TranslationDicts {
        [key: string]: TranslationDict | null;
    }

    interface gettextConfig {
        getTextPluginMuteLanguages: string[],
        getTextPluginSilent: boolean,
        language: string,
        silent: boolean
    }

    declare namespace translate {
        function getTranslation(msgid: string, n?: number, context?: string, defaultPlural?: string, language?: string): string;
        function gettext(msgid: string, language?: string): string;
        function pgettext(context: string, msgid: string, language?: string): string;
        function ngettext(msgid: string, plural: string, n: number, language?: string): string;
        function npgettext(context: string, msgid: string, plural: string, n: number, language?: string): string;
        function initTranslations(translations: TranslationDicts, config: gettextConfig): void;
        function gettextInterpolate(message: string, context: object, disableHtmlEscaping?: boolean): string;
    }

    export { translate, StringDict, TranslationDict, TranslationDicts };

    export default GettextPlugin;
}
