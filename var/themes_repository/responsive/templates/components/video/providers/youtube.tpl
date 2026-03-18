<script>
    function getPlayerYoutubeMessages(state, get_state_message, get_volume_message) {
        const messages = {};

        if (state && get_state_message) {
            const state_message = {
                event: 'command',
                func: state === 'play' ? 'playVideo' : 'pauseVideo',
                args: []
            };

            messages.state_message = state_message;
        }

        if (get_volume_message) {
            const volume_message = {
                event: 'command',
                func: 'setVolume',
                args: [0]
            };

            messages.volume_message = volume_message;
        }

        return messages;
    }
</script>