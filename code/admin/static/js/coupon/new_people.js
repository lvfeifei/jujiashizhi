import richText from "../../../src/page/common/richText";
export default {
    components: {
        richText,
    },
    data() {
        return {
            form_data: {
                status: '1',
				picture: '',
				receive_type: '1',
				content:'',
            },
            rules: {
 
            },

            token: '',
			is_dialog: false,
			// 七牛云信息
			upload_img_url: this.adminApi.upload_url,
			postData: {},
			domain: '',
			page:1,
			limit:10,
			count:0,
			tableData:[],
			can_use_list:[],
			can_use_count:0,
			can_use_page:1,
			can_use_limit:10,

			range:'',
            range_list:[{
                id:1,
                name:'全场优惠券'
            },{
                id:2,
                name:'指定商品优惠券'
            }],
            type:'',
            type_list:[{
                id:1,
                name:'直减券'
            },{
                id:2,
                name:'满减券'
            }],
            status:'',
            status_list:[{
                id:1,
                name:'推广中'
            },{
                id:2,
                name:'已过期'
            },{
                id:3,
                name:'已作废' 
			}],
			
			can_use_ids:[],
        }
    },

    /**
     * 进入页面加载
     */
    mounted() {
        var that = this;
		
		that.getQiNiuToken();
		that.get_detail();
		that.get_coupon_list();
		that.can_use_coupon();
    },

    /**
     * 方法
     */
    methods: {
		/**
		 * 获取七牛云token
		 */
		getQiNiuToken: function () {
			var that = this;
			//请求登陆接口
			that.axios.post("/Qiniu/getToken", {
				token: that.token,
			}, {
				emulateJSON: true
			}).then(
				function (res) {
					// 处理成功的结果
					that.postData = {
						token: res.upToken,
					}
					that.domain = res.domain;
				},
				function () {
					// 处理失败的结果
					that.$message({
						type: 'error',
						message: `操作提示: ${'处理异常'}`
					});
				});
		},

		/**
         * 筛选
         */
        search(){
            var that = this;
            that.can_use_page = 1;
            that.can_use_coupon();
        },

        /**
         * 获取可用优惠券列表
         */
        can_use_coupon() {
            var that = this;
            var form_data = {};
            form_data.token = that.token;
            if(that.key){
                form_data.key = that.key;
            }
            if(that.class_id){
                form_data.class_id = that.class_id;
            }
            form_data.page = that.page;
            form_data.limit = that.limit;
            that.axios.post("/New_gift/can_use_coupon",form_data, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
					data.list.forEach(ele => {
                        ele.type_text = ele.type==1?'直减券':'满减券';
                        ele.range_text = ele.range == 1 ?'全场优惠券':'指定商品优惠券';
                        ele.date = ele.date_type == 1 ? (ele.start_time+'--'+ele.end_time):('领取后'+ ele.day +'天过期')           
                    })
                    that.can_use_list = data.list;
                    that.can_use_count = data.count
                }
            });
		},
		
		/**
		 * 可用优惠券下一页
		 */
		can_use_next(currentPage){
			var that = this;
			that.can_use_page = currentPage;
			that.get_goods_list();
		},

        /**
         * 添加优惠券
         */
        add_people() {
            var that = this;
            that.is_dialog = true;
        },

        /**
         * 获取详情
         */
        get_detail() {
            var that = this;
            that.axios.post("/New_gift/show_edit", {
                token: that.token,
            }, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
                    that.form_data.status = data.status.toString();
					that.form_data.picture = data.picture;
					that.form_data.receive_type = data.receive_type.toString();
					that.form_data.content = data.content;
                }
            });
        },



		/**
         * 获取优惠券列表
         */
        get_coupon_list() {
            var that = this;
            that.axios.post("/New_gift/coupon_list", {
				token: that.token,
				page:that.page,
				limit:that.limit
            }, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
					data.list.forEach(ele => {
                        ele.type_text = ele.type==1?'直减券':'满减券';
                        ele.range_text = ele.range == 1 ?'全场优惠券':'指定商品优惠券';
                        ele.date = ele.date_type == 1 ? (ele.start_time+'--'+ele.end_time):('领取后'+ ele.day +'天过期')           
                    })
					that.tableData = data.list;
					that.count = data.count
                }
            });
        },
        /**
         * 图片上传成功
         */
        img_succ(res) {
			const that = this;
			that.form_data.picture = that.domain + res.key;
            // that.form_data.picture.push({
            //     url: that.domain + res.key,
            // })
        },



        /**
         * 富文本改变时
         * @param {*} currentPage 
         */
        editor_change(e) {
            this.form_data.content = e;
        },

        /**
         * 保存
         */
        save(formName) {
            const that = this;
            that.$refs[formName].validate((valid) => {
                if (!valid) return that.$message.warning('请完整填写内容!');
                var formData = {};
                formData = that.form_data;
                var url = '/New_gift/edit_gift';
                that.axios.post(url, formData, {
                    emulateJSON: true
                }).then(() => {
                    that.$message.success('保存成功');
                    that.get_detail();
                });
            })
		},
		
		/**
		 * 批量选中
		 */
		change_select(e) {
			let that = this;
            that.can_use_ids = e;
		},
		
		/**
		 * 提交选中优惠券
		 */
		confirm_can_use(){
			let that = this;
			if(that.can_use_ids.length <=0){
				return that.$message.warning('请先选择要关联的优惠券')
			};
			that.$confirm('确定选中关联吗?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
				var coupon_ids = [];
                for(var i in that.can_use_ids){
                    coupon_ids.push(that.can_use_ids[i].id)
				}
				var formData = {};
                formData.coupon_ids = coupon_ids;
                var url = '/New_gift/add_coupon_list';
                that.axios.post(url, formData, {
                    emulateJSON: true
                }).then(() => {
					that.$message.success('关联成功');
					that.is_dialog = false;
					that.get_coupon_list();
					that.can_use_coupon();
                });
			}).catch(res => { });
		},
		
		/**
		 * 删除
		 */
		del_item(e){
			var that = this;

            that.$confirm('确定删除此优惠券吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                that.axios.post("/New_gift/del_gift_coupon", {
                    token: that.token,
                    gift_coupon_id: e
                }, {
                    emulateJSON: true
                }).then(
                    function(res) {
                        that.$message.success('操作成功');
						that.get_coupon_list();
						that.can_use_coupon();
                    })
            }).catch(res => {});
		}
    }
}