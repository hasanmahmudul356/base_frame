import AdminDashboard from "../views/admin/adminDashboard";
import baseLayouts from "../views/admin/baseLayouts";
import permissionList from "../views/rbac/permission/permissionList";
import NotFound from "../views/NotFound";
import userList from "../views/rbac/users/userList";
import rolesList from "../views/rbac/roles/rolesList";
import modulesList from "../views/rbac/modules/modulesList";
import roleModuleList from "../views/rbac/role_module/roleModuleList";
import configurationList from "../views/configuration/configurationList";


const admin_routes = [
    {
        path: '/admin',
        component: baseLayouts,
        children: [
            {
                path: 'dashboard',
                component: AdminDashboard,
                meta : {dataUrl:'api/permissions', pageTitle:'Dashboard'}
            },
            {
                path: 'permissions',
                component: permissionList,
                meta : {dataUrl:'api/permissions',pageTitle:'Permissions'}
            },
            {
                path: 'user',
                component: userList,
                meta : {dataUrl:'api/user', pageTitle:'Users'}
            },
            {
                path: 'role',
                component: rolesList,
                meta : {dataUrl:'api/role', pageTitle:'Roles'}
            },
            {
                path: 'module',
                component: modulesList,
                meta : {dataUrl:'api/module', pageTitle:'Module List'}
            },
            {
                path: 'role_module',
                component: roleModuleList,
                meta : {dataUrl:'api/role_module', pageTitle:'Role Modules'}
            },
            {
                path: 'configuration',
                component: configurationList,
                meta : {dataUrl:'api/settings', pageTitle:'Configurations'}
            },
            { path: '*', redirect: '/404' },
            { path: '/404', component: NotFound },
        ]
    }
];

export default admin_routes;