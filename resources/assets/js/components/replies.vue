<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <paginator :dataSet="dataSet" @updated="fetch"></paginator>

        <new-reply :endpoint="endpoint" @created="add"></new-reply>
    </div>
</template>

<script>
    import Reply from './reply.vue';
    import NewReply from './NewReply.vue';
    import Collection from '../mixins/collections';
    import CommonCode from '../mixins/commonCode';

    export default {
        components: { Reply, NewReply },

        mixins: [Collection, CommonCode],

        data() {
            return {
                dataSet: false,
                endpoint: location.pathname + '/replies'
            }
        },
        created() {
            this.fetch();
        },
        mounted() {
            console.log('-- enter replies.vue');

            let _self = this;

            this.$on('PageChangeEvent', function(page){
                _self.fetch(page);
            });

            console.log('-- exit replies.vue');
        },
        methods: {
            fetch(page) {
                axios.get(this.url(page))
                    .then(this.refresh);
            },
            url(page) {
                if ( ! page ) {
                    page = this.getCurrentPageNumber();
                }

                return `${location.pathname}/replies?page=${page}`;
            },
            refresh({data}) {
                this.dataSet = data;
                this.items = data.data;
            },
        }
    }
</script>