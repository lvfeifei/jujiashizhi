import richText from "../../src/page/common/richText";

export default {
	components: {
		richText
	},
	data() {
		return {
			goods_id: 0,
			place_origin: '',
			goods_name: '', //名称
			class_list: [], //分类
			class_value: '',
			goods_min_price: '', //原价
			goods_post_price: '',
			serve_list: [], //服务保障
			serve_value: '',
			goods_sort: '', //权重
			is_country: '1', //是否国内
			checked: true,
			inputVisible: false,
			inputValue: '',
			dialogVisible: false, //添加规格弹窗
			dialogVisible2: false, // 修改规格弹窗
			class_name: '',
			class_price: '',
			class_stock: '',
			cost: '',
			class_table: [], //规格
			class_table_index: -1, // 修改的规格当前索引
			goods_desc: '',
			dialogImageUrl: '', //轮播图
			banner: [],
			goods_banner: false,
			postData: {},
			descImageUrl: '', //商品简介图片
			desc_img: false,
			desc: '',
			goods_desc_text: '', //商品简介文字
			domain: '', //图片域名
			fileList: [], //轮播图列表数据
			desclist: [], //简介图列表数据

			// 商品分类
			goods_type_list: [],
			class_id: '',
			// 市场价
			market_price: '',
			status: '1',

			remaining_stock: 0,
			sale_stock: 0,
		};
	},

	//进入页面加载
	mounted: function () {
		var that = this;

		if (that.$route.query.goods_id) {
			that.goods_id = that.$route.query.goods_id;
			that.detail();
		}

		that.get_goods_type();
	},

	/**
	 * 方法
	 */
	methods: {
		/**
		 * 分类列表
		 */
		get_goods_type() {
			let that = this;

			let formData = {};
			formData.token = that.token;

			that.axios.post("/classif/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					var data = res;
					if (data) {

						that.goods_type_list = data.list;
					}
				});
		},

		//规格
		handleClose(tag) {
			this.dynamicTags.splice(this.dynamicTags.indexOf(tag), 1);
		},

		//删除
		handleRemove(file, fileList) {
			var that = this;
			that.fileList = fileList;
		},

		handlePictureCardPreview(file) {
			this.dialogImageUrl = file.url;
			this.goods_banner = true;
		},

		//显示错误
		handleError(res) {
		},

		//上传成功后在图片框显示图片
		handleAvatarSuccess(res, file) {
			var that = this;
			var bannerUrl = {
				url: that.domain + res.key,
				uid: file['raw']['uid']
			};
			that.fileList.push(bannerUrl);
		},

		//文件超出个数限制时的钩子
		handleExceed(files, fileList) {
			this.$message.error('最多上传10张图片');
		},

		//获取七牛云token
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

		//添加规格弹窗
		confirm() {
			var that = this;
			that.dialogVisible = false;
			if (that.class_table_index == -1) {
				that.class_table.push({
					id: 0,
					name: that.class_name,
					price: that.class_price,
					total_stock: that.class_stock,
					remaining_stock: parseInt(that.class_stock) - parseInt(that.sale_stock),
					sale_stock: that.sale_stock,
					cost_price: that.cost
				});
			} else {
				that.class_table[that.class_table_index].name = that.class_name;
				that.class_table[that.class_table_index].price = that.class_price;
				that.class_table[that.class_table_index].total_stock = that.class_stock;
				that.class_table[that.class_table_index].remaining_stock = parseInt(that.class_stock) - parseInt(that.sale_stock);
				that.class_table[that.class_table_index].sale_stock = that.sale_stock;
				that.class_table[that.class_table_index].cost_price = that.cost;
			}


			// 清空表单数据
			that.edit_spec_empty();
		},

		/**
		 * 修改-规格弹窗
		 * @param indx
		 */
		edit_spec_dialog: function (indx) {
			var that = this;
			that.dialogVisible = true;
			that.class_table_index = indx;
			that.class_name = that.class_table[indx].name;
			that.class_price = that.class_table[indx].price;
			that.class_stock = that.class_table[indx].total_stock;
			that.remaining_stock = that.class_table[indx].remaining_stock;
			that.sale_stock = that.class_table[indx].sale_stock;
			that.cost = that.class_table[indx].cost_price;
		},

		/**
		 * 确认编辑内容
		 */
		// confirm_edit_spec: function () {
		// 	var that = this;
		// 	// 获取规格ID
		// 	let spec_id = that.class_table[that.class_table_index].id || 0;
		// 	if (spec_id != 0) {
		// 		that.edit_spec_ajax(spec_id);
		// 	} else {
		// 		// 请求回调
		// 		that.edit_spec_fun();
		// 	}
		// },

		/**
		 * 回调请求操作
		 */
		edit_spec_fun: function () {
			var that = this;
			// 设置
			that.class_table[that.class_table_index].name = that.class_name;
			that.class_table[that.class_table_index].price = that.class_price;
			that.class_table[that.class_table_index].total_stock = that.class_stock;
			that.class_table[that.class_table_index].cost_price = that.cost;

			// 关闭-修改规格弹窗
			that.dialogVisible2 = false;

			// 清空数据
			that.edit_spec_empty();
		},

		/**
		 * 清空-规格表单字段
		 */
		edit_spec_empty: function () {
			var that = this;
			// 清空值
			that.class_name = '';
			that.class_price = '';
			that.class_stock = '';
			that.cost = '';
			that.class_table_index = -1;
		},

		/**
		 * 异步修改内容
		 */
		// edit_spec_ajax: function (spec_id) {
		// 	let that = this;
		// 	if (!spec_id) {
		// 		return false;
		// 	}
		// 	that.axios.post("/Goods/editGoodsSpec", {
		// 		token: that.token,
		// 		spec_id: spec_id,
		// 		name: that.class_name,
		// 		cost_price: that.class_price,
		// 		total_stock: that.class_stock,
		// 		price: that.cost
		// 	}, {
		// 		emulateJSON: true
		// 	}).then(function (res) {
		// 		// 处理成功的结果
		// 		that.$message({
		// 			type: 'success',
		// 			message: `操作提示: ${'修改成功'}`
		// 		});

		// 		// 请求回调
		// 		that.edit_spec_fun();
		// 	},
		// 		function () {
		// 			// 处理失败的结果
		// 			that.$message({
		// 				type: 'error',
		// 				message: `操作提示: ${'处理异常'}`
		// 			});

		// 			// 请求回调
		// 			that.edit_spec_fun();
		// 		});
		// },

		//删除之前的
		delete_spec: function (index) {
			var that = this;
			// let spec_id = that.class_table[index].id || 0;
			// if (spec_id == 0) {
			// that.class_table.splice(index, 1);
			// } else {
			// 	that.$confirm('此操作将永久删除商品已经添加的规格, 是否继续?', '提示', {
			// 		confirmButtonText: '确定',
			// 		cancelButtonText: '取消',
			// 		type: 'warning'
			// 	}).then(() => {
			// 		that.do_delete(spec_id);
			// 		that.class_table.splice(index, 1);
			// 	}).catch(() => {
			// 		this.$message({
			// 			type: 'info',
			// 			message: '已取消删除'
			// 		});
			// 	});
			// }


			that.$confirm('确定删除此规格吗?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
				that.class_table.splice(index, 1);
			}).catch(() => {

			});
		},

		/**
		 * 执行删除规格
		 */
		// do_delete: function (e) {
		// 	var that = this;
		// 	that.axios.post("/Goods/delGoodsSpec", {
		// 		token: that.token,
		// 		spec_id: e
		// 	}, {
		// 		emulateJSON: true
		// 	}).then(
		// 		function (res) {
		// 			// 处理成功的结果
		// 			that.$message({
		// 				type: 'success',
		// 				message: `操作提示: ${'删除成功'}`
		// 			});
		// 		},
		// 		function () {
		// 			// 处理失败的结果
		// 			that.$message({
		// 				type: 'error',
		// 				message: `操作提示: ${'处理异常'}`
		// 			});
		// 		});
		// },

		//修改商品
		exitData: function () {
			var that = this;
			if (!that.goods_name) {
				that.$message({
					type: 'error',
					message: `操作提示: ${'请输入商品名称'}`
				});
				return false;
			}
			if (!that.place_origin) {
				that.$message({
					type: 'error',
					message: `操作提示: ${'请输入商品产地'}`
				});
				return false;
			}

			if (that.class_table === '') {
				that.$message({
					type: 'error',
					message: `操作提示: ${'至少添加一个规格'}`
				});
				return false;
			}
			if (that.fileList.length === 0) {
				that.$message({
					type: 'error',
					message: `操作提示: ${'至少添加一张轮播图'}`
				});
				return false;
			}
			if (that.desclist.length === 0) {
				that.$message({
					type: 'error',
					message: `操作提示: ${'至少添加一张详情图'}`
				});
				return false;
			}

			if (!that.goods_id) {
				that.$message.warning('请选择商品分类');
				return false;
			}

			var formData = {};
			formData.id = that.goods_id;
			formData.token = that.token;
			formData.class_id = that.class_id;
			formData.market_price = that.market_price;
			formData.title = that.goods_name;
			formData.place_origin = that.place_origin;
			formData.cost_price = that.goods_min_price;
			if (that.goods_post_price) {
				formData.freight = that.goods_post_price;
			} else {
				formData.freight = 0;
			}
			formData.goods_spec = that.class_table;
			if (that.fileList) {
				for (var i in that.fileList) {
					that.banner.push(that.fileList[i].url)
				}
			}
			formData.goods_imgs = that.desc;
			formData.goods_picture = that.banner;
			formData.picture = that.fileList[0].url;
			formData.sort = that.goods_sort;
			formData.is_abroad = that.is_country;
			formData.status = that.status;

			//请求登陆接口
			that.axios.post("/Goods/editGoods", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					// 处理成功的结果
					that.$message({
						type: 'success',
						message: `操作提示: ${'发布成功'}`
					});
					that.$router.push('/nav1/shop_list');
				},
				function () {
					// 处理失败的结果
					that.$message({
						type: 'error',
						message: `操作提示: ${'处理异常'}`
					});
				});
		},

		//放弃编辑
		cancel: function () {
			var that = this;
			that.$router.push('/nav1/shop_list');
		},

		/**
		 * 富文本改变时
		 */
		editor_change(e) {
			this.desc = e;
		},

		//请求商品信息
		detail: function () {
			var that = this;
			that.axios.post("/Goods/show_edit", {
				token: that.token,
				id: that.goods_id
			}, {
				emulateJSON: true
			}).then(
				function (res) {
					// 处理成功的结果
					that.goods_name = res.title;
					that.place_origin = res.place_origin;
					that.desc = res.goods_imgs;
					that.class_id = res.class_id;
					that.market_price = res.market_price;
					that.status = res.status + '';

					//权重
					that.goods_sort = res.sort;
					if (res.is_abroad) {
						that.is_country = (res.is_abroad).toString();
					}

					//规格
					for (var i in res.spec_info) {
						that.class_table.push({
							id: res.spec_info[i].id,
							name: res.spec_info[i].name,
							price: res.spec_info[i].price,
							total_stock: res.spec_info[i].total_stock,
							sale_stock: res.spec_info[i].sale_stock,
							remaining_stock: res.spec_info[i].remaining_stock,
							cost_price: res.spec_info[i].cost_price
						})
					}

					//轮播图
					for (var i in res.goods_picture) {
						that.fileList.push({
							url: res.goods_picture[i]
						})
					}

					//文字简介
					if (res.content) {
						that.goods_desc_text = res.content;
					}

					//简介图片
					for (var i in res.goods_picture) {
						that.desclist.push({
							url: res.goods_picture[i]
						})
					}
				},
				function () {
					// 处理失败的结果
					that.$message({
						type: 'error',
						message: `操作提示: ${'处理异常'}`
					});
				});
		},

		//关闭规格弹窗
		close_table: function () {
			var that = this;
			that.dialogVisible = false;
		},

	}
}
