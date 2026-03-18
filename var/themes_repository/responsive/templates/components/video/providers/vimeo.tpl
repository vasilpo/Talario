<script src="https://player.vimeo.com/api/player.js"></script>
<script type="text/javascript">
    function getPlayerVimeoMessages(state, get_state_message, get_volume_message) {
        const messages = {};

        if (state && get_state_message) {
            const state_message = {
                method: state
            };

            messages.state_message = state_message;
        }

        if (get_volume_message) {
            const volume_message = {
                method: 'setVolume',
                value: 0
            };

            messages.volume_message = volume_message;
        }

        return messages;
    }
</script>