<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <slot name="baselink"/>
            <button class="navbar-toggler"
                    type="button"
                    @click="hamClicked"
                    aria-label="Navbar Menu Button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div :class="['navbar-collapse', {'collapse': menuDisplay}]">
                <div class="mr-auto">
                    <slot name="collapsible"/>
                </div>
                <slot name="status"/>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        name: 'main-nav',
        data: () => ({
            menuDisplay: true,
        }),
        methods: {
            hamClicked() {
                this.menuDisplay = !this.menuDisplay;
            },
        },
    };
</script>
