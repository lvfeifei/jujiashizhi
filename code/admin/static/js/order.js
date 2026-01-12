export default {
    data() {
        return {
            order_id: 0,
            label: [{
                text: '待审核',
                name: 'first'
            }, {
                text: '待评估',
                name: 'second'
            }, {
                text: '已评估',
                name: 'third'
            }, {
                text: '待施工',
                name: 'fourth'
            }, {
                text: '已完成',
                name: 'fifth'
            }, {
                text: '取消审核',
                name: 'sixth'
            }, {
                text: '已取消',
                name: 'seventh'
            }, {
                text: '已评价',
                name: 'eighth'
            },
            ],
            list: [],
            orderList: [],
            province: [],
            city: [],
            county: [],
            street: [],
            activeName: 'first',
            select_name: '',
            select_province_name: '',
            select_city_name: '',
            select_county_name: '',
            select_street_name: '',
            input_content: '',
            date: '',
            type: 1,
            dialogVisible: false, //备注弹窗
            logistics: false, //修改物流弹窗
            dialogRefund: false, //主动退款弹窗
            delivery: false, //发货弹窗
            goodsDetail: [],
            logistics_radio: "1",
            remark: "", //备注文字
            detail_data: '', //订单详情
            logisticsList: [], //物流列表
            logistics_id: "",
            logistics_number: '',
            canRefundPrice: 0, //可退款金额
            setRefundPrice: '', //输入的退款金额
            src: '',
            start_time: '',
            end_time: '',
            count: 0,
            pageNum: 1,
            limit: 10,
        }
    },
    //进入页面加载
    mounted: function () {
        var that = this;
        //在缓存中获取值
        that.src = '/Experience/excel?select_name=' + that.select_name + '&input_content=' + that.input_content + '&start_time=' + that.start_time +
            '&end_time=' + that.end_time + '&type=' + that.type;
            
        //获取商品
        that.getOrderList(that.pageNum, that.limit);
        that.getprovince();
    },
    methods: {
        //类型
        handleClick(tab) {
            var that = this;
            if (tab.name == 'first') {
                that.type = 1;//【1:待审核】【2:待评估】【3:已评估】【4:待施工】【5:已完成】【6:取消审核】【7:已取消】[8:已评价]
            }
            if (tab.name == 'second') {
                that.type = 2;
            }
            if (tab.name == 'third') {
                that.type = 3;
            }
            if (tab.name == 'fourth') {
                that.type = 4;
            }
            if (tab.name == 'fifth') {
                that.type = 5;
            }
            if (tab.name == 'sixth') {
                that.type = 6;
            }
            if (tab.name == 'seventh') {
                that.type = 7;
            }
            if (tab.name == 'eighth') {
                that.type = 8;
            }

            // that.getOrderList(1, that.limit);
        },


        //请订单列表api
        getOrderList: function (pageNum, limit) {
            var that = this;
            //初始化数据
            that.orderList = [];
            //请求的参数
            var formData = {};
            formData.status = that.type;
            formData.token = that.token;
            formData.page = pageNum;
            formData.limit = that.limit;
            //查询的分类
            if (that.select_name) {
                formData.select_name = that.select_name;
            }
            //查询内容
            if (that.input_content) {
                formData.input_content = that.input_content;
            }

            //时间条件
            if (that.date) {
                that.start_time = that.formatDateTime(that.date[0]);
                that.end_time = that.formatDateTime(that.date[1]);
                formData.start_time = that.formatDateTime(that.date[0]);
                formData.end_time = that.formatDateTime(that.date[1]);
            }
            //请求登陆接口
            that.axios.post("/order/orderlist", formData, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    //获取状态
                    for (var i in res.data.list) {
                        res.data.list[i].status_text = that.getOrderStatus(res.data.list[i]);
                        that.orderList.push(res.data.list[i]);
                    }
                    that.count = res.data.count;
                },
                function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'处理异常'}`
                    });
                });
        },
        getprovince() {
            this.axios.post('/Procityarea/prov_list').then(res => {
                console.log(res);
                this.province = res.province
            })
        },
        getcity() {
            this.city.length = 0;
            this.select_city_name = '';
            this.county.length = 0;
            this.select_county_name = '';
            this.street.length = 0;
            this.select_street_name = '';
            var param = {
                province_code: this.select_province_name
            }
            this.axios.post('/procityarea/city_list', param, {
                emulateJSON: true
            }).then(res => {
                console.log(res);
                this.city = res.data
            })
        },
        getcounty() {
            this.county.length = 0;
            this.select_county_name = '';
            this.street.length = 0;
            this.select_street_name = '';
            var param = {
                city_code: this.select_city_name
            }
            this.axios.post('/procityarea/area_list', param, {
                emulateJSON: true
            }).then(res => {
                console.log(res);
                this.county = res.data
            })
        },
        getstreet() {
            this.street.length = 0;
            this.select_street_name = '';
            var param = {
                area_code: this.select_county_name
            }
            this.axios.post('/procityarea/street_list', param, {
                emulateJSON: true
            }).then(res => {
                console.log(res);
                this.street = res.data
            })
        },

        //获取订单状态
        getOrderStatus: function (res) {
            var status_text = "已取消";
            switch (res.status) {
                case 1:
                    if (res.is_refund === 1) {
                        status_text = '已全部退款';
                        break;
                    } else {
                        status_text = '已完成';
                        break;
                    }
                case 2:
                    if (res.is_refund === 1) {
                        status_text = '已全部退款';
                        break;
                    } else {
                        if (res.is_pay === 3) {
                            status_text = '待支付';
                            break;
                        }
                        if ((res.is_pay !== 3) && (res.is_deliver === 3)) {
                            status_text = '待发货';
                            break;
                        }
                        if ((res.is_pay !== 3) && (res.is_deliver === 1)) {
                            status_text = '待收货';
                            break;
                        }

                        if (res.is_refund === 3) {
                            status_text = '退款处理中';
                            break;
                        }
                        if (res.is_refund === 3) {
                            status_text = '退款处理中';
                            break;
                        }
                    }
                default:
                    if (res.is_refund === 1) {
                        status_text = '已全部退款';
                        break;
                    } else {
                        status_text = '已取消';
                        break;
                    }

            }
            return status_text;
        },

        //下一页
        handleCurrentChange: function (pageNum) {
            var that = this;
            that.pageNum = pageNum;
            that.getOrderList(that.pageNum, 10);
        },

        //搜索
        search: function () {
            var that = this;
            that.src = '/Experience/excel?select_name=' + that.select_name + '&input_content=' + that.input_content + '&start_time=' + that.start_time +
                '&end_time=' + that.end_time + '&type=' + that.type;
            that.getOrderList(that.pageNum, that.limit);
        },

        //时间转换
        formatDateTime: function (date) {
            var y = date.getFullYear();
            var m = date.getMonth() + 1;
            m = m < 10 ? ('0' + m) : m;
            var d = date.getDate();
            d = d < 10 ? ('0' + d) : d;
            var h = date.getHours();
            var minute = date.getMinutes();
            minute = minute < 10 ? ('0' + minute) : minute;
            return y + '-' + m + '-' + d + ' ' + h + ':' + minute;
        },

        //跳转到详情
        toDetail: function (e) {
            var that = this;
            that.$router.push({
                path: '/nav3/order_detail',
                query: {
                    order_id: e
                }
            });
        },


        //备注弹窗
        remarks: function (e) {
            var that = this;
            that.dialogVisible = true;
            that.order_id = e;
        },
        //执行添加备注
        confirm: function () {
            var that = this;
            that.axios.post("/Order_info/edit", {
                token: that.token,
                order_id: that.order_id,
                seller_remark: that.remark
            }, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${'添加成功'}`
                    });
                    that.dialogVisible = false;
                    that.remark = '';
                    that.getOrderList(that.pageNum, that.limit);
                },
                function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'处理异常'}`
                    });
                });
        },

        //查询单个分类值
        detail: function (e) {
            var that = this;
            //请求登陆接口
            that.axios.post("/Order_info/detail", {
                token: that.token,
                order_id: e,
            }, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    that.detail_data = res;
                    that.logistics_id = res.express_id ? res.express_id : "";
                    that.logistics_number = res.compannum;
                },
                function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'处理异常'}`
                    });
                });
        },

        //查询物流api
        getLogisticsList: function () {
            //请求登陆接口
            var that = this;
            that.axios.post("/Order_info/get_express", {
                token: that.token,
            }, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    that.logisticsList = res;
                },
                function () {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'处理异常'}`
                    });
                });
        },

        //发货
        confirmOrder: function () {
            var that = this;
            var formData = {};
            formData.token = that.token;
            formData.order_id = that.order_id;

            if (that.logistics_radio === '1') {
                if (!that.logistics_id) {
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'请选择物流公司'}`
                    });
                    return false;
                }
                if (!that.logistics_number) {
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'请输入物流单号'}`
                    });
                    return false;
                }
                formData.express_id = that.logistics_id;
                formData.compannum = that.logistics_number;
            }


            that.axios.post("/Order_info/order_send", formData, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${'发货成功'}`
                    });
                    that.delivery = false;
                    that.getOrderList(that.pageNum, that.limit);
                },
                function (res) {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${res.msg}`
                    });
                });
        },

        //退款弹窗
        checkRefund: function (e) {
            var that = this;
            that.dialogRefund = true;
            that.order_id = e;
            that.getRefundPrice(e);
        },
        //可退款金额
        getRefundPrice: function (order_id) {
            var that = this;
            that.axios.post("/Order_info/order_refund_price", {
                token: that.token,
                order_id: order_id
            }, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    that.canRefundPrice = res;
                },
                function (res) {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${res.msg}`
                    });
                });
        },
        //退款
        refund: function () {
            var that = this;
            if (that.setRefundPrice) {
                if (Number(that.setRefundPrice) > Number(that.canRefundPrice)) {
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'退款金额不能大于剩余金额'}`
                    });
                    return false;
                }

                if (Number(that.setRefundPrice) === 0) {
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'退款金额不能为0'}`
                    });
                    return false;
                }
            }
            //请求api
            that.axios.post("/Order_info/order_refund", {
                token: that.token,
                order_id: that.order_id,
                price: that.setRefundPrice,
                system_manager_id: that.user_id
            }, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${'退款成功'}`
                    });
                    that.dialogRefund = false;
                    that.setRefundPrice = '';
                    that.getOrderList(that.pageNum, that.limit);
                },
                function (res) {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${res.msg}`
                    });
                });
        },

        //发货弹窗
        sendDelivery: function (e, index) {
            var that = this;
            that.delivery = true;
            that.order_id = e;
            that.detail(e);
            that.getLogisticsList();
            that.goodsDetail = [];
            that.goodsDetail.push(that.orderList[index]);

            that.logistics_radio = '1';
            that.logistics_id = '';
            that.logistics_number = '';
        },

        //修改物流弹窗
        editDelivery: function (e, index) {
            var that = this;
            that.logistics = true;
            that.order_id = e;
            that.detail(e);
            that.getLogisticsList();
            that.goodsDetail = [];
            that.goodsDetail.push(that.orderList[index]);

            that.logistics_radio = '1';
            that.logistics_id = that.orderList[index].express_id;
            that.logistics_number = that.orderList[index].compannum;
        },

        //执行修改物流
        doEdit: function () {
            var that = this;
            var formData = {};
            formData.token = that.token;
            formData.id = that.order_id;

            if (that.logistics_radio === '1') {
                if (!that.logistics_id) {
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'请选择物流公司'}`
                    });
                    return false;
                }
                if (!that.logistics_number) {
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${'请输入物流单号'}`
                    });
                    return false;
                }
                formData.express_id = that.logistics_id;
                formData.compannum = that.logistics_number;
            }

            that.axios.post("/Order_info/edit_express", formData, {
                emulateJSON: true
            }).then(
                function (res) {
                    // 处理成功的结果
                    that.$message({
                        type: 'success',
                        message: `操作提示: ${'修改成功'}`
                    });
                    that.logistics = false;
                    that.getOrderList(that.pageNum, that.limit);
                },
                function (res) {
                    // 处理失败的结果
                    that.$message({
                        type: 'error',
                        message: `操作提示: ${res.msg}`
                    });
                });
        },

        //取消订单
        cancelOrder: function (e) {
            var that = this;
            that.$confirm('确定取消此订单?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                that.axios.post("/Order_info/delOrder", {
                    token: that.token,
                    order_id: e
                }, {
                    emulateJSON: true
                }).then(
                    function (res) {
                        // 处理成功的结果
                        that.$message({
                            type: 'success',
                            message: `操作提示: ${'取消成功'}`
                        });
                        that.getOrderList(that.pageNum, that.limit);
                    },
                    function (res) {
                        // 处理失败的结果
                        that.$message({
                            type: 'error',
                            message: `操作提示: ${res.msg}`
                        });
                    });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        }

    }
}
