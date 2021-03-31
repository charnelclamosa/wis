<template>
<v-progress-linear v-if="this.$store.state.cardProgressBar == true" color="primary" indeterminate></v-progress-linear>
</template>

<script>
export default {
    data: () => {
        return {}
    }
}
</script>
<style scoped>
</style>
