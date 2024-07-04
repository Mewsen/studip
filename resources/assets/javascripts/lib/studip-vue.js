class Vue
{
    static async load()
    {
        return STUDIP.loadChunk('vue');
    }

    static async on(...args)
    {
        const { eventBus } = await this.load();
        eventBus.on(...args);
    }

    static async off(...args) {
        const { eventBus } = await this.load();
        eventBus.off(...args);
}

    static async emit(...args)
    {
        const { eventBus } = await this.load();
        eventBus.emit(...args);
    }
}

export default Vue;
