<template>
    <div>
        <article v-for="(server, index) in serverConfig" :key="index" class="memcached-server">
            <header>
                <h3>
                    {{ $gettext('Memcached-Server') }} {{ index + 1 }}
                    <studip-icon shape="trash" class="remove-server"
                                 @click.prevent="removeServer(index)"
                                 :size="24"></studip-icon>
                </h3>
            </header>
            <section class="col-4">
                <label>
                    {{ $gettext('Hostname') }}
                    <input type="text"
                           :name="`servers[${index}][hostname]`"
                           placeholder="localhost"
                           v-model.trim="server.hostname">
                </label>
            </section>
            <section class="col-2">
                <label>
                    {{ $gettext('Port') }}
                    <input type="text"
                           :name="`servers[${index}][port]`"
                           placeholder="11211"
                           v-model.number="server.port">
                </label>
            </section>
        </article>
        <label class="add-server" @click="addServer()">
            <studip-icon shape="add" :size="20"></studip-icon>
            {{ $gettext('Server hinzufügen') }}
        </label>
    </div>
</template>
<script>
export default {
    name: 'MemcachedCacheConfig',
    props: {
        servers: {
            type: Array,
            default: () => []
        }
    },
    data () {
        return {
            serverConfig: this.servers.concat([])
        }
    },
    methods: {
        addServer () {
            this.serverConfig.push({ hostname: 'localhost', port: 11211 })
        },
        removeServer (index) {
            this.serverConfig.splice(index, 1)
        }
    },
    computed: {
        isValid() {
            return this.serverConfig.length > 0
                && this.serverConfig.every(server => {
                    return server.hostname.length > 0
                        && server.port > 0;
                });
        }
    },
    watch: {
        serverConfig: {
            handler () {
                this.$emit('is-valid', this.isValid);
            },
            deep: true,
            immediate: true
        }
    }
}
</script>
<style lang="scss" scoped>
.memcached-server {
    .remove-server {
        vertical-align: text-bottom;
    }
}

.add-server {
    &:not(:only-child) {
        margin-top: 25px;
    }

    img {
        vertical-align: top;
    }
}
</style>
