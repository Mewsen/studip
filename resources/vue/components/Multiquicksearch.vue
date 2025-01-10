<template>
    <div>
        <ul class="clean multiquicksearch">
            <li v-for="(item, index) in items" :key="index">
                <quicksearch :name="name"
                             :searchtype="searchtype"
                             :autocomplete="autocomplete"
                             :modelValue="autocomplete ? item.item_name : item.item_id"
                             :needle="item.item_name"
                             :ref="'qs_' + index"
                             @update:modelValue="(new_id, new_item_name) => editItem(new_id, new_item_name, index)"></quicksearch>
                <a href="" class="delete_item" @click.prevent="deleteItem(index)">
                    <studip-icon shape="trash" class="text-bottom"></studip-icon>
                </a>
            </li>
        </ul>
        <a href="#" @click.prevent="addItem">
            <studip-icon shape="add" class="text-bottom"></studip-icon>
            {{ addlabel }}
        </a>
    </div>
</template>

<script>
export default {
    name: 'multiquicksearch',
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: false
        },
        value: {
            type: Object,
            required: false,
            default: []
        },
        searchtype: {
            type: String,
            required: true
        },
        autocomplete: {
            type: Boolean,
            required: false,
            default: false
        },
        addlabel: {
            type: String,
            required: false,
            default: ""
        }
    },
    data () {
        return {
            items: []
        };
    },
    mounted () {
        for (let i in this.value) {
            this.items.push({
                item_id: this.autocomplete ? this.value[i] : i,
                item_name: this.value[i]
            });
        }
    },
    watch: {
        items: {
            handler(newValue, oldValue) {
                let new_val = {};
                for (let i in newValue) {
                    new_val[newValue[i].item_id] = newValue[i].item_name;
                }
                this.$emit('update:modelValue', new_val);
            },
            deep: true
        }
    },
    methods: {
        addItem: function () {
            this.items.push({
                item_id: '',
                item_name: ''
            });
        },
        editItem: function (item_id, item_name, index) {
            this.items[index].item_id = item_id;
            this.items[index].item_name = item_name;
        },
        deleteItem: function (index) {
            if (this.items.length > 0) {
                this.items.splice(index, 1);
            }
        }
    }
}
</script>
