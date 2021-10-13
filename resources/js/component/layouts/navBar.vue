<template>
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" v-if="Config.user !== undefined">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="sidebar-brand-text mx-3">{{Config.user.name}}</div>
        </a>

        <template>

        </template>
        <template v-for="(menu, m_index) in Config.menus">
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <template v-if="menu.submenus.length === 0">
                    <router-link class="nav-link" :to="menu.link">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>{{menu.display_name}}</span>
                    </router-link>
                </template>
                <template v-else>
                    <a class="nav-link collapsed" data-toggle="collapse" :data-target="`#students${m_index}`" aria-expanded="true" aria-controls="students">
                        <i class="fas fa-fw fa-cog"></i>
                        <span>{{menu.display_name}}</span>
                    </a>
                    <template v-for="(eachSubmenu, sub_index) in menu.submenus">
                        <div :id="`students${m_index}`" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                            <div class="collapse-inner rounded">
                                <router-link :to="eachSubmenu.link" class="collapse-item text-white">
                                    <i class="fa fa-list"></i>
                                    <span>{{eachSubmenu.display_name}}</span>
                                </router-link>
                            </div>
                        </div>
                    </template>
                </template>
            </li>
            <hr class="sidebar-divider my-0">
        </template>
    </ul>
</template>

<script>
    export default {
        name: "navBar",
        data(){
            return{
                configuration : [],
            }
        },
        methods : {

        },
        mounted() {
            this.getConfigurations();
        },
        created() {

        }
    }
</script>

<style scoped>
    .sidebar .nav-item .nav-link {
        padding: 8px 1rem !important;
    }
    .sidebar .nav-item .collapse .collapse-inner, .sidebar .nav-item .collapsing .collapse-inner{
        padding: 0;
        margin: 0;
    }
    .sidebar .nav-item .collapse .collapse-inner .collapse-item:hover,
    .sidebar .nav-item .collapse .collapse-inner .collapse-item.active,
    .nav-link.router-link-exact-active.active{
        background-color: transparent;
    }
</style>