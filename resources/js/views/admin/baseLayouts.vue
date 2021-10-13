<template>
    <div class="page-top">
        <top-navbar></top-navbar>
        <div id="wrapper">
            <nav-bar></nav-bar>
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <div class="container-fluid mt-3">
                        <router-view></router-view>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import TopNavbar from "../../component/layouts/topNavbar";
    import NavBar from "../../component/layouts/navBar";
    export default {
        name: "baseLayouts",
        components: {NavBar, TopNavbar}
    }
</script>

<style scoped>

</style>