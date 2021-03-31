<template>
    <v-btn tile color="primary" :disabled="disabled">{{text}}</v-btn>
</template>
<script>
export default {
    props: {
        text: {
            type: String
        },
        disabled: {
            type: Boolean
        }
    }
}
</script>