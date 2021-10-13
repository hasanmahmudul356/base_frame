export default {
    data() {
        return {
            baseUrl: baseUrl,
        }
    },
    watch: {
        'errors': {
            handler: function (value) {
                var is_invalid = $('.is-invalid');
                $(is_invalid).removeAttr("title", '');
                $(is_invalid).removeClass('is-invalid');
                if (value.items.length > 0) {
                    value.items.forEach(function (val) {
                        var message = val.msg;
                        var target = $("[name='" + val.field + "']");
                        $(target).addClass('is-invalid');
                        $(target).attr("title", message.replace(val.field, ""));
                    });
                }
            },
            deep: true
        },
        '$store.getters.httpRequest': function () {
            if (this.httpRequest) {
                $('button').attr('disabled', 'disabled');
                $('input').attr('disabled', 'disabled');
            } else {
                $('button').removeAttr('disabled');
                $('input').removeAttr('disabled');
            }
        },
        '$route.path': function () {
            this.assignCurrentAccess();
        }
    },
    computed: {
        formType: function () {
            return this.$store.getters.formType;
        },
        formObject: function () {
            return this.$store.getters.formObject;
        },
        dataList: function () {
            return this.$store.getters.dataList;
        },
        updateId: function () {
            return this.$store.getters.updateId;
        },
        httpRequest: function () {
            return this.$store.getters.httpRequest;
        },
        requiredData: function () {
            return this.$store.getters.requiredData;
        },
        modalTitle: function () {
            return this.$store.getters.modalTitle;
        },
        Config: function () {
            return this.$store.getters.Config;
        },
        currentPage: function () {
            return this.$store.getters.currentPage;
        },

    },
    methods: {
        assignCurrentAccess: function () {
            const _this = this;
            var path = this.$route.path;
            var currentPage = {};

            $.each(_this.Config.menus, function (index, each) {
                if (path == each.link) {
                    currentPage = each;
                } else {
                    $.each(each.submenus, function (index, eachSub) {
                        if (path == eachSub.link) {
                            currentPage = eachSub;
                        }
                    });
                }
            });
            _this.$store.commit('currentPage', currentPage);

        },

        can: function (permission) {
            if (this.currentPage.name !== undefined && this.currentPage.name !== null){
                var name = `${this.currentPage.name}_${permission}`;
                var access = false;
                if (this.currentPage.permissions[name] !== undefined){
                    access = true;
                }else{
                    access = false;
                }
                console.log(name+'|'+access);
                return access;
            }
            return false;
        },
        showData(dataArray, fieldName) {
            if ((dataArray !== null && dataArray !== undefined)
                && (dataArray[fieldName] !== undefined && dataArray[fieldName] !== null)) {
                return dataArray[fieldName];
            } else {
                return '-';
            }
        },
        getConfig: function (Obj, name) {
            if ((Obj !== undefined && Obj !== null !== null) &&
                (Obj[name] !== undefined && Obj[name] !== null)) {
                return Obj[name];
            } else {
                return name;
            }
        },
        openModal: function (modalName = 'formModal', title = false) {
            if (title) {
                this.$store.commit('modalTitle', title);
            }
            $('#' + modalName).modal({
                backdrop: 'static',
                keyboard: true,
                show: true
            });
            this.$validator.reset();
        },
        closeModal: function (modalName = 'createModal', resetForm = true, resetFormType = true) {
            const _this = this;
            this.$validator.reset();
            $('#' + modalName).modal('hide');
            this.$store.commit('formType', 1);
            if (resetForm) {
                this.$store.commit('formObject', {});
            }
            if (resetFormType) {
                _this.$store.state.formType = 1;
            }
        },
        getUrl: function () {
            if (this.$route.meta.dataUrl !== undefined) {
                return this.$route.meta.dataUrl;
            }
            return '';
        },
        urlGenerate: function (customUrl = false) {
            var url = '';
            if (customUrl) {
                url = `${baseUrl}/${customUrl}`;
            } else {
                url = `${baseUrl}/${this.getUrl()}`;
            }
            return url;
        },
        assignValidationError: function (errors) {
            const _this = this;
            $.each(errors, function (index, errorValue) {
                _this.$validator.errors.add({
                    id: index,
                    field: index,
                    name: index,
                    msg: errorValue[0],
                });
            })
        },
        resetForm: function (formData) {
            if (typeof formData == 'object') {
                Object.keys(formData).forEach(function (key) {
                    formData[key] = '';
                });
                return formData;
            }
        },
        pageTitle: function () {
            return this.$route.meta.pageTitle;
        },
        resetFilter: function (parameter = []) {
            this.$store.commit('resetFilter', parameter);
            this.getDataList();
        },
        clickImageInput: function (ID) {
            $('#' + ID).click();
        },
        getImage: function (imagePath) {
            if (imagePath !== undefined && imagePath !== '') {
                return `${baseUrl}/${imagePath}`;
            }
        },
        indexToLabel: function (string) {
            var removed_space = '';
            if (typeof string === 'string') {
                removed_space = string.replace(/_/g, ' ');
                if (typeof removed_space !== 'string') {
                    return index;
                }
                return removed_space.charAt(0).toUpperCase() + removed_space.slice(1)
            }
            return '';
        },
        addRow: function (object, pushEr) {
            if (typeof object === 'object') {
                object.push(pushEr);
            }
        },
        deleteRow: function (object, index) {
            object.splice(index, 1);
        },
    },
    mounted() {

    }
}
