<template>
<v-app>
    <router-view name="navigation"></router-view>
    <transition name="fade">
        <router-view></router-view>
    </transition>
    <notifications group="app" position="bottom right" />
</v-app>
</template>
<script>
export default {
    data: () => {
        return {

        }
    }
}
</script>
<style>
.fade-enter-active,
.fade-leave-active {
    transition-property: opacity;
    transition-duration: .50s;
}

.fade-enter-active {
    transition-delay: .50s;
}

.fade-enter,
.fade-leave-active {
    opacity: 0
}

</style>