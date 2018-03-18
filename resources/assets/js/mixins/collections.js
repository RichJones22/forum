
import CommonCode from "../mixins/commonCode";

export default {
    mixins: [CommonCode],
    data() {
        return {
            items: []
        }
    },
    mounted() {
        console.log('-- collections.js --');
    },
    methods: {
        add(item) {
            this.items.push(item);

            if (this.items.length > 5) {
                this.$emit('PageChangeEvent', this.page() + 1);
            }
        },
        remove(index) {
            this.items.splice(index, 1);

            if (this.items.length === 0) {
                this.$emit('PageChangeEvent', this.page() - 1);
            } else {
                this.$emit('PageChangeEvent', this.page());
            }
        },
        page() {
            return this.getCurrentPageNumber();
        }
    }
}