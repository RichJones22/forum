<script>

    import Favorite from './favorite.vue';

    export default {
        props: ['attributes'],
        component: ['Favorite'],
        data() {
            return {
                editing: false,
                body: this.attributes.body
            };
        },
        methods: {

            update() {
                console.log('im here');

                axios.patch('/replies/' + this.attributes.id, {
                    body: this.body
                });

                this.editing = false;

                flash('Updated!');
            },
            destroy() {
                axios.delete('/replies/' + this.attributes.id);

                $(this.$el).fadeOut(300, () => {
                    flash('Your reply has been deleted!');
                });
            }
        }
    }
</script>