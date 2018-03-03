<template>
    <div>
        <div v-for="(reply, index) in items">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <new-reply :endpoint="endpoint" @created="add"></new-reply>
    </div>
</template>

<script>
    import Reply from './reply.vue'
    import NewReply from './NewReply.vue'
    export default {
        props: ['data'],

        components: { Reply, NewReply },

        data() {
            return {
                items: this.data,
                endpoint: window.location.pathname + '/replies'
            }
        },
        mounted() {
            console.log('-- enter replies.vue');

            let me = this.items;

            console.log('-- exit replies.vue');
        },
        methods: {
            add(reply) {
                this.items.push(reply);

                this.$emit('added');
            },
            remove(index) {
                this.items.splice(index, 1);

                this.$emit('removed');

                flash('Reply was deleted');
            }
        }
    }
</script>