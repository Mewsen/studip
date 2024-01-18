<template>
    <div>
        <div class="formpart">
            <ul class="clean editablelist">
                <li v-for="item in sortedItems" :key="item.id">
                    <input v-if="name" type="hidden" :name="name + '[]'" :value="item.id">
                    <span>{{item.name}}</span>
                    <button v-if="item.deletable" @click.prevent="deleteItem(item)" :title="$gettextInterpolate($gettext('%{ name } löschen'), {name: item.name})" class="undecorated">
                        <studip-icon shape="trash" size="20" class="text-bottom"></studip-icon>
                    </button>
                </li>
            </ul>
            <quicksearch v-if="quicksearch" :searchtype="quicksearch" name="qs" @input="addRange" :placeholder="$gettext('Suchen')"></quicksearch>
        </div>
    </div>
</template>

<script>
export default {
    name: 'item-list',
    props: {
        name: {
            type: String,
            required: false
        },
        selected_items: {
            required: false,
            type: Array
        },
        quicksearch: {
            required: false
        },
        selectable: {
            type: Array,
            required: false
        },
        category_order: {
            type: Array,
            required: false,
            default: () => [],
        }
    },
    data () {
        return {
            resort: false, //this is just for triggering the computed property sortedItems to be sorted again
            preventChangeOfQuickselect: false,
            allItems: this.selected_items
        };
    },
    methods: {
        addRange (id, name) {
            let icon = null;
            if (id.includes('__')) {
                id = id.split('__')[0];
            }
            if (!this.allItems.find(item => item.id === id)) {
                this.allItems.push({
                    id: id,
                    name: name,
                    deletable: true
                });
                this.changed();
            }
        },
        changed () {
            this.resort = !this.resort;
            this.$emit('input', this.selected_items.map(function (item) {
                return item;
            }));
            this.$emit('selected_items', this.selected_items.map(function (item) {
                return {
                    id: item.id,
                    name: item.name,
                    deletable: true
                };
            }));
        },
        quickselect (event) {
            if (event.target.value && !this.preventChangeOfQuickselect) {
                let new_value = JSON.parse(event.target.value);
                this.addRange(new_value.value, new_value.name);
                event.target.value = '';
            }
        },
        navigate_or_select (event) {
            if (['ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight'].includes(event.key)) {
                //don't trigger change for 250 ms
                this.preventChangeOfQuickselect = true;
                window.setTimeout(() => {
                    this.preventChangeOfQuickselect = false;
                }, 250);
            } else if (event.key === 'Enter') {
                //select current selection
                let new_value = JSON.parse(event.target.value);
                this.addRange(new_value.value, new_value.name);
                event.target.value = '';
            }
        },
        deleteItem (item) {
            for (let i in this.selected_items) {
                this.$emit('input', this.selected_items.filter(i => i.id !== item.id));
            }
            this.changed();
        },
        isSelected (id) {
            if (id.includes('__')) {
                id = id.split('__')[0];
            }
            return this.selected_items.some(item => item.id === id);
        }
    },
    computed: {
        sortedItems () {
            return [...this.selected_items].sort((a, b) => {
                return a.name.localeCompare(b.name);
            });
        }
    },
}
</script>
