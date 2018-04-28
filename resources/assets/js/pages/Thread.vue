<script>
    import Replies from '../components/replies.vue';
    import SubscribeButton from '../components/subscribeButton.vue';

    export default {
        props: ['initRepliesCount'],
        components: {Replies, SubscribeButton},

        data() {
            return {
                repliesCount: this.initRepliesCount
            }
        },
        mounted() {
            console.log('-- Thread.vue --');

            this.$emit('repliesCountIs', this.repliesCount);
        },
    }
</script>