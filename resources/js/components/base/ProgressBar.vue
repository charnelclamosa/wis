<template>
<v-progress-linear v-if="this.$store.state.progressBar == true" color="primary" indeterminate></v-progress-linear>
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
