<script type="text/javascript">
    function getPlayerRutubeMessages(state, get_state_message, get_volume_message) {
        const messages = {};

        if (state && get_state_message) {
            const state_message = {
                type: 'player:' + state,
                data: {}
            };

            messages.state_message = state_message;
        }

        if (get_volume_message) {
            const volume_message = {
                type: 'player:mute',
                data: {}
            };

            messages.volume_message = volume_message;
        }

        return messages;
    }
</script>
