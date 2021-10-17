export default {
    data() {
        return {
            formData: {},
            SelectFilter: {},
            filter : {},
            per_page : 20,
            imageData: [],
        }
    },
    methods: {
        onFileSelected: function (event, fieldIndex) {
            let file = event.target.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.imageData[fieldIndex] = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        getGeneralData: function ($array) {
            const _this = this;
            _this.axios.post(_this.urlGenerate('api/general'), $array).then(response => {
                _this.$store.commit('requiredData', response.data.result)
            }).catch(function (error) {
                _this.$toastr('error', 'Whoops..!! something went wrong', 'Error');
            });
        },
        getDataList: function (page = 1) {
            const _this = this;
            var data_params = Object.assign(this.filter, _this.$store.state.filter, {page: page});
            this.$store.commit('httpRequest', true);
            _this.axios({method: "get", url: _this.urlGenerate(), params: data_params})
                .then(function (response) {
                var retData = response.data;
                _this.$store.commit('httpRequest', false);
                if (parseInt(retData.status) === 2000) {
                    _this.$store.commit('dataList', retData.result)
                } else {
                    _this.$store.state.DataList = [];
                    _this.$toastr(retData.type, retData.message, 'Warning');
                }
            }).catch(function (error) {
                var retData = error.response.data;
                _this.$toastr('error', retData.message, 'Error');
            });
        },
        onFileSelect(event, name){
            const _this = this;
            var formData = new FormData();
            var imagefile = event.target.files[0];
            formData.append("image", imagefile);

            _this.axios.post(_this.urlGenerate('api/auth/image_upload'), formData).then(response => {
                if (parseInt(response.data.status) === 2000) {
                    var retData = response.data.result;
                    _this.$set(_this.formObject, name, retData.path);
                    _this.$set(_this.formObject, 'image_url', retData.image_url);
                } else {
                    _this.$toastr('error', response.data.message, 'Error');
                }
            }).catch(function (error) {
                _this.$toastr('error', 'Something wrong', 'Error');
            });
        },
        submitForm: function (formData, model = true, callback = false) {
            const _this = this;
            var URL,method;
            if (_this.formType === 2) {
                 URL = `${_this.urlGenerate()}/${_this.updateId}`;
                 method = 'put';
            } else {
                 URL = _this.urlGenerate();
                 method = 'post';
            }
            this.$validator.validate().then(valid => {
                if (valid) {
                    this.$validator.errors.clear();
                    _this.$store.state.httpRequest = true;
                    var form_data = Object.assign(formData, this.imageData);
                    _this.axios({method: method, url: URL, data: form_data}).then(function (response) {
                        var retData = response.data;
                        _this.$store.state.httpRequest = false;
                        if (parseInt(retData.status) === 2000) {
                            if (model){
                                _this.$store.state.currentFromModel = 1;
                                _this.closeModal(model);
                                _this.getDataList();
                                _this.resetForm(formData);
                            }
                            if (typeof callback == 'function'){
                                callback(retData.result);
                            }
                            _this.$toastr('success', retData.message, 'Success');
                        }
                        if (parseInt(retData.status) === 3000) {
                            _this.$toastr('warning', retData.message, 'Warning');
                            _this.assignValidationError(retData.result);
                        }
                        if (parseInt(retData.status) === 5000) {
                            _this.$toastr('error', retData.message, 'Error');
                        }
                    }).catch(function (error) {
                        _this.$toastr('error', 'Something Wrong', 'Error');
                    });
                }
            });
        },
        editData: function (data, updateId, model = 'formModal', callback = false) {
            const _this = this;
            _this.$store.commit('formObject', data);
            _this.$store.commit('updateId', updateId);
            _this.$store.commit('formType', 2);
            _this.openModal(model);

            if (typeof callback == 'function'){
                callback(data);
            }
        },
        deleteInformation: function (index, ID, url = false, callback = false, callDataList = true) {
            const _this = this;
            this.$swal({
                title: 'Are you sure..??',
                text: 'Data will be delete Permanently??',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-check"></i>',
                cancelButtonText: '<i class="fa fa-close"></i>',
                showCloseButton: true,
            }).then((result) => {
                if (result.value) {
                    var URL = !url ? `${_this.urlGenerate()}/${ID}` : url;
                    _this.axios.delete(URL)
                        .then(function (response) {
                            var retData = response.data;
                            _this.$store.commit('httpRequest', false);
                            if (parseInt(retData.status) === 2000) {
                                _this.$toastr('success', retData.message, 'Success');
                                if (callDataList){
                                    _this.getDataList();
                                }
                                if (typeof callback == 'function'){
                                    callback(true);
                                }
                            } else {
                                _this.$toastr('warning', retData.message, 'Warning');
                            }
                        }).catch(function (error) {
                        _this.$toastr('error', 'Something Wrong', 'Error');
                    });
                }
            });
        },

        getConfigurations: function (page = 1) {
            const _this = this;
            this.$store.commit('httpRequest', true);
            _this.axios({method: "get", url: _this.urlGenerate('api/configurations')})
                .then(function (response) {
                    _this.$store.commit('httpRequest', false);
                    if (parseInt(response.data.status) === 2000) {
                        _this.$store.commit('Config', response.data.result);
                        _this.assignCurrentAccess();
                    }
                }).catch(function (error) {
                var retData = error.response.data;
                _this.$toastr('error', retData.message, 'Error');
            });
        },
    },
}
