<template>
    <v-btn tile color="primary" :disabled="disabled" :loading="loading">{{text}}</v-btn>
</template>
<script>
export default {
    props: {
        text: {
            type: String
        },
        disabled: {
            type: Boolean
        },
        loading: {
            type: Boolean
        }
    }
}
</script>