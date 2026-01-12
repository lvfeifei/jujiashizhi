import richText from "../../../src/page/common/richText";
export default {
    components: {
        richText,
    },
    data() {
        return {
            coupomn_id:0,
            form_data: {
                name: '',
                type: '1',
                total_price: '',
                price: '',
                range: '1',
                goods_ids: [],
                check_goods_list:[],
                date_type: '1',
                day:'',
                date:[],
                total_count:'',
                content:'',
            },
            rules: {
                name: [{
                    required: true,
                    message: '请输入优惠券名称',
                    trigger: 'blur',
                }],
                total_price: [{
                    required: true,
                    message: '请输入满多少钱',
                    trigger: 'blur'
                }],
                price: [{
                    required: true,
                    message: '请输入优惠券金额',
                    trigger: 'blur'
                }],
                check_goods_list: [{
                    type:'array',
                    required: true,
                    message: '请选择关联商品',
                    min: 1
                }],
                day: [{
                    required: true,
                    message: '请输入使用时间',
                    trigger: 'blur'
                }],
                date:[{
                    type:'array',
                    required: true,
                    message: '请选择开始结束时间',
                    min: 2
                }],
                total_count:[{
                    required: true,
                    message: '请输入库存',
                    trigger: 'blur'
                }],
                content:[{
                    required: true,
                    message: '请输入优惠券说明',
                    trigger: 'blur'
                }]
            },

            token: '',
            is_dialog: false,

            key:'',
            page:1,
            limit:10,
            class_list:[],
            class_id:'',
            goods_list:[],
            count:0,
            can_use_ids:[],
        }
    },

    /**
     * 进入页面加载
     */
    mounted() {
        var that = this;
        
        if (that.$route.query.coupon_id) {
			that.coupon_id = that.$route.query.coupon_id;
			that.get_detail();
        }
        that.get_class_list();
        that.get_goods_list();
    },

    /**
     * 方法
     */
    methods: {

        /**
         * 获取分类
         */
        get_class_list(){
            let that = this;

            let formData = {};
            formData.token = that.token;
      
            that.axios.post("/classif/index", formData, {
              emulateJSON: true
            }).then(
              function (res) {
                var data = res;
                if (data) {
                  that.class_list = data.list;
                }
              });
        },

        /**
         * 筛选
         */
        search(){
            var that = this;
            that.page = 1;
            that.get_goods_list();
        },

        /**
         * 获取商品列表
         */
        get_goods_list() {
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
            that.axios.post("/Coupon/can_use_goods",form_data, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
                    that.goods_list = data.list;
                    that.count = data.count
                }
            });
        },

        /**
         * 关联
         */
        relation_shop() {
            var that = this;
            that.is_dialog = true;
        },

        /**
		 * 下一页
		 */
		handleCurrentChange: function (currentPage) {
			var that = this;
			that.page = currentPage;
			that.get_goods_list();
		},

        /**
         * 获取详情
         */
        get_detail() {
            var that = this;
            that.axios.post("/Coupon/show_edit", {
                token: that.token,
                coupon_id:that.coupon_id
            }, {
                emulateJSON: true
            }).then(res => {
                var data = res;
                if (data) {
                    that.form_data.name = data.name;
                    that.form_data.type = data.type.toString();
                    that.form_data.total_price= data.total_price;
                    that.form_data.price= data.price;
                    that.form_data.range= data.range.toString();
                    that.form_data.check_goods_list=[],
                    that.form_data.date_type = data.date_type.toString();
                    that.form_data.day=data.day;

                    that.form_data.total_count=data.total_count;
                    that.form_data.content=data.content;
                    if(data.date_type == 1){
                        that.form_data.date.push(data.start_time,data.end_time);
                    }
                    if(data.range == 2){
                        that.form_data.check_goods_list = data.goods_list;
                        that.can_use_ids = data.goods_list;
                    }
                }
            });
        },

        back(){
            this.$router.go(-1);
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
                if(that.form_data.date_type == 1){
                    formData.start_time = that.form_data.date[0];
                    formData.end_time = that.form_data.date[1];
                }
                if(that.form_data.type == 2){
                    if(!that.form_data.total_price || !that.form_data.price){
                        return that.$message.warning('请完整填写满减内容!');
                    }
                }
                if(that.form_data.range == 2){
                    if(that.form_data.check_goods_list.length<=0){
                        return that.$message.warning('请先选择关联商品!');
                    }
                    for(var i in that.form_data.check_goods_list){
                        formData.goods_ids.push(that.form_data.check_goods_list[i].id)
                    }
                }
                
                if (that.coupon_id) {
                    formData.coupon_id = that.coupon_id;
                }
                var url = that.coupon_id ? '/Coupon/edit_coupon':'/Coupon/add_coupon';
                that.axios.post(url, formData, {
                    emulateJSON: true
                }).then(() => {
                    that.$message.success('保存成功');
                    that.$router.go(-1);
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
		 * 提交选中商品
		 */
		confirm_can_use(){
			let that = this;
			if(that.can_use_ids.length <=0){
				return that.$message.warning('请先选择要关联的商品')
			};
			that.$confirm('确定选中关联吗?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
                for(var i in that.can_use_ids){
                    that.form_data.check_goods_list.push(that.can_use_ids[i])
                }
                that.is_dialog = false;
			}).catch(res => { });
        },
        
        /**
         * 取消关联
         */
        del_goods(index, rows) {
            var that= this;
            that.$confirm('确定取消关联吗?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
                rows.splice(index, 1);
			}).catch(res => { });

        }

    }
}