
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

            this.$emit('added');

            let page = parseInt(this.getCurrentPageNumber());

            if (this.items.length > 5) {
                this.$emit('PageChangeEvent', page + 1);
            }
        },
        remove(index) {
            this.items.splice(index, 1);

            this.$emit('removed');

            let page = parseInt(this.getCurrentPageNumber());

            if (this.items.length === 0) {
                this.$emit('PageChangeEvent', page - 1);
            }
        }
    }
}