import axios from 'axios';

export function initialize(store, router) {
    router.beforeEach((to, from, next) => {
        window.document.title = to.meta.pageTitle !== undefined ? to.meta.pageTitle : '';
        store.state.dataList = [];
        next();
    });



    axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';

    axios.interceptors.response.use(function (response) {
        return response;
    }, function (error) {
        if (parseInt(error.response.status) === 401){
            // store.commit('logout');
        }
        return Promise.reject(error);
    });

}
