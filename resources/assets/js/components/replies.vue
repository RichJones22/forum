<template>
    <div>
        <div v-for="(reply, index) in items">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>
    </div>
</template>

<script>
    import Reply from './reply.vue'
    export default {
        props: ['data'],

        components: { Reply },

        data() {
            return {
                items: this.data,
            }
        },
        mounted() {
            console.log('-- enter replies.vue');

            let me = this.items;

            console.log('-- exit replies.vue');
        },
        methods: {
            remove(index) {
                this.items.splice(index, 1);

                this.$emit('removed');

                flash('Reply was deleted');
            }
        }
    }
</script>